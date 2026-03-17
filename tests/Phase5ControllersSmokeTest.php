<?php

it('autoloads migrated phase 5 controllers', function () {
    $controllers = [
        \Callcocam\LaravelRaptorPlanogram\Http\Controllers\PlanogramEditorController::class,
        \Callcocam\LaravelRaptorPlanogram\Http\Controllers\Editor\PlanogramApiController::class,
        \Callcocam\LaravelRaptorPlanogram\Http\Controllers\Editor\GondolaController::class,
        \Callcocam\LaravelRaptorPlanogram\Http\Controllers\Editor\SectionController::class,
        \Callcocam\LaravelRaptorPlanogram\Http\Controllers\Editor\ShelfController::class,
        \Callcocam\LaravelRaptorPlanogram\Http\Controllers\Editor\SegmentController::class,
        \Callcocam\LaravelRaptorPlanogram\Http\Controllers\Editor\LayerController::class,
        \Callcocam\LaravelRaptorPlanogram\Http\Controllers\Editor\SaveChangesController::class,
        \Callcocam\LaravelRaptorPlanogram\Http\Controllers\Editor\CategoryController::class,
        \Callcocam\LaravelRaptorPlanogram\Http\Controllers\Editor\ProductDimensionController::class,
        \Callcocam\LaravelRaptorPlanogram\Http\Controllers\Editor\ProductSalesController::class,
        \Callcocam\LaravelRaptorPlanogram\Http\Controllers\GondolaPdfPreviewController::class,
        \Callcocam\LaravelRaptorPlanogram\Http\Controllers\AutoPlanogramController::class,
    ];

    foreach ($controllers as $controller) {
        expect(class_exists($controller))->toBeTrue();
    }
});