<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{

    // Menampilkan form register
    public function showRegistrationForm()
    {
        if (auth()->check()) {
            return view('already_logged_in');
        }
        return view('auth.register');
    }

    // Proses register
    public function register(Request $request)
    {
        try {

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6|confirmed',
                // Optional fields - these can be updated later in the profile
                'phone_number' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:255',
                'province_id' => 'nullable|exists:provinces,id',
                'city_id' => 'nullable|exists:cities,id',
                'birth_date' => 'nullable|date',
                'gender' => 'nullable|string|in:male,female,other',
            ]);


            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone_number' => $request->phone_number,
                'address' => $request->address,
                'province_id' => $request->province_id,
                'city_id' => $request->city_id,
                'birth_date' => $request->birth_date,
                'gender' => $request->gender,
            ]);
            auth()->login($user);
            return redirect()->route('home');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }
    }
}
