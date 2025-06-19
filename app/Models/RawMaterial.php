<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RawMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'quantity',
        'unit',
        'supplier_id',
        'minimum_quantity',
        'reorder_point'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function manufacturingOrders()
    {
        return $this->belongsToMany(ManufacturingOrder::class, 'manufacturing_order_raw_materials')
            ->withPivot('quantity')
            ->withTimestamps();
    }
} 