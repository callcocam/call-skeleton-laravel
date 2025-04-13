<?php

use Callcocam\Plannerate\Http\Controllers\Api\GondolaController;
use Callcocam\Plannerate\Http\Controllers\Api\LayerController;
use Callcocam\Plannerate\Http\Controllers\Api\PlannerateController;
use Callcocam\Plannerate\Http\Controllers\Api\SectionController;
use Callcocam\Plannerate\Http\Controllers\Api\SegmentController;
use Callcocam\Plannerate\Http\Controllers\Api\ShelfController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api', 'auth:sanctum'])
    ->prefix('api')
    ->name('api.')
    ->group(function () {
        Route::resource('plannerate', PlannerateController::class);
        Route::resource('gondolas', GondolaController::class);
        Route::resource('sections', SectionController::class);
        Route::resource('shelves', ShelfController::class)
            ->only(['index', 'show', 'store', 'update', 'destroy']);
        Route::resource('segments', SegmentController::class)
            ->only(['index', 'show', 'store', 'update', 'destroy']);
        Route::put('segments/{shelf}/reorder', [SegmentController::class, 'reorder'])
            ->name('segments.reorder');
        Route::post('shelves/{shelf}/segments', [ShelfController::class, 'segment'])
            ->name('shelves.segments');
        Route::patch('shelves/{shelf}/transfer', [ShelfController::class, 'transfer'])
            ->name('shelves.transfer');
        Route::resource('layers',  LayerController::class)
            ->only(['index', 'show', 'store', 'update', 'destroy']);
    });
