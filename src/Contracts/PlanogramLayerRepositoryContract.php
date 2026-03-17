<?php

/**
 * Created by Claudio Campos.
 * User: callcocam, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptorPlanogram\Contracts;

interface PlanogramLayerRepositoryContract
{
    public function findByProductId(string $productId): ?object;

    public function find(string $layerId): ?object;

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): bool;

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(string $layerId, array $data): int;

    public function delete(string $layerId): int;

    public function countBySegmentId(string $segmentId): int;
}
