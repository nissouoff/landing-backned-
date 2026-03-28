<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingView extends Model
{
    protected $fillable = [
        'landing_id',
        'ip_address',
    ];

    protected $table = 'landing_views';

    public function landing()
    {
        return $this->belongsTo(Landing::class);
    }
}
