<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'verified', 'throttle:global'])->group(function () {
    include __DIR__ . '/User/routes.php';
    include __DIR__ . '/Tokens/routes.php';
});

Route::prefix('v1')->name('api.v1.')->group(function () {
    include __DIR__ . '/Api/v1/routes.php';
})->middleware(['throttle:global']);
