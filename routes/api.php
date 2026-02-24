<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FormularioController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::get('/formularios', [FormularioController::class, 'index']);
    Route::post('/formularios', [FormularioController::class, 'store']);
    Route::put('/formularios/{id}', [FormularioController::class, 'update']);
    Route::delete('/formularios/{id}', [FormularioController::class, 'destroy']);
});
