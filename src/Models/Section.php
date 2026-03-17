<?php

/**
 * Created by Claudio Campos.
 * User: callcocam, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\LaravelRaptorPlanogram\Models;

use Callcocam\LaravelRaptor\Models\AbstractModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Section extends AbstractModel
{
    use SoftDeletes;

    protected $table = 'sections';

    protected $appends = ['section_width', 'section_height'];

    protected $hidden = ['gondola'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        static::$landlord->enable();
    }

    protected function casts(): array
    {
        return [
            'width' => 'float',
            'settings' => 'array',
            'height' => 'float',
            'base_width' => 'float',
            'base_height' => 'float',
            'base_depth' => 'float',
            'cremalheira_width' => 'float',
            'hole_height' => 'float',
            'hole_spacing' => 'float',
            'hole_width' => 'float',
        ];
    }

    public function shelves()
    {
        return $this->hasMany(Shelf::class)->orderBy('ordering');
    }

    public function gondola()
    {
        return $this->belongsTo(Gondola::class);
    }

    public function getSectionWidthAttribute()
    {
        $scaleFactor = 3;

        if ($this->relationLoaded('gondola') && $this->gondola) {
            $scaleFactor = (float) $this->gondola->scale_factor;
        }

        $totalWidth = ($this->width + 2) + $this->cremalheira_width * $scaleFactor;

        return $totalWidth;
    }

    public function getSectionHeightAttribute()
    {
        $scaleFactor = 3;

        if ($this->relationLoaded('gondola') && $this->gondola) {
            $scaleFactor = (float) $this->gondola->scale_factor;
        }

        return $this->height * $scaleFactor;
    }

    protected function slugTo()
    {
        return false;
    }
}
