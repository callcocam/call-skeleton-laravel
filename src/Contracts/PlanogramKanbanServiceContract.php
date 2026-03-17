<?php

/**
 * Created by Claudio Campos.
 * User: callcocam, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptorPlanogram\Contracts;

use Callcocam\LaravelRaptorFlow\Support\Builders\ConfigureKanbanCard;
use Callcocam\LaravelRaptorFlow\Support\Builders\ConfigureKanbanModal;
use Closure;

interface PlanogramKanbanServiceContract
{
    public function setFilters(array $filters): static;

    public function withDetailModal(bool $enable = true): static;

    public function modal(ConfigureKanbanModal $modal): static;

    public function card(ConfigureKanbanCard $card): static;

    public function getBoardData(): array;

    public function getDetailModalConfig(): array;

    public function buildDefaultModalBuilder(array $urls): ConfigureKanbanModal;

    public function buildDefaultCardBuilder(
        ?Closure $openUrlResolver = null,
        ?Closure $openVisibilityResolver = null,
    ): ConfigureKanbanCard;
}
