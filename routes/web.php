<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\MaintenanceBlockController;

Route::get('/',[UsersController::class, 'LoginPage'])->name('LoginPage');
Route::Post('/vendor-login',[UsersController::class, 'VendorLogin'])->name('VendorLogin');
Route::get('/calendar',[UsersController::class, 'calendar'])->name('calendar');
Route::get('/calendar/data',[UsersController::class, 'calendarData'])->name('calendar.data');
Route::get('/booking/{id}',[UsersController::class, 'getBookingDetails'])->name('booking.details');
Route::post('/save-maintenance-block',[MaintenanceBlockController::class, 'saveMaintenanceBlock'])->name('saveMaintenanceBlock');
Route::post('/quick-reservations',[UsersController::class, 'saveQuickReservation'])->name('quickReservations.store');
Route::get('/logout',[UsersController::class, 'logout'])->name('logout');
