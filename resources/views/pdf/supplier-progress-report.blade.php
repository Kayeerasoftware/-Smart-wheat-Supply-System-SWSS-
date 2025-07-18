<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SWSS - Supplier Progress Report</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            border-bottom: 3px solid #667eea;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .header h1 {
            color: #667eea;
            margin: 0;
            font-size: 28px;
        }
        
        .header p {
            color: #666;
            margin: 5px 0 0 0;
        }
        
        .section {
            margin-bottom: 30px;
        }
        
        .section h2 {
            color: #667eea;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        
        .progress-step {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        
        .step-icon {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-weight: bold;
            color: white;
        }
        
        .step-completed {
            background-color: #10b981;
        }
        
        .step-pending {
            background-color: #f59e0b;
        }
        
        .step-locked {
            background-color: #6b7280;
        }
        
        .step-info {
            flex: 1;
        }
        
        .step-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .step-description {
            color: #666;
            font-size: 14px;
        }
        
        .score-bar {
            width: 100%;
            height: 20px;
            background-color: #e5e7eb;
            border-radius: 10px;
            overflow: hidden;
            margin: 5px 0;
        }
        
        .score-fill {
            height: 100%;
            background: linear-gradient(90deg, #10b981 0%, #059669 100%);
            transition: width 0.3s ease;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .info-card {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
        }
        
        .info-card h3 {
            color: #667eea;
            margin-top: 0;
            margin-bottom: 15px;
        }
        
        .info-item {
            margin-bottom: 10px;
        }
        
        .info-label {
            font-weight: bold;
            color: #555;
        }
        
        .info-value {
            color: #333;
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
            color: white;
        }
        
        .status-pending {
            background-color: #f59e0b;
        }
        
        .status-failed {
            background-color: #ef4444;
        }
        
        .status-approved {
            background-color: #10b981;
        }
        
        .status-rejected {
            background-color: #6b7280;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
        
        .contact-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
        
        .contact-info h3 {
            color: #667eea;
            margin-top: 0;
        }
        
        .contact-item {
            margin-bottom: 8px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>SWSS Supplier Progress Report</h1>
        <p>Application Status and Progress Tracking</p>
        <p>Generated on: {{ now()->format('F d, Y \a\t g:i A') }}</p>
    </div>

    <div class="section">
        <h2>Application Progress</h2>
        
        <!-- Step 1: Registration -->
        <div class="progress-step">
            <div class="step-icon step-completed">✓</div>
            <div class="step-info">
                <div class="step-title">Registration Complete</div>
                <div class="step-description">Your account has been successfully created</div>
            </div>
        </div>

        <!-- Step 2: PDF Validation -->
        <div class="progress-step">
            @if($vendor->processing_status === 'pdf_validation_failed')
                <div class="step-icon" style="background-color: #ef4444;">✗</div>
                <div class="step-info">
                    <div class="step-title">PDF Validation Failed</div>
                    <div class="step-description">Your documents require correction</div>
                    @if($vendor->total_score > 0)
                        <div style="margin-top: 10px;">
                            <strong>Validation Score: {{ $vendor->total_score ?? 0 }}/100</strong>
                            <div class="score-bar">
                                <div class="score-fill" style="width: {{ ($vendor->total_score ?? 0) }}%"></div>
                            </div>
                        </div>
                    @endif
                </div>
            @else
                <div class="step-icon step-completed">✓</div>
                <div class="step-info">
                    <div class="step-title">PDF Validation Passed</div>
                    <div class="step-description">Your documents have been successfully validated</div>
                    <div style="margin-top: 10px;">
                        <strong>Validation Score: {{ $vendor->total_score ?? 0 }}/100</strong>
                        <div class="score-bar">
                            <div class="score-fill" style="width: {{ ($vendor->total_score ?? 0) }}%"></div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Step 3: Facility Visit -->
        <div class="progress-step">
            @if(in_array($vendor->processing_status, ['pending_visit', 'visit_completed']))
                <div class="step-icon step-pending">⏳</div>
                <div class="step-info">
                    <div class="step-title">Facility Visit {{ $vendor->processing_status === 'visit_completed' ? 'Completed' : 'Pending' }}</div>
                    <div class="step-description">
                        @if($vendor->processing_status === 'visit_completed')
                            Our team has completed the facility visit
                        @else
                            Our team will schedule a visit to your facility
                        @endif
                    </div>
                </div>
            @elseif($vendor->processing_status === 'approved')
                <div class="step-icon step-completed">✓</div>
                <div class="step-info">
                    <div class="step-title">Facility Visit Completed</div>
                    <div class="step-description">Our team has completed the facility visit</div>
                </div>
            @else
                <div class="step-icon step-locked">🔒</div>
                <div class="step-info">
                    <div class="step-title">Facility Visit</div>
                    <div class="step-description">Pending PDF validation approval</div>
                </div>
            @endif
        </div>

        <!-- Step 4: Final Approval -->
        <div class="progress-step">
            @if($vendor->processing_status === 'approved')
                <div class="step-icon step-completed">✓</div>
                <div class="step-info">
                    <div class="step-title">Final Approval</div>
                    <div class="step-description">Full access to supplier dashboard granted</div>
                </div>
            @else
                <div class="step-icon step-locked">🔒</div>
                <div class="step-info">
                    <div class="step-title">Final Approval</div>
                    <div class="step-description">Access to full supplier dashboard</div>
                </div>
            @endif
        </div>
    </div>

    <div class="section">
        <h2>Application Details</h2>
        
        <div class="info-grid">
            <div class="info-card">
                <h3>Business Information</h3>
                <div class="info-item">
                    <span class="info-label">Business Name:</span>
                    <span class="info-value">{{ $vendor->application_data['business_name'] ?? 'Not provided' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Business Type:</span>
                    <span class="info-value">{{ ucfirst($vendor->application_data['business_type'] ?? 'Not specified') }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Registration Date:</span>
                    <span class="info-value">{{ $vendor->created_at->format('M d, Y') }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Current Status:</span>
                    <span class="status-badge 
                        @switch($vendor->processing_status)
                            @case('pdf_validation_failed')
                                status-failed
                                @break
                            @case('approved')
                                status-approved
                                @break
                            @case('rejected')
                                status-rejected
                                @break
                            @default
                                status-pending
                        @endswitch">
                        @switch($vendor->processing_status)
                            @case('pdf_validation_failed')
                                PDF Validation Failed
                                @break
                            @case('pending_review')
                                Pending Review
                                @break
                            @case('pending_visit')
                                Pending Facility Visit
                                @break
                            @case('visit_completed')
                                Facility Visit Completed
                                @break
                            @case('approved')
                                Approved
                                @break
                            @case('rejected')
                                Rejected
                                @break
                            @default
                                {{ ucfirst(str_replace('_', ' ', $vendor->processing_status)) }}
                        @endswitch
                    </span>
                </div>
            </div>
            
            <div class="info-card">
                <h3>Validation Scores</h3>
                <div class="info-item">
                    <span class="info-label">Financial Stability:</span>
                    <span class="info-value">{{ $vendor->score_financial ?? 0 }}/100</span>
                    <div class="score-bar">
                        <div class="score-fill" style="width: {{ ($vendor->score_financial ?? 0) }}%"></div>
                    </div>
                </div>
                <div class="info-item">
                    <span class="info-label">Business Reputation:</span>
                    <span class="info-value">{{ $vendor->score_reputation ?? 0 }}/100</span>
                    <div class="score-bar">
                        <div class="score-fill" style="width: {{ ($vendor->score_reputation ?? 0) }}%"></div>
                    </div>
                </div>
                <div class="info-item">
                    <span class="info-label">Regulatory Compliance:</span>
                    <span class="info-value">{{ $vendor->score_compliance ?? 0 }}/100</span>
                    <div class="score-bar">
                        <div class="score-fill" style="width: {{ ($vendor->score_compliance ?? 0) }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="section">
        <h2>What Happens Next?</h2>
        
        <div class="info-grid">
            <div class="info-card">
                <h3>Facility Visit Process</h3>
                <ul>
                    <li>We'll contact you to schedule a facility visit</li>
                    <li>Our team will assess your operations and capacity</li>
                    <li>We'll verify your business documentation</li>
                    <li>Visit typically takes 1-2 hours</li>
                </ul>
            </div>
            
            <div class="info-card">
                <h3>After Approval</h3>
                <ul>
                    <li>Full access to supplier dashboard</li>
                    <li>Create and manage orders</li>
                    <li>View analytics and reports</li>
                    <li>Connect with farmers and buyers</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="contact-info">
        <h3>Need Help?</h3>
        <div class="contact-item">
            <strong>Phone:</strong> +256 123 456 789 (Mon-Fri, 8AM-5PM)
        </div>
        <div class="contact-item">
            <strong>Email:</strong> support@swss.com (24/7 support)
        </div>
        <div class="contact-item">
            <strong>Live Chat:</strong> Available on our website
        </div>
    </div>

    <div class="footer">
        <p>This report was generated automatically by the SWSS system.</p>
        <p>For questions about your application status, please contact our support team.</p>
    </div>
</body>
</html> 