<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SWSS Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --accent-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            position: relative;
            overflow: hidden;
        }
        
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("{{ asset('images/1.jpg') }}") center/cover no-repeat;
            z-index: -2;
            opacity: 0.9;
        }
        
        body::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.4);
            z-index: -1;
        }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 20px;
            width: 100%;
            max-width: 450px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        }
        
        .gradient-text {
            background: var(--accent-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .btn-primary {
            background: var(--primary-gradient);
            border: none;
            position: relative;
            overflow: hidden;
            color: #fff;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }
        
        input, select, textarea {
            transition: all 0.3s ease;
            backdrop-filter: blur(5px);
        }
        
        input:focus, select:focus, textarea:focus {
            box-shadow: 0 0 0 3px rgba(79, 172, 254, 0.3);
            background: rgba(255, 255, 255, 0.2);
        }
        
        .logo-container {
            display: flex;
            justify-content: center;
            margin-bottom: 1rem;
        }
        
        .curved-logo {
            border-radius: 12px;
            width: 5rem;
            height: 5rem;
            object-fit: contain;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.15);
            transition: all 0.3s ease;
        }
        
        .curved-logo:hover {
            transform: scale(1.05);
        }
        
        /* Custom scrollbar */
        .glass-card::-webkit-scrollbar {
            width: 6px;
        }
        
        .glass-card::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }
        
        .glass-card::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 10px;
        }
        
        .glass-card::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.4);
        }
        
        @media (max-width: 768px) {
            .curved-logo {
                width: 4rem;
                height: 4rem;
                border-radius: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="glass-card p-8 mx-4">
        <div class="text-center mb-6">
            <div class="logo-container">
                <img src="{{ asset('images/2.jpeg') }}" alt="SWSS Logo" class="curved-logo">
            </div>
            <h2 class="text-3xl font-space gradient-text font-bold">WHEAT SCM REGISTER</h2>
            <p class="text-gray-300 mt-2">Join our smart wheat supply network</p>
        </div>
        
        @if (session('error'))
            <div class="mb-4 p-3 bg-red-600/80 text-white rounded-lg">{{ session('error') }}</div>
        @endif
        
        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-600/80 text-white rounded-lg">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form method="POST" action="{{ route('register') }}">
            @csrf
            
            <div class="mb-4">
                <label for="username" class="block text-gray-200 mb-1 font-medium">Username</label>
                <input type="text" id="username" name="username" class="w-full px-4 py-3 rounded-lg bg-white/10 border border-white/20 placeholder-gray-400 outline-none focus:border-blue-400 @error('username') border-red-400 @enderror" value="{{ old('username') }}" required>
                @error('username')<p class="text-red-300 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            
            <div class="mb-4">
                <label for="email" class="block text-gray-200 mb-1 font-medium">Email</label>
                <input type="email" id="email" name="email" class="w-full px-4 py-3 rounded-lg bg-white/10 border border-white/20 placeholder-gray-400 outline-none focus:border-blue-400 @error('email') border-red-400 @enderror" value="{{ old('email') }}" required>
                @error('email')<p class="text-red-300 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            
            <div class="mb-4">
                <label for="password" class="block text-gray-200 mb-1 font-medium">Password</label>
                <input type="password" id="password" name="password" class="w-full px-4 py-3 rounded-lg bg-white/10 border border-white/20 placeholder-gray-400 outline-none focus:border-blue-400 @error('password') border-red-400 @enderror" required>
                @error('password')<p class="text-red-300 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            
            <div class="mb-4">
                <label for="password_confirmation" class="block text-gray-200 mb-1 font-medium">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="w-full px-4 py-3 rounded-lg bg-white/10 border border-white/20 placeholder-gray-400 outline-none focus:border-blue-400" required>
            </div>
            
            <div class="mb-4">
                <label for="role" class="block text-gray-200 mb-1 font-medium">Role</label>
                <select id="role" name="role" class="w-full px-4 py-3 rounded-lg bg-white/10 border border-white/20 outline-none focus:border-blue-400 @error('role') border-red-400 @enderror" required>
                    <option value="">Select Role</option>
                    <option value="farmer" {{ old('role') == 'farmer' ? 'selected' : '' }}>Farmer</option>
                    <option value="supplier" {{ old('role') == 'supplier' ? 'selected' : '' }}>Supplier</option>
                    <option value="manufacturer" {{ old('role') == 'manufacturer' ? 'selected' : '' }}>Manufacturer</option>
                    <option value="distributor" {{ old('role') == 'distributor' ? 'selected' : '' }}>Distributor</option>
                    <option value="retailer" {{ old('role') == 'retailer' ? 'selected' : '' }}>Retailer</option>
                </select>
                @error('role')<p class="text-red-300 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            
            <div class="mb-4">
                <label for="phone" class="block text-gray-200 mb-1 font-medium">Phone</label>
                <input type="tel" id="phone" name="phone" class="w-full px-4 py-3 rounded-lg bg-white/10 border border-white/20 placeholder-gray-400 outline-none focus:border-blue-400 @error('phone') border-red-400 @enderror" value="{{ old('phone') }}">
                @error('phone')<p class="text-red-300 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            
            <div class="mb-6">
                <label for="address" class="block text-gray-200 mb-1 font-medium">Address</label>
                <textarea id="address" name="address" rows="3" class="w-full px-4 py-3 rounded-lg bg-white/10 border border-white/20 placeholder-gray-400 outline-none focus:border-blue-400 @error('address') border-red-400 @enderror">{{ old('address') }}</textarea>
                @error('address')<p class="text-red-300 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            
            <button type="submit" class="btn-primary w-full py-3 rounded-xl font-semibold text-lg mb-4 hover:shadow-lg transition-all">
                REGISTER
            </button>
            
            <div class="text-center text-gray-300">
                Already have an account? 
                <a href="{{ route('login') }}" class="text-blue-300 font-medium hover:underline">Login here</a>
            </div>
        </form>
    </div>
</body>
</html>