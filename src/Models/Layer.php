<?php

/**
 * Created by Claudio Campos.
 * User: callcocam, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptorPlanogram\Models;

use Callcocam\LaravelRaptor\Models\AbstractModel;
use Callcocam\LaravelRaptorPlanogram\Contracts\PlanogramProductContract;
use Illuminate\Database\Eloquent\SoftDeletes;

class Layer extends AbstractModel
{
    use SoftDeletes;

    protected $table = 'layers';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        static::$landlord->enable();
    }

    public function product()
    {
        return $this->belongsTo($this->productModelClass());
    }

    protected function productModelClass(): string
    {
        return get_class(app(PlanogramProductContract::class));
    }

    protected function slugTo()
    {
        return false;
    }
}
