<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function login()
    {
        $validated = request()->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if (!auth()->attempt($validated)) {
            return back()->with('error', 'Invalid login details');
        }
        $user= User::where('email', $validated['email'])->first();
        $token = $user->createToken('authToken')->plainTextToken;
    }
}
