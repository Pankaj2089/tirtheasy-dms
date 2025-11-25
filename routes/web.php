<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\MaintenanceBlockController;

Route::get('/',[UsersController::class, 'LoginPage'])->name('LoginPage');
Route::Post('/vendor-login',[UsersController::class, 'VendorLogin'])->name('VendorLogin');
Route::get('/calendar',[UsersController::class, 'calendar'])->name('calendar');
Route::get('/calendar/data',[UsersController::class, 'calendarData'])->name('calendar.data');
Route::post('/save-maintenance-block',[MaintenanceBlockController::class, 'saveMaintenanceBlock'])->name('saveMaintenanceBlock');
Route::get('/logout',[UsersController::class, 'logout'])->name('logout');
