<?php

/**
 * Created by Claudio Campos.
 * User: callcocam, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptorPlanogram\Contracts;

interface PlanogramSegmentRepositoryContract
{
    public function find(string $segmentId): ?object;

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): bool;

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(string $segmentId, array $data): int;

    public function delete(string $segmentId): int;
}
