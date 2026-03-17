<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptorPlanogram;

use Callcocam\LaravelRaptorPlanogram\Commands\LaravelRaptorPlanogramCommand;
use Callcocam\LaravelRaptorPlanogram\Services\AiGenerate\IAPlanogramService;
use Callcocam\LaravelRaptorPlanogram\Services\AiGenerate\IAPromptBuilderService;
use Callcocam\LaravelRaptorPlanogram\Services\AiGenerate\IAResponseParserService;
use Callcocam\LaravelRaptorPlanogram\Services\Analysis\AbcAnalysisService;
use Callcocam\LaravelRaptorPlanogram\Services\Analysis\TargetStockService;
use Callcocam\LaravelRaptorPlanogram\Services\AutoGenerate\AutoPlanogramService;
use Callcocam\LaravelRaptorPlanogram\Services\AutoGenerate\LayoutOptimizationService;
use Callcocam\LaravelRaptorPlanogram\Services\AutoGenerate\MerchandisingRulesService;
use Callcocam\LaravelRaptorPlanogram\Services\AutoGenerate\ProductSelectionService;
use Callcocam\LaravelRaptorPlanogram\Services\GondolaPayloadService;
use Callcocam\LaravelRaptorPlanogram\Services\GondolaService;
use Callcocam\LaravelRaptorPlanogram\Services\LayerService;
use Callcocam\LaravelRaptorPlanogram\Services\PlanogramChangeService;
use Callcocam\LaravelRaptorPlanogram\Services\Printing\GondolaPrintService;
use Callcocam\LaravelRaptorPlanogram\Services\ProductService;
use Callcocam\LaravelRaptorPlanogram\Services\QRCode\QRCodeService;
use Callcocam\LaravelRaptorPlanogram\Services\SectionGenerate\SectionAIAllocator;
use Callcocam\LaravelRaptorPlanogram\Services\SectionGenerate\SectionContextBuilder;
use Callcocam\LaravelRaptorPlanogram\Services\SectionGenerate\SectionPersistenceService;
use Callcocam\LaravelRaptorPlanogram\Services\SectionGenerate\SectionPlanogramService;
use Callcocam\LaravelRaptorPlanogram\Services\SectionGenerate\SectionRulesAllocator;
use Callcocam\LaravelRaptorPlanogram\Services\SectionService;
use Callcocam\LaravelRaptorPlanogram\Services\SegmentService;
use Callcocam\LaravelRaptorPlanogram\Services\ShelfPositioningService;
use Callcocam\LaravelRaptorPlanogram\Services\ShelfService;
use Illuminate\Support\Facades\Route;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelRaptorPlanogramServiceProvider extends PackageServiceProvider
{
    public function register(): void
    {
        parent::register();

        $this->mergeConfigFrom(__DIR__.'/../config/plannogram.php', 'plannogram');

        foreach ([
            PlanogramChangeService::class,
            GondolaService::class,
            GondolaPayloadService::class,
            SectionService::class,
            ShelfService::class,
            ShelfPositioningService::class,
            SegmentService::class,
            LayerService::class,
            ProductService::class,
            AbcAnalysisService::class,
            TargetStockService::class,
            AutoPlanogramService::class,
            LayoutOptimizationService::class,
            MerchandisingRulesService::class,
            ProductSelectionService::class,
            IAPlanogramService::class,
            IAPromptBuilderService::class,
            IAResponseParserService::class,
            SectionAIAllocator::class,
            SectionContextBuilder::class,
            SectionPersistenceService::class,
            SectionPlanogramService::class,
            SectionRulesAllocator::class,
            GondolaPrintService::class,
            QRCodeService::class,
        ] as $serviceClass) {
            $this->app->singleton($serviceClass);
        }
    }

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-raptor-planogram')
            ->hasConfigFile('plannogram')
            ->hasViews()
            ->hasCommand(LaravelRaptorPlanogramCommand::class);
    }

    public function boot(): void
    {
        parent::boot();

        $this->publishes([
            __DIR__.'/../config/plannogram.php' => config_path('plannogram.php'),
        ], 'plannogram-config');

        $this->registerPackageRoutes();
    }

    protected function registerPackageRoutes(): void
    {
        $planogramRouteFile = __DIR__.'/../routes/planogram.php';
        $exportRouteFile = __DIR__.'/../routes/export.php';

        if (is_file($planogramRouteFile)) {
            Route::middleware(config('plannogram.route_middleware', ['web', 'auth']))
                ->prefix(config('plannogram.route_prefix', 'planogram-package'))
                ->name(config('plannogram.route_name_prefix', 'planogram-package.'))
                ->group($planogramRouteFile);
        }

        if (is_file($exportRouteFile)) {
            Route::middleware(['web'])->group($exportRouteFile);
        }
    }
}
