<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Payment\PaymentController;
use App\Http\Controllers\Payment\PaymentControllerCore;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/payments', [PaymentController::class, 'create']);
Route::post('/webhooks/midtrans', [PaymentController::class, 'webhook']);

Route::post('/payments-core', [PaymentControllerCore::class, 'create']);
Route::post('/tokenize-card', [PaymentControllerCore::class, 'getCardToken']);
Route::post('/webhooks-core/midtrans', [PaymentControllerCore::class, 'webhook']);

Route::post('register', [RegisterController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::prefix('auth')->group(function () {
    Route::get('google', [GoogleAuthController::class, 'redirectToGoogle']);
    Route::get('google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);
});

Route::prefix('password')->group(function () {
    Route::post('email', [PasswordController::class, 'forgotPassword']);
    Route::post('reset', [PasswordController::class, 'resetPassword']);
});

Route::middleware('auth:api')->group(function () {
    // User information and actions
    Route::get('user', function (Request $request) {
        return $request->user();
    });

    Route::put('user/profile', [ProfileController::class, 'update']);
    Route::put('user/password', [ProfileController::class, 'changePassword']);
    Route::delete('user', [ProfileController::class, 'destroy']);
    Route::get('users', [AdminController::class, 'index'])->middleware('admin');

    Route::post('logout', [AuthController::class, 'logout']);

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return response()->json(['message' => 'Verification link sent!']);
    })->name('verification.send');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return response()->json(['message' => 'Email verified!']);
    })->name('verification.verify')->middleware(['signed', 'throttle:6,1']);
});


