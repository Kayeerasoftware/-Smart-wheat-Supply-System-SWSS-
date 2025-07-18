<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FarmerInventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'farmer_id',
        'product_id',
        'quantity',
        'quality_grade',
        'harvest_date',
        'moisture_content',
        'protein_content',
        'price_per_kg',
        'location',
        'notes',
        'is_available'
    ];

    protected $casts = [
        'harvest_date' => 'date',
        'is_available' => 'boolean',
        'price_per_kg' => 'decimal:2',
        'moisture_content' => 'decimal:2',
        'protein_content' => 'decimal:2'
    ];

    public function farmer()
    {
        return $this->belongsTo(User::class, 'farmer_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeByQuality($query, $grade)
    {
        return $query->where('quality_grade', $grade);
    }
} 