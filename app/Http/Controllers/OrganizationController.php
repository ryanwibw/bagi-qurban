<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function edit()
    {
        $organization = Organization::findOrFail(session('active_organization_id'));
        
        // Authorization check
        if (!auth()->user()->isAdmin($organization->id)) {
            abort(403);
        }

        return view('organization.edit', compact('organization'));
    }

    public function update(Request $request)
    {
        $organization = Organization::findOrFail(session('active_organization_id'));

        if (!auth()->user()->isAdmin($organization->id)) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'address' => 'required|string',
        ]);

        $organization->update($request->only('name', 'city', 'address'));

        return redirect()->route('organization.edit')->with('success', 'Profil organisasi berhasil diperbarui.');
    }

    public function create()
    {
        // Strict Role Separation: Panitia cannot create organizations (cannot become Admin)
        $isPanitia = \Illuminate\Support\Facades\DB::table('organization_user')
            ->where('user_id', auth()->id())
            ->where('role', 'panitia')
            ->exists();

        if ($isPanitia) {
            return redirect()->route('dashboard')->with('error', 'Akun Panitia tidak diperbolehkan membuat organisasi baru (menjadi Admin).');
        }

        return view('organization.create');
    }

    public function store(Request $request)
    {
        // Strict Role Separation check
        $isPanitia = \Illuminate\Support\Facades\DB::table('organization_user')
            ->where('user_id', auth()->id())
            ->where('role', 'panitia')
            ->exists();

        if ($isPanitia) {
            abort(403, 'Panitia tidak boleh menjadi Admin.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'address' => 'required|string',
        ]);

        $organization = Organization::create([
            'name' => $request->name,
            'slug' => \Illuminate\Support\Str::slug($request->name) . '-' . \Illuminate\Support\Str::random(5),
            'city' => $request->city,
            'address' => $request->address,
            'owner_id' => auth()->id(),
        ]);

        // Link the current user as Admin
        auth()->user()->organizations()->attach($organization->id, ['role' => 'admin']);

        // Set as active organization
        session(['active_organization_id' => $organization->id]);

        return redirect()->route('dashboard')->with('success', 'Organisasi baru berhasil dibuat.');
    }

    public function select()
    {
        $organizations = auth()->user()->organizations;
        return view('organization.select', compact('organizations'));
    }

    public function set(Request $request)
    {
        $request->validate([
            'organization_id' => 'required|exists:organizations,id',
        ]);

        // Verify user belongs to this organization
        if (!auth()->user()->organizations()->where('organization_id', $request->organization_id)->exists()) {
            abort(403);
        }

        session(['active_organization_id' => $request->organization_id]);

        return redirect()->route('dashboard');
    }
}
