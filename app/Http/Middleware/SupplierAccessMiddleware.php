<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Vendor;

class SupplierAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $accessLevel = 'basic')
    {
        $user = Auth::user();
        
        // Only apply to suppliers
        if ($user->role !== 'supplier') {
            return $next($request);
        }

        $vendor = Vendor::where('user_id', $user->id)->first();
        
        if (!$vendor) {
            return redirect()->route('login')->with('error', 'Vendor profile not found.');
        }

        // Check access based on vendor status
        switch ($accessLevel) {
            case 'basic':
                if (!$vendor->hasBasicAccess() && !$vendor->hasFullAccess()) {
                    return redirect()->route('supplier.dashboard')
                        ->with('error', 'You need PDF validation to access this feature.');
                }
                break;
                
            case 'full':
                if (!$vendor->hasFullAccess()) {
                    return redirect()->route('supplier.dashboard')
                        ->with('error', 'You need full approval to access this feature.');
                }
                break;
                
            case 'pdf_validated':
                if ($vendor->status === Vendor::STATUS_PDF_REJECTED) {
                    return redirect()->route('supplier.dashboard')
                        ->with('error', 'Your PDF validation failed. Please upload a valid document.');
                }
                break;
        }

        return $next($request);
    }
} 