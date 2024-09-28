<?php

use App\Http\Controllers\Api\v1\Users\UsersController;
use Illuminate\Support\Facades\Route;

Route::prefix('users')->name('users.')->group(function () {

    Route::get('/', [UsersController::class, 'index'])->name('index')
        ->middleware(['throttle:api_list_users']);
    Route::get('/search', [UsersController::class, 'search'])->name('search');

});
