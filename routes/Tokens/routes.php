<?php

use App\Http\Controllers\Tokens\TokenController;
use Illuminate\Support\Facades\Route;

Route::prefix('tokens')->name('tokens.')->group(function () {

    Route::get('/', [TokenController::class, 'index'])->name('index');
    Route::post('/', [TokenController::class, 'store'])->name('store');
    Route::delete('/{id}', [TokenController::class, 'destroy'])->name('destroy');

});
