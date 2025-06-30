<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\PapaLeguasReact\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasTenant
{
    /**
     * Boot the trait.
     */
    protected static function bootHasTenant()
    {
        // Add global scope for tenant filtering if needed
    }

    /**
     * Get the tenant column name.
     */
    public function getTenantColumn(): string
    {
        return 'tenant_id';
    }

    /**
     * Scope a query to only include records for a specific tenant.
     */
    public function scopeForTenant(Builder $query, $tenantId): Builder
    {
        return $query->where($this->getTenantColumn(), $tenantId);
    }
}
