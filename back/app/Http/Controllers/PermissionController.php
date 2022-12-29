<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::all();
        return $permissions;
    }
    public function store()
    {
        $validated = request()->validate([
            'name' => 'required',
        ]);
        $permission = Permission::create($validated);
        return $permission;
    }
    public function show(Permission $permission)
    {
        return $permission;
    }
    public function update(Permission $permission, Request $request)
    {
        $validated = request()->validate([
            'name' => 'required',
        ]);
        $permission->update($validated);
        return $permission;
    }
    public function destroy(Permission $permission)
    {
        $permission->delete();
        return response()->json([
            'message' => 'Permission deleted',
        ], 200);
    }
    public function attach(Request $request)
    {
        $validated = request()->validate([
            'user_id' => 'required',
        ]);
        $user = User::find($validated['user_id']);
        if ($request->permission==[]) {
            $user->syncPermissions();
        } else {
            $user->syncPermissions($request->permission);
        }
        return $user->permissions;
    }
}
