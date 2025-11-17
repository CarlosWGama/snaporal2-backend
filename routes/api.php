<?php

use App\Http\Controllers\Api\UsuariosController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::prefix('usuarios')->controller(UsuariosController::class)->group(function () {

    Route::post('/login', 'login');

    
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/perfil', 'perfil');
        Route::get('/{id}', 'get');
        Route::delete('/{id}', 'delete');
        Route::put('/{id}', 'update');
        
        Route::middleware(['auth:sanctum', 'ability:admin'])->group(function () {
            Route::post('/', 'create');
            Route::get('/', 'list');
        });

    });
});