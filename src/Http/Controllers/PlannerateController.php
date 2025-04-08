<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\Plannerate\Http\Controllers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class PlannerateController extends Controller
{
     

    public function index()
    {
        return Inertia::render('plannerate/Index', [
            'title' => 'Planejamento de Tarefas',
            'description' => 'Planejamento de Tarefas',
        ]);
    }
}