<?php

use Callcocam\Plannerate\Http\Controllers\Api\PlannerateController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['api', 'auth:sanctum'], 'prefix'=>'api'], function () {
    Route::resource('plannerate', PlannerateController::class)
        ->only(['index', 'show', 'store', 'update', 'destroy'])
        ->names([
            'index' => 'plannerate.index',
            'show' => 'plannerate.show',
            'store' => 'plannerate.store',
            'update' => 'plannerate.update',
            'destroy' => 'plannerate.destroy',
        ]);
});
