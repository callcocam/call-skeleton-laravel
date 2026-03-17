<?php

/**
 * Created by Claudio Campos.
 * User: callcocam, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptorPlanogram\Http\Controllers;

use Callcocam\LaravelRaptor\Http\Controllers\ResourceController;
use Callcocam\LaravelRaptorPlanogram\Contracts\PlanogramKanbanServiceContract;
use Callcocam\LaravelRaptorPlanogram\Contracts\PlanogramStoreRepositoryContract;
use Callcocam\LaravelRaptorPlanogram\Contracts\PlanogramUserRepositoryContract;
use Callcocam\LaravelRaptorPlanogram\Contracts\PlanogramWorkflowContract;
use Callcocam\LaravelRaptorPlanogram\Models\Planogram;
use Callcocam\LaravelRaptor\Support\Pages\Show;
use Callcocam\LaravelRaptorFlow\Models\FlowConfigStep;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PlanogramEditorController extends ResourceController
{
    public function getPages(): array
    {
        return [
            'show' => Show::route('/plannograma/{record}/editor')
                ->label('Plannerates')
                ->name('plannerates.index')
                ->icon('Store')
                ->group('Planogramas')
                ->groupCollapsible(true)
                ->order(20)
                ->middlewares(['auth', 'verified']),
        ];
    }

    public function show(Request $request, $record): Response
    {
        $record = Planogram::findOrFail($record);
        if (! $record) {
            abort(403);
        }

        // Carrega apenas a estrutura das gôndolas sem produtos (otimizado)
        $record->load(['gondolas.sections.shelves']);

        // Converte para array e adiciona store (cross-database)
        $recordArray = $record->toArray();

        // Carrega dados do mapa da store se existir (está no banco landlord)
        if ($record->store_id) {
            $store = $this->storeRepository()->findMapDataById($record->store_id);
            if ($store !== null) {
                $recordArray['store'] = $store;
            }
        }

        $hasWorkflowConfig = FlowConfigStep::query()
            ->where('configurable_type', $this->planogramWorkflowModelClass())
            ->where('configurable_id', $record->id)
            ->exists();

        // Monta filtros com planogram_id fixo
        $filters = $this->buildFilters($request, $record->id);

        // Se não existe configuração de workflow, mostra apenas a view de gôndolas
        if (! $hasWorkflowConfig) {
            return Inertia::render('tenant/plannerates/index', [
                'filters' => $filters,
                'record' => $recordArray,
                'users' => $this->userRepository()->listBasicUsers(),
            ]);
        }

        // Carrega dados do Kanban filtrados pelo planograma atual (com dados para o modal de detalhes)
        $service = app(PlanogramKanbanServiceContract::class)
            ->setFilters($filters)
            ->withDetailModal(true)
            ->modal(app(PlanogramKanbanServiceContract::class)->buildDefaultModalBuilder([
                'start' => $this->gondolaActionUrl('flow.execution.start', 'execution', 'id'),
                'pause' => $this->gondolaActionUrl('flow.execution.pause', 'execution', 'id'),
                'resume' => $this->gondolaActionUrl('flow.execution.resume', 'execution', 'id'),
                'abandon' => $this->gondolaActionUrl('flow.execution.abandon', 'execution', 'id'),
                'notes' => $this->gondolaActionUrl('flow.execution.notes', 'execution', 'id'),
            ]))
            ->card(app(PlanogramKanbanServiceContract::class)->buildDefaultCardBuilder());

        $kanbanData = $service
            ->getBoardData();

        return Inertia::render('tenant/plannerates/kanban', [
            'message' => 'Visualização Kanban do Planograma',
            'resourceName' => $record->name,
            'resourcePluralName' => 'kanbans',
            'resourceLabel' => $record->name,
            'resourcePluralLabel' => $record->name,
            'maxWidth' => 'full',
            'filters' => $filters,
            'record' => $recordArray,
            'planogramIdForCreate' => $record->id, // Permite criar góndola neste planograma
            'breadcrumbs' => $this->buildBreadcrumbs($record),
            'detailModalConfig' => $service->getDetailModalConfig(),
            ...$kanbanData,
        ]);
    }

    /**
     * Constrói os filtros a partir da requisição
     *
     * Filtros disponíveis:
     * - planogram_id: Fixo (planograma atual) - não pode ser mudado
     * - loja_id: Filtra execuções por loja (NÃO afeta options de planogramas)
     * - user_id: Filtra execuções pelo responsável atual
     * - assigned_to: Filtra execuções por atribuição (role/user)
     * - status: Filtra execuções por status (not_started, in_progress, completed, etc)
     * - only_overdue: Mostra apenas execuções atrasadas
     * - show_completed: Inclui execuções completadas
     */
    protected function buildFilters(Request $request, string $planogramId): array
    {
        return [
            'planogram_id' => $planogramId, // Fixo no planograma atual
            'loja_id' => $request->input('loja_id'),
            'user_id' => $request->input('user_id'),
            'assigned_to' => $request->input('assigned_to'),
            'status' => $request->input('status'),
            'only_overdue' => $request->boolean('only_overdue'),
            'show_completed' => $request->boolean('show_completed'),
        ];
    }

    /**
     * Constrói o breadcrumb para navegação
     */
    protected function buildBreadcrumbs(Planogram $record): array
    {
        return [
            [
                'label' => 'Painel de controle',
                'url' => route('dashboard', [], false),
            ],
            [
                'label' => 'Planogramas',
                'url' => route('tenant.planograms.index', [], false),
            ],
            [
                'label' => $record->name,
                'url' => null,
            ],
        ];
    }

    protected function resourcePath(): ?string
    {
        return 'tenant';
    }

    protected function gondolaActionUrl(
        string $routeName,
        string $routeParam = 'gondola',
        string $placeholder = 'workable.id'
    ): string {
        $marker = '__PLACEHOLDER__';
        $generated = route($routeName, [$routeParam => $marker], false);

        return str_replace($marker, "{{$placeholder}}", $generated);
    }

    protected function getResourceLabel(): ?string
    {
        return 'Plannerate';
    }

    protected function storeRepository(): PlanogramStoreRepositoryContract
    {
        return app(PlanogramStoreRepositoryContract::class);
    }

    protected function userRepository(): PlanogramUserRepositoryContract
    {
        return app(PlanogramUserRepositoryContract::class);
    }

    protected function planogramWorkflowModelClass(): string
    {
        return get_class(app(PlanogramWorkflowContract::class));
    }
}
