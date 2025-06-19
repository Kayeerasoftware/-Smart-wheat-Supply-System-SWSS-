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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique(); // Stock Keeping Unit
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('category_id');
            $table->string('brand')->nullable();
            $table->string('unit_of_measure'); // kg, pieces, liters, etc.
            $table->decimal('unit_price', 10, 2);
            $table->decimal('cost_price', 10, 2); // Cost to produce/purchase
            $table->integer('reorder_point')->default(0); // Minimum stock level
            $table->integer('reorder_quantity')->default(0); // Quantity to reorder
            $table->string('supplier_id')->nullable(); // Reference to supplier
            $table->string('manufacturer_id')->nullable(); // Reference to manufacturer
            $table->json('specifications')->nullable(); // Product specifications
            $table->json('images')->nullable(); // Product images
            $table->enum('status', ['active', 'inactive', 'discontinued'])->default('active');
            $table->boolean('is_raw_material')->default(false); // Is this a raw material?
            $table->boolean('is_finished_good')->default(true); // Is this a finished product?
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->index(['sku', 'status']);
            $table->index(['category_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
