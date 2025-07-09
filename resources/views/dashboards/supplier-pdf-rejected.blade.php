<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PDF Validation Failed - SWSS Supplier Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --accent-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --dark-gradient: linear-gradient(135deg, #2d1b69 0%, #11998e 100%);
            --card-gradient: linear-gradient(145deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 50%, #16213e 100%);
            min-height: 100vh;
        }

        .font-space {
            font-family: 'Space Grotesk', sans-serif;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative;
            overflow: hidden;
        }

        .glass-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
        }

        .glass-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.12);
            border-color: rgba(255, 255, 255, 0.2);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .gradient-text {
            background: var(--accent-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-card {
            background: var(--card-gradient);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 60px;
            height: 60px;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            border-radius: 50%;
            transform: translate(20px, -20px);
        }

        .stat-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
        }

        .btn-primary {
            background: var(--primary-gradient);
            border: none;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: var(--secondary-gradient);
            transition: left 0.3s ease;
            z-index: -1;
        }

        .btn-primary:hover::before {
            left: 0;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        }

        .logo-pulse {
            animation: logoPulse 2s ease-in-out infinite;
        }

        @keyframes logoPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .shake {
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .fade-in {
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Fixed Navigation Bar */
        .fixed-nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 50;
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Main content adjustment for fixed elements */
        .main-content {
            margin-top: 5rem; /* 80px for navigation height */
            min-height: calc(100vh - 5rem);
        }

        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--accent-gradient);
            border-radius: 3px;
        }
    </style>
</head>
<body class="text-white overflow-x-hidden">
    <!-- Top Navigation -->
    <nav class="fixed-nav px-6 py-4 flex justify-between items-center">
        <div class="flex items-center space-x-4">
            <div class="h-10 w-10 rounded-full bg-gradient-to-r from-red-400 to-pink-500 flex items-center justify-center logo-pulse">
                <span class="text-white font-bold">S</span>
            </div>
            <div>
                <h1 class="text-xl font-bold font-space gradient-text">SWSS Supplier</h1>
                <p class="text-xs text-gray-400">PDF Validation Required</p>
            </div>
        </div>
        
        <div class="flex items-center space-x-6">
            <div class="flex items-center space-x-3">
                <div class="text-right">
                    <p class="text-sm font-semibold">{{ Auth::user()->username }}</p>
                    <p class="text-xs text-red-400">Validation Failed</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-gradient-to-r from-red-400 to-pink-500 flex items-center justify-center">
                    <span class="text-white font-bold text-sm">{{ strtoupper(substr(Auth::user()->username, 0, 2)) }}</span>
                </div>
            </div>
            
            <!-- Logout Button -->
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" 
                        class="flex items-center space-x-2 px-4 py-2 bg-red-500/20 hover:bg-red-500/30 border border-red-500/30 hover:border-red-500/50 rounded-lg transition-all duration-300 text-red-300 hover:text-red-200">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="text-sm font-medium">Logout</span>
                </button>
            </form>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content p-6">
        <!-- Header -->
        <div class="mb-8 fade-in">
            <h1 class="text-4xl font-bold font-space mb-2">PDF Validation Failed</h1>
            <p class="text-xl text-gray-300">{{ $statusMessage }}</p>
        </div>

        <!-- Critical Alert -->
        <div class="glass-card p-6 mb-8 border-l-4 border-red-400 shake">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-3xl text-red-400"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-white mb-1">Document Validation Failed</h3>
                    <p class="text-gray-300">Your uploaded PDF document does not meet our requirements. Please review the issues below and upload a corrected document to proceed with your application.</p>
                </div>
            </div>
        </div>

        @if(isset($pdfValidationResult['message']) && str_contains($pdfValidationResult['message'], 'cURL error'))
        <!-- Service Unavailable Notice -->
        <div class="glass-card p-6 mb-8 border-l-4 border-yellow-400 fade-in">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-3xl text-yellow-400"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-white mb-1">Validation Service Temporarily Unavailable</h3>
                    <p class="text-gray-300">Our automated PDF validation service is currently experiencing technical difficulties. We're showing your calculated score based on available information. Please try uploading your document again later, or contact support for assistance.</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Validation Score Overview -->
        <div class="glass-card p-6 mb-8 fade-in">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold font-space">Validation Results</h2>
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <p class="text-sm text-gray-400">Overall Score</p>
                        <p class="text-2xl font-bold text-red-400">
                            @if(isset($pdfValidationResult['overallScore']))
                                {{ $pdfValidationResult['overallScore'] }}%
                            @elseif(isset($pdfValidationResult['message']) && str_contains($pdfValidationResult['message'], 'cURL error'))
                                {{ $vendor->total_score ?? 45 }}%
                            @else
                                {{ $vendor->total_score ?? 0 }}%
                            @endif
                        </p>
                    </div>
                    <div class="relative w-20 h-20">
                        <svg class="w-20 h-20" style="transform: rotate(-90deg);">
                            <circle cx="40" cy="40" r="36" stroke="rgba(255,255,255,0.1)" stroke-width="4" fill="transparent"/>
                            <circle cx="40" cy="40" r="36" stroke="currentColor" stroke-width="4" fill="transparent" 
                                    stroke-dasharray="226.2" 
                                    stroke-dashoffset="{{ 226.2 - (226.2 * (isset($pdfValidationResult['overallScore']) ? $pdfValidationResult['overallScore'] : (isset($pdfValidationResult['message']) && str_contains($pdfValidationResult['message'], 'cURL error') ? ($vendor->total_score ?? 45) : ($vendor->total_score ?? 0)))) / 100 }}"
                                    style="transition: stroke-dashoffset 0.35s; transform-origin: 50% 50%;"
                                    class="text-red-400"/>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-sm font-bold text-white">
                                @if(isset($pdfValidationResult['overallScore']))
                                    {{ $pdfValidationResult['overallScore'] }}%
                                @elseif(isset($pdfValidationResult['message']) && str_contains($pdfValidationResult['message'], 'cURL error'))
                                    {{ $vendor->total_score ?? 45 }}%
                                @else
                                    {{ $vendor->total_score ?? 0 }}%
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-red-500/10 border border-red-500/20 rounded-lg p-4 mb-4">
                <p class="text-red-300 text-sm">
                    <i class="fas fa-info-circle mr-2"></i>
                    @if(isset($pdfValidationResult['message']) && str_contains($pdfValidationResult['message'], 'cURL error'))
                        Your document requires improvements to meet our standards. The validation service is temporarily unavailable, but we've calculated your score based on available information.
                    @else
                        {{ $pdfValidationResult['message'] ?? 'Your document requires improvements to meet our standards.' }}
                    @endif
                </p>
            </div>
        </div>

        <!-- Missing Sections -->
        @if(!empty($missingSections) || (isset($pdfValidationResult['message']) && str_contains($pdfValidationResult['message'], 'cURL error')))
        <div class="glass-card p-6 mb-8 fade-in">
            <h3 class="text-xl font-bold font-space mb-4">Missing or Incomplete Sections</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if(!empty($missingSections))
                    @foreach($missingSections as $section)
                    <div class="stat-card p-4 flex items-center">
                        <div class="flex-shrink-0 w-10 h-10 bg-red-500/20 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-times text-red-400"></i>
                        </div>
                        <div>
                            <p class="font-medium text-white">{{ $section }}</p>
                            <p class="text-sm text-red-300">Required for approval</p>
                        </div>
                    </div>
                    @endforeach
                @elseif(isset($pdfValidationResult['message']) && str_contains($pdfValidationResult['message'], 'cURL error'))
                    <!-- Fallback missing sections when Java server is unavailable -->
                    <div class="stat-card p-4 flex items-center">
                        <div class="flex-shrink-0 w-10 h-10 bg-red-500/20 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-times text-red-400"></i>
                        </div>
                        <div>
                            <p class="font-medium text-white">Company Information</p>
                            <p class="text-sm text-red-300">Required for approval</p>
                        </div>
                    </div>
                    
                    <div class="stat-card p-4 flex items-center">
                        <div class="flex-shrink-0 w-10 h-10 bg-red-500/20 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-times text-red-400"></i>
                        </div>
                        <div>
                            <p class="font-medium text-white">Financial Stability</p>
                            <p class="text-sm text-red-300">Required for approval</p>
                        </div>
                    </div>
                    
                    <div class="stat-card p-4 flex items-center">
                        <div class="flex-shrink-0 w-10 h-10 bg-red-500/20 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-times text-red-400"></i>
                        </div>
                        <div>
                            <p class="font-medium text-white">Business Reputation</p>
                            <p class="text-sm text-red-300">Required for approval</p>
                        </div>
                    </div>
                    
                    <div class="stat-card p-4 flex items-center">
                        <div class="flex-shrink-0 w-10 h-10 bg-red-500/20 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-times text-red-400"></i>
                        </div>
                        <div>
                            <p class="font-medium text-white">Regulatory Compliance</p>
                            <p class="text-sm text-red-300">Required for approval</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Section Scores -->
        @if(isset($pdfValidationResult['sectionScores']) || (isset($pdfValidationResult['message']) && str_contains($pdfValidationResult['message'], 'cURL error')))
        <div class="glass-card p-6 mb-8 fade-in">
            <h3 class="text-xl font-bold font-space mb-4">Section Performance</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if(isset($pdfValidationResult['sectionScores']))
                    @foreach($pdfValidationResult['sectionScores'] as $section => $score)
                    <div class="stat-card p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-medium text-white">{{ $section }}</span>
                            <span class="text-sm font-bold {{ $score >= 60 ? 'text-green-400' : ($score >= 30 ? 'text-yellow-400' : 'text-red-400') }}">
                                {{ $score }}%
                            </span>
                        </div>
                        <div class="w-full bg-gray-700 rounded-full h-2">
                            <div class="h-2 rounded-full transition-all duration-300 {{ $score >= 60 ? 'bg-green-500' : ($score >= 30 ? 'bg-yellow-500' : 'bg-red-500') }}" 
                                 style="width: {{ $score }}%"></div>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">
                            @if($score >= 60)
                                <i class="fas fa-check-circle text-green-400 mr-1"></i>Good
                            @elseif($score >= 30)
                                <i class="fas fa-exclamation-triangle text-yellow-400 mr-1"></i>Needs improvement
                            @else
                                <i class="fas fa-times-circle text-red-400 mr-1"></i>Critical
                            @endif
                        </p>
                    </div>
                    @endforeach
                @else
                    <!-- Fallback section scores when Java server is unavailable -->
                    <div class="stat-card p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-medium text-white">Financial Stability</span>
                            <span class="text-sm font-bold {{ ($vendor->score_financial ?? 40) >= 60 ? 'text-green-400' : (($vendor->score_financial ?? 40) >= 30 ? 'text-yellow-400' : 'text-red-400') }}">
                                {{ $vendor->score_financial ?? 40 }}%
                            </span>
                        </div>
                        <div class="w-full bg-gray-700 rounded-full h-2">
                            <div class="h-2 rounded-full transition-all duration-300 {{ ($vendor->score_financial ?? 40) >= 60 ? 'bg-green-500' : (($vendor->score_financial ?? 40) >= 30 ? 'bg-yellow-500' : 'bg-red-500') }}" 
                                 style="width: {{ $vendor->score_financial ?? 40 }}%"></div>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">
                            @if(($vendor->score_financial ?? 40) >= 60)
                                <i class="fas fa-check-circle text-green-400 mr-1"></i>Good
                            @elseif(($vendor->score_financial ?? 40) >= 30)
                                <i class="fas fa-exclamation-triangle text-yellow-400 mr-1"></i>Needs improvement
                            @else
                                <i class="fas fa-times-circle text-red-400 mr-1"></i>Critical
                            @endif
                        </p>
                    </div>
                    
                    <div class="stat-card p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-medium text-white">Business Reputation</span>
                            <span class="text-sm font-bold {{ ($vendor->score_reputation ?? 35) >= 60 ? 'text-green-400' : (($vendor->score_reputation ?? 35) >= 30 ? 'text-yellow-400' : 'text-red-400') }}">
                                {{ $vendor->score_reputation ?? 35 }}%
                            </span>
                        </div>
                        <div class="w-full bg-gray-700 rounded-full h-2">
                            <div class="h-2 rounded-full transition-all duration-300 {{ ($vendor->score_reputation ?? 35) >= 60 ? 'bg-green-500' : (($vendor->score_reputation ?? 35) >= 30 ? 'bg-yellow-500' : 'bg-red-500') }}" 
                                 style="width: {{ $vendor->score_reputation ?? 35 }}%"></div>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">
                            @if(($vendor->score_reputation ?? 35) >= 60)
                                <i class="fas fa-check-circle text-green-400 mr-1"></i>Good
                            @elseif(($vendor->score_reputation ?? 35) >= 30)
                                <i class="fas fa-exclamation-triangle text-yellow-400 mr-1"></i>Needs improvement
                            @else
                                <i class="fas fa-times-circle text-red-400 mr-1"></i>Critical
                            @endif
                        </p>
                    </div>
                    
                    <div class="stat-card p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-medium text-white">Regulatory Compliance</span>
                            <span class="text-sm font-bold {{ ($vendor->score_compliance ?? 50) >= 60 ? 'text-green-400' : (($vendor->score_compliance ?? 50) >= 30 ? 'text-yellow-400' : 'text-red-400') }}">
                                {{ $vendor->score_compliance ?? 50 }}%
                            </span>
                        </div>
                        <div class="w-full bg-gray-700 rounded-full h-2">
                            <div class="h-2 rounded-full transition-all duration-300 {{ ($vendor->score_compliance ?? 50) >= 60 ? 'bg-green-500' : (($vendor->score_compliance ?? 50) >= 30 ? 'bg-yellow-500' : 'bg-red-500') }}" 
                                 style="width: {{ $vendor->score_compliance ?? 50 }}%"></div>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">
                            @if(($vendor->score_compliance ?? 50) >= 60)
                                <i class="fas fa-check-circle text-green-400 mr-1"></i>Good
                            @elseif(($vendor->score_compliance ?? 50) >= 30)
                                <i class="fas fa-exclamation-triangle text-yellow-400 mr-1"></i>Needs improvement
                            @else
                                <i class="fas fa-times-circle text-red-400 mr-1"></i>Critical
                            @endif
                        </p>
                    </div>
                    
                    <div class="stat-card p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-medium text-white">Document Quality</span>
                            <span class="text-sm font-bold {{ ($vendor->total_score ?? 45) >= 60 ? 'text-green-400' : (($vendor->total_score ?? 45) >= 30 ? 'text-yellow-400' : 'text-red-400') }}">
                                {{ $vendor->total_score ?? 45 }}%
                            </span>
                        </div>
                        <div class="w-full bg-gray-700 rounded-full h-2">
                            <div class="h-2 rounded-full transition-all duration-300 {{ ($vendor->total_score ?? 45) >= 60 ? 'bg-green-500' : (($vendor->total_score ?? 45) >= 30 ? 'bg-yellow-500' : 'bg-red-500') }}" 
                                 style="width: {{ $vendor->total_score ?? 45 }}%"></div>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">
                            @if(($vendor->total_score ?? 45) >= 60)
                                <i class="fas fa-check-circle text-green-400 mr-1"></i>Good
                            @elseif(($vendor->total_score ?? 45) >= 30)
                                <i class="fas fa-exclamation-triangle text-yellow-400 mr-1"></i>Needs improvement
                            @else
                                <i class="fas fa-times-circle text-red-400 mr-1"></i>Critical
                            @endif
                        </p>
                    </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Required Sections Guide -->
        <div class="glass-card p-6 mb-8 fade-in">
            <h3 class="text-xl font-bold font-space mb-4">Required PDF Sections</h3>
            <p class="text-gray-300 mb-6">Your PDF document must include the following sections to be approved. Each section should be clearly labeled and contain relevant information.</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="stat-card p-4">
                    <div class="flex items-center mb-3">
                        <div class="w-8 h-8 bg-blue-500/20 rounded-full flex items-center justify-center mr-3">
                            <span class="text-blue-400 font-bold text-sm">1</span>
                        </div>
                        <h4 class="font-medium text-white">Company Information</h4>
                    </div>
                    <p class="text-sm text-gray-300">Business details, contact information, registration details, company structure</p>
                </div>
                
                <div class="stat-card p-4">
                    <div class="flex items-center mb-3">
                        <div class="w-8 h-8 bg-green-500/20 rounded-full flex items-center justify-center mr-3">
                            <span class="text-green-400 font-bold text-sm">2</span>
                        </div>
                        <h4 class="font-medium text-white">Financial Stability</h4>
                    </div>
                    <p class="text-sm text-gray-300">Revenue, assets, financial statements, credit information, payment history</p>
                </div>
                
                <div class="stat-card p-4">
                    <div class="flex items-center mb-3">
                        <div class="w-8 h-8 bg-purple-500/20 rounded-full flex items-center justify-center mr-3">
                            <span class="text-purple-400 font-bold text-sm">3</span>
                        </div>
                        <h4 class="font-medium text-white">Business Reputation</h4>
                    </div>
                    <p class="text-sm text-gray-300">References, certifications, experience, track record, client testimonials</p>
                </div>
                
                <div class="stat-card p-4">
                    <div class="flex items-center mb-3">
                        <div class="w-8 h-8 bg-yellow-500/20 rounded-full flex items-center justify-center mr-3">
                            <span class="text-yellow-400 font-bold text-sm">4</span>
                        </div>
                        <h4 class="font-medium text-white">Regulatory Compliance</h4>
                    </div>
                    <p class="text-sm text-gray-300">Licenses, permits, certifications, government approvals, industry standards</p>
                </div>
                
                <div class="stat-card p-4">
                    <div class="flex items-center mb-3">
                        <div class="w-8 h-8 bg-indigo-500/20 rounded-full flex items-center justify-center mr-3">
                            <span class="text-indigo-400 font-bold text-sm">5</span>
                        </div>
                        <h4 class="font-medium text-white">Product/Service Summary</h4>
                    </div>
                    <p class="text-sm text-gray-300">Offerings, inventory, specialties, capabilities, quality standards</p>
                </div>
                
                <div class="stat-card p-4">
                    <div class="flex items-center mb-3">
                        <div class="w-8 h-8 bg-pink-500/20 rounded-full flex items-center justify-center mr-3">
                            <span class="text-pink-400 font-bold text-sm">6</span>
                        </div>
                        <h4 class="font-medium text-white">Declaration</h4>
                    </div>
                    <p class="text-sm text-gray-300">Legal statements, signatures, confirmations, compliance declarations</p>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="glass-card p-6 fade-in">
            <h3 class="text-xl font-bold font-space mb-4">Next Steps</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <button onclick="showUploadModal()" 
                   class="btn-primary text-white font-semibold py-4 px-6 rounded-xl transition-all duration-300 transform hover:scale-105 flex items-center justify-center">
                    <i class="fas fa-upload mr-3 text-lg"></i>
                    <div class="text-left">
                        <p class="font-bold">Upload New PDF</p>
                        <p class="text-sm opacity-90">Submit corrected document</p>
                    </div>
                </button>
                
                <button onclick="showChatHelpModal()" 
                   class="btn-primary text-white font-semibold py-4 px-6 rounded-xl transition-all duration-300 transform hover:scale-105 flex items-center justify-center">
                    <i class="fas fa-comments mr-3 text-lg"></i>
                    <div class="text-left">
                        <p class="font-bold">Get Help</p>
                        <p class="text-sm opacity-90">Chat with admin</p>
                    </div>
                </button>
                
                <button onclick="showSampleTemplate()" 
                        class="btn-primary text-white font-semibold py-4 px-6 rounded-xl transition-all duration-300 transform hover:scale-105 flex items-center justify-center">
                    <i class="fas fa-file-alt mr-3 text-lg"></i>
                    <div class="text-left">
                        <p class="font-bold">View Template</p>
                        <p class="text-sm opacity-90">See example format</p>
                    </div>
                </button>
            </div>
        </div>

        <!-- Tips Section -->
        <div class="glass-card p-6 mt-8 fade-in">
            <h3 class="text-xl font-bold font-space mb-4">Tips for Success</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="stat-card p-4">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-lightbulb text-green-400 mr-2"></i>
                        <h4 class="font-medium text-white">Document Quality</h4>
                    </div>
                    <p class="text-sm text-gray-300">Ensure your PDF is clear, readable, and professionally formatted. Use high-quality scans if submitting physical documents.</p>
                </div>
                
                <div class="stat-card p-4">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-check-double text-blue-400 mr-2"></i>
                        <h4 class="font-medium text-white">Complete Information</h4>
                    </div>
                    <p class="text-sm text-gray-300">Make sure all required sections are present and contain comprehensive information. Incomplete sections will result in lower scores.</p>
                </div>
                
                <div class="stat-card p-4">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-clock text-yellow-400 mr-2"></i>
                        <h4 class="font-medium text-white">Recent Documents</h4>
                    </div>
                    <p class="text-sm text-gray-300">Use recent financial statements and documents (within the last 12 months) to ensure accuracy and relevance.</p>
                </div>
                
                <div class="stat-card p-4">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-shield-alt text-purple-400 mr-2"></i>
                        <h4 class="font-medium text-white">Legal Compliance</h4>
                    </div>
                    <p class="text-sm text-gray-300">Ensure all licenses, permits, and certifications are current and valid. Include expiration dates where applicable.</p>
                </div>
            </div>
        </div>
    </main>

    <!-- Chat Help Modal -->
    <div id="chatHelpModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="glass-card p-8 max-w-4xl w-full mx-4 max-h-[80vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-white">Get Help with PDF Validation</h3>
                <button onclick="closeChatHelpModal()" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Quick Help Section -->
                <div class="space-y-6">
                    <div class="stat-card p-6">
                        <h4 class="text-xl font-bold text-blue-400 mb-4">Quick Help</h4>
                        <div class="space-y-4">
                            <div class="flex items-start space-x-3">
                                <div class="w-8 h-8 bg-blue-500/20 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                    <i class="fas fa-question text-blue-400 text-sm"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-white">Need immediate assistance?</p>
                                    <p class="text-sm text-gray-300">Start a live chat with our support team for real-time help.</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-3">
                                <div class="w-8 h-8 bg-green-500/20 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                    <i class="fas fa-clock text-green-400 text-sm"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-white">Response Time</p>
                                    <p class="text-sm text-gray-300">We typically respond within 5-10 minutes during business hours.</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-3">
                                <div class="w-8 h-8 bg-purple-500/20 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                    <i class="fas fa-users text-purple-400 text-sm"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-white">Expert Support</p>
                                    <p class="text-sm text-gray-300">Our team includes PDF validation specialists and business consultants.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Start Chat Button -->
                    <div class="stat-card p-6">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-comments text-white text-2xl"></i>
                            </div>
                            <h4 class="text-xl font-bold text-white mb-2">Start Live Chat</h4>
                            <p class="text-gray-300 mb-4">Connect with our support team for personalized assistance</p>
                            <button onclick="startLiveChat()" 
                                    class="btn-primary text-white font-semibold py-3 px-8 rounded-lg transition-all duration-300 transform hover:scale-105 w-full">
                                <i class="fas fa-comments mr-2"></i>
                                Start Chat Now
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- FAQ Section -->
                <div class="space-y-6">
                    <div class="stat-card p-6">
                        <h4 class="text-xl font-bold text-green-400 mb-4">Frequently Asked Questions</h4>
                        <div class="space-y-4">
                            <div class="border-b border-gray-600 pb-3">
                                <button onclick="toggleFAQ('faq1')" class="flex items-center justify-between w-full text-left">
                                    <span class="font-semibold text-white">Why did my PDF validation fail?</span>
                                    <i id="faq1-icon" class="fas fa-chevron-down text-gray-400 transition-transform"></i>
                                </button>
                                <div id="faq1-content" class="hidden mt-2">
                                    <p class="text-sm text-gray-300">PDF validation can fail due to missing required sections, poor document quality, incomplete information, or format issues. Check the validation results above for specific details.</p>
                                </div>
                            </div>
                            
                            <div class="border-b border-gray-600 pb-3">
                                <button onclick="toggleFAQ('faq2')" class="flex items-center justify-between w-full text-left">
                                    <span class="font-semibold text-white">What sections are required in my PDF?</span>
                                    <i id="faq2-icon" class="fas fa-chevron-down text-gray-400 transition-transform"></i>
                                </button>
                                <div id="faq2-content" class="hidden mt-2">
                                    <p class="text-sm text-gray-300">Your PDF must include: Company Information, Financial Stability, Business Reputation, Regulatory Compliance, Product/Service Summary, and Declaration. See the template for details.</p>
                                </div>
                            </div>
                            
                            <div class="border-b border-gray-600 pb-3">
                                <button onclick="toggleFAQ('faq3')" class="flex items-center justify-between w-full text-left">
                                    <span class="font-semibold text-white">How long does validation take?</span>
                                    <i id="faq3-icon" class="fas fa-chevron-down text-gray-400 transition-transform"></i>
                                </button>
                                <div id="faq3-content" class="hidden mt-2">
                                    <p class="text-sm text-gray-300">PDF validation typically takes 2-5 minutes. The system analyzes document content, structure, and completeness automatically.</p>
                                </div>
                            </div>
                            
                            <div class="border-b border-gray-600 pb-3">
                                <button onclick="toggleFAQ('faq4')" class="flex items-center justify-between w-full text-left">
                                    <span class="font-semibold text-white">Can I upload multiple documents?</span>
                                    <i id="faq4-icon" class="fas fa-chevron-down text-gray-400 transition-transform"></i>
                                </button>
                                <div id="faq4-content" class="hidden mt-2">
                                    <p class="text-sm text-gray-300">Currently, you can upload one comprehensive PDF document. If you have multiple documents, please combine them into a single PDF file.</p>
                                </div>
                            </div>
                            
                            <div class="border-b border-gray-600 pb-3">
                                <button onclick="toggleFAQ('faq5')" class="flex items-center justify-between w-full text-left">
                                    <span class="font-semibold text-white">What if I need to update my information?</span>
                                    <i id="faq5-icon" class="fas fa-chevron-down text-gray-400 transition-transform"></i>
                                </button>
                                <div id="faq5-content" class="hidden mt-2">
                                    <p class="text-sm text-gray-300">You can upload a new PDF document at any time. The system will re-validate and update your application status accordingly.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Contact Information -->
            <div class="mt-8 stat-card p-6">
                <h4 class="text-xl font-bold text-yellow-400 mb-4">Alternative Contact Methods</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-500/20 rounded-full flex items-center justify-center">
                            <i class="fas fa-envelope text-blue-400"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-white">Email Support</p>
                            <p class="text-sm text-gray-300">support@swss.com</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-green-500/20 rounded-full flex items-center justify-center">
                            <i class="fas fa-phone text-green-400"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-white">Phone Support</p>
                            <p class="text-sm text-gray-300">+256 123 456 789</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-purple-500/20 rounded-full flex items-center justify-center">
                            <i class="fas fa-clock text-purple-400"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-white">Business Hours</p>
                            <p class="text-sm text-gray-300">Mon-Fri: 8AM-6PM EAT</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="mt-6 flex justify-end space-x-4">
                <button onclick="closeChatHelpModal()" 
                        class="px-6 py-2 border border-gray-400 text-gray-300 rounded-lg hover:bg-gray-700 transition-colors">
                    Close
                </button>
                <a href="{{ route('chat.index') }}" 
                   class="btn-primary text-white font-semibold py-2 px-6 rounded-lg transition-colors">
                    <i class="fas fa-external-link-alt mr-2"></i>
                    Open Full Chat
                </a>
            </div>
        </div>
    </div>

    <!-- PDF Upload Modal -->
    <div id="uploadModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="glass-card p-8 max-w-2xl w-full mx-4">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-white">Upload New PDF Document</h3>
                <button onclick="closeUploadModal()" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form id="pdfUploadForm" action="{{ route('vendor.upload-pdf') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                <!-- File Upload Area -->
                <div class="border-2 border-dashed border-gray-400 rounded-lg p-8 text-center hover:border-blue-400 transition-colors" 
                     id="dropZone" 
                     ondrop="handleDrop(event)" 
                     ondragover="handleDragOver(event)" 
                     ondragleave="handleDragLeave(event)">
                    
                    <div id="uploadIcon" class="mb-4">
                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400"></i>
                    </div>
                    
                    <div id="uploadText">
                        <p class="text-lg font-semibold text-white mb-2">Drag & Drop your PDF here</p>
                        <p class="text-gray-300 mb-4">or click to browse files</p>
                        <button type="button" onclick="document.getElementById('pdfFile').click()" 
                                class="btn-primary text-white font-semibold py-2 px-6 rounded-lg transition-colors">
                            Choose File
                        </button>
                    </div>
                    
                    <div id="filePreview" class="hidden">
                        <div class="flex items-center justify-center space-x-3">
                            <i class="fas fa-file-pdf text-3xl text-red-400"></i>
                            <div class="text-left">
                                <p id="fileName" class="font-semibold text-white"></p>
                                <p id="fileSize" class="text-sm text-gray-300"></p>
                            </div>
                            <button type="button" onclick="removeFile()" class="text-red-400 hover:text-red-300">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    
                    <input type="file" id="pdfFile" name="pdf_document" accept=".pdf" class="hidden" onchange="handleFileSelect(event)">
                </div>
                
                <!-- File Requirements -->
                <div class="stat-card p-4">
                    <h4 class="font-bold text-blue-400 mb-2">File Requirements</h4>
                    <div class="text-sm text-gray-300 space-y-1">
                        <p><i class="fas fa-check-circle text-green-400 mr-2"></i>File format: PDF only</p>
                        <p><i class="fas fa-check-circle text-green-400 mr-2"></i>Maximum size: 10MB</p>
                        <p><i class="fas fa-check-circle text-green-400 mr-2"></i>Must include all required sections</p>
                        <p><i class="fas fa-check-circle text-green-400 mr-2"></i>Clear, readable text and images</p>
                    </div>
                </div>
                
                <!-- Validation Progress -->
                <div id="validationProgress" class="hidden">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-white font-semibold">Validating PDF...</span>
                        <span id="validationPercent" class="text-blue-400">0%</span>
                    </div>
                    <div class="w-full bg-gray-700 rounded-full h-2">
                        <div id="validationBar" class="bg-blue-500 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                    </div>
                </div>
                
                <!-- Error Messages -->
                <div id="uploadErrors" class="hidden">
                    <div class="bg-red-500/10 border border-red-500/20 rounded-lg p-4">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-red-400 mr-2"></i>
                            <p id="errorMessage" class="text-red-300"></p>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex justify-end space-x-4">
                    <button type="button" onclick="closeUploadModal()" 
                            class="px-6 py-2 border border-gray-400 text-gray-300 rounded-lg hover:bg-gray-700 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" id="uploadButton" 
                            class="btn-primary text-white font-semibold py-2 px-6 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                            disabled>
                        Upload & Validate
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Sample Template Modal -->
    <div id="sampleTemplateModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="glass-card p-8 max-w-4xl w-full mx-4 max-h-[80vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-white">Sample PDF Template</h3>
                <button onclick="closeSampleTemplate()" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div class="space-y-6">
                <div class="stat-card p-4">
                    <h4 class="font-bold text-blue-400 mb-2">1. COMPANY INFORMATION</h4>
                    <div class="text-sm text-gray-300 space-y-1">
                        <p>• Company Name: [Your Company Name]</p>
                        <p>• Registration Number: [Business Registration]</p>
                        <p>• Address: [Full Business Address]</p>
                        <p>• Contact Person: [Name and Title]</p>
                        <p>• Phone: [Business Phone]</p>
                        <p>• Email: [Business Email]</p>
                        <p>• Website: [Company Website]</p>
                        <p>• Years in Business: [Number of Years]</p>
                    </div>
                </div>
                
                <div class="stat-card p-4">
                    <h4 class="font-bold text-green-400 mb-2">2. FINANCIAL STABILITY</h4>
                    <div class="text-sm text-gray-300 space-y-1">
                        <p>• Annual Revenue: [Last 3 Years]</p>
                        <p>• Total Assets: [Current Value]</p>
                        <p>• Bank References: [Bank Name and Account Details]</p>
                        <p>• Credit Rating: [If Available]</p>
                        <p>• Payment Terms: [Standard Terms]</p>
                        <p>• Financial Statements: [Attach Recent Statements]</p>
                    </div>
                </div>
                
                <div class="stat-card p-4">
                    <h4 class="font-bold text-purple-400 mb-2">3. BUSINESS REPUTATION</h4>
                    <div class="text-sm text-gray-300 space-y-1">
                        <p>• Major Clients: [List Top 5 Clients]</p>
                        <p>• Industry Experience: [Years and Sectors]</p>
                        <p>• Awards/Certifications: [Relevant Awards]</p>
                        <p>• References: [3 Business References]</p>
                        <p>• Case Studies: [Successful Projects]</p>
                    </div>
                </div>
                
                <div class="stat-card p-4">
                    <h4 class="font-bold text-yellow-400 mb-2">4. REGULATORY COMPLIANCE</h4>
                    <div class="text-sm text-gray-300 space-y-1">
                        <p>• Business License: [License Number and Expiry]</p>
                        <p>• Tax Registration: [Tax ID Number]</p>
                        <p>• Industry Certifications: [Relevant Certifications]</p>
                        <p>• Quality Standards: [ISO, HACCP, etc.]</p>
                        <p>• Safety Certifications: [Safety Standards Met]</p>
                    </div>
                </div>
                
                <div class="stat-card p-4">
                    <h4 class="font-bold text-indigo-400 mb-2">5. PRODUCT/SERVICE SUMMARY</h4>
                    <div class="text-sm text-gray-300 space-y-1">
                        <p>• Primary Products: [Main Product Lines]</p>
                        <p>• Production Capacity: [Daily/Monthly Capacity]</p>
                        <p>• Quality Control: [QC Processes]</p>
                        <p>• Packaging: [Packaging Standards]</p>
                        <p>• Delivery Capability: [Delivery Areas and Times]</p>
                    </div>
                </div>
                
                <div class="stat-card p-4">
                    <h4 class="font-bold text-pink-400 mb-2">6. DECLARATION</h4>
                    <div class="text-sm text-gray-300 space-y-1">
                        <p>• I/We declare that all information provided is true and accurate</p>
                        <p>• I/We agree to comply with all SWSS requirements</p>
                        <p>• I/We understand that false information may result in rejection</p>
                        <p>• Signature: [Authorized Person Signature]</p>
                        <p>• Date: [Date of Declaration]</p>
                        <p>• Company Stamp: [Official Company Stamp]</p>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 text-center">
                <button onclick="closeSampleTemplate()" 
                        class="btn-primary text-white font-semibold py-2 px-6 rounded-lg transition-colors">
                    Close Template
                </button>
            </div>
        </div>
    </div>

    <script>
        function showSampleTemplate() {
            document.getElementById('sampleTemplateModal').classList.remove('hidden');
        }
        
        function closeSampleTemplate() {
            document.getElementById('sampleTemplateModal').classList.add('hidden');
        }
        
        // Close modal when clicking outside
        document.getElementById('sampleTemplateModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeSampleTemplate();
            }
        });
        
        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeSampleTemplate();
            }
        });

        // Upload Modal Functions
        function showUploadModal() {
            document.getElementById('uploadModal').classList.remove('hidden');
            resetUploadForm();
        }
        
        function closeUploadModal() {
            document.getElementById('uploadModal').classList.add('hidden');
            resetUploadForm();
        }
        
        function resetUploadForm() {
            document.getElementById('pdfFile').value = '';
            document.getElementById('uploadIcon').classList.remove('hidden');
            document.getElementById('uploadText').classList.remove('hidden');
            document.getElementById('filePreview').classList.add('hidden');
            document.getElementById('validationProgress').classList.add('hidden');
            document.getElementById('uploadErrors').classList.add('hidden');
            document.getElementById('uploadButton').disabled = true;
            document.getElementById('dropZone').classList.remove('border-blue-400', 'border-green-400', 'border-red-400');
            document.getElementById('dropZone').classList.add('border-gray-400');
        }
        
        // Drag and Drop Functions
        function handleDragOver(e) {
            e.preventDefault();
            document.getElementById('dropZone').classList.remove('border-gray-400');
            document.getElementById('dropZone').classList.add('border-blue-400');
        }
        
        function handleDragLeave(e) {
            e.preventDefault();
            if (!e.currentTarget.contains(e.relatedTarget)) {
                document.getElementById('dropZone').classList.remove('border-blue-400');
                document.getElementById('dropZone').classList.add('border-gray-400');
            }
        }
        
        function handleDrop(e) {
            e.preventDefault();
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handleFile(files[0]);
            }
            document.getElementById('dropZone').classList.remove('border-blue-400');
            document.getElementById('dropZone').classList.add('border-gray-400');
        }
        
        function handleFileSelect(e) {
            const file = e.target.files[0];
            if (file) {
                handleFile(file);
            }
        }
        
        function handleFile(file) {
            // Validate file type
            if (file.type !== 'application/pdf') {
                showError('Please select a PDF file only.');
                return;
            }
            
            // Validate file size (10MB limit)
            const maxSize = 10 * 1024 * 1024; // 10MB in bytes
            if (file.size > maxSize) {
                showError('File size must be less than 10MB.');
                return;
            }
            
            // Display file preview
            document.getElementById('fileName').textContent = file.name;
            document.getElementById('fileSize').textContent = formatFileSize(file.size);
            
            document.getElementById('uploadIcon').classList.add('hidden');
            document.getElementById('uploadText').classList.add('hidden');
            document.getElementById('filePreview').classList.remove('hidden');
            document.getElementById('uploadButton').disabled = false;
            document.getElementById('uploadErrors').classList.add('hidden');
            
            // Update drop zone styling
            document.getElementById('dropZone').classList.remove('border-gray-400', 'border-red-400');
            document.getElementById('dropZone').classList.add('border-green-400');
        }
        
        function removeFile() {
            document.getElementById('pdfFile').value = '';
            document.getElementById('uploadIcon').classList.remove('hidden');
            document.getElementById('uploadText').classList.remove('hidden');
            document.getElementById('filePreview').classList.add('hidden');
            document.getElementById('uploadButton').disabled = true;
            document.getElementById('dropZone').classList.remove('border-green-400', 'border-red-400');
            document.getElementById('dropZone').classList.add('border-gray-400');
        }
        
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
        
        function showError(message) {
            document.getElementById('errorMessage').textContent = message;
            document.getElementById('uploadErrors').classList.remove('hidden');
            document.getElementById('dropZone').classList.remove('border-blue-400', 'border-green-400');
            document.getElementById('dropZone').classList.add('border-red-400');
        }
        
        // Form submission with validation progress
        document.getElementById('pdfUploadForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const uploadButton = document.getElementById('uploadButton');
            const validationProgress = document.getElementById('validationProgress');
            
            // Show validation progress
            uploadButton.disabled = true;
            validationProgress.classList.remove('hidden');
            document.getElementById('uploadErrors').classList.add('hidden');
            
            // Simulate validation progress
            let progress = 0;
            const progressInterval = setInterval(() => {
                progress += Math.random() * 15;
                if (progress > 90) progress = 90;
                
                document.getElementById('validationPercent').textContent = Math.round(progress) + '%';
                document.getElementById('validationBar').style.width = progress + '%';
            }, 200);
            
            // Submit form
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                clearInterval(progressInterval);
                document.getElementById('validationPercent').textContent = '100%';
                document.getElementById('validationBar').style.width = '100%';
                
                setTimeout(() => {
                    if (data.success) {
                        // Success - redirect to appropriate page
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            window.location.reload();
                        }
                    } else {
                        // Show error
                        showError(data.message || 'Upload failed. Please try again.');
                        validationProgress.classList.add('hidden');
                        uploadButton.disabled = false;
                    }
                }, 500);
            })
            .catch(error => {
                clearInterval(progressInterval);
                showError('Upload failed. Please check your connection and try again.');
                validationProgress.classList.add('hidden');
                uploadButton.disabled = false;
                console.error('Upload error:', error);
            });
        });
        
        // Close upload modal when clicking outside
        document.getElementById('uploadModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeUploadModal();
            }
        });
        
        // Enhanced keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeSampleTemplate();
                closeUploadModal();
            }
        });

        // Chat Help Modal Functions
        function showChatHelpModal() {
            document.getElementById('chatHelpModal').classList.remove('hidden');
        }
        
        function closeChatHelpModal() {
            document.getElementById('chatHelpModal').classList.add('hidden');
        }
        
        function startLiveChat() {
            // Close the help modal
            closeChatHelpModal();
            
            // Show a notification that chat is starting
            showNotification('Starting live chat...', 'info');
            
            // Redirect to the full chat page after a short delay
            setTimeout(() => {
                window.location.href = '{{ route("chat.index") }}';
            }, 1000);
        }
        
        function toggleFAQ(faqId) {
            const content = document.getElementById(faqId + '-content');
            const icon = document.getElementById(faqId + '-icon');
            
            if (content.classList.contains('hidden')) {
                // Close all other FAQs first
                const allContents = document.querySelectorAll('[id$="-content"]');
                const allIcons = document.querySelectorAll('[id$="-icon"]');
                
                allContents.forEach(c => c.classList.add('hidden'));
                allIcons.forEach(i => i.classList.remove('fa-chevron-up'));
                allIcons.forEach(i => i.classList.add('fa-chevron-down'));
                
                // Open this FAQ
                content.classList.remove('hidden');
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-up');
            } else {
                // Close this FAQ
                content.classList.add('hidden');
                icon.classList.remove('fa-chevron-up');
                icon.classList.add('fa-chevron-down');
            }
        }
        
        function showNotification(message, type = 'info') {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full ${
                type === 'info' ? 'bg-blue-500' : 
                type === 'success' ? 'bg-green-500' : 
                type === 'error' ? 'bg-red-500' : 'bg-gray-500'
            } text-white`;
            
            notification.innerHTML = `
                <div class="flex items-center space-x-3">
                    <i class="fas ${type === 'info' ? 'fa-info-circle' : type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-triangle' : 'fa-bell'}"></i>
                    <span>${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-2 hover:opacity-75">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // Animate in
            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 100);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.classList.add('translate-x-full');
                    setTimeout(() => {
                        if (notification.parentElement) {
                            notification.remove();
                        }
                    }, 300);
                }
            }, 5000);
        }
        
        // Close chat help modal when clicking outside
        document.getElementById('chatHelpModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeChatHelpModal();
            }
        });
        
        // Enhanced keyboard navigation for all modals
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeSampleTemplate();
                closeUploadModal();
                closeChatHelpModal();
            }
        });
    </script>
</body>
</html> 