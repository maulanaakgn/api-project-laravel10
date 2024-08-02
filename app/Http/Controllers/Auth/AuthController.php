<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('Personal Access Token')->accessToken;

        if ($user->hasVerifiedEmail()) {
            return response()->json(['token' => $token, 'isVerified' => true]);
        } else {
            return response()->json(['error' => 'Email not verified', 'isVerified' => false]);
        }
    }


    // public function logout(Request $request)
    // {
    //     // $user = Auth::user();

    //     $request->user()->token()->revoke();

    //     return response()->json(['message' => 'Logout Berhasil'], 200);
    // }

    public function logout(Request $request)
    {
        $user = Auth::guard('api')->user();
        $user->tokens->each(function ($token, $key) {
            $token->delete();
        });

        return response()->json(['message' => 'Logout berhasil.'], 200);
    }
}