<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SWSS Login</title>
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
            padding: 1rem;
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
            opacity: 0.85;
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
            max-width: 420px;
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
        
        input {
            transition: all 0.3s ease;
            backdrop-filter: blur(5px);
        }
        
        input:focus {
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
        <div class="text-center mb-8">
            <div class="logo-container">
                <img src="{{ asset('images/2.jpeg') }}" alt="SWSS Logo" class="curved-logo">
            </div>
            <h2 class="text-3xl font-space gradient-text font-bold">WHEAT SCM LOGIN</h2>
            <p class="text-gray-300 mt-2">Access your smart wheat supply dashboard</p>
        </div>
        
        @if (session('error'))
            <div class="mb-4 p-3 bg-red-600/80 text-white rounded-lg">{{ session('error') }}</div>
        @endif
        
        @if (session('success'))
            <div class="mb-4 p-3 bg-green-600/80 text-white rounded-lg">{{ session('success') }}</div>
        @endif
        
        @if (session('info'))
            <div class="mb-4 p-3 bg-blue-600/80 text-white rounded-lg">{{ session('info') }}</div>
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
        
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-5">
                <label for="email" class="block text-gray-200 mb-2 font-medium">Email Address</label>
                <input type="email" id="email" name="email" class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 placeholder-gray-400 outline-none focus:border-blue-400 @error('email') border-red-400 @enderror" placeholder="your@email.com" value="{{ old('email') }}" required>
                @error('email')<p class="text-red-300 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            
            <div class="mb-6">
                <label for="password" class="block text-gray-200 mb-2 font-medium">Password</label>
                <input type="password" id="password" name="password" class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 placeholder-gray-400 outline-none focus:border-blue-400 @error('password') border-red-400 @enderror" placeholder="••••••••" required>
                @error('password')<p class="text-red-300 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            
            <div class="flex items-center justify-between mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="remember" class="rounded bg-white/20 border-white/30 text-blue-500 focus:ring-blue-400">
                    <span class="ml-2 text-gray-300">Remember me</span>
                </label>
                <a href="{{ route('password.request') }}" class="text-gray-300 hover:text-blue-300 text-sm underline">Forgot Password?</a>
            </div>
            
            <button type="submit" class="btn-primary w-full py-3 rounded-xl font-semibold text-lg mb-4 hover:shadow-lg transition-all">
                LOGIN
            </button>
            
            <div class="text-center text-gray-300 pt-4 border-t border-white/10">
                New to SWSS? 
                <a href="{{ route('register') }}" class="text-blue-300 font-medium hover:underline">Create account</a>
            </div>
        </form>
    </div>
</body>
</html>