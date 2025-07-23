<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->json('application_data')->nullable();
            $table->string('status')->default('pending'); // pending, pending_visit, approved, rejected
            $table->float('score_financial')->nullable();
            $table->float('score_reputation')->nullable();
            $table->float('score_compliance')->nullable();
            $table->float('total_score')->nullable();
            $table->json('image_paths')->nullable();
            $table->json('pdf_paths')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('vendors');
    }
}; 