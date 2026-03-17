<?php

/**
 * Created by Claudio Campos.
 * User: callcocam, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptorPlanogram\Models;

use Callcocam\LaravelRaptor\Models\AbstractModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Segment extends AbstractModel
{
    use SoftDeletes;

    protected $table = 'segments';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        static::$landlord->enable();
    }

    public function layer()
    {
        return $this->hasOne(Layer::class);
    }

    protected function slugTo()
    {
        return false;
    }
}
