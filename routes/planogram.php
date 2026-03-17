<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

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
use Illuminate\Support\Facades\Route;

Route::prefix('api')->name('api.')->group(function () {
	$gondolaController = config('plannogram.controllers.gondola_editor', GondolaController::class);
	$productDetailsController = config('plannogram.controllers.product_details');
	$productImageController = config('plannogram.controllers.product_image');
	$gondolaAnalysisController = config('plannogram.controllers.gondola_analysis');

	if (! is_string($gondolaController) || ! class_exists($gondolaController)) {
		$gondolaController = GondolaController::class;
	}

	if (is_string($productDetailsController) && class_exists($productDetailsController)) {
		Route::get('products/details/{ean}', [$productDetailsController, 'show'])->name('products.details');
	}

	if (is_string($productImageController) && class_exists($productImageController)) {
		Route::post('products/update-image', [$productImageController, 'update'])
			->name('products.update-image');
		Route::post('products/{product}/upload-image', [$productImageController, 'uploadImage'])
			->name('products.upload-image');
		Route::delete('products/{product}/delete-image', [$productImageController, 'deleteImage'])
			->name('products.delete-image');
	}

	Route::post('editor/planograms/{planogram}/gondolas', [$gondolaController, 'store'])
		->name('editor.gondolas.store');
	Route::put('editor/gondolas/{gondola}', [$gondolaController, 'update'])
		->name('editor.gondolas.update');
	Route::delete('editor/gondolas/{gondola}', [$gondolaController, 'destroy'])
		->name('editor.gondolas.destroy');
	Route::get('editor/gondolas/{gondola}/sections', [$gondolaController, 'sections'])
		->name('editor.gondolas.sections');
	Route::get('plannograma/{planogram}/editor/gondolas/{gondola}/products', [$gondolaController, 'products'])
		->name('editor.gondolas.products');
	Route::post('editor/gondolas/{gondola}/update-images', [$gondolaController, 'updateImages'])
		->name('editor.gondolas.update-images');

	Route::get('editor/categories', [CategoryController::class, 'index'])
		->name('editor.categories.index');
	Route::get('editor/{categoryId}/categories', [CategoryController::class, 'index'])
		->name('editor.categories.show');

	Route::get('editor/sections/{section}', [SectionController::class, 'show'])
		->name('editor.sections.show');
	Route::post('editor/gondolas/{gondola}/sections', [SectionController::class, 'store'])
		->name('editor.sections.store');
	Route::put('editor/sections/{id}', [SectionController::class, 'update'])
		->name('editor.sections.update');
	Route::delete('editor/sections/{section}', [SectionController::class, 'destroy'])
		->name('editor.sections.destroy');
	Route::post('editor/sections/{section}/transfer', [SectionController::class, 'transfer'])
		->name('editor.sections.transfer');

	Route::get('editor/planograms', [PlanogramApiController::class, 'index'])
		->name('editor.planograms.index');
	Route::get('editor/planograms/{planogram}/gondolas', [PlanogramApiController::class, 'gondolas'])
		->name('editor.planograms.gondolas');

	Route::post('editor/sections/{section}/shelves', [ShelfController::class, 'store'])
		->name('editor.shelves.store');
	Route::put('editor/shelves/{id}', [ShelfController::class, 'update'])
		->name('editor.shelves.update');
	Route::delete('editor/shelves/{shelf}', [ShelfController::class, 'destroy'])
		->name('editor.shelves.destroy');

	Route::put('editor/segments/{id}', [SegmentController::class, 'update'])
		->name('editor.segments.update');

	Route::put('editor/layers/{id}', [LayerController::class, 'update'])
		->name('editor.layers.update');
	Route::delete('editor/layers/{layer}', [LayerController::class, 'destroy'])
		->name('editor.layers.destroy');

	Route::post('editor/gondolas/{gondola}/save-changes', SaveChangesController::class)
		->name('editor.gondolas.save-changes');

	Route::post('plannograma/{planogram}/products/{product}/dimensions', [ProductDimensionController::class, 'update'])
		->name('editor.products.dimensions.update');
	Route::get('plannerate/products/{product}/sales/summary', [ProductSalesController::class, 'summary'])
		->name('plannerate.products.sales.summary');

	if (is_string($gondolaAnalysisController) && class_exists($gondolaAnalysisController)) {
		Route::post('editor/gondolas/{gondola}/analysis/abc', [$gondolaAnalysisController, 'calculateAbcApi'])
			->name('editor.gondolas.analysis.abc');
		Route::post('editor/gondolas/{gondola}/analysis/target-stock', [$gondolaAnalysisController, 'calculateTargetStockApi'])
			->name('editor.gondolas.analysis.target-stock');
		Route::delete('editor/gondolas/{gondola}/analysis', [$gondolaAnalysisController, 'clearAnalysisApi'])
			->name('editor.gondolas.analysis.clear');
	}
});
