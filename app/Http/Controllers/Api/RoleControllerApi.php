<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;

use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class RoleControllerApi extends Controller
{
    public function createRole(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:roles',
        ]);

        $role = Role::create(['name' => $data['name']]);

        return response()->json(['message' => 'Role created successfully', 'role' => $role], 201);
    }

    public function createPermission(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:permissions',
        ]);

        $permission = Permission::create(['name' => $data['name']]);

        return response()->json(['message' => 'Permission created successfully', 'permission' => $permission], 201);
    }
}
