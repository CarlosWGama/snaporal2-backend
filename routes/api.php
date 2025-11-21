<?php

use App\Http\Controllers\Api\GatewayController;
use App\Http\Controllers\Api\UsuariosController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::prefix('usuarios')->controller(UsuariosController::class)->group(function () {

    Route::post('/login', 'login');
    Route::post('/recuperar-senha', 'solicitarRecuperacaoSenha');
    Route::put('/recuperar-senha/{token}', 'recuperarSenha');
    Route::post('/', 'create');
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/perfil', 'perfil');
        Route::get('/logout', 'logout');
        Route::get('/{id}', 'get');
        Route::delete('/{id}', 'delete');
        Route::put('/{id}', 'update');
        
        Route::middleware(['auth:sanctum', 'ability:admin'])->group(function () {
            Route::get('/', 'list');
        });

    });
});

//SÓ ROTAS AUTENTICADAS
Route::middleware('auth:sanctum')->group(function () {
    
    //GATEWAY API
    Route::controller(GatewayController::class)->group(function() {
        Route::post('/scan/{type}', 'scan');
    });

});
