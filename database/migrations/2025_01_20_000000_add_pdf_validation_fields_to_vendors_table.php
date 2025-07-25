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
            // Only add columns that don't already exist
            if (!Schema::hasColumn('vendors', 'processing_status')) {
                $table->string('processing_status')->default('pending_review')->after('status');
            }
            
            // image_paths already exists from previous migration
            // if (!Schema::hasColumn('vendors', 'image_paths')) {
            //     $table->json('image_paths')->nullable()->after('pdf_paths');
            // }
            
            if (!Schema::hasColumn('vendors', 'pdf_validation_result')) {
                $table->json('pdf_validation_result')->nullable()->after('image_paths');
            }
            
            if (!Schema::hasColumn('vendors', 'facility_visit_scheduled')) {
                $table->boolean('facility_visit_scheduled')->default(false)->after('pdf_validation_result');
            }
            
            if (!Schema::hasColumn('vendors', 'facility_visit_date')) {
                $table->timestamp('facility_visit_date')->nullable()->after('facility_visit_scheduled');
            }
            
            if (!Schema::hasColumn('vendors', 'facility_visit_notes')) {
                $table->text('facility_visit_notes')->nullable()->after('facility_visit_date');
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
                'processing_status',
                'pdf_validation_result',
                'facility_visit_scheduled',
                'facility_visit_date',
                'facility_visit_notes'
            ]);
        });
    }
}; 