<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\PapaLeguasReact\Models\Traits;

use Callcocam\PapaLeguasReact\Models\Address;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasAddresses
{
    /**
     * Get all addresses for the model.
     */
    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    /**
     * Get the primary address.
     */
    public function primaryAddress()
    {
        return $this->addresses()->where('is_primary', true)->first();
    }

    /**
     * Get addresses by type.
     */
    public function addressesByType(string $type)
    {
        return $this->addresses()->where('type', $type)->get();
    }
}
