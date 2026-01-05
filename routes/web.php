<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\MaintenanceBlockController;
use App\Http\Controllers\AssignRoomController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\SettingsController;

Route::get('/',[UsersController::class, 'LoginPage'])->name('LoginPage');
Route::Post('/vendor-login',[UsersController::class, 'VendorLogin'])->name('VendorLogin');
Route::get('/calendar',[UsersController::class, 'calendar'])->name('calendar');
Route::get('/calendar/data',[UsersController::class, 'calendarData'])->name('calendar.data');
Route::get('/booking/{id}',[UsersController::class, 'getBookingDetails'])->name('booking.details');
Route::post('/save-maintenance-block',[MaintenanceBlockController::class, 'saveMaintenanceBlock'])->name('saveMaintenanceBlock');
Route::post('/quick-reservations',[UsersController::class, 'saveQuickReservation'])->name('quickReservations.store');
Route::get('/order-payments/{orderId}',[UsersController::class, 'getOrderPayments'])->name('order.payments');
Route::post('/booking/save-charges-payments',[UsersController::class, 'saveBookingChargesPayments'])->name('booking.saveChargesPayments');
Route::get('/logout',[UsersController::class, 'logout'])->name('logout');

// Reservation routes
Route::get('/reservation',[ReservationController::class, 'index'])->name('reservation.index');

// Assign Room routes
Route::get('/assign-rooms/unassigned',[AssignRoomController::class, 'fetchUnassignedBookings'])->name('assign.rooms.unassigned');
Route::get('/assign-rooms/available',[AssignRoomController::class, 'availableRoomNumbers'])->name('assign.rooms.available');
Route::post('/assign-rooms/assign',[AssignRoomController::class, 'assignRoomToBooking'])->name('assign.rooms.assign');

// Account routes
Route::get('/account',[AccountController::class, 'index'])->name('account.index');

// Settings routes
Route::get('/settings',[SettingsController::class, 'index'])->name('settings.index');
Route::post('/settings/store',[SettingsController::class, 'store'])->name('settings.store');
Route::get('/settings/get',[SettingsController::class, 'getSettings'])->name('settings.get');
Route::get('/settings/room-types',[SettingsController::class, 'getRoomTypes'])->name('settings.roomTypes');
