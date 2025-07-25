<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('code')->unique(); // Warehouse code
            $table->string('address');
            $table->string('city');
            $table->string('state')->nullable();
            $table->string('country');
            $table->string('postal_code')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->unsignedBigInteger('manager_id')->nullable(); // Warehouse manager
            $table->decimal('capacity', 10, 2)->nullable(); // Total capacity
            $table->string('capacity_unit')->default('sq_meters'); // Capacity unit
            $table->enum('type', ['raw_materials', 'finished_goods', 'distribution', 'cold_storage', 'hazardous'])->default('finished_goods');
            $table->boolean('is_active')->default(true);
            $table->json('facilities')->nullable(); // Available facilities
            $table->timestamps();

            $table->foreign('manager_id')->references('id')->on('users')->onDelete('set null');
            $table->index(['code', 'is_active']);
            $table->index(['type', 'is_active']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('warehouses');
    }
}; 