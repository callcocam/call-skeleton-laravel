<?php
/**
 * Created by :author_name.
 * User: :author_username, author@domain.com
 * https://www.sigasmart.com.br
 */

use VendorName\Skeleton\Facades\Skeleton;
use VendorName\Skeleton\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Rotas do Admin
|--------------------------------------------------------------------------
|
| Aqui é onde você pode registrar rotas web para o painel administrativo.
| Estas rotas são carregadas pelo RouteServiceProvider dentro
| de um grupo que contém o middleware "web".
|
*/ 

Route::prefix('admin')
    ->name(Skeleton::getId().'.')
    ->middleware(Skeleton::getMiddlewares())
    ->group(function () { 
        Route::middleware(['auth', 'verified','web'])->group(function () {
            Route::get('/', function () {
                return Inertia::render('app');
            })->name('dashboard'); 

            Route::resource('users', UserController::class);
        });
    });
