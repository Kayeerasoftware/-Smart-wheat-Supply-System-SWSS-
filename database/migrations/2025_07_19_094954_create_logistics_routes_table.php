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
        Schema::create('logistics_routes', function (Blueprint $table) {
            $table->id();
            $table->string('route_id')->unique();
            $table->string('origin');
            $table->string('destination');
            $table->string('deliverer');
            $table->string('status');
            $table->string('eta')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logistics_routes');
    }
};
