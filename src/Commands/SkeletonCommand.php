<?php
/**
 * Created by :author_name.
 * User: :author_username, author@domain.com
 * https://www.sigasmart.com.br
 */
namespace VendorName\Skeleton\Commands;

use Illuminate\Console\Command;

class SkeletonCommand extends Command
{
    /**
     * A assinatura do comando no console.
     *
     * @var string
     */
    protected $signature = 'skeleton:command {--option=default : Descrição da opção}';

    /**
     * A descrição do comando no console.
     *
     * @var string
     */
    protected $description = 'Comando base para o pacote Skeleton';

    /**
     * Executa o comando no console.
     */
    public function handle(): int
    {
        $this->info('🔧 Executando comando base do Skeleton...');
        $this->comment('Opção fornecida: ' . $this->option('option'));
        $this->newLine();
        
        $this->info('✅ Comando executado com sucesso!');

        return self::SUCCESS;
    }
}
