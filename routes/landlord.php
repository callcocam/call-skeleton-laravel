<?php
/**
 * Created by :author_name.
 * User: :author_username, author@domain.com
 * https://www.sigasmart.com.br
 */

use VendorName\Skeleton\Http\Controllers\Auth\LandlordLoginController;
use VendorName\Skeleton\Http\Controllers\Landlord\DashboardController;
use VendorName\Skeleton\Http\Controllers\Landlord\TenantController;
 
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotas do Landlord
|--------------------------------------------------------------------------
|
| Aqui estão as rotas para autenticação e dashboard do landlord.
| Estas rotas são carregadas pelo SkeletonServiceProvider dentro
| de um grupo que contém o middleware específico do landlord.
|
*/
 

// Rotas de Autenticação (Apenas visitantes)
Route::middleware(['guest:landlord', 'disable.tenant.scoping'])->group(function () {
    Route::get('/login', [LandlordLoginController::class, 'showLoginForm'])
        ->name('landlord.login');

    Route::post('/login', [LandlordLoginController::class, 'login'])
        ->name('landlord.login.post');
});

// Rotas Autenticadas
Route::middleware(['landlord.auth', 'disable.tenant.scoping'])
    ->as('landlord.')
    ->group(function () {
        Route::post('/logout', [LandlordLoginController::class, 'logout'])
            ->name('landlord.logout');

        Route::get('/logout', [LandlordLoginController::class, 'logout'])
            ->name('landlord.logout.get');

        Route::get('/', [DashboardController::class, 'index'])
            ->name('dashboard');

        // Rotas de Tenant
        Route::resource('tenants', TenantController::class);
        Route::get('tenants/test', [TenantController::class, 'test'])
            ->name('tenants.test');
        Route::post('tenants/bulk-destroy', [TenantController::class, 'bulkDestroy'])
            ->name('tenants.bulk-destroy');
        Route::patch('tenants/{tenant}/toggle-status', [TenantController::class, 'toggleStatus'])
            ->name('tenants.toggle-status');
        Route::post('tenants/export', [TenantController::class, 'export'])
            ->name('tenants.export');
    });
