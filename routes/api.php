<?php

use App\Http\Controllers\Auth\AuthenticationController;
use App\Http\Controllers\Feed\FeedController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//Default Route for handling error unauthorized
Route::get('/', function () {
    return response()->json([
        'status' => false,
        'message' => "Access Denied"
    ], 401);
})->name('login');

Route::post('/auth/register', [AuthenticationController::class, 'register']);
Route::post('/auth/login', [AuthenticationController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/feed', [FeedController::class, 'index']);
    Route::post('/feed', [FeedController::class, 'store']);
    Route::post('/feed/like/{feed_id}', [FeedController::class, 'likePost']);
    Route::post('/feed/comment/{feed_id}', [FeedController::class, 'comment']);
    Route::get('/feed/comment/{feed_id}', [FeedController::class, 'getComment']);
});
