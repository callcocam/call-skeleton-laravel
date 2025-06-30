<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\PapaLeguasReact\Models;
 
use Callcocam\PapaLeguasReact\Shinobi\Models\Permission as ModelsPermission;
use Callcocam\PapaLeguasReact\Enums\PermissionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends ModelsPermission
{
    use SoftDeletes, HasFactory;
 

    protected $casts = [
        'status' => PermissionStatus::class,
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }
}
