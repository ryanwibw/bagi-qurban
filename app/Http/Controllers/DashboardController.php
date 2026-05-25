<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $orgId = session('active_organization_id');

        $stats = [
            'total' => Coupon::where('organization_id', $orgId)->count(),
            'approved' => Coupon::where('organization_id', $orgId)->where('status', 'approved')->count(),
            'claimed' => Coupon::where('organization_id', $orgId)->where('status', 'claimed')->count(),
            'panitia' => \App\Models\User::whereHas('organizations', function($q) use ($orgId) {
                    $q->where('organization_id', $orgId)->where('role', 'panitia');
                })
                ->count(),
        ];

        // Stats for chart: claimed vs (approved + pending)
        $chartData = [
            'claimed' => $stats['claimed'],
            'unclaimed' => Coupon::where('organization_id', $orgId)
                ->whereIn('status', ['pending', 'approved'])
                ->count(),
        ];

        return view('dashboard', compact('stats', 'chartData'));
    }
}
