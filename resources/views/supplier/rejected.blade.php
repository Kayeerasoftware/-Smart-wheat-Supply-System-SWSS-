<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SWSS - Application Rejected</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
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
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        }
        .error-icon {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>
<body class="min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-red-500/20 rounded-full mb-4">
                    <i class="fas fa-times-circle text-4xl error-icon"></i>
                </div>
                <h1 class="text-4xl font-bold text-white mb-2">Application Rejected</h1>
                <p class="text-gray-300 text-lg">We regret to inform you that your application has not been approved</p>
            </div>

            <div class="glass-card p-8 mb-8">
                <h2 class="text-2xl font-bold text-white mb-6">Application Status</h2>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-semibold text-white mb-3">Business Information</h3>
                        <div class="space-y-2 text-gray-300">
                            <p><strong>Business Name:</strong> {{ $vendor->application_data['business_name'] ?? 'Not provided' }}</p>
                            <p><strong>Business Type:</strong> {{ ucfirst($vendor->application_data['business_type'] ?? 'Not specified') }}</p>
                            <p><strong>Application Date:</strong> {{ $vendor->created_at->format('M d, Y') }}</p>
                            <p><strong>Status:</strong> 
                                <span class="px-2 py-1 bg-red-500 text-white rounded-full text-xs">
                                    Rejected
                                </span>
                            </p>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-semibold text-white mb-3">Final Scores</h3>
                        <div class="space-y-2 text-gray-300">
                            <p><strong>Overall Score:</strong> {{ $vendor->total_score ?? 0 }}/100</p>
                            <p><strong>Financial Stability:</strong> {{ $vendor->score_financial ?? 0 }}/100</p>
                            <p><strong>Business Reputation:</strong> {{ $vendor->score_reputation ?? 0 }}/100</p>
                            <p><strong>Regulatory Compliance:</strong> {{ $vendor->score_compliance ?? 0 }}/100</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="glass-card p-8 mb-8">
                <h2 class="text-2xl font-bold text-white mb-6">What This Means</h2>
                
                <div class="space-y-4 text-gray-300">
                    <p>Unfortunately, your application did not meet our current supplier requirements. This decision was based on a comprehensive evaluation of your business documentation and facility assessment.</p>
                    
                    <p>Common reasons for rejection include:</p>
                    <ul class="list-disc list-inside space-y-2 ml-4">
                        <li>Insufficient financial stability or business history</li>
                        <li>Incomplete or inadequate business documentation</li>
                        <li>Facility standards not meeting our requirements</li>
                        <li>Regulatory compliance issues</li>
                        <li>Business capacity or operational concerns</li>
                    </ul>
                </div>
            </div>

            <div class="glass-card p-8 mb-8">
                <h2 class="text-2xl font-bold text-white mb-6">Next Steps</h2>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-white">If You Wish to Reapply:</h3>
                        <ul class="space-y-3 text-gray-300">
                            <li class="flex items-start space-x-3">
                                <i class="fas fa-clock text-blue-400 mt-1"></i>
                                <span>Wait at least 6 months before submitting a new application</span>
                            </li>
                            <li class="flex items-start space-x-3">
                                <i class="fas fa-file-alt text-blue-400 mt-1"></i>
                                <span>Address the issues identified in your evaluation</span>
                            </li>
                            <li class="flex items-start space-x-3">
                                <i class="fas fa-chart-line text-blue-400 mt-1"></i>
                                <span>Improve your business operations and documentation</span>
                            </li>
                            <li class="flex items-start space-x-3">
                                <i class="fas fa-shield-alt text-blue-400 mt-1"></i>
                                <span>Ensure regulatory compliance is maintained</span>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-white">Alternative Options:</h3>
                        <ul class="space-y-3 text-gray-300">
                            <li class="flex items-start space-x-3">
                                <i class="fas fa-handshake text-green-400 mt-1"></i>
                                <span>Consider partnering with existing approved suppliers</span>
                            </li>
                            <li class="flex items-start space-x-3">
                                <i class="fas fa-users text-green-400 mt-1"></i>
                                <span>Join our network as a farmer or other role</span>
                            </li>
                            <li class="flex items-start space-x-3">
                                <i class="fas fa-info-circle text-green-400 mt-1"></i>
                                <span>Stay informed about future opportunities</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="glass-card p-8">
                <h2 class="text-2xl font-bold text-white mb-6">Need More Information?</h2>
                
                <div class="grid md:grid-cols-3 gap-6 mb-6">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-blue-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-phone text-2xl text-blue-400"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-white mb-2">Call Us</h3>
                        <p class="text-gray-300">+256 123 456 789</p>
                        <p class="text-gray-400 text-sm">Mon-Fri, 8AM-5PM</p>
                    </div>
                    
                    <div class="text-center">
                        <div class="w-16 h-16 bg-green-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-envelope text-2xl text-green-400"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-white mb-2">Email Us</h3>
                        <p class="text-gray-300">support@swss.com</p>
                        <p class="text-gray-400 text-sm">24/7 support</p>
                    </div>
                    
                    <div class="text-center">
                        <div class="w-16 h-16 bg-purple-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-comments text-2xl text-purple-400"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-white mb-2">Live Chat</h3>
                        <p class="text-gray-300">Available now</p>
                        <p class="text-gray-400 text-sm">Instant help</p>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row gap-4 justify-center">
                    <a href="{{ route('supplier.contact-support') }}" class="btn-primary px-8 py-3 rounded-xl font-semibold text-center">
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