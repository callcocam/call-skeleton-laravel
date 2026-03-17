<?php

/**
 * Created by Claudio Campos.
 * User: callcocam, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptorPlanogram\Contracts;

interface PlanogramShelfRepositoryContract
{
    public function exists(string $shelfId): bool;

    public function find(string $shelfId): ?object;

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): bool;

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(string $shelfId, array $data): int;

    /**
     * @return array<int, object>
     */
    public function findBySectionId(string $sectionId): array;

    /**
     * @param  array<int, array{id: string, ordering: int}>  $shelves
     */
    public function updateBatch(array $shelves): void;
}