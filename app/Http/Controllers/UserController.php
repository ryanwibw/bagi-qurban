<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('role', 'panitia')->get();
        return view('panitia.index', compact('users'));
    }

    public function create()
    {
        return view('panitia.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'organization_id' => auth()->user()->organization_id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'panitia',
        ]);

        return redirect()->route('panitia.index')->with('success', 'Panitia berhasil ditambahkan.');
    }

    public function destroy(User $user)
    {
        // Pastikan hanya bisa menghapus panitia di organisasinya sendiri
        if ($user->role === 'panitia') {
            $user->delete();
        }

        return redirect()->route('panitia.index')->with('success', 'Panitia berhasil dihapus.');
    }
}
