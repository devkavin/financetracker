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
use App\Http\Controllers\Budget\BudgetController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Insights\InsightsController;

Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [LogoutController::class, 'logout']);
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'send']);
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::apiResource('transactions', TransactionController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('budgets', BudgetController::class);
    Route::middleware('signed')->get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])->name('verification.verify');
    Route::middleware('auth:sanctum')->get('/dashboard', [DashboardController::class, 'overview']);
    Route::middleware('auth:sanctum')->get('/insights', [InsightsController::class, 'index']);
});
