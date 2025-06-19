<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('shipment_number')->unique();
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('set null');
            $table->foreignId('purchase_order_id')->nullable()->constrained('purchase_orders')->onDelete('set null');
            $table->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade');
            $table->enum('shipment_type', ['outbound', 'inbound', 'transfer'])->default('outbound');
            $table->enum('status', [
                'pending', 'packed', 'shipped', 'in_transit', 
                'delivered', 'cancelled', 'returned'
            ])->default('pending');
            $table->string('carrier')->nullable();
            $table->string('tracking_number')->nullable();
            $table->string('shipping_method')->nullable();
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->decimal('insurance_amount', 10, 2)->default(0);
            $table->text('shipping_address');
            $table->text('billing_address')->nullable();
            $table->date('ship_date')->nullable();
            $table->date('expected_delivery_date')->nullable();
            $table->date('actual_delivery_date')->nullable();
            $table->text('notes')->nullable();
            $table->string('signature_required')->default('no');
            $table->string('signature_name')->nullable();
            $table->timestamp('signature_time')->nullable();
            $table->timestamps();
            
            $table->index(['shipment_number', 'status', 'ship_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
