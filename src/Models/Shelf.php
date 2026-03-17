<?php

/**
 * Created by Claudio Campos.
 * User: callcocam, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptorPlanogram\Models;

use Callcocam\LaravelRaptor\Models\AbstractModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shelf extends AbstractModel
{
    use SoftDeletes;

    protected $table = 'shelves';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        static::$landlord->enable();
    }

    protected function casts(): array
    {
        return [
            'settings' => 'array',
            'shelf_width' => 'integer',
            'shelf_height' => 'integer',
            'shelf_depth' => 'integer',
            'shelf_position' => 'integer',
        ];
    }

    public function segments()
    {
        return $this->hasMany(Segment::class)->orderBy('ordering', 'asc');
    }

    protected function slugTo()
    {
        return false;
    }
}
