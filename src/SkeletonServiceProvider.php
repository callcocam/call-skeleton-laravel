<?php

/**
 * Created by :author_name.
 * User: :author_username, author@domain.com
 * https://www.sigasmart.com.br
 */

namespace VendorName\Skeleton;

use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use VendorName\Skeleton\Commands\SkeletonCommand;
use VendorName\Skeleton\Commands\CreateAdminCommand;
use VendorName\Skeleton\Commands\CreateTenantAdminCommand;
use VendorName\Skeleton\Commands\MigrateToPapaLeguasStandardsCommand;
use VendorName\Skeleton\Commands\CheckStandardsCommand;
use Illuminate\Support\Facades\Auth;
use VendorName\Skeleton\Providers\LandlordAuthProvider;
use VendorName\Skeleton\Guards\LandlordGuard;
use VendorName\Skeleton\Http\Middleware\LandlordAuth;
use VendorName\Skeleton\Http\Middleware\DisableTenantScoping;
use Illuminate\Support\Facades\Route;

class SkeletonServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * Esta classe é um Provedor de Serviços do Pacote
         *
         * Mais informações: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('skeleton')
            ->hasConfigFile()
            ->hasViews()
            ->hasRoutes([
                'admin',
                'landlord',
                'web',
                'api',
            ])
            ->hasMigrations([
                'create_users_table',
                'create_admins_table',
                'create_tenants_table',
                'create_addresses_table',
                'create_roles_table',
                'create_permissions_table',
                'create_role_user_table',
                'create_permission_user_table',
                'create_permission_role_table',
                'create_admin_role_table',
                'create_admin_tenant_table',
                'create_workflows_table',
                'create_workflow_templates_table',
                'create_workflowables_table'
            ])
            ->hasCommand(SkeletonCommand::class)
            ->hasCommand(MigrateToPapaLeguasStandardsCommand::class)
            ->hasCommand(CheckStandardsCommand::class)
            ->hasCommand(CreateAdminCommand::class)
            ->hasCommand(CreateTenantAdminCommand::class)
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishAssets()
                    ->publishMigrations()
                    ->publish('skeletont:translations')
                    ->askToRunMigrations()
                    ->copyAndRegisterServiceProviderInApp()
                    ->askToStarRepoOnGitHub(':vendor_slug/skeletont')
                    ->endWith(function (InstallCommand $command) {
                        $command->info('');
                        $command->info('🚀 Papa Leguas React instalado com sucesso!');
                        $command->info('');

                        if ($command->confirm('Deseja migrar seus modelos e migrations para os padrões Papa Leguas?', true)) {
                            $command->call('papa-leguas:migrate-standards', [
                                '--backup' => true,
                                '--force' => true
                            ]);

                            $command->info('');
                            $command->info('✅ Migração concluída! Verifique os arquivos gerados.');
                            $command->info('️  As migrations necessárias foram publicadas automaticamente.');
                            $command->info('📝 Consulte packages/:vendor_slug/skeletont/UPDATES.md para mais detalhes.');
                        } else {
                            $command->info('');
                            $command->warn('⚠️  Para migrar mais tarde, execute: php artisan papa-leguas:migrate-standards --backup');
                            $command->info('📝 Consulte packages/:vendor_slug/skeletont/UPDATES.md para instruções detalhadas.');
                        }

                        $command->info('');
                        $command->info('📚 Documentação disponível em:');
                        $command->info('   - packages/:vendor_slug/skeletont/DEVELOPMENT_STANDARDS.md');
                        $command->info('   - packages/:vendor_slug/skeletont/EXAMPLES.md');
                        $command->info('   - packages/:vendor_slug/skeletont/OPTIMIZATION_REPORT.md');
                    });
            });
    }

    public function register(): void
    {
        parent::register();

        // Registra os provedores de serviço primeiro
        $this->registerServiceProviders();
    }

    public function packageBooted(): void
    {
        // Registra o guard de autenticação landlord
        $this->registerLandlordAuth();

        // Registra os middlewares
        $this->registerMiddleware();

        // Carrega as rotas do landlord
        $this->loadLandlordRoutes();

        // Mostra mensagem de atualização dos padrões se necessário (apenas no console)
        $this->showStandardsUpdateMessage();
    }

    /**
     * Registra o guard de autenticação landlord e seu provedor.
     */
    protected function registerLandlordAuth(): void
    {
        Auth::provider('landlord', function ($app, array $config) {
            return new LandlordAuthProvider(
                $app['hash'],
                $config['model'] ?? config('skeletont.landlord.model')
            );
        });

        Auth::extend('landlord', function ($app, $name, array $config) {
            return new LandlordGuard(
                $name,
                Auth::createUserProvider($config['provider']),
                $app['session.store'],
                $app['request']
            );
        });
    }

    /**
     * Registra os middlewares.
     */
    protected function registerMiddleware(): void
    {
        $router = $this->app['router'];

        $router->aliasMiddleware('landlord.auth', LandlordAuth::class);
        $router->aliasMiddleware('disable.tenant.scoping', DisableTenantScoping::class);
    }

    /**
     * Carrega as rotas do landlord.
     */
    protected function loadLandlordRoutes(): void
    {
        $config = config('skeletont.landlord.routes', []);

        Route::middleware($config['middleware'] ?? ['web'])
            ->prefix($config['prefix'] ?? 'landlord')
            ->group(function () {
                $this->loadRoutesFrom(__DIR__ . '/../routes/landlord.php');
            });
    }

    /**
     * Registra provedores de serviço adicionais.
     */
    protected function registerServiceProviders(): void
    {
        $this->app->register(\VendorName\Skeleton\Shinobi\ShinobiServiceProvider::class);
        $this->app->register(\VendorName\Skeleton\Landlord\LandlordServiceProvider::class);
    }

    /**
     * Verifica se o projeto precisa ser atualizado para os padrões Papa Leguas.
     */
    public function checkForStandardsUpdate(): bool
    {
        $userModelPath = app_path('Models/User.php');

        if (!file_exists($userModelPath)) {
            return false;
        }

        $userModelContent = file_get_contents($userModelPath);

        // Verifica se o modelo User já segue os padrões Papa Leguas
        $hasUlid = str_contains($userModelContent, 'HasUlids') || str_contains($userModelContent, 'ulid');
        $hasSlug = str_contains($userModelContent, 'HasSlug') || str_contains($userModelContent, 'slug');
        $hasStatus = str_contains($userModelContent, 'status') || str_contains($userModelContent, 'BaseStatus');
        $hasTenantId = str_contains($userModelContent, 'tenant_id');
        $hasSoftDeletes = str_contains($userModelContent, 'SoftDeletes');

        // Se estiver faltando recursos chave do Papa Leguas, precisa atualizar
        return !($hasUlid && $hasSlug && $hasStatus && $hasTenantId && $hasSoftDeletes);
    }

    /**
     * Mostra mensagem de atualização dos padrões se necessário.
     */
    public function showStandardsUpdateMessage(): void
    {
        if ($this->checkForStandardsUpdate()) {
            if (app()->runningInConsole()) {
                echo "\n";
                echo "📦 \033[33mAtualização dos Padrões Papa Leguas Disponível\033[0m\n";
                echo "   Seu projeto pode se beneficiar dos padrões Papa Leguas mais recentes.\n";
                echo "   Execute: \033[32mphp artisan papa-leguas:migrate-standards --backup\033[0m\n";
                echo "\n";
            }
        }
    }
}
