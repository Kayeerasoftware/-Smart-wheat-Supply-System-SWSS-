<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'capacity',
        'status',
        'current_order_id'
    ];

    public function currentOrder()
    {
        return $this->belongsTo(ManufacturingOrder::class, 'current_order_id');
    }

    public function qualityChecks()
    {
        return $this->hasMany(QualityCheck::class);
    }

    public function manufacturingOrders()
    {
        return $this->hasMany(ManufacturingOrder::class);
    }
} 