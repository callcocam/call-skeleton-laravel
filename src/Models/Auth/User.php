<?php
/**
 * Created by :author_name.
 * User: :author_username, author@domain.com
 * https://www.sigasmart.com.br
 */

namespace VendorName\Skeleton\Models\Auth;
use VendorName\Skeleton\Models\AbstractModel;
use VendorName\Skeleton\Shinobi\Concerns\HasRolesAndPermissions;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract; 
use Illuminate\Foundation\Auth\Access\Authorizable;
use VendorName\Skeleton\Enums\UserStatus;
use VendorName\Skeleton\Models\Traits\HasTenant;

class User extends AbstractModel implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword, MustVerifyEmail, HasRolesAndPermissions, HasTenant;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => UserStatus::class,
        ];
    }

    /**
     * Check if user account is active
     */
    public function isActive(): bool
    {
        return $this->status === UserStatus::Published;
    }
}
