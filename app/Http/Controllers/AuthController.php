<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    public function index()
    {
        //echo 9;
    }
    public function register(Request $request)
    {

        $validate = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
        if ($validate->fails()) {
            return response()->json([
                'status' => 'false',
                'message' => 'validation error',
                'error' => $validate->errors(),
            ]);
        }
        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
        ]);
        if ($user) {
            return response()->json([
                'status' => 'success',
                'message' => 'User Registered successfully',
                'data' => $user,
                'token' => $user->createToken('API Token')->plainTextToken,
            ], 200);
        } else {
            return response()->json([
                'status' => 'false',
                'message' => 'error creating user ',
                'data' => $user,
            ], 404);
        }
    }
    public function login(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required|min:6',
        ]);
        if ($validate->fails()) {
            return response()->json([
                'status' => 'false',
                'message' => 'validation error',
                'error' => $validate->errors(),
            ]);
        }
        $user = User::where('email', $request->email)->first();
        if (Auth::attempt($request->only(['email', 'password']))) {
            return response()->json([
                'status' => 'success',
                'message' => 'User login successfully',
                'token' => $user->createToken('API Token')->plainTextToken,

            ], 200);
        } else {
            return response()->json([
                'status' => 'false',
                'message' => 'login failed ',
                // 'data'=> $user,
            ], 404);
        }
    }
}

