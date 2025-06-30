<?php
/**
 * Created by :author_name.
 * User: :author_username, author@domain.com
 * https://www.sigasmart.com.br
 */
namespace VendorName\Skeleton\Shinobi\Models;

use VendorName\Skeleton\Shinobi\Concerns\RefreshesPermissionCache;
use VendorName\Skeleton\Shinobi\Contracts\Permission as PermissionContract;
use VendorName\Skeleton\Models\AbstractModel;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Permission extends AbstractModel implements PermissionContract
{
    use RefreshesPermissionCache, HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'status',
        'user_id',
        'tenant_id',
    ];

    /**
     * Create a new Permission instance.
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setTable(config('shinobi.tables.permissions'));
    }

    /**
     * Permissions can belong to many roles.
     *
     * @return Model
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(config('shinobi.models.role'))->withTimestamps();
    }
}
