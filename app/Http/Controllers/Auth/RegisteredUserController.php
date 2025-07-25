<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vendor;
use App\Providers\RouteServiceProvider;
use App\Services\JavaServerService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    protected $javaServerService;

    public function __construct(JavaServerService $javaServerService)
    {
        $this->javaServerService = $javaServerService;
    }

    public function create(): View
    {
        // Check if user is already authenticated
        if (Auth::check()) {
            // If authenticated, show a message that they can register another account
            session()->flash('info', 'You are currently logged in. You can register a new account below, or logout to register with a different email.');
        }
        
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        try {
            $request->validate([
                'username' => ['required', 'string', 'max:255', 'unique:users'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'role' => ['required', 'string', 'in:admin,farmer,supplier,manufacturer,distributor,retailer'],
                'phone' => ['nullable', 'string', 'max:15'],
                'address' => ['nullable', 'string'],
                // Supplier-specific validation
                'business_name' => ['nullable', 'string', 'max:255'],
                'business_type' => ['nullable', 'string', 'in:wholesaler,distributor,processor,storage,other'],
                'business_description' => ['nullable', 'string', 'max:1000'],
                'application_pdf' => ['nullable', 'file', 'mimes:pdf', 'max:2048'],
                'business_image' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,gif', 'max:8192'],
            ]);

            // Create user
            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'phone' => $request->phone,
                'address' => $request->address,
                'status' => 'active',
            ]);

            // If user is a supplier, create vendor record and handle file uploads
            if ($request->role === 'supplier') {
                $this->handleSupplierRegistration($request, $user);
            }

            event(new Registered($user));

            // Check if there's already an authenticated user
            if (Auth::check()) {
                // If already logged in, don't log in the new user
                // Instead, redirect with a success message
                return redirect()->route('register')
                    ->with('success', 'New account created successfully! You can now login with the new credentials.');
            } else {
                // If not logged in, redirect to login page with success message
                // Don't automatically log in the user
                return redirect()->route('login')
                    ->with('success', 'Account created successfully! Please login with your credentials.');
            }
            
        } catch (\Exception $e) {
            Log::error('Registration failed: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Registration failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Handle supplier-specific registration process
     */
    protected function handleSupplierRegistration(Request $request, User $user): void
    {
        $pdfPaths = [];
        $imagePaths = [];
        $pdfValidationResult = null;

        // Handle PDF upload
        if ($request->hasFile('application_pdf')) {
            $pdfPath = $request->file('application_pdf')->store('supplier_docs', 'public');
            $pdfPaths['application_pdf'] = $pdfPath;
            
            // Validate PDF with Java server
            try {
                $fullPdfPath = storage_path('app/public/' . $pdfPath);
                $pdfValidationResult = $this->javaServerService->validateSupplierPdf(
                    $fullPdfPath,
                    $user->id,
                    $request->business_name
                );
                
                Log::info('PDF validation completed for supplier registration', [
                    'user_id' => $user->id,
                    'valid' => $pdfValidationResult['valid'],
                    'score' => $pdfValidationResult['overallScore'] ?? 0
                ]);
                
            } catch (\Exception $e) {
                Log::error('PDF validation failed during registration', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage()
                ]);
                
                // If Java server is unavailable, we'll still create the vendor record
                // but mark it as pending validation
                $pdfValidationResult = [
                    'valid' => false,
                    'message' => 'PDF validation service unavailable: ' . $e->getMessage(),
                    'overallScore' => 0
                ];
            }
        }

        // Handle image upload
        if ($request->hasFile('business_image')) {
            $imagePath = $request->file('business_image')->store('supplier_images', 'public');
            $imagePaths['business_image'] = $imagePath;
        }

        // Determine initial status based on PDF validation
        $initialStatus = 'pending';
        $processingStatus = 'pending_review';
        
        if ($pdfValidationResult && $pdfValidationResult['valid']) {
            $initialStatus = 'pdf_validated';
            $processingStatus = 'pending_visit';
        } elseif ($pdfValidationResult && !$pdfValidationResult['valid']) {
            $initialStatus = 'pdf_rejected';
            $processingStatus = 'pdf_validation_failed';
        }

        // Create vendor record
        Vendor::create([
            'user_id' => $user->id,
            'application_data' => [
                'business_name' => $request->business_name,
                'business_type' => $request->business_type,
                'business_description' => $request->business_description,
            ],
            'pdf_paths' => $pdfPaths,
            'image_paths' => $imagePaths,
            'status' => $initialStatus,
            'processing_status' => $processingStatus,
            'pdf_validation_result' => $pdfValidationResult,
            'score_financial' => $pdfValidationResult['sectionScores']['Financial Stability'] ?? 0,
            'score_reputation' => $pdfValidationResult['sectionScores']['Business Reputation'] ?? 0,
            'score_compliance' => $pdfValidationResult['sectionScores']['Regulatory Compliance'] ?? 0,
            'total_score' => $pdfValidationResult['overallScore'] ?? 0,
        ]);

        Log::info('Supplier registration completed', [
            'user_id' => $user->id,
            'business_name' => $request->business_name,
            'has_pdf' => !empty($pdfPaths),
            'has_image' => !empty($imagePaths),
            'status' => $initialStatus,
            'pdf_valid' => $pdfValidationResult['valid'] ?? false
        ]);
    }
}