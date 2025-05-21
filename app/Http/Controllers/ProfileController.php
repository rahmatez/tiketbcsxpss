<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function show($id)
    {
        $user = User::findOrFail($id);
        
        // Get recent orders if available (assuming you have an Order model and relationship)
        $recentOrders = [];
        if (Auth::id() == $user->id && method_exists($user, 'orders')) {
            $recentOrders = $user->orders()->with('game')->latest()->take(3)->get();
        }
        
        return view('profile.show', compact('user', 'recentOrders'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);

        if (Auth::id() !== $user->id) {
            return redirect()->route('profile.show', $id)->withErrors('You are not authorized to edit this profile.');
        }

        return view('profile.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if (Auth::id() !== $user->id) {
            return redirect()->route('profile.show', $id)->withErrors('You are not authorized to edit this profile.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|string|max:15',
            'address' => 'nullable|string|max:255',
            'province_id' => 'nullable|exists:provinces,id',
            'city_id' => 'nullable|exists:cities,id',
        ]);

        $user->update($request->all());

        return redirect()->route('profile.show', $id)->with('success', 'Profile updated successfully.');
    }

    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
