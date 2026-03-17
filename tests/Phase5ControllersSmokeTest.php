<?php

use Callcocam\LaravelRaptorPlanogram\Http\Controllers\AutoPlanogramController;
use Callcocam\LaravelRaptorPlanogram\Http\Controllers\Editor\CategoryController;
use Callcocam\LaravelRaptorPlanogram\Http\Controllers\Editor\GondolaController;
use Callcocam\LaravelRaptorPlanogram\Http\Controllers\Editor\LayerController;
use Callcocam\LaravelRaptorPlanogram\Http\Controllers\Editor\PlanogramApiController;
use Callcocam\LaravelRaptorPlanogram\Http\Controllers\Editor\ProductDimensionController;
use Callcocam\LaravelRaptorPlanogram\Http\Controllers\Editor\ProductSalesController;
use Callcocam\LaravelRaptorPlanogram\Http\Controllers\Editor\SaveChangesController;
use Callcocam\LaravelRaptorPlanogram\Http\Controllers\Editor\SectionController;
use Callcocam\LaravelRaptorPlanogram\Http\Controllers\Editor\SegmentController;
use Callcocam\LaravelRaptorPlanogram\Http\Controllers\Editor\ShelfController;
use Callcocam\LaravelRaptorPlanogram\Http\Controllers\GondolaPdfPreviewController;
use Callcocam\LaravelRaptorPlanogram\Http\Controllers\PlanogramEditorController;

it('autoloads migrated phase 5 controllers', function () {
    $controllers = [
        PlanogramEditorController::class,
        PlanogramApiController::class,
        GondolaController::class,
        SectionController::class,
        ShelfController::class,
        SegmentController::class,
        LayerController::class,
        SaveChangesController::class,
        CategoryController::class,
        ProductDimensionController::class,
        ProductSalesController::class,
        GondolaPdfPreviewController::class,
        AutoPlanogramController::class,
    ];

    foreach ($controllers as $controller) {
        expect(class_exists($controller))->toBeTrue();
    }
});
