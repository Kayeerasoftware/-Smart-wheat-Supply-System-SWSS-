<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('manufacturing_order_raw_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manufacturing_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('raw_material_id')->constrained()->cascadeOnDelete();
            $table->decimal('quantity', 10, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('manufacturing_order_raw_materials');
    }
}; 