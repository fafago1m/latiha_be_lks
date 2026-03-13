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
    Route::put('/admin/pembiayaan/{id}/status', [PembiayaanController::class, 'updateStatus']);
});