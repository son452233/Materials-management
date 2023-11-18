<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User; // Thay đổi tên model từ "categories" thành "User"
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserControllerApi extends Controller
{
    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string',
                'date_of_birth' => 'nullable|date',
                'phone' => [
                    'required',
                    'string',
                    'max:15',
                    // Sử dụng regex để xác thực số điện thoại Việt Nam
                    'regex:/(((\+|)84)|0)(3|5|7|8|9)+([0-9]{8})\b/',
                ],
                'username' => 'nullable|required|string|unique:users,username',
                'citizen_identity_id' => 'required|numeric|digits:12|unique:users,citizen_identity_id',
            ]);

            // $data['password'] = Hash::make($data['password']);

            $user = User::create($data);

            return response()->json($user, 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422); // Trả về mã lỗi 422 (Unprocessable Entity)
        }
    }


    public function show(User $user)
    {
        return response()->json($user);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'date_of_birth' => 'nullable|date',
            'phone' => [
                'nullable',
                'string',
                'max:15',
                // Sử dụng regex để xác thực số điện thoại Việt Nam
                'regex:/(((\+|)84)|0)(3|5|7|8|9)+([0-9]{8})\b/',
            ],
            'username' => 'nullable|string|unique:users,username,' . $user->id,
            'citizen_identity_id' => 'nullable|numeric|digits:12',
        ]);

        // Kiểm tra nếu có mật khẩu được gửi, thực hiện cập nhật mật khẩu
        if ($request->has('password')) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return response()->json($user);
    }


    public function destroy(User $user)
    {
        $user->delete();

        return response()->json(['message' => 'Delete successfully']);
    }

    // public function register(Request $request)
    // {
    //     $validatedData = $request->validate([
    //         'name' => 'required|string',
    //         'email' => 'required|string|email|unique:users',
    //         'password' => 'required|string',
    //     ]);

    //     $user = User::create($validatedData);

    //     return response()->json(['message' => 'User registered successfully', 'user' => $user]);
    // }

    // public function login(Request $request)
    // {
    //     $credentials = $request->only('email', 'password');

    //     if (auth()->attempt($credentials)) {
    //         $user = auth()->user();
    //         $token = $user->createToken('MyApp')->accessToken;

    //         return response()->json([
    //             'message' => 'Login successful',
    //             'user' => $user,
    //             'access_token' => $token,
    //         ]);
    //     } else {
    //         return response()->json(['message' => 'Login failed'], 401);
    //     }
    // }
}
