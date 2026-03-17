<?php

namespace Callcocam\LaravelRaptorPlanogram\Contracts;

use Laravel\Ai\Responses\AgentResponse;

interface PlanogramSectionAllocatorContract
{
    /**
     * @param  array<int, mixed>  $attachments
     * @param  array<string, mixed>|string|null  $provider
     */
    public function prompt(string $prompt, array $attachments = [], array|string|null $provider = null, ?string $model = null, ?int $timeout = null): AgentResponse;
}
