<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */
namespace Callcocam\LaravelRaptorPlanogram\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Callcocam\LaravelRaptorPlanogram\LaravelRaptorPlanogram
 */
class LaravelRaptorPlanogram extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Callcocam\LaravelRaptorPlanogram\LaravelRaptorPlanogram::class;
    }
}
