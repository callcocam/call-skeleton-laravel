<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */
namespace Callcocam\Plannerate;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Callcocam\Plannerate\Commands\PlannerateCommand;

class PlannerateServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('plannerate')
            ->hasConfigFile()
            ->hasViews() 
            ->hasRoutes('api', 'web')
            ->hasMigration('create_plannerate_table')
            ->hasCommand(PlannerateCommand::class);
    }
}
