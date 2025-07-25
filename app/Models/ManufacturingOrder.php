<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManufacturingOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'product_name',
        'quantity',
        'unit',
        'status',
        'priority',
        'production_line_id',
        'user_id',
        'start_date',
        'end_date',
        'notes'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime'
    ];

    public function productionLine()
    {
        return $this->belongsTo(ProductionLine::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function qualityChecks()
    {
        return $this->hasMany(QualityCheck::class, 'order_id');
    }

    public function rawMaterials()
    {
        return $this->belongsToMany(RawMaterial::class, 'manufacturing_order_raw_materials')
            ->withPivot('quantity')
            ->withTimestamps();
    }
} 