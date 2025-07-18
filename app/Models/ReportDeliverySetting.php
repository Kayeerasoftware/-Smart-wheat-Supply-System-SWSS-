<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportDeliverySetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'frequency',
        'method',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 