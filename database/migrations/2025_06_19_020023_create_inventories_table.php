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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('warehouse_id');
            $table->integer('quantity_on_hand')->default(0);
            $table->integer('quantity_reserved')->default(0); // Reserved for orders
            $table->integer('quantity_available')->default(0); // Available for sale
            $table->integer('quantity_on_order')->default(0); // On order from suppliers
            $table->decimal('average_cost', 10, 2)->nullable(); // Average cost of inventory
            $table->string('location')->nullable(); // Specific location within warehouse
            $table->string('batch_number')->nullable(); // For batch tracking
            $table->date('expiry_date')->nullable(); // For perishable goods
            $table->enum('status', ['active', 'quarantine', 'damaged', 'expired'])->default('active');
            $table->json('attributes')->nullable(); // Additional inventory attributes
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
            $table->unique(['product_id', 'warehouse_id', 'batch_number']);
            $table->index(['product_id', 'status']);
            $table->index(['warehouse_id', 'status']);
            $table->index(['expiry_date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
