<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'name',
        'description',
        'category_id',
        'brand',
        'unit_of_measure',
        'unit_price',
        'cost_price',
        'reorder_point',
        'reorder_quantity',
        'supplier_id',
        'manufacturer_id',
        'specifications',
        'images',
        'status',
        'is_raw_material',
        'is_finished_good',
    ];

    protected $casts = [
        'specifications' => 'array',
        'images' => 'array',
        'is_raw_material' => 'boolean',
        'is_finished_good' => 'boolean',
        'unit_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
    ];

    /**
     * Get the category that owns the product
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the supplier that owns the product
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supplier_id', 'id');
    }

    /**
     * Get the inventory records for the product
     */
    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    /**
     * Get total quantity across all warehouses
     */
    public function getTotalQuantityAttribute(): int
    {
        return $this->inventories()->sum('quantity_on_hand');
    }

    /**
     * Get total available quantity across all warehouses
     */
    public function getTotalAvailableAttribute(): int
    {
        return $this->inventories()->sum('quantity_available');
    }

    /**
     * Get total reserved quantity across all warehouses
     */
    public function getTotalReservedAttribute(): int
    {
        return $this->inventories()->sum('quantity_reserved');
    }

    /**
     * Check if product needs reordering
     */
    public function needsReorder(): bool
    {
        return $this->total_available <= $this->reorder_point;
    }

    /**
     * Get low stock alerts
     */
    public function getLowStockAlerts(): array
    {
        $alerts = [];
        foreach ($this->inventories as $inventory) {
            if ($inventory->quantity_available <= $this->reorder_point) {
                $alerts[] = [
                    'warehouse' => $inventory->warehouse->name,
                    'current_stock' => $inventory->quantity_available,
                    'reorder_point' => $this->reorder_point,
                    'suggested_order' => $this->reorder_quantity,
                ];
            }
        }
        return $alerts;
    }

    /**
     * Scope for active products
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for raw materials
     */
    public function scopeRawMaterials($query)
    {
        return $query->where('is_raw_material', true);
    }

    /**
     * Scope for finished goods
     */
    public function scopeFinishedGoods($query)
    {
        return $query->where('is_finished_good', true);
    }

    /**
     * Generate SKU automatically
     */
    public static function generateSku(string $categoryCode, string $brand = null): string
    {
        $prefix = strtoupper($categoryCode);
        $brandCode = $brand ? strtoupper(substr($brand, 0, 3)) : 'GEN';
        $timestamp = now()->format('ymd');
        $random = strtoupper(substr(md5(uniqid()), 0, 4));
        
        return "{$prefix}-{$brandCode}-{$timestamp}-{$random}";
    }
}
