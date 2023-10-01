<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function createUser(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'account_type' => 'required|string',
                'email' => 'required|email|unique:users',
                'password' => 'required|string',
            ]);

            $user = new User();
            $user->name = $request->input('name');
            $user->account_type = $request->input('account_type');
            $user->email = $request->input('email');
            $user->password = bcrypt($request->input('password'));
            $user->balance = 0.00;
            $user->save();

            return response()->json(['message' => 'User created successfully'], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if (Auth::attempt($request->only('email', 'password'))) {
                $user = Auth::user();
                $token = $user->createToken('app-token')->plainTextToken;

                return response()->json(['message' => 'Logged in successfully', 'token' => $token]);
            }

            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}