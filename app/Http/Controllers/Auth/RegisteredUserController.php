<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        try {
            $request->validate([
                'username' => ['required', 'string', 'max:255', 'unique:users'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'role' => ['required', 'string', 'in:admin,farmer,supplier,manufacturer,distributor,retailer'],
                'phone' => ['nullable', 'string', 'max:15'],
                'address' => ['nullable', 'string'],
            ]);

            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => $request->password, // Will be hashed by the User model
                'role' => $request->role,
                'phone' => $request->phone,
                'address' => $request->address,
                'status' => 'active',
            ]);

            event(new Registered($user));

            Auth::login($user);

            return redirect()->route('dashboard');
        } catch (\Exception $e) {
            Log::error('Registration failed: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Registration failed: ' . $e->getMessage()]);
        }
    }
}