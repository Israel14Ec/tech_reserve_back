<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ComputerLabController;
use App\Http\Controllers\SchedulesLabController;
use App\Http\Controllers\ReservationController;

//Rutas no protegidas
Route::group(['middleware' => ['auth:sanctum']], function() {
    
    //Rutas de AuthController
    Route::get('v1/auth', [AuthController::class, 'userProfile']);
    Route::delete('v1/auth', [AuthController::class, 'logout']);

    //Rutas de ComputerLabController
    Route::post('v1/computer_lab', [ComputerLabController::class, 'create']);
    Route::put('v1/computer_lab/{id}', [ComputerLabController::class, 'update']);
    Route::get('v1/computer_lab', [ComputerLabController::class, 'getAll']);
    Route::get('v1/computer_lab/{id}', [ComputerLabController::class, 'getById']);
    Route::delete('v1/computer_lab/soft/{id}', [ComputerLabController::class, 'deleteSoft']);
    Route::patch('v1/computer_lab/soft/{id}', [ComputerLabController::class, 'restartLab']);
    Route::delete('v1/computer_lab', [ComputerLabController::class, 'delete']);

    //Rutas de SchedulesLab
    Route::post('v1/schedules_lab', [SchedulesLabController::class, 'create']);
    Route::patch('v1/schedules_lab/{id}', [SchedulesLabController::class, 'update']);
    Route::patch('v1/schedules_lab/availability/{id}', [SchedulesLabController::class, 'availabilityLab']);

    //Rutas de reservation
    Route::post('v1/reservation', [ReservationController::class, 'createReservation']);
    Route::get('v1/reservation', [ReservationController::class, 'getReservationByUser']);
    Route::patch('v1/reservation/{id}', [ReservationController::class, 'statusReservation']);
    Route::delete('v1/reservation/{id}', [ReservationController::class, 'deleteReservation']);
});

//Rutas para UserController
Route::post('v1/user', [UserController::class, 'create']);
Route::post('v1/user/confirm-account', [UserController::class, 'confirmAccount']);

//Rutas de AuthController
Route::post('v1/auth', [AuthController::class, 'loggin']);
