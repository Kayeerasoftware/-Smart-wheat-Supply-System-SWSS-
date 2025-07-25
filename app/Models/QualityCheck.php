<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QualityCheck extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_line_id',
        'order_id',
        'status',
        'notes',
        'checked_by',
        'checked_at'
    ];

    protected $casts = [
        'checked_at' => 'datetime'
    ];

    public function productionLine()
    {
        return $this->belongsTo(ProductionLine::class);
    }

    public function order()
    {
        return $this->belongsTo(ManufacturingOrder::class, 'order_id');
    }

    public function checkedBy()
    {
        return $this->belongsTo(User::class, 'checked_by');
    }
} 