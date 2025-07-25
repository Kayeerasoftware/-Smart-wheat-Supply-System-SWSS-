<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shipment extends Model
{
    protected $fillable = [
        'shipment_number',
        'order_id',
        'purchase_order_id',
        'warehouse_id',
        'shipment_type',
        'status',
        'carrier',
        'tracking_number',
        'shipping_method',
        'shipping_cost',
        'insurance_amount',
        'shipping_address',
        'billing_address',
        'ship_date',
        'expected_delivery_date',
        'actual_delivery_date',
        'notes',
        'signature_required',
        'signature_name',
        'signature_time',
    ];

    protected $casts = [
        'ship_date' => 'date',
        'expected_delivery_date' => 'date',
        'actual_delivery_date' => 'date',
        'shipping_cost' => 'decimal:2',
        'insurance_amount' => 'decimal:2',
        'signature_time' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }
}
