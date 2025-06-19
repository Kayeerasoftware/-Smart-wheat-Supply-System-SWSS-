<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Vendor;
use App\Models\FacilityVisit;
use App\Models\User;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin'); // TODO: Create admin middleware
    }

    public function scheduleFacilityVisit(Request $request, $vendorId)
    {
        $request->validate([
            'scheduled_at' => 'required|date|after:today',
            'notes' => 'nullable|string',
        ]);

        $vendor = Vendor::findOrFail($vendorId);
        
        // Create facility visit
        $visit = FacilityVisit::create([
            'vendor_id' => $vendor->id,
            'scheduled_at' => $request->scheduled_at,
            'status' => 'scheduled',
            'notes' => $request->notes,
        ]);

        // Optionally: Trigger notification to supplier here
        // if (class_exists('App\\Notifications\\FacilityVisitScheduled')) {
        //     $vendor->user->notify(new \App\Notifications\FacilityVisitScheduled($visit));
        // }

        // Update vendor status
        $vendor->update(['status' => 'pending_visit']);

        return response()->json([
            'success' => true,
            'message' => 'Facility visit scheduled successfully'
        ]);
    }

    public function approveVendor($vendorId)
    {
        $vendor = Vendor::findOrFail($vendorId);
        
        DB::transaction(function () use ($vendor) {
            // Update vendor status
            $vendor->update(['status' => 'approved']);
            
            // Change user role from vendor to supplier
            $user = $vendor->user;
            $user->update(['role' => 'supplier']);
            
            // Update facility visit outcome
            $latestVisit = $vendor->facilityVisits()->latest()->first();
            if ($latestVisit) {
                $latestVisit->update([
                    'status' => 'completed',
                    'outcome' => 'approved'
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Vendor approved successfully. Role changed to supplier.'
        ]);
    }

    public function rejectVendor(Request $request, $vendorId)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $vendor = Vendor::findOrFail($vendorId);
        
        DB::transaction(function () use ($vendor, $request) {
            // Update vendor status
            $vendor->update(['status' => 'rejected']);
            
            // Update facility visit outcome
            $latestVisit = $vendor->facilityVisits()->latest()->first();
            if ($latestVisit) {
                $latestVisit->update([
                    'status' => 'completed',
                    'outcome' => 'rejected'
                ]);
            }
            
            // TODO: Send rejection notification to vendor
        });

        return response()->json([
            'success' => true,
            'message' => 'Vendor rejected successfully'
        ]);
    }

    public function viewVendorDetails($vendorId)
    {
        $vendor = Vendor::with(['user', 'facilityVisits'])->findOrFail($vendorId);
        
        return response()->json([
            'success' => true,
            'vendor' => $vendor,
            'application_data' => $vendor->application_data,
            'pdf_paths' => $vendor->pdf_paths,
            'facility_visits' => $vendor->facilityVisits,
            'scores' => [
                'financial' => $vendor->score_financial,
                'reputation' => $vendor->score_reputation,
                'compliance' => $vendor->score_compliance,
                'total' => $vendor->total_score,
            ]
        ]);
    }

    public function updateVendorScores(Request $request, $vendorId)
    {
        $request->validate([
            'score_financial' => 'required|numeric|min:0|max:100',
            'score_reputation' => 'required|numeric|min:0|max:100',
            'score_compliance' => 'required|numeric|min:0|max:100',
        ]);

        $vendor = Vendor::findOrFail($vendorId);
        
        $totalScore = ($request->score_financial * 0.4) + 
                     ($request->score_reputation * 0.3) + 
                     ($request->score_compliance * 0.3);
        
        $vendor->update([
            'score_financial' => $request->score_financial,
            'score_reputation' => $request->score_reputation,
            'score_compliance' => $request->score_compliance,
            'total_score' => $totalScore,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Vendor scores updated successfully',
            'total_score' => $totalScore
        ]);
    }
}
