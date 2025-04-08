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
    Route::get(sprintf("%s/{any?}", Plannerate::getPath()), [PlannerateController::class, 'index'])
        ->name(Plannerate::getRoute())
        ->where('any', '.*');
});
