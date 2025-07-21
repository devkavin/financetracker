<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\Transaction\TransactionController;
use App\Http\Controllers\Category\CategoryController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [LogoutController::class, 'logout']);
Route::middleware('auth:sanctum')->post('/email/verification-notification', [EmailVerificationController::class, 'send']);
Route::middleware(['auth:sanctum', 'signed'])->get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])->name('verification.verify');
Route::middleware('auth:sanctum')->get('/profile', [ProfileController::class, 'show']);
Route::middleware('auth:sanctum')->put('/profile', [ProfileController::class, 'update']);
Route::middleware('auth:sanctum')->apiResource('transactions', TransactionController::class);
Route::middleware('auth:sanctum')->apiResource('categories', CategoryController::class);
