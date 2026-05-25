<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index()
    {
        $organization = Organization::findOrFail(session('active_organization_id'));
        $users = $organization->users()->wherePivot('role', 'panitia')->get();
        
        return view('panitia.index', compact('users'));
    }

    public function create()
    {
        return view('panitia.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
        ]);

        $organization = Organization::findOrFail(session('active_organization_id'));
        $user = User::where('email', $request->email)->first();

        // 1. Strict Role Separation: Check if email is an Admin anywhere
        if ($user) {
            $isAdmin = DB::table('organization_user')
                ->where('user_id', $user->id)
                ->where('role', 'admin')
                ->exists();
            
            if ($isAdmin) {
                return back()->withErrors(['email' => 'Email ini sudah terdaftar sebagai Admin. Akun Admin tidak diperbolehkan menjadi Panitia.']);
            }

            // 2. Check if already a panitia in THIS organization
            if ($organization->users()->where('user_id', $user->id)->exists()) {
                return back()->withErrors(['email' => 'User ini sudah menjadi panitia di organisasi ini.']);
            }
        }

        if (!$user) {
            // New Panitia
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
        }

        // Link as Panitia
        $user->organizations()->attach($organization->id, ['role' => 'panitia']);

        return redirect()->route('panitia.index')->with('success', 'Panitia berhasil ditambahkan.');
    }

    public function destroy(User $user)
    {
        $organizationId = session('active_organization_id');
        
        // Detach from this organization only
        $user->organizations()->detach($organizationId);

        return redirect()->route('panitia.index')->with('success', 'Panitia berhasil dihapus dari organisasi ini.');
    }
}
