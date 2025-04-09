<?php

use Callcocam\Plannerate\Http\Controllers\Api\GondolaController;
use Callcocam\Plannerate\Http\Controllers\Api\PlannerateController;
use Callcocam\Plannerate\Http\Controllers\Api\SectionController;
use Illuminate\Support\Facades\Route;

Route::middleware('api')
    ->prefix('api')
    ->group(function () {
        Route::resource('plannerate', PlannerateController::class);
        Route::resource('gondolas', GondolaController::class);
        Route::resource('sections', SectionController::class);
    });
