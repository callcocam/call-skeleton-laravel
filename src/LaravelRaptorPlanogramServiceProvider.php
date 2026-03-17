<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */
namespace Callcocam\LaravelRaptorPlanogram;

use Illuminate\Support\Facades\Route;
use Callcocam\LaravelRaptorPlanogram\Commands\LaravelRaptorPlanogramCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelRaptorPlanogramServiceProvider extends PackageServiceProvider
{
    public function register(): void
    {
        parent::register();

        $this->mergeConfigFrom(__DIR__.'/../config/plannogram.php', 'plannogram');

        foreach ([
            \Callcocam\LaravelRaptorPlanogram\Services\PlanogramChangeService::class,
            \Callcocam\LaravelRaptorPlanogram\Services\GondolaService::class,
            \Callcocam\LaravelRaptorPlanogram\Services\GondolaPayloadService::class,
            \Callcocam\LaravelRaptorPlanogram\Services\SectionService::class,
            \Callcocam\LaravelRaptorPlanogram\Services\ShelfService::class,
            \Callcocam\LaravelRaptorPlanogram\Services\ShelfPositioningService::class,
            \Callcocam\LaravelRaptorPlanogram\Services\SegmentService::class,
            \Callcocam\LaravelRaptorPlanogram\Services\LayerService::class,
            \Callcocam\LaravelRaptorPlanogram\Services\ProductService::class,
            \Callcocam\LaravelRaptorPlanogram\Services\Analysis\AbcAnalysisService::class,
            \Callcocam\LaravelRaptorPlanogram\Services\Analysis\TargetStockService::class,
            \Callcocam\LaravelRaptorPlanogram\Services\AutoGenerate\AutoPlanogramService::class,
            \Callcocam\LaravelRaptorPlanogram\Services\AutoGenerate\LayoutOptimizationService::class,
            \Callcocam\LaravelRaptorPlanogram\Services\AutoGenerate\MerchandisingRulesService::class,
            \Callcocam\LaravelRaptorPlanogram\Services\AutoGenerate\ProductSelectionService::class,
            \Callcocam\LaravelRaptorPlanogram\Services\AiGenerate\IAPlanogramService::class,
            \Callcocam\LaravelRaptorPlanogram\Services\AiGenerate\IAPromptBuilderService::class,
            \Callcocam\LaravelRaptorPlanogram\Services\AiGenerate\IAResponseParserService::class,
            \Callcocam\LaravelRaptorPlanogram\Services\SectionGenerate\SectionAIAllocator::class,
            \Callcocam\LaravelRaptorPlanogram\Services\SectionGenerate\SectionContextBuilder::class,
            \Callcocam\LaravelRaptorPlanogram\Services\SectionGenerate\SectionPersistenceService::class,
            \Callcocam\LaravelRaptorPlanogram\Services\SectionGenerate\SectionPlanogramService::class,
            \Callcocam\LaravelRaptorPlanogram\Services\SectionGenerate\SectionRulesAllocator::class,
            \Callcocam\LaravelRaptorPlanogram\Services\Printing\GondolaPrintService::class,
            \Callcocam\LaravelRaptorPlanogram\Services\QRCode\QRCodeService::class,
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
