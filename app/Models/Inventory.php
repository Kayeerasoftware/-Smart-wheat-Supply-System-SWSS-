<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'warehouse_id',
        'quantity_on_hand',
        'quantity_reserved',
        'quantity_available',
        'quantity_on_order',
        'average_cost',
        'location',
        'batch_number',
        'expiry_date',
        'status',
        'attributes',
    ];

    protected $casts = [
        'attributes' => 'array',
        'average_cost' => 'decimal:2',
        'expiry_date' => 'date',
    ];

    /**
     * Get the product that owns the inventory
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the warehouse that owns the inventory
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Update available quantity based on on-hand and reserved
     */
    public function updateAvailableQuantity(): void
    {
        $this->quantity_available = $this->quantity_on_hand - $this->quantity_reserved;
        $this->save();
    }

    /**
     * Reserve quantity for an order
     */
    public function reserveQuantity(int $quantity): bool
    {
        if ($this->quantity_available >= $quantity) {
            $this->quantity_reserved += $quantity;
            $this->quantity_available -= $quantity;
            $this->save();
            return true;
        }
        return false;
    }

    /**
     * Release reserved quantity
     */
    public function releaseQuantity(int $quantity): void
    {
        $this->quantity_reserved = max(0, $this->quantity_reserved - $quantity);
        $this->quantity_available = $this->quantity_on_hand - $this->quantity_reserved;
        $this->save();
    }

    /**
     * Add stock to inventory
     */
    public function addStock(int $quantity, float $cost = null): void
    {
        $oldQuantity = $this->quantity_on_hand;
        $oldCost = $this->average_cost ?? 0;
        
        $this->quantity_on_hand += $quantity;
        $this->quantity_available += $quantity;
        
        // Update average cost if new cost is provided
        if ($cost !== null) {
            $totalValue = ($oldQuantity * $oldCost) + ($quantity * $cost);
            $this->average_cost = $totalValue / $this->quantity_on_hand;
        }
        
        $this->save();
    }

    /**
     * Remove stock from inventory
     */
    public function removeStock(int $quantity): bool
    {
        if ($this->quantity_on_hand >= $quantity) {
            $this->quantity_on_hand -= $quantity;
            $this->quantity_available = max(0, $this->quantity_available - $quantity);
            $this->save();
            return true;
        }
        return false;
    }

    /**
     * Check if inventory is low stock
     */
    public function isLowStock(): bool
    {
        return $this->quantity_available <= $this->product->reorder_point;
    }

    /**
     * Check if inventory is expired
     */
    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    /**
     * Check if inventory will expire soon (within 30 days)
     */
    public function expiresSoon(int $days = 30): bool
    {
        return $this->expiry_date && $this->expiry_date->diffInDays(now()) <= $days;
    }

    /**
     * Get inventory value
     */
    public function getValueAttribute(): float
    {
        return $this->quantity_on_hand * ($this->average_cost ?? $this->product->cost_price);
    }

    /**
     * Get days until expiry
     */
    public function getDaysUntilExpiryAttribute(): ?int
    {
        if (!$this->expiry_date) {
            return null;
        }
        return $this->expiry_date->diffInDays(now(), false);
    }

    /**
     * Scope for active inventory
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for low stock inventory
     */
    public function scopeLowStock($query)
    {
        return $query->whereHas('product', function ($q) {
            $q->whereRaw('inventories.quantity_available <= products.reorder_point');
        });
    }

    /**
     * Scope for expired inventory
     */
    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', now());
    }

    /**
     * Scope for expiring soon inventory
     */
    public function scopeExpiringSoon($query, int $days = 30)
    {
        return $query->where('expiry_date', '<=', now()->addDays($days))
                    ->where('expiry_date', '>', now());
    }

    /**
     * Boot method to automatically update available quantity
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($inventory) {
            $inventory->quantity_available = $inventory->quantity_on_hand - $inventory->quantity_reserved;
        });
    }
}
