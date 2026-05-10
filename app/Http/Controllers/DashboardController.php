<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total' => Coupon::count(),
            'approved' => Coupon::where('status', 'approved')->count(),
            'claimed' => Coupon::where('status', 'claimed')->count(),
            'panitia' => \App\Models\User::where('role', 'panitia')
                ->where('organization_id', Auth::user()->organization_id)
                ->count(),
        ];

        // Stats for chart: claimed vs (approved + pending)
        $chartData = [
            'claimed' => $stats['claimed'],
            'unclaimed' => Coupon::whereIn('status', ['pending', 'approved'])->count(),
        ];

        return view('dashboard', compact('stats', 'chartData'));
    }
}
