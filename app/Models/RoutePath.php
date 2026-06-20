<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['route_id', 'data'])]
class RoutePath extends Model
{
    protected function casts(): array {
        return [
            "data" => 'array'
        ];
    }

    public function route(): BelongsTo {
        return $this->belongsTo(Route::class, 'route_id');
    }
}
