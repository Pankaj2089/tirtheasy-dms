<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;

Route::get('/',[UsersController::class, 'LoginPage'])->name('LoginPage');
Route::Post('/vendor-login',[UsersController::class, 'VendorLogin'])->name('VendorLogin');
Route::get('/calendar',[UsersController::class, 'calendar'])->name('calendar');
Route::get('/logout',[UsersController::class, 'logout'])->name('logout');
