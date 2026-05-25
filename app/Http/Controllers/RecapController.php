<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecapController extends Controller
{
    public function index(Request $request)
    {
        $activeOrgId = session('active_organization_id');
        $organizationId = $request->get('organization_id', $activeOrgId);
        $status = $request->get('status', 'all');

        // Security: Ensure user belongs to the requested organization
        if (!Auth::user()->organizations()->where('organization_id', $organizationId)->exists()) {
            $organizationId = $activeOrgId;
        }

        // Only admins can switch between their organizations in this view
        if (!Auth::user()->isAdmin($organizationId)) {
            $organizationId = $activeOrgId;
        }

        $query = Coupon::withoutGlobalScope('organization')
            ->where('organization_id', $organizationId);

        if ($status && $status !== 'all') {
            if ($status === 'unclaimed') {
                $query->whereIn('status', ['pending', 'approved']);
            } else {
                $query->where('status', $status);
            }
        }

        $coupons = $query->with(['creator', 'approver'])->latest()->get();

        $summary = [
            'total_count' => $coupons->count(),
            'total_kg' => $coupons->sum(function($c) { 
                return $c->quantity * $c->weight_kg; 
            }),
            'claimed_count' => $coupons->where('status', 'claimed')->count(),
            'unclaimed_count' => $coupons->whereIn('status', ['pending', 'approved'])->count(),
        ];

        $organizations = Auth::user()->organizations;
        $selectedOrganization = Organization::find($organizationId);

        return view('recap.index', compact('coupons', 'summary', 'organizations', 'selectedOrganization'));
    }
}
