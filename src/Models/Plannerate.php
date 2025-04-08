<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Callcocam\Plannerate\Models;

use App\Models\Store;
use Callcocam\Plannerate\Enums\PlannerateStatus; 
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Tall\Sluggable\HasSlug;
use Tall\Sluggable\SlugOptions;

class Plannerate extends Model
{
    use HasFactory, HasUlids, SoftDeletes, HasSlug;

    protected $guarded = ['id'];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'status' => PlannerateStatus::class,
    ];

    public function stores()
    {
        return $this->belongsToMany(Store::class, 'planogram_store');
    }

    public function gondolas()
    {
        return $this->hasMany(Gondola::class);
    }


    /**
     * @return SlugOptions
     */
    public function getSlugOptions()
    {
        if (is_string($this->slugTo())) {
            return SlugOptions::create()
                ->generateSlugsFrom($this->slugFrom())
                ->saveSlugsTo($this->slugTo());
        }
    }
}
