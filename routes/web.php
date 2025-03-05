<?php

use App\Http\Controllers\Admin\CustomRegisterController;
use App\Http\Middleware\CheckIfAdmin;
use Backpack\CRUD\app\Http\Controllers\MyAccountController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect(config('backpack.base.route_prefix'));
});
Route::post('guest', [CustomRegisterController::class, 'asGuest'])->name('backpack.auth.guest');
