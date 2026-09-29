<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    /**
     * Show the registration form.
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle a registration request.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'student_id'   => ['nullable', 'string', 'max:50', 'unique:users'],
            'phone'        => ['nullable', 'string', 'max:20'],
            'department'   => ['nullable', 'string', 'max:255'],
            'date_of_birth'=> ['nullable', 'date', 'before:today'],
            'gender'       => ['nullable', 'in:male,female,other'],
            'password'     => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = User::create([
            'name'         => $validated['name'],
            'email'        => $validated['email'],
            'student_id'   => $validated['student_id'] ?? null,
            'phone'        => $validated['phone'] ?? null,
            'department'   => $validated['department'] ?? null,
            'date_of_birth'=> $validated['date_of_birth'] ?? null,
            'gender'       => $validated['gender'] ?? null,
            'password'     => Hash::make($validated['password']),
            'role'         => 'student',
        ]);

        Auth::login($user);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Welcome to Smart Health Advisor! Your account has been created.');
    }
}