<?php
/**
 * Created by :author_name.
 * User: :author_username, author@domain.com
 * https://www.sigasmart.com.br
 */
namespace VendorName\Skeleton\Shinobi\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface Role
{
    /**
     * Roles can belong to many users.
     *
     * @return Model
     */
    public function users(): BelongsToMany;
    
    public function hasPermissionFlags(): bool;
    public function hasPermissionThroughFlag(): bool;
}