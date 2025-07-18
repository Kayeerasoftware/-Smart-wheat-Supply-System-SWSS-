<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();
        
        // Check if supplier was recently approved
        if ($user->role === 'supplier') {
            $vendor = \App\Models\Vendor::where('user_id', $user->id)->first();
            \Log::info("Supplier login - User: {$user->id}, Vendor status: " . ($vendor ? $vendor->status : 'null') . ", Processing status: " . ($vendor ? $vendor->processing_status : 'null'));
            
            if ($vendor && $vendor->status === 'approved') {
                $latestVisit = $vendor->facilityVisits()->latest()->first();
                if ($latestVisit && $latestVisit->outcome === 'approved' && $latestVisit->updated_at->diffInHours(now()) <= 24) {
                    $request->session()->flash('recently_approved', true);
                }
                
                // Auto-complete overdue visits for approved suppliers
                if ($vendor->processing_status === 'pending_visit' || $vendor->processing_status === 'visit_completed') {
                    $pendingVisit = $vendor->facilityVisits()
                        ->where('status', 'scheduled')
                        ->where('scheduled_at', '<', now()->subMinute())
                        ->first();
                    
                    if ($pendingVisit) {
                        $pendingVisit->update([
                            'status' => 'completed',
                            'outcome' => 'approved',
                            'completed_at' => now()
                        ]);
                        
                        $vendor->update(['processing_status' => 'approved']);
                        \Log::info("Auto-completed overdue visit for supplier {$user->id} during login");
                    }
                }
            }
        }
        
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'farmer':
                return redirect()->route('farmer.dashboard');
            case 'supplier':
                return redirect()->route('supplier.dashboard');
            case 'manufacturer':
                return redirect()->route('manufacturer.dashboard');
            case 'distributor':
                return redirect()->route('distributor.dashboard');
            case 'retailer':
                return redirect()->route('retailer.dashboard');
            default:
                return redirect()->route('dashboard');
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
