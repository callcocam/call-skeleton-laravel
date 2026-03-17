<?php

/**
 * Created by Claudio Campos.
 * User: callcocam, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptorPlanogram\Contracts;

use Illuminate\Support\Collection;

interface PlanogramUserRepositoryContract
{
    public function listBasicUsers(): Collection;

    /**
     * @return array<int, array{id: string, name: string}>
     */
    public function listUsersByTenant(string $tenantId): array;
}
