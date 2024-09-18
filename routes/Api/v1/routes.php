<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum','verified'])->group(function () {

    include __DIR__ . '/User/routes.php';
    include __DIR__ . '/Users/routes.php';
});
