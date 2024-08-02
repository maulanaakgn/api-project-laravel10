<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $userFromGoogle = Socialite::driver('google')->user();

            $fullName = $userFromGoogle->getName();

            $nameParts = explode(' ', $fullName, 2);
            $firstName = $nameParts[0];
            $lastName = isset($nameParts[1]) ? $nameParts[1] : '';

            $existingUser = User::where('email', $userFromGoogle->getEmail())->first();

            if ($existingUser) {
                if (is_null($existingUser->google_id)) {
                    $existingUser->google_id = $userFromGoogle->getId();
                    $existingUser->save();
                }

                $user = $existingUser;
            } else {
                $user = User::create([
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $userFromGoogle->getEmail(),
                    'google_id' => $userFromGoogle->getId(),
                    'password' => bcrypt(Str::random(16))
                ]);
            }

            Auth::login($user);

            $token = $user->createToken('TestAPIANJAY')->accessToken;

            return response()->json([
                'status' => 'success',
                'token' => $token,
                'user' => $user
            ]);
        } catch (Exception $e) {
            // Log::error('Error during Google login: ', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Login gagal, coba lagi.'], 500);
        }
    }

}
