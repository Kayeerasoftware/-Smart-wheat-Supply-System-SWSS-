<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SWSS - Update Information</title>
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
        <div class="max-w-2xl mx-auto">
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
                <h1 class="text-4xl font-bold text-white mb-2">Update Information</h1>
                <p class="text-gray-300 text-lg">Keep your application details current</p>
            </div>

            <div class="glass-card p-8">
                <form action="{{ route('supplier.update-info.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-6">
                        <label class="block text-white font-semibold mb-2">Business Name</label>
                        <input type="text" name="business_name" 
                               value="{{ $vendor->application_data['business_name'] ?? '' }}"
                               class="w-full p-3 bg-gray-700 text-white rounded-lg border border-gray-600"
                               placeholder="Enter your business name">
                    </div>

                    <div class="mb-6">
                        <label class="block text-white font-semibold mb-2">Business Type</label>
                        <select name="business_type" class="w-full p-3 bg-gray-700 text-white rounded-lg border border-gray-600">
                            <option value="">Select business type</option>
                            <option value="wholesaler" {{ ($vendor->application_data['business_type'] ?? '') === 'wholesaler' ? 'selected' : '' }}>Wholesaler</option>
                            <option value="distributor" {{ ($vendor->application_data['business_type'] ?? '') === 'distributor' ? 'selected' : '' }}>Distributor</option>
                            <option value="processor" {{ ($vendor->application_data['business_type'] ?? '') === 'processor' ? 'selected' : '' }}>Processor</option>
                            <option value="storage" {{ ($vendor->application_data['business_type'] ?? '') === 'storage' ? 'selected' : '' }}>Storage</option>
                            <option value="other" {{ ($vendor->application_data['business_type'] ?? '') === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <div class="mb-6">
                        <label class="block text-white font-semibold mb-2">Business Description</label>
                        <textarea name="business_description" rows="4" 
                                  class="w-full p-3 bg-gray-700 text-white rounded-lg border border-gray-600"
                                  placeholder="Describe your business operations...">{{ $vendor->application_data['business_description'] ?? '' }}</textarea>
                    </div>

                    <div class="mb-6">
                        <label class="block text-white font-semibold mb-2">Phone Number</label>
                        <input type="tel" name="phone" 
                               value="{{ $user->phone ?? '' }}"
                               class="w-full p-3 bg-gray-700 text-white rounded-lg border border-gray-600"
                               placeholder="Enter your phone number">
                    </div>

                    <div class="mb-6">
                        <label class="block text-white font-semibold mb-2">Address</label>
                        <textarea name="address" rows="3" 
                                  class="w-full p-3 bg-gray-700 text-white rounded-lg border border-gray-600"
                                  placeholder="Enter your business address">{{ $user->address ?? '' }}</textarea>
                    </div>

                    <div class="mb-6">
                        <label class="block text-white font-semibold mb-2">Registration Number</label>
                        <input type="text" name="registration_number" value="{{ $vendor->application_data['registration_number'] ?? '' }}" class="w-full p-3 bg-gray-700 text-white rounded-lg border border-gray-600" placeholder="Enter your registration number">
                    </div>
                    <div class="mb-6">
                        <label class="block text-white font-semibold mb-2">Founded Year</label>
                        <input type="number" name="founded_year" value="{{ $vendor->application_data['founded_year'] ?? '' }}" class="w-full p-3 bg-gray-700 text-white rounded-lg border border-gray-600" placeholder="Enter the year your business was founded" min="1900" max="{{ date('Y') }}">
                    </div>
                    <div class="mb-6">
                        <label class="block text-white font-semibold mb-2">Annual Revenue</label>
                        <input type="text" name="annual_revenue" value="{{ $vendor->application_data['annual_revenue'] ?? '' }}" class="w-full p-3 bg-gray-700 text-white rounded-lg border border-gray-600" placeholder="Enter your annual revenue">
                    </div>
                    <div class="mb-6">
                        <label class="block text-white font-semibold mb-2">City</label>
                        <input type="text" name="city" value="{{ $vendor->application_data['city'] ?? '' }}" class="w-full p-3 bg-gray-700 text-white rounded-lg border border-gray-600" placeholder="Enter your city">
                    </div>
                    <div class="mb-6">
                        <label class="block text-white font-semibold mb-2">Country</label>
                        <input type="text" name="country" value="{{ $vendor->application_data['country'] ?? '' }}" class="w-full p-3 bg-gray-700 text-white rounded-lg border border-gray-600" placeholder="Enter your country">
                    </div>

                    <div class="mb-6">
                        <label class="block text-white font-semibold mb-2">Update Business Image (Optional)</label>
                        <input type="file" name="business_image" accept="image/*"
                               class="w-full p-3 bg-gray-700 text-white rounded-lg border border-gray-600">
                        <p class="text-gray-400 text-sm mt-1">Upload a new business image if needed (Max size: 10MB, Formats: JPEG, PNG, JPG, GIF)</p>
                    </div>

                    <div class="flex gap-4">
                        <button type="submit" class="btn-primary flex-1 px-6 py-3 rounded-lg font-semibold">
                            <i class="fas fa-save mr-2"></i>
                            Update Information
                        </button>
                        <a href="{{ url()->previous() }}" class="flex-1 px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors text-center">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Cancel
                        </a>
                    </div>
                </form>
            </div>

            <!-- Current Information -->
            <div class="glass-card p-8 mt-8">
                <h2 class="text-2xl font-bold text-white mb-6">Current Information</h2>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-semibold text-white mb-3">Business Details</h3>
                        <div class="space-y-2 text-gray-300">
                            <p><strong>Business Name:</strong> {{ $vendor->application_data['business_name'] ?? 'Not provided' }}</p>
                            <p><strong>Business Type:</strong> {{ ucfirst($vendor->application_data['business_type'] ?? 'Not specified') }}</p>
                            <p><strong>Registration Date:</strong> {{ $vendor->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-semibold text-white mb-3">Contact Information</h3>
                        <div class="space-y-2 text-gray-300">
                            <p><strong>Email:</strong> {{ $user->email }}</p>
                            <p><strong>Phone:</strong> {{ $user->phone ?? 'Not provided' }}</p>
                            <p><strong>Address:</strong> {{ $user->address ?? 'Not provided' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 