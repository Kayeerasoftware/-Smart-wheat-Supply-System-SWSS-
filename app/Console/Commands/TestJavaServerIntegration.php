<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\JavaServerService;
use App\Models\Vendor;

class TestJavaServerIntegration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:java-server {--vendor-id= : Test with specific vendor ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Java server integration for PDF processing and scoring';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $javaServerService = new JavaServerService();
        
        $this->info('Testing Java Server Integration...');
        $this->newLine();
        
        // Test 1: Health Check
        $this->info('1. Testing Java Server Health...');
        if ($javaServerService->checkHealth()) {
            $this->info('✅ Java server is healthy and responding');
        } else {
            $this->warn('⚠️ Java server is not responding - using fallback mode');
        }
        $this->newLine();
        
        // Test 2: Configuration
        $this->info('2. Checking Configuration...');
        $this->table(
            ['Setting', 'Value'],
            [
                ['Server URL', config('vendor.java_server.url')],
                ['Timeout', config('vendor.java_server.timeout') . 's'],
                ['API Key', config('vendor.java_server.api_key') ? 'Set' : 'Not Set'],
                ['Approval Threshold', config('vendor.approval_threshold') . '%'],
            ]
        );
        $this->newLine();
        
        // Test 3: Scoring Weights
        $this->info('3. Scoring Weights...');
        $weights = $javaServerService->getScoringWeights();
        $this->table(
            ['Category', 'Weight'],
            [
                ['Financial', ($weights['financial'] * 100) . '%'],
                ['Reputation', ($weights['reputation'] * 100) . '%'],
                ['Compliance', ($weights['compliance'] * 100) . '%'],
            ]
        );
        $this->newLine();
        
        // Test 4: Sample Score Calculation
        $this->info('4. Sample Score Calculation...');
        $sampleScores = [
            'financial' => 85,
            'reputation' => 90,
            'compliance' => 88
        ];
        
        $totalScore = $javaServerService->calculateTotalScore(
            $sampleScores['financial'],
            $sampleScores['reputation'],
            $sampleScores['compliance']
        );
        
        $this->table(
            ['Category', 'Score', 'Weighted Score'],
            [
                ['Financial', $sampleScores['financial'], round($sampleScores['financial'] * $weights['financial'], 1)],
                ['Reputation', $sampleScores['reputation'], round($sampleScores['reputation'] * $weights['reputation'], 1)],
                ['Compliance', $sampleScores['compliance'], round($sampleScores['compliance'] * $weights['compliance'], 1)],
                ['Total', '', round($totalScore, 1)],
            ]
        );
        $this->newLine();
        
        // Test 5: Process specific vendor if provided
        if ($vendorId = $this->option('vendor-id')) {
            $this->info('5. Testing with Vendor ID: ' . $vendorId);
            
            $vendor = Vendor::find($vendorId);
            if (!$vendor) {
                $this->error('Vendor not found with ID: ' . $vendorId);
                return 1;
            }
            
            $this->info('Processing vendor: ' . ($vendor->application_data['business_name'] ?? 'N/A'));
            
            try {
                $scores = $javaServerService->processVendorDocuments($vendor);
                
                $this->table(
                    ['Metric', 'Value'],
                    [
                        ['Financial Score', $scores['financial_score'] ?? 'N/A'],
                        ['Reputation Score', $scores['reputation_score'] ?? 'N/A'],
                        ['Compliance Score', $scores['compliance_score'] ?? 'N/A'],
                        ['Total Score', $scores['total_score'] ?? 'N/A'],
                        ['Status', $scores['processing_status'] ?? 'N/A'],
                    ]
                );
                
                // Check threshold
                $threshold = config('vendor.approval_threshold');
                $meetsThreshold = ($scores['total_score'] ?? 0) >= $threshold;
                
                $this->info('Threshold Check: ' . ($meetsThreshold ? '✅ Meets threshold' : '❌ Below threshold'));
                $this->info('Required: ' . $threshold . '%, Actual: ' . ($scores['total_score'] ?? 0) . '%');
                
            } catch (\Exception $e) {
                $this->error('Error processing vendor: ' . $e->getMessage());
                return 1;
            }
        } else {
            $this->info('5. Skipping vendor processing (use --vendor-id to test with specific vendor)');
        }
        
        $this->newLine();
        $this->info('✅ Java Server Integration Test Complete!');
        
        return 0;
    }
}
