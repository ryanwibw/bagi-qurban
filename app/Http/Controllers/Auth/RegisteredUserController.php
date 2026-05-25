<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'organization_name' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Strict Role Separation: Check if email is already a Panitia
        $existingUser = User::where('email', $request->email)->first();
        if ($existingUser) {
            $isPanitia = DB::table('organization_user')
                ->where('user_id', $existingUser->id)
                ->where('role', 'panitia')
                ->exists();
            
            if ($isPanitia) {
                throw ValidationException::withMessages([
                    'email' => 'Email ini sudah terdaftar sebagai Panitia. Akun Panitia tidak diperbolehkan menjadi Admin.',
                ]);
            }
        }

        $organization = Organization::create([
            'name' => $request->organization_name,
            'slug' => Str::slug($request->organization_name) . '-' . Str::random(5),
            'city' => $request->city,
            'address' => $request->address,
        ]);

        if (!$existingUser) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
        } else {
            $user = $existingUser;
        }

        // Link as Admin
        $user->organizations()->attach($organization->id, ['role' => 'admin']);
        
        // Set as owner
        $organization->update(['owner_id' => $user->id]);

        event(new Registered($user));

        Auth::login($user);

        session(['active_organization_id' => $organization->id]);

        return redirect(route('dashboard', absolute: false));
    }
}
