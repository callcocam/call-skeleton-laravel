<?php

/**
 * Created by Claudio Campos.
 * User: callcocam, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptorPlanogram\Contracts;

interface PlanogramProductImageDispatcherContract
{
    /**
     * @param  array<int, string>  $eans
     */
    public function dispatchByEans(array $eans, string $database): void;
}
