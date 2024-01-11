<?php

use App\Http\Controllers\Admin\AppointmentController;
use App\Http\Controllers\Admin\UserController;
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

Route::put('/api/users/{id}', [UserController::class, 'update']);
Route::delete('/api/users/{id}', [UserController::class, 'delete']);
Route::delete('/api/users', [UserController::class, 'bulkDelete']);
Route::get('/api/users', [UserController::class, 'listUsers']);
Route::post('/api/users', [UserController::class, 'store']);
Route::patch('/api/users/{id}/change-role', [UserController::class, 'changeRole']);
Route::get('/api/users/search', [UserController::class, 'search']);

Route::get('/api/appointments',[AppointmentController::class,'index']);


Route::get('/', function () {
    return view('welcome');
});

Route::get('{view}', ApplicationController::class)->where('view', '(.*)');