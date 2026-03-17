<?php

/**
 * Created by Claudio Campos.
 * User: callcocam, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptorPlanogram\Support\Traits;

use Illuminate\Support\Facades\DB;

trait HasCrossDatabaseRelations
{
    public function getStoreAttribute()
    {
        if (! $this->store_id) {
            return null;
        }

        return cache()->remember("store:{$this->store_id}", 3600, function () {
            return DB::connection(config('raptor.database.landlord_connection_name', 'landlord'))
                ->table('stores')
                ->where('id', $this->store_id)
                ->first();
        });
    }

    public function getClusterAttribute()
    {
        if (! $this->cluster_id) {
            return null;
        }

        return cache()->remember("cluster:{$this->cluster_id}", 3600, function () {
            return DB::connection(config('raptor.database.landlord_connection_name', 'landlord'))
                ->table('clusters')
                ->where('id', $this->cluster_id)
                ->first();
        });
    }

    public function getClientAttribute()
    {
        if (! $this->client_id) {
            return null;
        }

        return cache()->remember("client:{$this->client_id}", 3600, function () {
            return DB::connection(config('raptor.database.landlord_connection_name', 'landlord'))
                ->table('clients')
                ->where('id', $this->client_id)
                ->first();
        });
    }
}
