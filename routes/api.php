<?php

use Callcocam\Plannerate\Http\Controllers\Api\PlannerateController;
use Illuminate\Support\Facades\Route;

Route::middleware('api')
    ->prefix('api') 
    ->group(function () {
        Route::resource('plannerate', PlannerateController::class) ;
        Route::post('plannerate/store', [PlannerateController::class, 'store'])->name('plannerate.store');
    });
