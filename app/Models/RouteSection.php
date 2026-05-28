<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RouteSection extends Model
{

    protected $fillable = [
        'route_id',
        'section_order',
        'section_origin_lat',
        'section_origin_lng',
        'section_destination_lat',
        'section_destination_lng',
        'instructions',
        'distance_km',
        'duration_minutes',
    ];

    public function route(): BelongsTo {
        return $this->belongsTo(Route::class);
    }

}
