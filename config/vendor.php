<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Vendor Approval Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for vendor approval process including score thresholds
    | and Java server integration settings.
    |
    */

    'approval_threshold' => env('VENDOR_APPROVAL_THRESHOLD', 70),

    'java_server' => [
        'url' => env('JAVA_SERVER_URL', 'http://localhost:8080'),
        'timeout' => env('JAVA_SERVER_TIMEOUT', 60),
        'api_key' => env('JAVA_SERVER_API_KEY'),
        'endpoints' => [
            'validate_vendor' => '/api/validation/validate-vendor',
            'process_documents' => '/api/process-vendor-documents',
            'health' => '/health'
        ],
        'shared_storage' => [
            'path' => storage_path('app/public/vendor_docs'),
            'allowed_extensions' => ['pdf'],
            'max_file_size' => 8192 // in KB
        ]
    ],

    'scoring_weights' => [
        'financial' => 0.4,    // 40%
        'reputation' => 0.3,   // 30%
        'compliance' => 0.3,   // 30%
    ],

    'required_documents' => [
        'tax_id' => 'Business Registration Certificate',
        'financial_records' => 'Financial Statements (Last 2 Years)',
        'certifications' => 'Quality Certifications (ISO, FDA, etc.)',
        'insurance' => 'Insurance Certificates',
    ],

    'status_messages' => [
        'pending' => 'Your application is under review.',
        'pending_visit' => 'A facility visit has been scheduled.',
        'approved' => 'Your application has been approved.',
        'rejected' => 'Your application has been rejected.',
    ]
]; 