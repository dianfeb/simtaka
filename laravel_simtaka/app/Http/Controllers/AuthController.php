<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle login
     */
    // public function login(Request $request)
    // {
    //     $request->validate([
    //         'email' => 'required|email',
    //         'password' => 'required',
    //     ]);

    //     $credentials = $request->only('email', 'password');

    //     if (Auth::attempt($credentials, $request->filled('remember'))) {
    //         $request->session()->regenerate();

    //         $user = Auth::user();

    //         // Redirect based on role
    //         return match ($user->role) {
    //             'admin' => redirect()->intended('/admin/dashboard'),
    //             'guru' => redirect()->intended('/guru/dashboard'),
    //             'orang_tua' => redirect()->intended('/parent/dashboard'),
    //             'owner' => redirect()->intended('/owner/dashboard'),
    //             default => redirect('/'),
    //         };
    //     }

    //     throw ValidationException::withMessages([
    //         'email' => 'Email atau password salah.',
    //     ]);
    // }


    public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials, $request->filled('remember'))) {
        // PENTING: Regenerate session
        $request->session()->regenerate();
        
        $user = Auth::user();
        
        // Redirect based on role - JANGAN pakai intended()
        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'guru' => redirect()->route('teacher.dashboard'),
            'orang_tua' => redirect()->route('parent.dashboard'),
            'owner' => redirect()->route('owner.dashboard'),
            default => redirect('/'),
        };
    }

    throw ValidationException::withMessages([
        'email' => 'Email atau password salah.',
    ]);
}

    /**
     * Show registration form (only for parents)
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Handle registration (only for parents)
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'role' => 'orang_tua',
            'is_active' => true,
        ]);

        Auth::login($user);

        return redirect('/parent/dashboard')
            ->with('success', 'Registrasi berhasil! Silakan lengkapi data anak Anda.');
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}