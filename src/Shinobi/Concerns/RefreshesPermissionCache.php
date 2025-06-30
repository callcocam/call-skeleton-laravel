<?php
/**
 * Created by :author_name.
 * User: :author_username, author@domain.com
 * https://www.sigasmart.com.br
 */
namespace VendorName\Skeleton\Shinobi\Concerns;

trait RefreshesPermissionCache
{
    public static function bootRefreshesPermissionCache()
    {
        // static::saved(function() {
        //     cache()->tags(config('shinobi.cache.tag'))->flush();
        // });

        // static::deleted(function() {
        //     cache()->tags(config('shinobi.cache.tag'))->flush();
        // });
    }
}