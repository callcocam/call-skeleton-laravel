<?php
/**
 * Created by :author_name.
 * User: :author_username, author@domain.com
 * https://www.sigasmart.com.br
 */

use VendorName\Skeleton\Facades\Skeleton;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Rotas Web
|--------------------------------------------------------------------------
|
| Aqui é onde você pode registrar rotas web para sua aplicação.
| Estas rotas são carregadas pelo RouteServiceProvider dentro
| de um grupo que contém o middleware "web".
|
*/

Route::prefix(Skeleton::getPrefix())
    ->name(Skeleton::getId())
    ->middleware(Skeleton::getMiddlewares())
    ->group(function () {
        // Route::middleware(['auth', 'verified'])->group(function () {
        //     Route::get('/', function () {
        //         return Inertia::render('app');
        //     })->name('dashboard'); 
        // });
    });
