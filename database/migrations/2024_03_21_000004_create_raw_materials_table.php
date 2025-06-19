<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('raw_materials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('quantity', 10, 2);
            $table->string('unit', 50);
            $table->unsignedBigInteger('supplier_id');
            $table->decimal('minimum_quantity', 10, 2)->default(0);
            $table->decimal('reorder_point', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('raw_materials');
    }
}; 