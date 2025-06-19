<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vendor;
use App\Providers\RouteServiceProvider;
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
    public function create(): View
    {
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
                'password' => $request->password, // Will be hashed by the User model
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

            Auth::login($user);

            return redirect()->route('dashboard');
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

        // Handle PDF upload
        if ($request->hasFile('application_pdf')) {
            $pdfPath = $request->file('application_pdf')->store('supplier_docs', 'public');
            $pdfPaths['application_pdf'] = $pdfPath;
        }

        // Handle image upload
        if ($request->hasFile('business_image')) {
            $imagePath = $request->file('business_image')->store('supplier_images', 'public');
            $imagePaths['business_image'] = $imagePath;
        }

        // Create vendor record
        Vendor::create([
            'user_id' => $user->user_id,
            'application_data' => [
                'business_name' => $request->business_name,
                'business_type' => $request->business_type,
                'business_description' => $request->business_description,
            ],
            'pdf_paths' => $pdfPaths,
            'image_paths' => $imagePaths,
            'status' => 'pending',
            'processing_status' => 'pending_review'
        ]);

        Log::info('Supplier registration completed', [
            'user_id' => $user->user_id,
            'business_name' => $request->business_name,
            'has_pdf' => !empty($pdfPaths),
            'has_image' => !empty($imagePaths)
        ]);
    }
}