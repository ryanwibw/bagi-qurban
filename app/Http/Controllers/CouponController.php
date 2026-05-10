<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');
        
        $query = Coupon::with(['creator', 'approver'])
            ->latest();

        if ($status) {
            $query->where('status', $status);
        }

        $coupons = $query->paginate(20)->withQueryString();

        return view('coupon.index', compact('coupons'));
    }

    public function show(Coupon $coupon)
    {
        $coupon->load(['creator', 'approver', 'organization']);
        return view('coupon.show', compact('coupon'));
    }

    public function create()
    {
        return view('coupon.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'count' => 'required|integer|min:1|max:100',
            'quantity' => 'required|integer|min:1',
            'weight_kg' => 'required|numeric|min:0.1',
        ]);

        for ($i = 0; $i < $request->count; $i++) {
            Coupon::create([
                'organization_id' => Auth::user()->organization_id,
                'created_by' => Auth::id(),
                'qr_code' => (string) Str::uuid(),
                'quantity' => $request->quantity,
                'weight_kg' => $request->weight_kg,
                'status' => 'pending',
            ]);
        }

        return redirect()->route('coupon.index')->with('success', "{$request->count} kupon ({$request->weight_kg} Kg) berhasil diajukan.");
    }

    public function approve(Coupon $coupon)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $coupon->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Kupon berhasil disetujui.');
    }

    public function approveAll()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        Coupon::where('status', 'pending')->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Semua kupon pending berhasil disetujui.');
    }

    public function batchPrint(Request $request)
    {
        $ids = $request->get('ids');
        
        $query = Coupon::with(['organization', 'creator'])->where('status', 'approved');

        if ($ids) {
            $idArray = explode(',', $ids);
            $query->whereIn('id', $idArray);
        }

        $coupons = $query->get();

        if ($coupons->isEmpty()) {
            return redirect()->route('coupon.index')->with('error', 'Tidak ada kupon yang terpilih atau disetujui untuk dicetak.');
        }

        return view('coupon.print', compact('coupons'));
    }

    public function scan()
    {
        return view('coupon.scan');
    }

    public function claim(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string',
        ]);

        $coupon = Coupon::where('qr_code', $request->qr_code)->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Kupon tidak ditemukan atau tidak valid.'
            ], 404);
        }

        if ($coupon->status !== 'approved') {
            $message = $coupon->status === 'claimed' 
                ? 'Kupon ini sudah pernah diklaim pada ' . $coupon->claimed_at->format('d M Y H:i')
                : 'Kupon ini belum disetujui oleh Admin.';

            return response()->json([
                'success' => false,
                'message' => $message
            ], 422);
        }

        $coupon->update([
            'status' => 'claimed',
            'claimed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kupon Berhasil Diklaim!',
            'data' => [
                'recipient' => $coupon->recipient_name ?? 'Umum',
                'quantity' => $coupon->quantity,
                'weight_kg' => $coupon->weight_kg,
                'claimed_at' => $coupon->claimed_at->format('d M Y H:i'),
            ]
        ]);
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return back()->with('success', 'Kupon berhasil dihapus.');
    }
}
