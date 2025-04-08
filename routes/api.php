<?php

use Callcocam\Plannerate\Http\Controllers\Api\PlannerateController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['api', 'auth:sanctum'], 'prefix' => 'api'], function () {
    Route::resource('plannerate', PlannerateController::class);
});
