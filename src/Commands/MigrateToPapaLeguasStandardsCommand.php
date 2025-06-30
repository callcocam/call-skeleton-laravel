<?php
/**
 * Created by :author_name.
 * User: :author_username, author@domain.com
 * https://www.sigasmart.com.br
 */

namespace VendorName\Skeleton\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MigrateToPapaLeguasStandardsCommand extends Command
{
    /**
     * A assinatura do comando no console.
     *
     * @var string
     */
    protected $signature = 'skeleton:migrate-standards 
                           {--backup : Criar backup dos arquivos existentes}
                           {--force : Forçar migração sem confirmação}';

    /**
     * A descrição do comando no console.
     *
     * @var string
     */
    protected $description = 'Migra o modelo User e migrations existentes para os padrões do Skeleton';

    /**
     * Executa o comando no console.
     */
    public function handle()
    {
        $this->info('🔧 Migração para Padrões Skeleton');
        $this->info('=====================================');

        if (!$this->option('force') && !$this->confirm('Isso irá modificar seu modelo User e criar novas migrations. Deseja continuar?')) {
            $this->info('Migração cancelada.');
            return;
        }

        // Criar backup se solicitado
        if ($this->option('backup')) {
            $this->createBackups();
        }

        // Migrar modelo User
        $this->migrateUserModel(); 
 
        // Mostrar resumo
        $this->showSummary();
    }

    /**
     * Criar backups dos arquivos existentes.
     */
    protected function createBackups()
    {
        $this->info('📁 Criando backups...');

        $backupDir = base_path('backups/skeleton-migration-' . date('Y-m-d-H-i-s'));
        File::makeDirectory($backupDir, 0755, true);

        // Backup do modelo User
        $userModelPath = app_path('Models/User.php');
        if (File::exists($userModelPath)) {
            File::copy($userModelPath, $backupDir . '/User.php.backup');
            $this->line("✅ User model salvo em backup: {$backupDir}/User.php.backup");
        }

        // Backup das migrations existentes
        $migrationsPath = database_path('migrations');
        $userMigrations = File::glob($migrationsPath . '/*_create_users_table.php');
        
        foreach ($userMigrations as $migration) {
            $filename = basename($migration);
            File::copy($migration, $backupDir . '/' . $filename . '.backup');
            $this->line("✅ Migration salva em backup: {$filename}");
        }

        $this->info("📁 Backups criados em: {$backupDir}");
    }

    /**
     * Migrar o modelo User para os padrões do Skeleton.
     */
    protected function migrateUserModel()
    {
        $this->info('🔄 Migrando modelo User...');

        $userModelPath = app_path('Models/User.php');
        
        if (!File::exists($userModelPath)) {
            $this->error('Modelo User não encontrado em app/Models/User.php');
            return;
        }

        $currentContent = File::get($userModelPath);
        
        // Verificar se já usa os padrões do Skeleton
        if (Str::contains($currentContent, 'AbstractModel')) {
            $this->warn('O modelo User já parece usar os padrões do Skeleton.');
            return;
        }

        $newContent = $this->generateUpdatedUserModel($currentContent);
        
        File::put($userModelPath, $newContent);
        $this->line('✅ Modelo User atualizado com padrões do Skeleton');
    }

    /**
     * Gerar o conteúdo atualizado do modelo User.
     */
    protected function generateUpdatedUserModel(string $currentContent): string
    {
         return File::get(__DIR__ . '/stubs/UserModel.stub');
    }

    /**
     * Mostrar resumo da migração.
     */
    protected function showSummary()
    {
        $this->info('');
        $this->info('🎉 Migração concluída com sucesso!');
        $this->info('=====================================');
        
        $this->line('✅ Modelo User atualizado com padrões do Skeleton');
        $this->line('✅ As migrations do pacote já incluem a estrutura atualizada da tabela users');
        
        $this->info('');
        $this->warn('⚠️  Próximos Passos:');
        $this->line('1. As migrations necessárias já foram publicadas pelo pacote');
        $this->line('2. Execute: php artisan migrate');
        $this->line('3. Para projetos existentes: considere fazer backup dos dados antes da migração');
        $this->line('4. Gere slugs para usuários existentes: User::whereNull("slug")->each(fn($u) => $u->save())');
        $this->line('5. Revise e teste o modelo User atualizado');
        
        if ($this->option('backup')) {
            $this->info('');
            $this->info('📁 Backups foram criados caso precise fazer rollback');
        }
        
        $this->info('');
        $this->info('📚 Documentação: Veja DEVELOPMENT_STANDARDS.md para detalhes completos');
        $this->info('🗂️  Migrations disponíveis: database/migrations/ (publicadas pelo pacote)');
    }
}
