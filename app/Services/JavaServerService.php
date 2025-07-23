<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Vendor;
use Exception;

class JavaServerService
{
    protected $baseUrl;
    protected $timeout;
    protected $apiKey;
    protected $endpoints;

    public function __construct()
    {
        $this->baseUrl = config('vendor.java_server.url', 'http://localhost:8080');
        $this->timeout = config('vendor.java_server.timeout', 60);
        $this->apiKey = config('vendor.java_server.api_key');
        $this->endpoints = config('vendor.java_server.endpoints');
    }

    /**
     * Validate supplier PDF document using the Java server
     */
    public function validateSupplierPdf($pdfFilePath, $supplierId, $businessName)
    {
        try {
            Log::info('Validating PDF for supplier', [
                'supplier_id' => $supplierId,
                'business_name' => $businessName,
                'pdf_path' => $pdfFilePath
            ]);

            $response = Http::timeout($this->timeout)
                ->withHeaders($this->getHeaders())
                ->post($this->baseUrl . $this->endpoints['validate_pdf'], [
                    'pdfFilePath' => $pdfFilePath,
                    'supplierId' => $supplierId,
                    'businessName' => $businessName
                ]);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('PDF validation successful', [
                    'supplier_id' => $supplierId,
                    'valid' => $data['valid'],
                    'score' => $data['overallScore'] ?? 0
                ]);
                return $data;
            } else {
                Log::error('PDF validation failed', [
                    'supplier_id' => $supplierId,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                throw new Exception('PDF validation failed: ' . $response->body());
            }

        } catch (Exception $e) {
            Log::error('Error calling Java server for PDF validation', [
                'supplier_id' => $supplierId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Process vendor documents and validate PDFs
     */
    public function processVendorDocuments(Vendor $vendor): array
    {
        try {
            $this->validateVendorDocuments($vendor);
            
            // Get the main application PDF for validation
            $mainPdfPath = $this->getMainApplicationPdf($vendor);
            
            if (!$mainPdfPath) {
                throw new Exception('No application PDF found for validation');
            }

            // Validate the main PDF with Java server
            $validationResult = $this->validateSupplierPdf(
                $mainPdfPath,
                $vendor->user_id,
                $vendor->application_data['business_name'] ?? 'Unknown Business'
            );

            // Calculate scores based on validation result
            $scores = $this->calculateScoresFromValidation($validationResult, $vendor);
            
            Log::info('Vendor document processing completed', [
                'vendor_id' => $vendor->id,
                'scores' => $scores
            ]);
            
            return $scores;
            
        } catch (Exception $e) {
            Log::error('Vendor document processing failed', [
                'vendor_id' => $vendor->id,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }

    /**
     * Get the main application PDF path
     */
    protected function getMainApplicationPdf(Vendor $vendor): ?string
    {
        // Priority order for PDF validation
        $pdfPriorities = ['tax_id', 'financial_records', 'certifications', 'insurance'];
        
        foreach ($pdfPriorities as $type) {
            if (isset($vendor->pdf_paths[$type])) {
                $fullPath = storage_path('app/public/' . $vendor->pdf_paths[$type]);
                if (file_exists($fullPath)) {
                    return $fullPath;
                }
            }
        }
        
        return null;
    }

    /**
     * Calculate scores from validation result
     */
    protected function calculateScoresFromValidation(array $validationResult, Vendor $vendor): array
    {
        $overallScore = $validationResult['overallScore'] ?? 0;
        $sectionScores = $validationResult['sectionScores'] ?? [];
        
        // Map section scores to our scoring categories
        $financialScore = $sectionScores['Financial Stability'] ?? 0;
        $reputationScore = $sectionScores['Business Reputation'] ?? 0;
        $complianceScore = $sectionScores['Regulatory Compliance'] ?? 0;
        
        // Calculate weighted total score
        $weights = $this->getScoringWeights();
        $totalScore = ($financialScore * $weights['financial']) +
                     ($reputationScore * $weights['reputation']) +
                     ($complianceScore * $weights['compliance']);
        
        return [
            'financial_score' => $financialScore,
            'reputation_score' => $reputationScore,
            'compliance_score' => $complianceScore,
            'total_score' => $totalScore,
            'overall_score' => $overallScore,
            'processing_status' => 'completed',
            'validation_result' => $validationResult
        ];
    }

    /**
     * Validate vendor documents before processing
     */
    protected function validateVendorDocuments(Vendor $vendor): void
    {
        if (!$vendor->pdf_paths || empty($vendor->pdf_paths)) {
            throw new Exception('No documents found for vendor');
        }

        $maxSize = config('vendor.java_server.shared_storage.max_file_size', 2048) * 1024; // Convert to bytes
        $allowedExtensions = config('vendor.java_server.shared_storage.allowed_extensions', ['pdf']);

        foreach ($vendor->pdf_paths as $type => $path) {
            if (!Storage::disk('public')->exists($path)) {
                throw new Exception("Document not found: {$type}");
            }

            $size = Storage::disk('public')->size($path);
            if ($size > $maxSize) {
                throw new Exception("Document too large: {$type}");
            }

            $extension = pathinfo($path, PATHINFO_EXTENSION);
            if (!in_array(strtolower($extension), $allowedExtensions)) {
                throw new Exception("Invalid document type: {$type}");
            }
        }
    }

    /**
     * Get headers for Java server requests
     */
    protected function getHeaders(): array
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'X-Request-ID' => uniqid('swss_', true)
        ];
        
        if ($this->apiKey) {
            $headers['Authorization'] = 'Bearer ' . $this->apiKey;
        }
        
        return $headers;
    }

    /**
     * Check if Java server is healthy and available
     */
    public function checkHealth(): bool
    {
        try {
            $response = Http::timeout(10)
                ->get($this->baseUrl . $this->endpoints['health']);

            return $response->successful();
        } catch (Exception $e) {
            Log::warning('Java server health check failed', [
                'error' => $e->getMessage(),
                'url' => $this->baseUrl . $this->endpoints['health']
            ]);
            return false;
        }
    }

    /**
     * Get scoring weights from configuration
     */
    public function getScoringWeights(): array
    {
        return config('vendor.scoring_weights', [
            'financial' => 0.4,
            'reputation' => 0.3,
            'compliance' => 0.3
        ]);
    }

    /**
     * Calculate total score manually if needed
     */
    public function calculateTotalScore(float $financial, float $reputation, float $compliance): float
    {
        $weights = $this->getScoringWeights();
        
        return ($financial * $weights['financial']) +
               ($reputation * $weights['reputation']) +
               ($compliance * $weights['compliance']);
    }

    /**
     * Get status message for vendor
     */
    public function getStatusMessage(string $status): string
    {
        return config("vendor.status_messages.{$status}", 'Status unknown');
    }

    /**
     * Get required PDF sections from Java server
     */
    public function getRequiredSections()
    {
        try {
            $response = Http::timeout(10)
                ->get($this->baseUrl . $this->endpoints['required_sections']);

            if ($response->successful()) {
                return $response->json();
            }
            
            return [];
        } catch (Exception $e) {
            Log::error('Failed to get required sections', [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Process vendor documents (legacy method for compatibility)
     */
    public function processVendorDocumentsLegacy($vendor)
    {
        // This method is kept for backward compatibility
        // New implementations should use validateSupplierPdf
        Log::warning('Using deprecated processVendorDocuments method');
        
        if (isset($vendor->pdf_paths['tax_id'])) {
            return $this->validateSupplierPdf(
                storage_path('app/public/' . $vendor->pdf_paths['tax_id']),
                $vendor->user_id,
                $vendor->application_data['business_name'] ?? 'Unknown'
            );
        }
        
        throw new Exception('No application PDF found for vendor');
    }
} 