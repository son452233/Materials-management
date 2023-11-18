<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PermissionControllerApi extends Controller
{
    public function createPermission(Request $request)
    {
        // Kiểm tra xem người dùng hiện tại có quyền tạo quyền không
        if (!auth()->user()->can('create permissions')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $data = $request->validate([
            'name' => 'required|string|unique:permissions,name',
        ]);

        $permission = Permission::create($data);

        return response()->json($permission, 201);
    }
}
