<?php
/**
 * Created by :author_name.
 * User: :author_username, author@domain.com
 * https://www.sigasmart.com.br
 */
namespace VendorName\Skeleton\Shinobi\Tactics;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;
use Callcocam\PapaLeguasReact\Shinobi\Facades\Shinobi;

class RevokePermissionFrom
{
    /**
     * @var array
     */
    protected $permissions;

    /**
     * Create a new GivePermissionTo instance.
     *
     * @param  array  $permissions
     */
    public function __construct(...$permissions)
    {
        $this->permissions = Arr::flatten($permissions);
    }

    public function to($roleOrUser)
    {
        if ($roleOrUser instanceof Model) {
            $instance = $roleOrUser;
        } else {
            $instance = Shinobi::role()->where('slug', $roleOrUser)->firstOrFail();
        }

        $instance->revokePermissionTo($this->permissions);
    }
}
