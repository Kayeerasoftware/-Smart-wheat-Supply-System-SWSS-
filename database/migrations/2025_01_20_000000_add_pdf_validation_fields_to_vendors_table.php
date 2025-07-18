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
        Schema::table('vendors', function (Blueprint $table) {
            // First, add image_paths if it doesn't exist
            if (!Schema::hasColumn('vendors', 'image_paths')) {
                $table->json('image_paths')->nullable()->after('pdf_paths');
            }
            
            // Then add pdf_validation_result
            if (!Schema::hasColumn('vendors', 'pdf_validation_result')) {
                $table->json('pdf_validation_result')->nullable()->after('image_paths');
            }
            
            // Add scoring columns if they don't exist
            if (!Schema::hasColumn('vendors', 'score_financial')) {
                $table->integer('score_financial')->default(0)->after('pdf_validation_result');
            }
            
            if (!Schema::hasColumn('vendors', 'score_reputation')) {
                $table->integer('score_reputation')->default(0)->after('score_financial');
            }
            
            if (!Schema::hasColumn('vendors', 'score_compliance')) {
                $table->integer('score_compliance')->default(0)->after('score_reputation');
            }
            
            if (!Schema::hasColumn('vendors', 'total_score')) {
                $table->integer('total_score')->default(0)->after('score_compliance');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn([
                'pdf_validation_result',
                'score_financial',
                'score_reputation', 
                'score_compliance',
                'total_score'
            ]);
        });
    }
}; 