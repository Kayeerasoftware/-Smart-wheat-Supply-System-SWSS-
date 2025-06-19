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
        $this->baseUrl = config('vendor.java_server.url');
        $this->timeout = config('vendor.java_server.timeout', 60);
        $this->apiKey = config('vendor.java_server.api_key');
        $this->endpoints = config('vendor.java_server.endpoints');
    }

    /**
     * Process vendor documents and get scoring
     */
    public function processVendorDocuments(Vendor $vendor): array
    {
        try {
            $this->validateVendorDocuments($vendor);
            $pdfData = $this->preparePdfData($vendor);
            
            $response = Http::timeout($this->timeout)
                ->withHeaders($this->getHeaders())
                ->post($this->baseUrl . $this->endpoints['process_documents'], [
                    'vendor_id' => $vendor->id,
                    'business_name' => $vendor->application_data['business_name'] ?? '',
                    'business_type' => $vendor->application_data['business_type'] ?? '',
                    'documents' => $pdfData
                ]);

            if ($response->successful()) {
                $scores = $response->json();
                
                Log::info('Java server scoring successful', [
                    'vendor_id' => $vendor->id,
                    'scores' => $scores
                ]);
                
                return $scores;
            }
            
            throw new Exception('Java server returned error: ' . $response->body());
        } catch (Exception $e) {
            Log::error('Java server communication error', [
                'vendor_id' => $vendor->id,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
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
     * Prepare PDF data for Java server
     */
    protected function preparePdfData(Vendor $vendor): array
    {
        $pdfData = [];
        
        foreach ($vendor->pdf_paths as $type => $path) {
            if (Storage::disk('public')->exists($path)) {
                $pdfData[$type] = [
                    'content' => base64_encode(Storage::disk('public')->get($path)),
                    'filename' => basename($path),
                    'type' => $type,
                    'size' => Storage::disk('public')->size($path),
                    'hash' => hash_file('sha256', Storage::disk('public')->path($path))
                ];
            }
        }
        
        return $pdfData;
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
     * Check Java server health
     */
    public function checkHealth(): bool
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders($this->getHeaders())
                ->get($this->baseUrl . $this->endpoints['health']);
            
            return $response->successful();
        } catch (Exception $e) {
            Log::warning('Java server health check failed', [
                'error' => $e->getMessage()
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
} 