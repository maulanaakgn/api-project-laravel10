<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;

class PasswordController extends Controller
{
    public function forgotPassword(Request $request)
    {
        // \Log::info('Forgot Password Request:', $request->all());

        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
        ]);

        if ($validator->fails()) {
            \Log::error('Validation Failed:', $validator->errors()->toArray());
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $email = $request->input('email');
        // \Log::info('Sending Password Reset Link for Email:', ['email' => $email]);

        $response = Password::sendResetLink($request->only('email'));

        if ($response == Password::RESET_LINK_SENT) {
            return response()->json(['message' => 'Link reset password berhasil dikirim'], 200);
        } elseif ($response == Password::INVALID_USER) {
            return response()->json(['error' => 'Email tidak ditemukan'], 404);
        } else {
            return response()->json(['error' => 'Tidak bisa mengirim link'], 500);
        }

    }

    public function resetPassword(Request $request)
    {
        // \Log::info('Reset Password Request:', $request->all());

        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'email' => 'required|string|email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            // \Log::error('Validation Failed:', $validator->errors()->toArray());
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $response = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        // \Log::info('Password Reset Response:', ['response' => $response]);

        if ($response == Password::PASSWORD_RESET) {
            return redirect('/success')->with('status', 'Password berhasil direset!');
        }

        return redirect()->back()->withErrors(['email' => [__($response)]]);
    }

    public function showResetPasswordForm(Request $request, $token = null)
    {
        if (!$token) {
            // \Log::error('Token tidak ditemukan!');
            return redirect()->route('home')->withErrors(['error' => 'Token tidak ditemukan!']);
        }

        // \Log::info('Token: ' . $token);
        // \Log::info('Email: ' . $request->email);

        return view('auth.reset-password')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }
}
