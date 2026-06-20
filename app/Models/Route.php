<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Route extends Model
{

    protected $fillable = [
        'origin_lat',
        'origin_lng',
        'destination_lat',
        'destination_lng',
        'total_distance_km',
        'total_duration_minutes',
        'transport_mode',
    ];

    public function sections(): HasMany {
        return $this->hasMany(RouteSection::class)->orderBy('section_order', 'asc');
    }

    public function path(): HasOne {
        return $this->hasOne(RoutePath::class, 'route_id');
    }

}
