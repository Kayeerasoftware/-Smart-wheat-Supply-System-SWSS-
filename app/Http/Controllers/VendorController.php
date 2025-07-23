<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Vendor;
use App\Services\JavaServerService;
use Exception;

class VendorController extends Controller
{
    protected $javaServerService;

    public function __construct(JavaServerService $javaServerService)
    {
        $this->middleware('auth');
        $this->javaServerService = $javaServerService;
    }

    public function showApplicationForm()
    {
        $user = Auth::user();
        $vendor = Vendor::where('user_id', $user->user_id)->first();
        $requiredDocuments = config('vendor.required_documents');
        
        return view('dashboards.vendor', compact('vendor', 'requiredDocuments'));
    }

    public function submitApplication(Request $request)
    {
        try {
            $user = Auth::user();
            $vendor = Vendor::firstOrNew(['user_id' => $user->user_id]);

            $data = $request->validate([
                'business_name' => 'required|string|max:255',
                'business_type' => 'required|string|max:255',
                'business_description' => 'required|string',
                'tax_id' => 'nullable|file|mimes:pdf|max:2048',
                'financial_records' => 'nullable|file|mimes:pdf|max:2048',
                'certifications' => 'nullable|file|mimes:pdf|max:2048',
                'insurance' => 'nullable|file|mimes:pdf|max:2048',
            ]);

            $pdf_paths = $this->handleDocumentUploads($request, $vendor);

            $vendor->application_data = [
                'business_name' => $data['business_name'],
                'business_type' => $data['business_type'],
                'business_description' => $data['business_description'],
            ];
            $vendor->pdf_paths = $pdf_paths;
            $vendor->status = 'pending';
            $vendor->save();

            // Process documents with Java server
            if (!empty($pdf_paths)) {
                $this->processPdfScoring($vendor);
            }

            return redirect()
                ->route('vendor.dashboard')
                ->with('success', 'Application submitted successfully! Our team will review your documents.');
        } catch (Exception $e) {
            Log::error('Application submission failed', [
                'user_id' => $user->user_id,
                'error' => $e->getMessage()
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Application submission failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Handle document uploads
     */
    protected function handleDocumentUploads(Request $request, Vendor $vendor): array
    {
        $pdf_paths = [];
        $requiredDocuments = array_keys(config('vendor.required_documents'));

        foreach ($requiredDocuments as $field) {
            if ($request->hasFile($field)) {
                // Delete old file if it exists
                if ($vendor->pdf_paths && isset($vendor->pdf_paths[$field])) {
                    Storage::disk('public')->delete($vendor->pdf_paths[$field]);
                }

                // Store new file
                $path = $request->file($field)->store('vendor_docs', 'public');
                $pdf_paths[$field] = $path;
            } elseif ($vendor->pdf_paths && isset($vendor->pdf_paths[$field])) {
                // Keep existing file
                $pdf_paths[$field] = $vendor->pdf_paths[$field];
            }
        }

        return $pdf_paths;
    }

    /**
     * Process PDF scoring via Java server
     */
    protected function processPdfScoring(Vendor $vendor)
    {
        try {
            // Check Java server health first
            if (!$this->javaServerService->checkHealth()) {
                throw new Exception('Java server is not available');
            }

            // Process documents and get scores
            $scores = $this->javaServerService->processVendorDocuments($vendor);
            
            // Update vendor with scores
            $vendor->update([
                'score_financial' => $scores['financial_score'] ?? null,
                'score_reputation' => $scores['reputation_score'] ?? null,
                'score_compliance' => $scores['compliance_score'] ?? null,
                'total_score' => $scores['total_score'] ?? null,
                'processing_status' => $scores['processing_status'] ?? 'completed'
            ]);

            // Check if score meets threshold for facility visit
            $this->checkScoreThreshold($vendor);

            Log::info('PDF scoring completed successfully', [
                'vendor_id' => $vendor->id,
                'total_score' => $vendor->total_score
            ]);
        } catch (Exception $e) {
            Log::error('PDF scoring failed', [
                'vendor_id' => $vendor->id,
                'error' => $e->getMessage()
            ]);

            // Update vendor status to indicate manual review needed
            $vendor->update([
                'processing_status' => 'manual_review_required',
                'status' => 'pending'
            ]);

            throw $e;
        }
    }

    /**
     * Check if vendor score meets threshold for facility visit
     */
    protected function checkScoreThreshold(Vendor $vendor)
    {
        $threshold = config('vendor.approval_threshold', 70);
        
        if ($vendor->total_score >= $threshold) {
            // Score meets threshold - update status for facility visit
            $vendor->update(['status' => 'pending_visit']);
            
            Log::info('Vendor meets score threshold for facility visit', [
                'vendor_id' => $vendor->id,
                'score' => $vendor->total_score,
                'threshold' => $threshold
            ]);

            // Notify admin about pending facility visit
            // TODO: Implement admin notification
        } else {
            // Score below threshold - reject application
            $vendor->update(['status' => 'rejected']);
            
            Log::info('Vendor score below threshold - application rejected', [
                'vendor_id' => $vendor->id,
                'score' => $vendor->total_score,
                'threshold' => $threshold
            ]);

            // Notify vendor about rejection
            // TODO: Implement vendor notification
        }
    }

    public function showStatus()
    {
        $user = Auth::user();
        $vendor = Vendor::where('user_id', $user->user_id)
            ->with('facilityVisits')
            ->first();

        if (!$vendor) {
            return redirect()
                ->route('vendor.application')
                ->with('info', 'Please submit your application first.');
        }

        $statusMessage = $this->javaServerService->getStatusMessage($vendor->status);
        
        return view('dashboards.vendor', compact('vendor', 'statusMessage'));
    }

    /**
     * Download vendor document
     */
    public function downloadDocument(Request $request, $type)
    {
        $user = Auth::user();
        $vendor = Vendor::where('user_id', $user->user_id)->firstOrFail();

        if (!$vendor->pdf_paths || !isset($vendor->pdf_paths[$type])) {
            abort(404, 'Document not found');
        }

        $path = $vendor->pdf_paths[$type];
        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'Document file not found');
        }

        return Storage::disk('public')->download($path, $type . '.pdf');
    }

    /**
     * Handle PDF upload from rejected page
     */
    public function uploadNewPdf(Request $request)
    {
        try {
            $user = Auth::user();
            $vendor = Vendor::where('user_id', $user->id)->first();

            if (!$vendor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vendor record not found'
                ], 404);
            }

            $request->validate([
                'pdf_document' => 'required|file|mimes:pdf|max:10240', // 10MB max
            ]);

            // Delete old PDF files if they exist
            if ($vendor->pdf_paths) {
                foreach ($vendor->pdf_paths as $path) {
                    Storage::disk('public')->delete($path);
                }
            }

            // Store new PDF
            $pdfPath = $request->file('pdf_document')->store('vendor_docs', 'public');
            
            // Update vendor with new PDF path
            $vendor->pdf_paths = ['application_pdf' => $pdfPath];
            $vendor->status = 'pending';
            $vendor->processing_status = 'pending_review';
            $vendor->save();

            // Process PDF with Java server (optional)
            try {
                $this->processPdfScoring($vendor);
                
                // Check if validation was successful
                if ($vendor->status === 'pdf_validated' || $vendor->status === 'pending_visit') {
                    return response()->json([
                        'success' => true,
                        'message' => 'PDF uploaded and validated successfully!',
                        'redirect' => route('supplier.dashboard')
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'PDF validation failed. Please check your document and try again.'
                    ]);
                }
            } catch (Exception $e) {
                Log::error('PDF validation failed during upload', [
                    'vendor_id' => $vendor->id,
                    'error' => $e->getMessage()
                ]);

                // If Java server is not available, mark as pending for manual review
                $vendor->update([
                    'status' => 'pending',
                    'processing_status' => 'manual_review_required',
                    'pdf_validation_result' => [
                        'valid' => false,
                        'message' => 'PDF validation service unavailable. Document submitted for manual review.',
                        'overallScore' => 0
                    ]
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'PDF uploaded successfully! Document submitted for manual review.',
                    'redirect' => route('supplier.dashboard')
                ]);
            }

        } catch (Exception $e) {
            Log::error('PDF upload failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
