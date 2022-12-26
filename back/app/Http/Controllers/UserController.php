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
        $user= User::where('email', $validated['email'])->with('permissions')->first();
        $token = $user->createToken('authToken')->plainTextToken;
        return response()->json([
            'token' => $token,
            'user' => $user,
        ], 200);
    }
    public function me(Request $request)
    {
        $user = $request->user()->with('permissions')->first();
        return $user;
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Logged out',
        ], 200);
    }
}
