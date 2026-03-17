<?php

/**
 * Created by Claudio Campos.
 * User: callcocam, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptorPlanogram\Models;

use Callcocam\LaravelRaptor\Models\AbstractModel;
use Callcocam\LaravelRaptorFlow\Models\FlowConfigStep;
use Callcocam\LaravelRaptorPlanogram\Contracts\PlanogramCategoryContract;
use Callcocam\LaravelRaptorPlanogram\Contracts\PlanogramWorkflowContract;
use Callcocam\LaravelRaptorPlanogram\Enums\GondolaWorkflowStatus;
use Callcocam\LaravelRaptorPlanogram\Support\Traits\HasCrossDatabaseRelations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Planogram extends AbstractModel
{
    use HasCrossDatabaseRelations, HasFactory, SoftDeletes;

    /**
     * Hierarquia de níveis de categoria (ordem é importante).
     */
    protected const HIERARCHY_LEVELS = [
        1 => 'Segmento varejista',
        2 => 'Departamento',
        3 => 'Subdepartamento',
        4 => 'Categoria',
        5 => 'Subcategoria',
        6 => 'Segmento',
        7 => 'Subsegmento',
        8 => 'Atributo',
    ];

    protected $table = 'planograms';

    protected $appends = ['mercadologico_cascading', 'hierarchy_path', 'client_cascading', 'start_month', 'end_month'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        static::$landlord->enable();
    }

    protected function applyDomainContext(Builder $query): Builder
    {
        if ($clientId = config('plannogram.current_client_id')) {
            return $query->where('client_id', $clientId);
        }

        return $query;
    }

    public function gondolas()
    {
        return $this->hasMany(Gondola::class);
    }

    public function gondolasStarted()
    {
        $gondolaIds = DB::connection(config('raptor.database.landlord_connection_name', 'landlord'))
            ->table('gondola_workflow_executions')
            ->where('status', GondolaWorkflowStatus::InProgress->value)
            ->pluck('gondola_id'); 

        return $this->hasMany(Gondola::class)->whereIn('id', $gondolaIds);
    }

    public function getClientCascadingAttribute()
    {
        return [
            'client_id' => $this->client?->id,
            'store_id' => $this->store?->id,
            'cluster_id' => $this->cluster?->id,
        ];
    }

    /**
     * Retorna o mes inicial formatado para exibicao (ex: "Janeiro 2025")
     */
    public function getStartMonthAttribute()
    {
        if (! $this->start_date) {
            return null;
        }

        $date = is_string($this->start_date) ? Carbon::parse($this->start_date) : $this->start_date;
        $date->locale('pt_BR');

        return $date->translatedFormat('F de Y');
    }

    /**
     * Retorna o mes final formatado para exibicao (ex: "Julho 2025")
     */
    public function getEndMonthAttribute()
    {
        if (! $this->end_date) {
            return null;
        }

        $date = is_string($this->end_date) ? Carbon::parse($this->end_date) : $this->end_date;
        $date->locale('pt_BR');

        return $date->translatedFormat('F de Y');
    }

    /**
     * Retorna o mes inicial no formato YYYY-MM para inputs type="month"
     *
     * @return string|null Ex: "2025-01" ou null se nao houver data
     */
    public function getStartMonthInput(): ?string
    {
        if (! $this->start_date) {
            return null;
        }

        $date = is_string($this->start_date) ? Carbon::parse($this->start_date) : $this->start_date;

        return $date->format('Y-m');
    }

    /**
     * Retorna o mes final no formato YYYY-MM para inputs type="month"
     *
     * @return string|null Ex: "2025-07" ou null se nao houver data
     */
    public function getEndMonthInput(): ?string
    {
        if (! $this->end_date) {
            return null;
        }

        $date = is_string($this->end_date) ? Carbon::parse($this->end_date) : $this->end_date;

        return $date->format('Y-m');
    }

    public function category()
    {
        return $this->belongsTo($this->categoryModelClass());
    }

    /**
     * Retorna a hierarquia como string formatada.
     */
    public function getHierarchyPathAttribute()
    {
        if (! $this->category || ! method_exists($this->category, 'getFullHierarchy')) {
            return null;
        }

        return $this->category->getFullHierarchy()
            ->pluck('name')
            ->implode(' > ');
    }

    public function getMercadologicoCascadingAttribute()
    {
        $mercadologicoNivel = [];

        if ($category = $this->category) {
            if (method_exists($category, 'getFullHierarchy') && ($categories = $category->getFullHierarchy())) {
                foreach ($categories as $key => $level) {
                    if (! $level->level_name) {
                        $level->level_name = Str::slug(self::HIERARCHY_LEVELS[($key + 1)] ?? 'nivel_'.($key + 1), '_');
                        $level->nivel = $key + 1;
                        $level->save();
                    }

                    $levelName = Str::slug($level->level_name, '_');
                    $mercadologicoNivel[$levelName] = $level->id;
                }
            }
        }

        return $mercadologicoNivel;
    }

    /**
     * Etapas de workflow do planograma (FlowConfigStep com configuravel = PlanogramWorkflow + este id).
     */
    public function flowConfigSteps()
    {
        return FlowConfigStep::query()
            ->where('configurable_type', $this->planogramWorkflowModelClass())
            ->where('configurable_id', $this->id)
            ->orderBy('order');
    }

    protected function planogramWorkflowModelClass(): string
    {
        return get_class(app(PlanogramWorkflowContract::class));
    }

    protected function categoryModelClass(): string
    {
        return get_class(app(PlanogramCategoryContract::class));
    }
}
