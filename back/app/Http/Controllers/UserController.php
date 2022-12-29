<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $validated = request()->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if (!auth()->attempt($validated)) {
            return response()->json([
                'message' => 'Credenciales incorrectas',
            ], 401);
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
        $user = User::where('id', $request->user()->id)->with('permissions')->first();
        return $user;
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Logged out',
        ], 200);
    }
    public function index()
    {
        $users = User::with('permissions')->get();
        return $users;
    }
    public function store()
    {
        $validated = request()->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $user = new User();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->password = bcrypt($validated['password']);
        $user->save();
        return $user;
    }
    public function show(User $user)
    {
        return $user;
    }
    public function update(User $user, Request $request)
    {
        $validated = request()->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$user->id,
        ]);
        $user->update($validated);
        return $user;
    }
    public function destroy(User $user)
    {
        $user->delete();
        return response()->json([
            'message' => 'User deleted',
        ], 200);
    }
    public function updatePassword(User $user, Request $request)
    {
        $validated = request()->validate([
            'password' => 'required',
        ]);
        $user->password = bcrypt($validated['password']);
        $user->save();
        return $user;
    }
}
