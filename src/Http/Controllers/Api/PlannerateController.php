<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\Plannerate\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PlanogramResource;
use Callcocam\Plannerate\Facades\Plannerate;
use Callcocam\Plannerate\Models\Planogram; 

class PlannerateController extends Controller
{


    public function index()
    {

        $data = Planogram::query()->latest()->paginate();

        return  PlanogramResource::collection($data)
            ->additional([
                'meta' => [
                    'title' => 'Planejamento de Tarefas',
                    'description' => 'Planejamento de Tarefas',
                    'breadcrumbs' => [
                        ['title' => 'Planejamento de Tarefas', 'url' => route(Plannerate::getRoute())],
                    ],
                ],
            ]);
    }
}
