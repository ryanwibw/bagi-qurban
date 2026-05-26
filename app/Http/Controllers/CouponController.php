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
        $search = $request->get('search');
        $orgId = session('active_organization_id');
        
        $query = Coupon::with(['creator', 'approver'])
            ->where('organization_id', $orgId)
            ->latest();

        if ($status) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where('serial_number', 'like', "%{$search}%");
        }

        $coupons = $query->paginate(20)->withQueryString();

        return view('coupon.index', compact('coupons'));
    }

    public function show(Coupon $coupon)
    {
        if ($coupon->organization_id != session('active_organization_id')) {
            abort(403);
        }

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
            'count' => 'required|integer|min:1',
            'weight_kg' => 'required|numeric|min:0.1',
        ]);

        $orgId = session('active_organization_id');
        $lastSerial = Coupon::where('organization_id', $orgId)->max('serial_number') ?? 0;

        for ($i = 0; $i < $request->count; $i++) {
            Coupon::create([
                'organization_id' => $orgId,
                'serial_number' => $lastSerial + 1 + $i,
                'created_by' => Auth::id(),
                'qr_code' => (string) Str::uuid(),
                'quantity' => 1,
                'weight_kg' => $request->weight_kg,
                'status' => 'pending',
            ]);
        }

        return redirect()->route('coupon.index')->with('success', "{$request->count} kupon ({$request->weight_kg} Kg) berhasil diajukan.");
    }

    public function approve(Coupon $coupon)
    {
        if (!Auth::user()->isAdmin() || $coupon->organization_id !== session('active_organization_id')) {
            abort(403);
        }

        $coupon->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Kupon berhasil disetujui.');
    }

    public function approveAll(Request $request)
    {
        $query = Coupon::where('organization_id', session('active_organization_id'))
            ->where('status', 'pending');

        if (!$request->has('all_selected')) {
            $ids = $request->input('ids');
            if ($ids) $query->whereIn('id', $ids);
        }

        $query->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Kupon terpilih berhasil disetujui.');
    }

    public function batchPrint(Request $request)
    {
        $orgId = session('active_organization_id');
        
        $query = Coupon::with(['organization', 'creator'])
            ->where('organization_id', $orgId)
            ->where('status', 'approved');

        if ($request->has('all_selected')) {
            // Print all approved coupons in org
        } elseif ($request->has('ids')) {
            $idArray = explode(',', $request->input('ids'));
            $query->whereIn('id', $idArray);
        } else {
            return redirect()->route('coupon.index')->with('error', 'Tidak ada kupon yang dipilih.');
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

        $coupon = Coupon::where('qr_code', $request->qr_code)
            ->where('organization_id', session('active_organization_id'))
            ->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Kupon tidak ditemukan atau tidak valid untuk organisasi Anda.'
            ], 404);
        }

        if ($coupon->status !== 'approved') {
            $message = $coupon->status === 'claimed' 
                ? 'Kupon ini sudah pernah diklaim pada ' . $coupon->claimed_at->format('d M Y H:i')
                : 'Kupon ini belum disetujui oleh Admin.';

            return response()->json([
                'success' => false,
                'message' => $message,
                'coupon_id' => $coupon->id,
                'serial_number' => $coupon->serial_number
            ], 422);
        }

        $coupon->update([
            'status' => 'claimed',
            'claimed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kupon Berhasil Diklaim!',
            'organization_name' => $coupon->organization->name,
            'data' => [
                'id' => $coupon->id,
                'serial_number' => $coupon->serial_number,
                'recipient' => $coupon->recipient_name ?? 'Umum',
                'quantity' => $coupon->quantity,
                'weight_kg' => $coupon->weight_kg,
                'claimed_at' => $coupon->claimed_at->format('d M Y H:i'),
            ]
        ]);
    }

    public function destroy(Request $request, $id = null)
    {
        $query = Coupon::where('organization_id', session('active_organization_id'));

        if (!$request->has('all_selected')) {
            $ids = $request->input('ids', $id ? [$id] : []);
            if (empty($ids)) return back();
            $query->whereIn('id', $ids);
        }

        // Prevent deleting claimed coupons
        $query->where('status', '!=', 'claimed');

        $deletedCount = $query->delete();
        
        $message = $deletedCount > 0 ? 'Kupon berhasil dihapus.' : 'Tidak ada kupon yang dihapus (kupon berstatus claimed tidak bisa dihapus).';

        return back()->with('success', $message);
    }
}
