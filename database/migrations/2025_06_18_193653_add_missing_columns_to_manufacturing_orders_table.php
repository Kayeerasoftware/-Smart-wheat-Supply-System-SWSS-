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
        Schema::table('manufacturing_orders', function (Blueprint $table) {
            $table->string('unit')->nullable()->after('quantity');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium')->after('status');
            $table->unsignedBigInteger('user_id')->nullable()->after('production_line_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('manufacturing_orders', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['unit', 'priority', 'user_id']);
        });
    }
};
