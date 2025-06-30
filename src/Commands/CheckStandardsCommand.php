<?php

/**
 * Created by :author_name.
 * User: :author_username, author@domain.com
 * https://www.sigasmart.com.br
 */

namespace VendorName\Skeleton\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CheckStandardsCommand extends Command
{
    /**
     * A assinatura do comando no console.
     *
     * @var string
     */
    protected $signature = 'skeleton:check-standards {--show-details : Mostrar análise detalhada}';
    
    /**
     * A descrição do comando no console.
     *
     * @var string
     */
    protected $description = 'Verifica se o projeto segue os padrões do Skeleton';

    /**
     * Executa o comando no console.
     */
    public function handle(): int
    {
        $this->info('🔍 Verificando padrões do Skeleton...');
        $this->newLine();

        $checks = $this->performChecks();
        $passed = count(array_filter($checks, fn($check) => $check['status']));
        $total = count($checks);

        // Mostra o resumo
        $this->info("📊 Resultado: {$passed}/{$total} verificações passaram");
        $this->newLine();

        // Mostra os resultados detalhados
        foreach ($checks as $check) {
            $icon = $check['status'] ? '✅' : '❌';
            $this->line("{$icon} {$check['name']}");
            
            if ($this->option('show-details') && !empty($check['details'])) {
                $this->line("   {$check['details']}");
            }
        }

        $this->newLine();

        if ($passed < $total) {
            $this->warn('⚠️  Seu projeto não está seguindo todos os padrões do Skeleton.');
            $this->info('💡 Execute: php artisan skeleton:migrate-standards --backup');
            $this->newLine();
            
            return self::FAILURE;
        } else {
            $this->info('🎉 Parabéns! Seu projeto segue todos os padrões do Skeleton.');
            $this->newLine();
            
            return self::SUCCESS;
        }
    }

    protected function performChecks(): array
    {
        $checks = [];

        // Verifica o modelo User
        $userModelPath = app_path('Models/User.php');
        $checks[] = $this->checkUserModel($userModelPath);

        // Verifica a estrutura das migrations
        $checks[] = $this->checkMigrationsStructure();

        // Verifica os arquivos de configuração
        $checks[] = $this->checkConfigFiles();

        // Verifica se o Shinobi está configurado corretamente
        $checks[] = $this->checkShinobiConfiguration();

        return $checks;
    }

    /**
     * Verifica o modelo User
     */
    protected function checkUserModel(string $path): array
    {
        if (!File::exists($path)) {
            return [
                'name' => 'Modelo User Existe',
                'status' => false,
                'details' => 'Modelo User não encontrado em ' . $path
            ];
        }

        $content = File::get($path);
        
        // Verifica se está usando a classe Authenticatable do Skeleton
        $usesShinobiAuth = str_contains($content, 'VendorName\Skeleton\Models\Auth\User as Authenticatable');
        
        if ($usesShinobiAuth) {
            return [
                'name' => 'Padrões do Modelo User',
                'status' => true,
                'details' => 'Usando classe Authenticatable do Skeleton (padrões incluídos)'
            ];
        }
        
        // Verifica traits individuais se não estiver usando Authenticatable do Skeleton
        $hasUlid = str_contains($content, 'HasUlids') || str_contains($content, 'ulid');
        $hasSlug = str_contains($content, 'HasSlug') || str_contains($content, 'slug');
        $hasStatus = str_contains($content, 'status') || str_contains($content, 'BaseStatus');
        $hasTenantId = str_contains($content, 'tenant_id');
        $hasSoftDeletes = str_contains($content, 'SoftDeletes');
        $hasPermissions = str_contains($content, 'HasPermissions') || str_contains($content, 'HasRoles');

        $missing = [];
        if (!$hasUlid) $missing[] = 'ULID';
        if (!$hasSlug) $missing[] = 'Slug';
        if (!$hasStatus) $missing[] = 'Status Enum';
        if (!$hasTenantId) $missing[] = 'Tenant ID';
        if (!$hasSoftDeletes) $missing[] = 'Soft Deletes';
        if (!$hasPermissions) $missing[] = 'Permissões/Roles';

        return [
            'name' => 'Padrões do Modelo User',
            'status' => empty($missing),
            'details' => empty($missing) ? 'Todos os padrões implementados' : 'Faltando: ' . implode(', ', $missing)
        ];
    }

    /**
     * Verifica a estrutura das migrations
     */
    protected function checkMigrationsStructure(): array
    {
        $migrationPath = database_path('migrations');
        $migrations = File::glob($migrationPath . '/*_create_users_table.php');

        if (empty($migrations)) {
            return [
                'name' => 'Migration Users Existe',
                'status' => false,
                'details' => 'Migration create_users_table não encontrada'
            ];
        }

        $migrationContent = File::get($migrations[0]);
        
        $hasUlidColumn = str_contains($migrationContent, "table->ulid('id')") || 
                        str_contains($migrationContent, '$table->ulid(');
        $hasStatusEnum = str_contains($migrationContent, "enum('status'") || 
                        str_contains($migrationContent, 'status');
        $hasSlugColumn = str_contains($migrationContent, "string('slug')") || 
                        str_contains($migrationContent, 'slug');
        $hasTenantColumn = str_contains($migrationContent, 'tenant_id');
        $hasIndexes = str_contains($migrationContent, 'index(') || 
                     str_contains($migrationContent, 'unique(');

        $missing = [];
        if (!$hasUlidColumn) $missing[] = 'chave primária ULID';
        if (!$hasStatusEnum) $missing[] = 'enum status';
        if (!$hasSlugColumn) $missing[] = 'coluna slug';
        if (!$hasTenantColumn) $missing[] = 'Tenant ID';
        if (!$hasIndexes) $missing[] = 'índices de performance';

        return [
            'name' => 'Padrões da Migration Users',
            'status' => empty($missing),
            'details' => empty($missing) ? 'Migration segue os padrões' : 'Faltando: ' . implode(', ', $missing)
        ];
    }

    /**
     * Verifica os arquivos de configuração
     */
    protected function checkConfigFiles(): array
    {
        $configFiles = ['skeleton', 'shinobi', 'tenant'];
        $published = 0;

        foreach ($configFiles as $config) {
            if (File::exists(config_path($config . '.php'))) {
                $published++;
            }
        }

        return [
            'name' => 'Arquivos de Configuração Publicados',
            'status' => $published === count($configFiles),
            'details' => "{$published}/" . count($configFiles) . " arquivos de configuração publicados"
        ];
    }

    /**
     * Verifica a configuração do Shinobi
     */
    protected function checkShinobiConfiguration(): array
    {
        $configPath = config_path('shinobi.php');
        
        if (!File::exists($configPath)) {
            return [
                'name' => 'Configuração Shinobi',
                'status' => false,
                'details' => 'Arquivo de configuração shinobi.php não publicado'
            ];
        }

        // Verifica se as migrations do Shinobi existem
        $migrationPath = database_path('migrations');
        $rolesMigration = !empty(File::glob($migrationPath . '/*_create_roles_table.php'));
        $permissionsMigration = !empty(File::glob($migrationPath . '/*_create_permissions_table.php'));

        $status = $rolesMigration && $permissionsMigration;

        return [
            'name' => 'Sistema ACL Shinobi',
            'status' => $status,
            'details' => $status ? 'Sistema ACL configurado' : 'Faltam migrations do Shinobi'
        ];
    }
}
