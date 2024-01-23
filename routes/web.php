<?php

use App\Http\Controllers\Admin\AppointmentController;
use App\Http\Controllers\Admin\AppointmentStatusController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\DashboardStatController;
use App\Models\Appointment;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApplicationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::middleware('auth')->group(function () {
    Route::put('/api/users/{id}', [UserController::class, 'update']);
    Route::delete('/api/users/{id}', [UserController::class, 'delete']);
    Route::delete('/api/users', [UserController::class, 'bulkDelete']);
    Route::get('/api/users', [UserController::class, 'listUsers']);
    Route::post('/api/users', [UserController::class, 'store']);
    Route::patch('/api/users/{id}/change-role', [UserController::class, 'changeRole']);

    Route::get('/api/appointments', [AppointmentController::class, 'index']);
    Route::post('/api/appointment/create', [AppointmentController::class, 'store']);
    Route::get('/api/appointment/{id}/edit', [AppointmentController::class, 'edit']);
    Route::post('/api/appointment/{id}/update', [AppointmentController::class, 'update']);
    Route::delete('/api/appointment/{id}/delete', [AppointmentController::class, 'delete']);

    Route::get('/api/appointment-status', [AppointmentStatusController::class, 'getStatusWithCount']);

    Route::get('/api/stats/appointments', [DashboardStatController::class, 'appointments']);
    Route::get('/api/stats/users', [DashboardStatController::class, 'users']);

    Route::get('/api/clients', [ClientController::class, 'index']);

    Route::get('/api/settings', [SettingController::class, 'index']);
    Route::post('/api/settings', [SettingController::class, 'update']);

    Route::get('/api/profile', [ProfileController::class, 'index']);
    Route::put('/api/profile', [ProfileController::class, 'update']);

});



Route::get('{view}', ApplicationController::class)->where('view', '(.*)')->middleware('auth');