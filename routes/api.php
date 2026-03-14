<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PembiayaanController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/login', [AuthController::class, 'login']);

//yng butuh autentikasi Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/pembiayaan', [PembiayaanController::class, 'store']);
    Route::get('/admin/pembiayaan', [PembiayaanController::class, 'index']);
    Route::get('/pembiayaan/riwayat', [PembiayaanController::class, 'riwayat']);
    Route::put('/admin/pembiayaan/{id}/status', [PembiayaanController::class, 'updateStatus']);
    Route::get('/admin/users', [\App\Http\Controllers\UserController::class, 'index']);
    Route::put('/admin/users/{id}', [\App\Http\Controllers\UserController::class, 'update']);
    Route::delete('/admin/users/{id}', [\App\Http\Controllers\UserController::class, 'destroy']);
});