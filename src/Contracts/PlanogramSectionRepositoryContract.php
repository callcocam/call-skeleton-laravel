<?php

/**
 * Created by Claudio Campos.
 * User: callcocam, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptorPlanogram\Contracts;

interface PlanogramSectionRepositoryContract
{
    public function exists(string $sectionId): bool;

    public function find(string $sectionId): ?object;

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): bool;

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(string $sectionId, array $data): int;

    /**
     * @return array<int, object>
     */
    public function findByGondolaId(string $gondolaId): array;

    /**
     * @param  array<int, array{id: string, ordering: int, name?: string}>  $sections
     */
    public function updateBatch(array $sections): void;
}
