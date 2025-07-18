<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SWSS - Limited Access Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-gradient: linear-gradient(135deg, #10b981 0%, #059669 100%);
            --warning-gradient: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
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

        .btn-success {
            background: var(--success-gradient);
            border: none;
            transition: all 0.3s ease;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        }

        .success-icon {
            background: var(--success-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .progress-bar {
            background: linear-gradient(90deg, #10b981 0%, #059669 100%);
            height: 8px;
            border-radius: 4px;
            transition: width 0.3s ease;
        }
    </style>
</head>
<body class="min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Header with Notification Bell -->
        <div class="text-center mb-8 relative">
            <!-- Notification Bell -->
            @php
                $scheduledVisits = $vendor->facilityVisits()->where('status', 'scheduled')->orderBy('scheduled_at')->get();
                $nextVisit = $scheduledVisits->first();
            @endphp
            
            @if($nextVisit)
                <div class="absolute top-0 right-0">
                    <div class="relative">
                        <button onclick="toggleVisitNotification()" class="w-12 h-12 bg-blue-500/20 rounded-full flex items-center justify-center hover:bg-blue-500/30 transition-all group">
                            <i class="fas fa-bell text-blue-400 text-xl group-hover:scale-110 transition-transform"></i>
                            <div class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full flex items-center justify-center">
                                <span class="text-white text-xs font-bold">1</span>
                            </div>
                        </button>
                        
                        <!-- Notification Popup -->
                        <div id="visitNotification" class="absolute right-0 mt-2 w-80 bg-gray-800 border border-gray-600 rounded-lg shadow-xl z-50 hidden">
                            <div class="p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h3 class="text-lg font-semibold text-white">Facility Visit Scheduled</h3>
                                    <button onclick="toggleVisitNotification()" class="text-gray-400 hover:text-white">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                
                                <div class="space-y-3">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-blue-500/20 rounded-full flex items-center justify-center">
                                            <i class="fas fa-calendar-check text-blue-400"></i>
                                        </div>
                                        <div>
                                            <p class="text-white font-medium">{{ $nextVisit->scheduled_at->format('M d, Y') }}</p>
                                            <p class="text-blue-300 text-sm">{{ $nextVisit->scheduled_at->format('g:i A') }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="bg-blue-500/10 border border-blue-500/20 rounded-lg p-3">
                                        <p class="text-sm text-gray-300 mb-2">
                                            <i class="fas fa-clock mr-2 text-blue-400"></i>
                                            {{ $nextVisit->scheduled_at->diffForHumans() }}
                                        </p>
                                        @if($nextVisit->notes)
                                            <p class="text-sm text-gray-300">
                                                <i class="fas fa-sticky-note mr-2 text-blue-400"></i>
                                                {{ $nextVisit->notes }}
                                            </p>
                                        @endif
                                    </div>
                                    
                                    <div class="text-xs text-gray-400">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Please be prepared for the visit
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            
            <div class="inline-flex items-center justify-center w-20 h-20 bg-green-500/20 rounded-full mb-4">
                <i class="fas fa-check-circle text-4xl success-icon"></i>
            </div>
            <h1 class="text-4xl font-bold text-white mb-2">Welcome to SWSS!</h1>
            <p class="text-gray-300 text-lg">Your application is under review</p>
        </div>

        <!-- Progress Tracker -->
        <div class="glass-card p-8 mb-8">
            <h2 class="text-2xl font-bold text-white mb-6">Application Progress</h2>
            
            <div class="space-y-6">
                <!-- Step 1: Registration -->
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0 w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-check text-white"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-white">Registration Complete</h3>
                        <p class="text-gray-300">Your account has been successfully created</p>
                    </div>
                </div>

                <!-- Step 2: PDF Validation -->
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0 w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-check text-white"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-white">PDF Validation Passed</h3>
                        <p class="text-gray-300">Your documents have been successfully validated</p>
                        <div class="mt-2">
                            <div class="flex justify-between text-sm text-gray-400 mb-1">
                                <span>Validation Score</span>
                                <span>{{ $vendor->total_score ?? 0 }}/100</span>
                            </div>
                            <div class="w-full bg-gray-700 rounded-full h-2">
                                <div class="progress-bar" style="width: {{ ($vendor->total_score ?? 0) }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Facility Visit -->
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0 w-10 h-10 bg-yellow-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-clock text-white"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-white">Facility Visit Pending</h3>
                        <p class="text-gray-300">Our team will schedule a visit to your facility</p>
                    </div>
                </div>

                <!-- Step 4: Final Approval -->
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0 w-10 h-10 bg-gray-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-lock text-gray-400"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-400">Final Approval</h3>
                        <p class="text-gray-500">Access to full supplier dashboard</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Application Details -->
        <div class="glass-card p-8 mb-8">
            <h2 class="text-2xl font-bold text-white mb-6">Application Details</h2>
            
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-semibold text-white mb-3">Business Information</h3>
                    <div class="space-y-2 text-gray-300">
                        <p><strong>Business Name:</strong> {{ $vendor->application_data['business_name'] ?? 'Not provided' }}</p>
                        <p><strong>Business Type:</strong> {{ ucfirst($vendor->application_data['business_type'] ?? 'Not specified') }}</p>
                        <p><strong>Registration Date:</strong> {{ $vendor->created_at->format('M d, Y') }}</p>
                        <p><strong>Current Status:</strong> 
                            <span class="px-2 py-1 bg-yellow-500 text-white rounded-full text-xs">
                                Pending Facility Visit
                            </span>
                        </p>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold text-white mb-3">Validation Scores</h3>
                    <div class="space-y-3">
                        <div>
                            <div class="flex justify-between text-sm text-gray-300 mb-1">
                                <span>Financial Stability</span>
                                <span>{{ $vendor->score_financial ?? 0 }}/100</span>
                            </div>
                            <div class="w-full bg-gray-700 rounded-full h-2">
                                <div class="progress-bar" style="width: {{ ($vendor->score_financial ?? 0) }}%"></div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="flex justify-between text-sm text-gray-300 mb-1">
                                <span>Business Reputation</span>
                                <span>{{ $vendor->score_reputation ?? 0 }}/100</span>
                            </div>
                            <div class="w-full bg-gray-700 rounded-full h-2">
                                <div class="progress-bar" style="width: {{ ($vendor->score_reputation ?? 0) }}%"></div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="flex justify-between text-sm text-gray-300 mb-1">
                                <span>Regulatory Compliance</span>
                                <span>{{ $vendor->score_compliance ?? 0 }}/100</span>
                            </div>
                            <div class="w-full bg-gray-700 rounded-full h-2">
                                <div class="progress-bar" style="width: {{ ($vendor->score_compliance ?? 0) }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Facility Visit Information -->
        @if($nextVisit)
        <div class="glass-card p-8 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-white">Facility Visit Scheduled</h2>
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 bg-blue-400 rounded-full animate-pulse"></div>
                    <span class="text-blue-400 text-sm font-medium">Active</span>
                </div>
            </div>
            
            <!-- Manual completion button for testing -->
            @if($nextVisit->scheduled_at->isPast())
            <div class="mb-6 p-4 bg-yellow-500/10 border border-yellow-500/20 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-yellow-300 font-semibold">Visit Time Has Passed</h3>
                        <p class="text-gray-300 text-sm">The scheduled visit time has passed. You can manually complete the visit for testing.</p>
                    </div>
                    <button onclick="completeVisit()" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg text-sm">
                        <i class="fas fa-check mr-1"></i>Complete Visit
                    </button>
                </div>
            </div>
            @endif
            
            <div class="grid md:grid-cols-2 gap-6">
                <div class="bg-blue-500/10 border border-blue-500/20 rounded-lg p-6">
                    <div class="flex items-center space-x-4 mb-4">
                        <div class="w-12 h-12 bg-blue-500/20 rounded-full flex items-center justify-center">
                            <i class="fas fa-calendar-check text-blue-400 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-white">Visit Details</h3>
                            <p class="text-blue-300">{{ $nextVisit->scheduled_at->format('M d, Y \a\t g:i A') }}</p>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-300">Time Remaining:</span>
                            <span class="text-yellow-300 font-semibold">{{ $nextVisit->scheduled_at->diffForHumans() }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-300">Days Left:</span>
                            <span class="text-blue-300 font-semibold">{{ $nextVisit->scheduled_at->diffInDays(now()) }} days</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-300">Status:</span>
                            <span class="px-2 py-1 bg-blue-500/20 text-blue-300 rounded-full text-xs border border-blue-500/30">
                                <i class="fas fa-clock mr-1"></i>
                                Scheduled
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-white">Preparation Checklist:</h3>
                    <ul class="space-y-3 text-gray-300">
                        <li class="flex items-start space-x-3">
                            <i class="fas fa-check-circle text-green-400 mt-1"></i>
                            <span>Ensure all business documents are ready</span>
                        </li>
                        <li class="flex items-start space-x-3">
                            <i class="fas fa-check-circle text-green-400 mt-1"></i>
                            <span>Prepare your facility for inspection</span>
                        </li>
                        <li class="flex items-start space-x-3">
                            <i class="fas fa-check-circle text-green-400 mt-1"></i>
                            <span>Have key personnel available</span>
                        </li>
                        <li class="flex items-start space-x-3">
                            <i class="fas fa-check-circle text-green-400 mt-1"></i>
                            <span>Review your business operations</span>
                        </li>
                    </ul>
                    
                    @if($nextVisit->notes)
                    <div class="bg-yellow-500/10 border border-yellow-500/20 rounded-lg p-4">
                        <h4 class="text-yellow-300 font-semibold mb-2">
                            <i class="fas fa-sticky-note mr-2"></i>
                            Visit Notes
                        </h4>
                        <p class="text-gray-300 text-sm">{{ $nextVisit->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- What's Next -->
        <div class="glass-card p-8 mb-8">
            <h2 class="text-2xl font-bold text-white mb-6">What Happens Next?</h2>
            
            <div class="grid md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-white">Facility Visit Process:</h3>
                    <ul class="space-y-3 text-gray-300">
                        <li class="flex items-start space-x-3">
                            <i class="fas fa-calendar-check text-blue-400 mt-1"></i>
                            <span>We'll contact you to schedule a facility visit</span>
                        </li>
                        <li class="flex items-start space-x-3">
                            <i class="fas fa-users text-blue-400 mt-1"></i>
                            <span>Our team will assess your operations and capacity</span>
                        </li>
                        <li class="flex items-start space-x-3">
                            <i class="fas fa-clipboard-check text-blue-400 mt-1"></i>
                            <span>We'll verify your business documentation</span>
                        </li>
                        <li class="flex items-start space-x-3">
                            <i class="fas fa-clock text-blue-400 mt-1"></i>
                            <span>Visit typically takes 1-2 hours</span>
                        </li>
                    </ul>
                </div>
                
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-white">After Approval:</h3>
                    <ul class="space-y-3 text-gray-300">
                        <li class="flex items-start space-x-3">
                            <i class="fas fa-tachometer-alt text-green-400 mt-1"></i>
                            <span>Full access to supplier dashboard</span>
                        </li>
                        <li class="flex items-start space-x-3">
                            <i class="fas fa-shopping-cart text-green-400 mt-1"></i>
                            <span>Create and manage orders</span>
                        </li>
                        <li class="flex items-start space-x-3">
                            <i class="fas fa-chart-line text-green-400 mt-1"></i>
                            <span>View analytics and reports</span>
                        </li>
                        <li class="flex items-start space-x-3">
                            <i class="fas fa-handshake text-green-400 mt-1"></i>
                            <span>Connect with farmers and buyers</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="glass-card p-8 mb-8">
            <h2 class="text-2xl font-bold text-white mb-6">Need Help?</h2>
            
            <div class="grid md:grid-cols-3 gap-6">
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
        </div>

        <!-- Action Buttons -->
        <div class="glass-card p-8">
            <div class="flex flex-col md:flex-row gap-4 justify-center">
                <a href="{{ route('supplier.download-report') }}" class="btn-primary px-8 py-3 rounded-xl font-semibold text-center">
                    <i class="fas fa-download mr-2"></i>
                    Download Progress Report
                </a>
                
                <a href="{{ route('supplier.contact-support') }}" class="btn-secondary px-8 py-3 rounded-xl font-semibold text-center">
                    <i class="fas fa-headset mr-2"></i>
                    Contact Support
                </a>
                
                <a href="{{ route('supplier.update-info') }}" class="btn-success px-8 py-3 rounded-xl font-semibold text-center">
                    <i class="fas fa-edit mr-2"></i>
                    Update Information
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

    <script>
        function toggleVisitNotification() {
            const notification = document.getElementById('visitNotification');
            if (notification) {
                notification.classList.toggle('hidden');
            }
        }

        // Close notification when clicking outside
        document.addEventListener('click', function(event) {
            const notification = document.getElementById('visitNotification');
            const bell = event.target.closest('button[onclick="toggleVisitNotification()"]');
            
            if (notification && !notification.contains(event.target) && !bell) {
                notification.classList.add('hidden');
            }
        });

        // Auto-hide notification after 5 seconds
        setTimeout(function() {
            const notification = document.getElementById('visitNotification');
            if (notification && !notification.classList.contains('hidden')) {
                notification.classList.add('hidden');
            }
        }, 5000);

        function completeVisit() {
            if (confirm('Are you sure you want to mark this visit as completed?')) {
                fetch('/admin/vendors/{{ $vendor->id }}/complete-visit', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Visit completed successfully! You will now have access to the full supplier dashboard.');
                        location.reload();
                    } else {
                        alert('Error completing visit: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    alert('Error completing visit: ' + error.message);
                });
            }
        }
    </script>
</body>
</html> 