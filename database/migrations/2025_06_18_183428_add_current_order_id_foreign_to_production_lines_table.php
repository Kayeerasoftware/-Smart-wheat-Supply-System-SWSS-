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
        Schema::table('production_lines', function (Blueprint $table) {
            $table->foreign('current_order_id')
                ->references('id')
                ->on('manufacturing_orders')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('production_lines', function (Blueprint $table) {
            $table->dropForeign(['current_order_id']);
        });
    }
};
