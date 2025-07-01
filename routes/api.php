<?php

/**
 * Created by :author_name.
 * User: :author_username, author@domain.com
 * https://www.sigasmart.com.br
 */

use VendorName\Skeleton\Facades\Skeleton;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('api')
    ->middleware(config('skeleton.api.middleware', ['api'])) // Usar middleware de API padrão
    ->group(function () {
        // Aqui você pode adicionar suas rotas de API
    });
