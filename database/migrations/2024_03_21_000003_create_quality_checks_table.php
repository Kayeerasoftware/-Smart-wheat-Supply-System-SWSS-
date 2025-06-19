<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('quality_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_line_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->constrained('manufacturing_orders')->cascadeOnDelete();
            $table->enum('status', ['passed', 'failed'])->default('failed');
            $table->text('notes')->nullable();
            $table->foreignId('checked_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('checked_at');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('quality_checks');
    }
}; 