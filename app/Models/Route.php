<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Route extends Model
{

    protected $fillable = [
        'origin_lat',
        'origin_lng',
        'destination_lat',
        'destination_lng',
        'total_distance_km',
        'total_duration_minutes'
    ];

    public function sections(): HasMany {
        return $this->hasMany(RouteSection::class)->orderBy('section_order', 'asc');
    }

}
