<?php

/**
 * Created by Claudio Campos.
 * User: callcocam, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptorPlanogram\Contracts;

interface PlanogramGondolaRepositoryContract
{
    public function find(string $gondolaId): ?object;

    public function findOrFail(string $gondolaId): object;

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(string $gondolaId, array $data): int;
}
