<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FormularioController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/form', [FormularioController::class, 'store']);
    Route::get('/form/{id}', [FormularioController::class, 'show']);
    Route::get('/form', [FormularioController::class, 'index']);
    Route::get('/form/buscar',[FormularioController::class,'buscar']);
    Route::patch('/form/{id}/liberado-para-uso', [FormularioController::class, 'liberar']);
    Route::delete('/form/{id}', [FormularioController::class, 'destroy']);
    Route::get('/me', [AuthController::class, 'me']);
});
