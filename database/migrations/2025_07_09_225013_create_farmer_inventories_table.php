<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('farmer_inventories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('farmer_id');
            $table->unsignedBigInteger('product_id');
            $table->decimal('quantity', 10, 2); // in kg
            $table->enum('quality_grade', ['A', 'B', 'C']);
            $table->date('harvest_date');
            $table->decimal('moisture_content', 5, 2); // percentage
            $table->decimal('protein_content', 5, 2); // percentage
            $table->decimal('price_per_kg', 10, 2);
            $table->string('location', 255);
            $table->text('notes')->nullable();
            $table->boolean('is_available')->default(true);
            $table->timestamps();

            $table->foreign('farmer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            
            $table->index(['farmer_id', 'is_available']);
            $table->index(['quality_grade', 'is_available']);
            $table->index('harvest_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farmer_inventories');
    }
};
