<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('user')->group(function () {

    Route::get('/', function (Request $request) {
        return $request->user();
    })->name('api.user.index');

    Route::delete('/delete', function (Request $request) {
        $request->user()->delete();
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->noContent();
    })->name('api.user.destroy');

});
