<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptorPlanogram\Commands;

use Illuminate\Console\Command;

class LaravelRaptorPlanogramCommand extends Command
{
    public $signature = 'laravel-raptor-planogram';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
