<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SWSS - Contact Support</title>
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
    </style>
</head>
<body class="min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-500/20 border border-green-500/30 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-400 mr-3"></i>
                        <p class="text-green-300">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 bg-red-500/20 border border-red-500/30 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-400 mr-3"></i>
                        <p class="text-red-300">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 bg-red-500/20 border border-red-500/30 rounded-lg">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-exclamation-circle text-red-400 mr-3"></i>
                        <p class="text-red-300 font-semibold">Please fix the following errors:</p>
                    </div>
                    <ul class="text-red-300 text-sm">
                        @foreach($errors->all() as $error)
                            <li class="ml-6">• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-white mb-2">Contact Support</h1>
                <p class="text-gray-300 text-lg">We're here to help with your application</p>
            </div>

            <div class="grid md:grid-cols-2 gap-8">
                <!-- Contact Methods -->
                <div class="glass-card p-8">
                    <h2 class="text-2xl font-bold text-white mb-6">Get in Touch</h2>
                    
                    <div class="space-y-6">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-blue-500/20 rounded-full flex items-center justify-center">
                                <i class="fas fa-phone text-xl text-blue-400"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-white">Phone Support</h3>
                                <p class="text-gray-300">+256 123 456 789</p>
                                <p class="text-gray-400 text-sm">Mon-Fri, 8AM-5PM EAT</p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-green-500/20 rounded-full flex items-center justify-center">
                                <i class="fas fa-envelope text-xl text-green-400"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-white">Email Support</h3>
                                <p class="text-gray-300">support@swss.com</p>
                                <p class="text-gray-400 text-sm">24/7 support</p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-purple-500/20 rounded-full flex items-center justify-center">
                                <i class="fas fa-comments text-xl text-purple-400"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-white">Live Chat</h3>
                                <p class="text-gray-300">Available now</p>
                                <p class="text-gray-400 text-sm">Instant help</p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-orange-500/20 rounded-full flex items-center justify-center">
                                <i class="fas fa-map-marker-alt text-xl text-orange-400"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-white">Office Address</h3>
                                <p class="text-gray-300">Kampala, Uganda</p>
                                <p class="text-gray-400 text-sm">By appointment only</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="glass-card p-8">
                    <h2 class="text-2xl font-bold text-white mb-6">Send us a Message</h2>
                    
                    <form action="{{ route('supplier.contact-support.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="block text-white font-semibold mb-2">Subject</label>
                            <select name="subject" required class="w-full p-3 bg-gray-700 text-white rounded-lg border border-gray-600">
                                <option value="">Select a subject</option>
                                <option value="application_status">Application Status</option>
                                <option value="pdf_validation">PDF Validation Issues</option>
                                <option value="facility_visit">Facility Visit Questions</option>
                                <option value="technical_support">Technical Support</option>
                                <option value="general_inquiry">General Inquiry</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="block text-white font-semibold mb-2">Message</label>
                            <textarea name="message" rows="6" required 
                                      class="w-full p-3 bg-gray-700 text-white rounded-lg border border-gray-600"
                                      placeholder="Please describe your issue or question..."></textarea>
                        </div>

                        <div class="mb-6">
                            <label class="block text-white font-semibold mb-2">Preferred Contact Method</label>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="radio" name="contact_method" value="email" checked class="mr-2">
                                    <span class="text-gray-300">Email</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="contact_method" value="phone" class="mr-2">
                                    <span class="text-gray-300">Phone</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <button type="submit" class="btn-primary flex-1 px-6 py-3 rounded-lg font-semibold">
                                <i class="fas fa-paper-plane mr-2"></i>
                                Send Message
                            </button>
                            <a href="{{ url()->previous() }}" class="flex-1 px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors text-center">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Back
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- FAQ Section -->
            <div class="glass-card p-8 mt-8">
                <h2 class="text-2xl font-bold text-white mb-6">Frequently Asked Questions</h2>
                
                <div class="space-y-4">
                    <div class="border border-gray-600 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-white mb-2">How long does the application process take?</h3>
                        <p class="text-gray-300">The complete process typically takes 2-4 weeks, including PDF validation and facility visit.</p>
                    </div>

                    <div class="border border-gray-600 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-white mb-2">What if my PDF validation fails?</h3>
                        <p class="text-gray-300">You can resubmit your documents with corrections. Our team will guide you through the requirements.</p>
                    </div>

                    <div class="border border-gray-600 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-white mb-2">When will the facility visit be scheduled?</h3>
                        <p class="text-gray-300">Once your PDF validation passes, we'll contact you within 3-5 business days to schedule the visit.</p>
                    </div>

                    <div class="border border-gray-600 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-white mb-2">Can I update my application information?</h3>
                        <p class="text-gray-300">Yes, you can update your business information at any time during the application process.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 