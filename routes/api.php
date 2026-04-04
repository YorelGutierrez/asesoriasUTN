<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LogController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('jwt.auth')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

//logs
Route::middleware(['jwt.auth'])->group(function () {
    Route::get('/logs', [LogController::class, 'index']);
    Route::delete('/logs/{id}', [LogController::class, 'destroy']);
    Route::delete('/logs', [LogController::class, 'destroyAll']);
});
