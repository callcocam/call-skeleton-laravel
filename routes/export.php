<?php

use Callcocam\LaravelRaptorPlanogram\Http\Controllers\GondolaPdfPreviewController;
use Illuminate\Support\Facades\Route;

Route::prefix('export/gondola')
    ->name('export.gondola.')
    ->middleware(['auth'])
    ->group(function () {
        $gondolaExportController = config('plannogram.controllers.gondola_export');

        Route::get('{gondola}/view', [GondolaPdfPreviewController::class, 'show'])->name('view');

        if (is_string($gondolaExportController) && class_exists($gondolaExportController)) {
            Route::get('{gondola}/qr-code', [$gondolaExportController, 'generateQrCode'])->name('qrcode');
            Route::get('section/{section}/qr-code', [$gondolaExportController, 'generateSectionQrCode'])->name('section.qrcode');

            Route::get('{gondola}/report', [$gondolaExportController, 'exportReport'])->name('report');
        }
    });
