<?php

/**
 * Created by Claudio Campos.
 * User: callcocam, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptorPlanogram\Models;

use Callcocam\LaravelRaptor\Models\AbstractModel;
use Callcocam\LaravelRaptorFlow\Models\FlowExecution;
use Callcocam\LaravelRaptorPlanogram\Contracts\GondolaWorkflowContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Route;

class Gondola extends AbstractModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'gondolas';

    // Apenas route_gondolas (sem query); execution e workflow_execution_count sob demanda (evita loop/N+1)
    protected $appends = ['route_gondolas'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        static::$landlord->enable();
    }

    protected function casts(): array
    {
        return [
            'settings' => 'array',
        ];
    }

    public function sections()
    {
        return $this->hasMany(Section::class, 'gondola_id')
            ->orderBy('ordering', 'asc');
    }

    public function planogram()
    {
        return $this->belongsTo(Planogram::class);
    }

    public function getExecutionAttribute()
    {
        $execution = FlowExecution::query()
            ->where('workable_type', $this->gondolaWorkflowModelClass())
            ->where('workable_id', $this->id)
            ->first();

        if (! $execution) {
            return null;
        }

        return (object) [
            'id' => $execution->id,
            'started_at' => $execution->started_at?->format('Y-m-d'),
            'status' => $execution->status?->value ?? $execution->status,
            'sla_date' => $execution->sla_date,
        ];
    }

    public function getWorkflowExecutionCountAttribute(): int
    {
        return FlowExecution::query()
            ->where('workable_type', $this->gondolaWorkflowModelClass())
            ->where('workable_id', $this->id)
            ->count();
    }

    public function getRouteGondolasAttribute()
    {
        if (! Route::has('tenant.plannerates.editor.gondolas.edit')) {
            return null;
        }

        return route('tenant.plannerates.editor.gondolas.edit', ['planogram' => $this->planogram_id, 'record' => $this->id]);
    }

    protected function applyDomainContext(Builder $query): Builder
    {
        $clientId = config('plannogram.current_client_id');
        if (! $clientId) {
            return $query;
        }

        // Subquery direta no banco (evita usar o model Planogram e seus scopes/appends no scope)
        return $query->whereIn('planogram_id', function ($subquery) use ($clientId) {
            $subquery->from('planograms')
                ->where('client_id', $clientId)
                ->whereNull('deleted_at')
                ->select('id');
        });
    }

    protected function gondolaWorkflowModelClass(): string
    {
        return get_class(app(GondolaWorkflowContract::class));
    }
}
