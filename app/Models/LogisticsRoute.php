<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogisticsRoute extends Model
{
    protected $fillable = [
        'route_id', 'origin', 'destination', 'deliverer', 'status', 'eta'
    ];
}
