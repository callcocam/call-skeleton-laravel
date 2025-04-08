<?php


/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

use Callcocam\Plannerate\Facades\Plannerate;
use Illuminate\Support\Facades\Route;

use Callcocam\Plannerate\Http\Controllers\PlannerateController;

Route::group(['middleware' => ['web', 'auth', 'verified']], function () {
    Route::get(Plannerate::getPath(), [PlannerateController::class, 'index'])
        ->name(Plannerate::getRoute());
    Route::get(Plannerate::getPath() . '/create', [PlannerateController::class, 'create'])
        ->name(Plannerate::getRoute() . '.create');
    Route::get(Plannerate::getPath() . '/{id}', [PlannerateController::class, 'show'])
        ->name(Plannerate::getRoute() . '.show');
    Route::post(Plannerate::getPath(), [PlannerateController::class, 'store'])
        ->name(Plannerate::getRoute() . '.store');
    Route::put(Plannerate::getPath() . '/{id}', [PlannerateController::class, 'update'])
        ->name(Plannerate::getRoute() . '.update');
    Route::delete(Plannerate::getPath() . '/{id}', [PlannerateController::class, 'destroy'])
        ->name(Plannerate::getRoute() . '.destroy');
    Route::get(Plannerate::getPath() . '/{id}/edit', [PlannerateController::class, 'edit'])
        ->name(Plannerate::getRoute() . '.edit');
});
