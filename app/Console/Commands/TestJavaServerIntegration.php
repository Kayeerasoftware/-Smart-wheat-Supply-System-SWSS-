<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\JavaServerService;
use Illuminate\Support\Facades\Log;

class TestJavaServerIntegration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:java-server {--vendor-id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Java server integration with PDF validation';

    protected $javaServerService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(JavaServerService $javaServerService)
    {
        parent::__construct();
        $this->javaServerService = $javaServerService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Testing Java Server Integration...');
        $this->newLine();
        
        // Test 1: Health Check
        $this->info('1. Testing Java Server Health...');
        if ($this->javaServerService->checkHealth()) {
            $this->info('✅ Java server is healthy and responding');
            } else {
            $this->error('❌ Java server health check failed');
            return 1;
        }
        $this->newLine();
        
        // Test 2: Get Required Sections
        $this->info('2. Testing Required Sections Endpoint...');
        try {
            $sections = $this->javaServerService->getRequiredSections();
            if (!empty($sections)) {
                $this->info('✅ Required sections retrieved successfully');
                $this->table(['Section'], collect($sections)->map(fn($section) => [$section]));
            } else {
                $this->warn('⚠️  No required sections returned');
            }
        } catch (\Exception $e) {
            $this->error('❌ Failed to get required sections: ' . $e->getMessage());
        }
        $this->newLine();
        
        // Test 3: Test with sample PDF if vendor ID provided
        $vendorId = $this->option('vendor-id');
        if ($vendorId) {
            $this->info("3. Testing PDF Validation with Vendor ID: {$vendorId}");
            try {
                $vendor = \App\Models\Vendor::find($vendorId);
            if (!$vendor) {
                    $this->error("❌ Vendor with ID {$vendorId} not found");
                return 1;
            }
            
                if (empty($vendor->pdf_paths)) {
                    $this->warn('⚠️  No PDF documents found for this vendor');
                    return 0;
            }
            
                $scores = $this->javaServerService->processVendorDocuments($vendor);
                
                $this->info('✅ PDF validation completed successfully');
                $this->table(
                    ['Metric', 'Score'],
                    [
                        ['Financial Score', $scores['financial_score'] ?? 'N/A'],
                        ['Reputation Score', $scores['reputation_score'] ?? 'N/A'],
                        ['Compliance Score', $scores['compliance_score'] ?? 'N/A'],
                        ['Total Score', $scores['total_score'] ?? 'N/A'],
                        ['Overall Score', $scores['overall_score'] ?? 'N/A'],
                    ]
                );

                // Update vendor with scores
                $vendor->update([
                    'score_financial' => $scores['financial_score'] ?? null,
                    'score_reputation' => $scores['reputation_score'] ?? null,
                    'score_compliance' => $scores['compliance_score'] ?? null,
                    'total_score' => $scores['total_score'] ?? null,
                    'processing_status' => 'completed'
                ]);

                $this->info('✅ Vendor scores updated in database');
                
            } catch (\Exception $e) {
                $this->error('❌ PDF validation failed: ' . $e->getMessage());
                Log::error('Java server test failed', [
                    'vendor_id' => $vendorId,
                    'error' => $e->getMessage()
                ]);
                return 1;
            }
        } else {
            $this->info('3. Skipping PDF validation test (use --vendor-id to test with actual vendor)');
        }

        $this->newLine();
        $this->info('🎉 Java Server Integration Test Completed!');
        
        return 0;
    }
}
