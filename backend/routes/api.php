<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PublicController;
use App\Http\Middleware\EnsureAdminToken;
use Illuminate\Support\Facades\Route;

Route::get('/site', [PublicController::class, 'site']);
Route::get('/vehicles', [PublicController::class, 'vehicles']);
Route::get('/vehicles/{slug}', [PublicController::class, 'vehicle']);
Route::get('/auctions', [PublicController::class, 'auctions']);
Route::get('/pages/{slug}', [PublicController::class, 'page']);
Route::post('/leads', [PublicController::class, 'lead']);
Route::post('/bids', [PublicController::class, 'bid']);

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);
Route::get('/auth/me', [AuthController::class, 'me']);
Route::post('/auth/logout', [AuthController::class, 'logout']);
Route::put('/auth/profile', [AuthController::class, 'updateProfile']);
Route::put('/auth/password', [AuthController::class, 'updatePassword']);

Route::post('/admin/login', [AdminController::class, 'login']);

Route::prefix('admin')->middleware(EnsureAdminToken::class)->group(function (): void {
    Route::get('/dashboard', [AdminController::class, 'dashboard']);
    Route::get('/vehicles', [AdminController::class, 'vehicles']);
    Route::post('/vehicles', [AdminController::class, 'storeVehicle']);
    Route::put('/vehicles/{vehicle}', [AdminController::class, 'updateVehicle']);
    Route::delete('/vehicles/{vehicle}', [AdminController::class, 'destroyVehicle']);
    Route::get('/auctions', [AdminController::class, 'auctions']);
    Route::get('/leads', [AdminController::class, 'leads']);
    Route::patch('/leads/{lead}', [AdminController::class, 'updateLead']);
    Route::get('/pages', [AdminController::class, 'pages']);
    Route::put('/pages/{page}', [AdminController::class, 'updatePage']);
    Route::get('/settings', [AdminController::class, 'settings']);
    Route::put('/settings', [AdminController::class, 'updateSettings']);
});
