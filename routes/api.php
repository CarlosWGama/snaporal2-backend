<?php

use App\Http\Controllers\Api\GatewayController;
use App\Http\Controllers\Api\UsersController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\ConsultationController;
use App\Http\Controllers\Api\PatientsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


//USUARIOS
Route::prefix('users')->controller(UsersController::class)->group(function () {
    
    Route::post('/login', 'login');
    Route::post('/recover-password', 'requestRecoverPassword');
    Route::put('/recover-password/{token}', 'recoverPassword');
    Route::post('/', 'create');


    
    Route::middleware('auth:sanctum')->group(function () {
       
        Route::middleware(['auth:sanctum', 'ability:admin'])->group(function () {
            Route::get('/', 'list');
        });
        
        Route::get('/specialists', 'listSpecialists');
        Route::get('/profile', 'profile');
        Route::get('/logout', 'logout');
        Route::get('/{id}', 'get');
        Route::delete('/{id}', 'delete');
        Route::put('/{id}', 'update');
        
        
    });
});

//SÓ ROTAS AUTENTICADAS
Route::middleware('auth:sanctum')->group(function () {
    
    //CHAT LLM
    Route::post('/chat', [ChatController::class, 'chat']);

    //GATEWAY API
    Route::controller(GatewayController::class)->group(function() {
        Route::post('/scan/{type}', 'scan');
    });

    //CONSULTA
    Route::prefix('/consultation')->controller(ConsultationController::class)->group(function() {
        Route::put('/availability', 'updateAvailability');
        Route::get('/availability/{specialistID}', 'getAvailability');
        Route::post('/', 'create');
        Route::get('/', 'list');
        Route::get('/{id}', 'get');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'delete');
    });

    //PACIENTES
    Route::prefix('/patients')->controller(PatientsController::class)->group(function() {

        //Evolução
        Route::prefix('/{patientID}/progress')->controller(PatientsController::class)->group(function() {
            Route::post('/', 'createProgress');
            Route::get('/', 'listProgresses');
            Route::get('/{id}', 'getProgress');
            Route::put('/{id}', 'updateProgress');
            Route::delete('/{id}', 'deleteProgress');
        });

        //Pacientes 
        Route::post('/', 'create');
        Route::get('/', 'list');
        Route::get('/{id}', 'get');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'delete');
    });

});
