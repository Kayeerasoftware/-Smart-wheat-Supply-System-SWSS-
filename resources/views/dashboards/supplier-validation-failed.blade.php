<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SWSS - PDF Validation Failed</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --error-gradient: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
            --warning-gradient: linear-gradient(135deg, #feca57 0%, #ff9ff3 100%);
            --card-gradient: linear-gradient(145deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 50%, #16213e 100%);
            min-height: 100vh;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        .glass-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.12);
            border-color: rgba(255, 255, 255, 0.2);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .btn-primary {
            background: var(--primary-gradient);
            border: none;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        }

        .btn-secondary {
            background: var(--warning-gradient);
            border: none;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        }

        .error-icon {
            background: var(--error-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>
<body class="min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-red-500/20 rounded-full mb-4">
                <i class="fas fa-exclamation-triangle text-4xl error-icon"></i>
            </div>
            <h1 class="text-4xl font-bold text-white mb-2">PDF Validation Failed</h1>
            <p class="text-gray-300 text-lg">Your supplier application requires attention</p>
        </div>

        <!-- Main Content -->
        <div class="max-w-4xl mx-auto">
            <!-- Status Card -->
            <div class="glass-card p-8 mb-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-white">Application Status</h2>
                    <span class="px-4 py-2 bg-red-500 text-white rounded-full text-sm font-semibold">
                        PDF Validation Failed
                    </span>
                </div>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-semibold text-white mb-3">Business Information</h3>
                        <div class="space-y-2 text-gray-300">
                            <p><strong>Business Name:</strong> {{ $vendor->application_data['business_name'] ?? 'Not provided' }}</p>
                            <p><strong>Business Type:</strong> {{ ucfirst($vendor->application_data['business_type'] ?? 'Not specified') }}</p>
                            <p><strong>Registration Date:</strong> {{ $vendor->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-semibold text-white mb-3">Validation Score</h3>
                        <div class="space-y-2 text-gray-300">
                            <p><strong>Overall Score:</strong> {{ $vendor->total_score ?? 0 }}/100</p>
                            <p><strong>Financial Stability:</strong> {{ $vendor->score_financial ?? 0 }}/100</p>
                            <p><strong>Business Reputation:</strong> {{ $vendor->score_reputation ?? 0 }}/100</p>
                            <p><strong>Regulatory Compliance:</strong> {{ $vendor->score_compliance ?? 0 }}/100</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Error Details -->
            <div class="glass-card p-8 mb-8">
                <h2 class="text-2xl font-bold text-white mb-6">Validation Issues</h2>
                
                @if(isset($vendor->pdf_validation_result['validationErrors']))
                    <div class="space-y-4">
                        @foreach($vendor->pdf_validation_result['validationErrors'] as $error)
                            <div class="flex items-start space-x-3 p-4 bg-red-500/10 border border-red-500/20 rounded-lg">
                                <i class="fas fa-times-circle text-red-400 mt-1"></i>
                                <p class="text-red-300">{{ $error }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="flex items-start space-x-3 p-4 bg-yellow-500/10 border border-yellow-500/20 rounded-lg">
                        <i class="fas fa-exclamation-triangle text-yellow-400 mt-1"></i>
                        <p class="text-yellow-300">PDF validation could not be completed. Please ensure your PDF is readable and contains the required information.</p>
                    </div>
                @endif

                @if(isset($vendor->pdf_validation_result['missingSections']))
                    <div class="mt-6">
                        <h3 class="text-lg font-semibold text-white mb-3">Missing Required Sections</h3>
                        <div class="grid md:grid-cols-2 gap-3">
                            @foreach($vendor->pdf_validation_result['missingSections'] as $section)
                                <div class="flex items-center space-x-2 p-3 bg-orange-500/10 border border-orange-500/20 rounded-lg">
                                    <i class="fas fa-minus-circle text-orange-400"></i>
                                    <span class="text-orange-300">{{ $section }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Action Steps -->
            <div class="glass-card p-8 mb-8">
                <h2 class="text-2xl font-bold text-white mb-6">Next Steps</h2>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-white">To Fix Your Application:</h3>
                        <ul class="space-y-3 text-gray-300">
                            <li class="flex items-start space-x-3">
                                <i class="fas fa-check-circle text-green-400 mt-1"></i>
                                <span>Ensure your PDF is readable and not corrupted</span>
                            </li>
                            <li class="flex items-start space-x-3">
                                <i class="fas fa-check-circle text-green-400 mt-1"></i>
                                <span>Include all required business information</span>
                            </li>
                            <li class="flex items-start space-x-3">
                                <i class="fas fa-check-circle text-green-400 mt-1"></i>
                                <span>Provide clear financial statements and business details</span>
                            </li>
                            <li class="flex items-start space-x-3">
                                <i class="fas fa-check-circle text-green-400 mt-1"></i>
                                <span>Make sure all text is properly formatted and legible</span>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-white">Required Information:</h3>
                        <ul class="space-y-3 text-gray-300">
                            <li class="flex items-start space-x-3">
                                <i class="fas fa-file-alt text-blue-400 mt-1"></i>
                                <span>Business registration documents</span>
                            </li>
                            <li class="flex items-start space-x-3">
                                <i class="fas fa-chart-line text-blue-400 mt-1"></i>
                                <span>Financial statements and stability proof</span>
                            </li>
                            <li class="flex items-start space-x-3">
                                <i class="fas fa-award text-blue-400 mt-1"></i>
                                <span>Business reputation and references</span>
                            </li>
                            <li class="flex items-start space-x-3">
                                <i class="fas fa-shield-alt text-blue-400 mt-1"></i>
                                <span>Regulatory compliance certificates</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="glass-card p-8">
                <div class="flex flex-col md:flex-row gap-4 justify-center">
                    <a href="{{ route('supplier.download-report') }}" class="btn-primary px-8 py-3 rounded-xl font-semibold text-center">
                        <i class="fas fa-download mr-2"></i>
                        Download Progress Report
                    </a>
                    
                    <a href="{{ route('supplier.resubmit') }}" class="btn-secondary px-8 py-3 rounded-xl font-semibold text-center">
                        <i class="fas fa-upload mr-2"></i>
                        Resubmit Application
                    </a>
                    
                    <a href="{{ route('supplier.contact-support') }}" class="btn-secondary px-8 py-3 rounded-xl font-semibold text-center">
                        <i class="fas fa-headset mr-2"></i>
                        Contact Support
                    </a>
                    
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="px-8 py-3 bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition-all text-center w-full">
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 