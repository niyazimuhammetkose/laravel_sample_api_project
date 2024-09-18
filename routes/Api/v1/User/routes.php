<?php

use App\Http\Controllers\Api\v1\User\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('user')->name('user.')->group(function () {

    Route::get('/', [UserController::class, 'index'])->name('index');

    Route::delete('/delete', [UserController::class, 'destroy'])->name('destroy');

});
