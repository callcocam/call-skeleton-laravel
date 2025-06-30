<?php

/**
 * Created by :author_name.
 * User: :author_username, author@domain.com
 * https://www.sigasmart.com.br
 */

namespace VendorName\Skeleton\Shinobi\Concerns;

trait HasRolesAndPermissions
{
    use HasRoles, HasPermissions;

    /**
     * Run through the roles assigned to the permission and
     * checks if the user has any of them assigned.
     * 
     * @param  \Callcocam\PapaLeguasReact\Shinobi\Models\Permission  $permission
     * @return boolean
     */
    protected function hasPermissionThroughRole($permission): bool
    {
        if ($this->hasRoles()) {
            foreach ($permission->roles as $role) {
                if ($this->roles->contains($role)) {
                    return true;
                }
            }
        }

        return false;
    }

    protected function hasPermissionThroughRoleFlag(): bool
    { 
        if ($this->hasRoles()) {
            return  $this->roles
                ->filter(function ($role) { 
                    return $role->blocked;
                })->count() <= 0;
        }

        return false;
    }
}
