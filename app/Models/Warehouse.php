<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'code',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'phone',
        'email',
        'manager_id',
        'capacity',
        'capacity_unit',
        'type',
        'is_active',
        'facilities',
    ];

    protected $casts = [
        'facilities' => 'array',
        'is_active' => 'boolean',
        'capacity' => 'decimal:2',
    ];

    /**
     * Get the warehouse manager
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id', 'user_id');
    }

    /**
     * Get the inventory records for this warehouse
     */
    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    /**
     * Get total inventory value in this warehouse
     */
    public function getTotalInventoryValueAttribute(): float
    {
        return $this->inventories()
            ->join('products', 'inventories.product_id', '=', 'products.id')
            ->selectRaw('SUM(inventories.quantity_on_hand * products.cost_price) as total_value')
            ->value('total_value') ?? 0;
    }

    /**
     * Get total capacity utilization percentage
     */
    public function getCapacityUtilizationAttribute(): float
    {
        if (!$this->capacity) {
            return 0;
        }

        $totalVolume = $this->inventories()
            ->join('products', 'inventories.product_id', '=', 'products.id')
            ->selectRaw('SUM(inventories.quantity_on_hand) as total_volume')
            ->value('total_volume') ?? 0;

        return ($totalVolume / $this->capacity) * 100;
    }

    /**
     * Get low stock products in this warehouse
     */
    public function getLowStockProducts()
    {
        return $this->inventories()
            ->with('product')
            ->whereHas('product', function ($query) {
                $query->whereRaw('inventories.quantity_available <= products.reorder_point');
            })
            ->get();
    }

    /**
     * Get expired products in this warehouse
     */
    public function getExpiredProducts()
    {
        return $this->inventories()
            ->with('product')
            ->where('expiry_date', '<', now())
            ->where('status', '!=', 'expired')
            ->get();
    }

    /**
     * Get products by status
     */
    public function getProductsByStatus(string $status)
    {
        return $this->inventories()
            ->with('product')
            ->where('status', $status)
            ->get();
    }

    /**
     * Check if warehouse has available capacity
     */
    public function hasAvailableCapacity(int $additionalQuantity = 0): bool
    {
        if (!$this->capacity) {
            return true; // No capacity limit set
        }

        $currentUtilization = $this->capacity_utilization;
        $additionalUtilization = ($additionalQuantity / $this->capacity) * 100;
        
        return ($currentUtilization + $additionalUtilization) <= 100;
    }

    /**
     * Get warehouse address as string
     */
    public function getFullAddressAttribute(): string
    {
        $parts = [
            $this->address,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country
        ];

        return implode(', ', array_filter($parts));
    }

    /**
     * Scope for active warehouses
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for warehouses by type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Generate warehouse code
     */
    public static function generateCode(string $name, string $city): string
    {
        $nameCode = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $name), 0, 3));
        $cityCode = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $city), 0, 3));
        $timestamp = now()->format('ym');
        
        return "WH-{$nameCode}-{$cityCode}-{$timestamp}";
    }
}
