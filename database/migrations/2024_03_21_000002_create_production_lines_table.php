<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('production_lines', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('capacity', 10, 2);
            $table->enum('status', ['active', 'inactive', 'maintenance'])->default('inactive');
            $table->unsignedBigInteger('current_order_id')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('production_lines');
    }
}; 