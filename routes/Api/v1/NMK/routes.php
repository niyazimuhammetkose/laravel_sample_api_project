<?php

use App\Http\Controllers\Api\v1\NMK\NMKController;
use Illuminate\Support\Facades\Route;

Route::prefix('nmk')->name('nmk.')->group(function () {

    Route::get('/hello', [NMKController::class, 'hello'])->name('hello');

});
