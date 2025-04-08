<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\Plannerate\Http\Controllers;

use App\Http\Controllers\Controller;
use Callcocam\Plannerate\Facades\Plannerate;
use Callcocam\Plannerate\Models\Planogram;
use Inertia\Inertia;

class PlannerateController extends Controller
{


    public function index()
    {

        $data = Planogram::query()->latest()->paginate();

        return Inertia::render('plannerate/Index', [
            'title' => 'Planejamento de Tarefas',
            'description' => 'Planejamento de Tarefas',
            'breadcrumbs' => [
                ['title' => 'Planejamento de Tarefas', 'url' => route(Plannerate::getRoute())],
            ],
            'data' =>  $data
                ->withQueryString()
                ->through(fn($query) => [
                    'id' => $query->id,
                    'name' => $query->name,
                    'description' => $query->description,
                    'created_at' => $query->created_at,
                    'updated_at' => $query->updated_at,
                    'status' => [
                        'value' => $query->status,
                        'label' => $query->status->label(),
                        'color' => $query->status->color(),
                    ],
                    'gondola_count' => $query->gondola_count,
                    'tenant' => [
                        'id' => $query->tenant->id,
                        'name' => $query->tenant->name,
                    ],
                ]),
            'filters' => request()->only(['search', 'trashed']),
            'filtersData' => [
                'search' => request('search'),
                'trashed' => request('trashed'),
            ],
            'filtersOptions' => [
                'trashed' => [
                    'all' => 'Todos',
                    'only_trashed' => 'Somente Excluídos',
                    'only_active' => 'Somente Ativos',
                ],
            ],
            'actions' => [
                'create' => [
                    'label' => 'Novo Planejamento',
                    'url' => route(Plannerate::getRoute() . '.create'),
                ],
            ],
        ]);
    }
}
