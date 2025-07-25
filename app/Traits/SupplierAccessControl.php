<?php

namespace App\Traits;

use App\Models\Vendor;
use Illuminate\Support\Facades\Auth;

trait SupplierAccessControl
{
    /**
     * Check if supplier has basic access (PDF validated)
     */
    protected function checkSupplierBasicAccess()
    {
        $user = Auth::user();
        
        if ($user->role === 'supplier') {
            $vendor = Vendor::where('user_id', $user->id)->first();
            
            if (!$vendor || !$vendor->hasBasicAccess()) {
                return redirect()->route('supplier.dashboard')
                    ->with('error', 'You need PDF validation to access this feature.');
            }
        }
        
        return null; // Access allowed
    }

    /**
     * Check if supplier has full access (approved)
     */
    protected function checkSupplierFullAccess()
    {
        $user = Auth::user();
        
        if ($user->role === 'supplier') {
            $vendor = Vendor::where('user_id', $user->id)->first();
            
            if (!$vendor || !$vendor->hasFullAccess()) {
                return redirect()->route('supplier.dashboard')
                    ->with('error', 'You need full approval to access this feature.');
            }
        }
        
        return null; // Access allowed
    }

    /**
     * Check if supplier PDF is validated (not rejected)
     */
    protected function checkSupplierPdfValidated()
    {
        $user = Auth::user();
        
        if ($user->role === 'supplier') {
            $vendor = Vendor::where('user_id', $user->id)->first();
            
            if (!$vendor || $vendor->status === Vendor::STATUS_PDF_REJECTED) {
                return redirect()->route('supplier.dashboard')
                    ->with('error', 'Your PDF validation failed. Please upload a valid document.');
            }
        }
        
        return null; // Access allowed
    }
} 