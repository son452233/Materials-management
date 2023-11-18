<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors(),
            ];
            return response()->json($response, 400);
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $user->update(['remember_token' => Str::random(10)]);

        $success['token'] = $user->createToken('MyApp')->plainTextToken;
        $success['name'] = $user->name;

        $response = [
            'success' => true,
            'data' => $success,
            'message' => 'User registered successfully',
        ];
        return response()->json($response, 200);
    }

    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $success['token'] = $user->createToken('MyApp')->plainTextToken;
            $success['name'] = $user->name;
            $user->update(['remember_token' => Str::random(10)]);

            $response = [
                'success' => true,
                'data' => $success,
                'message' => 'User logged in successfully',
            ];
            return response()->json($response, 200);
        } else {
            $response = [
                'success' => false,
                'message' => 'Invalid email or password',
            ];
            return response()->json($response, 401);
        }
    }
    public function logout(Request $request)
{
    $request->user()->currentAccessToken()->delete();
    return response()->json([
        'message' => 'Logged out successfully',
        'data' => $request->user()
    ],200);
    
}
public function revokeToken(Request $request)
{
    $token = $request->user()->currentAccessToken();
    $token->delete();

    return response()->json(['message' => 'Token revoked successfully']);
}
}