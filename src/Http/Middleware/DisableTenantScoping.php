<?php
/**
 * Created by :author_name.
 * User: :author_username, author@domain.com
 * https://www.sigasmart.com.br
 */
namespace VendorName\Skeleton\Http\Middleware;

use VendorName\Skeleton\Landlord\TenantManager;
use Closure;
use Illuminate\Http\Request;

class DisableTenantScoping
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Disable tenant scoping for this request
        app(TenantManager::class)->disable();

        return $next($request);
    }
}
