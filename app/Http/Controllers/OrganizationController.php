<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function edit()
    {
        $organization = auth()->user()->organization;
        return view('organization.edit', compact('organization'));
    }

    public function update(Request $request)
    {
        $organization = auth()->user()->organization;

        $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'address' => 'required|string',
        ]);

        $organization->update($request->only('name', 'city', 'address'));

        return redirect()->route('organization.edit')->with('success', 'Profil organisasi berhasil diperbarui.');
    }
}
