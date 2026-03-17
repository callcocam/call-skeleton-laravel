<?php

/**
 * Created by Claudio Campos.
 * User: callcocam, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptorPlanogram\Contracts;

interface PlanogramProductRepositoryContract
{
    public function find(string $productId): ?object;

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(object $product, array $data): bool;
}