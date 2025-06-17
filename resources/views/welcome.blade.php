<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Wheat Supply System (SWSS)</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
            overflow-x: hidden;
        }

        .font-space {
            font-family: 'Space Grotesk', sans-serif;
        }

        .glass-nav {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .glass-nav.scrolled {
            background: rgba(255, 255, 255, 0.12);
            border-color: rgba(255, 255, 255, 0.2);
        }

        .hero-bg {
            background: transparent;
            position: relative;
            overflow: hidden;
        }

        .hero-bg::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("{{ asset('images/1.jpg') }}") center/cover no-repeat;
            z-index: -1;
            animation: slowFloat 20s ease-in-out infinite;
        }

        .hero-bg::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.3);
            z-index: 1;
        }

        @keyframes slowFloat {
            0%, 100% { transform: scale(1) rotate(0deg); }
            50% { transform: scale(1.05) rotate(1deg); }
        }

        .gradient-text {
            background: var(--accent-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
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

        .glass-card:hover {
            transform: translateY(-10px) scale(1.02);
            background: rgba(255, 255, 255, 0.12);
            border-color: rgba(255, 255, 255, 0.2);
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

        .btn-secondary {
            background: var(--accent-gradient);
            border: none;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .btn-secondary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: var(--dark-gradient);
            transition: left 0.3s ease;
            z-index: -1;
        }

        .btn-secondary:hover::before {
            left: 0;
        }

        .parallax-section {
            background: var(--dark-gradient);
            background-attachment: fixed;
            background-size: cover;
            background-position: center;
        }

        .modern-form input, .modern-form textarea {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            color: white;
            transition: all 0.3s ease;
        }

        .modern-form input::placeholder, .modern-form textarea::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .modern-form input:focus, .modern-form textarea:focus {
            background: rgba(255, 255, 255, 0.2);
            border-color: #4facfe;
            box-shadow: 0 0 20px rgba(79, 172, 254, 0.3);
            outline: none;
        }

        .fade-in {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s ease;
        }

        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .logo-pulse {
            animation: logoPulse 2s ease-in-out infinite;
        }

        .curved-logo {
            border-radius: 12px;
            width: 3.5rem;
            height: 3.5rem;
            object-fit: contain;
            transition: all 0.3s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.1);
        }

        @media (max-width: 768px) {
            .hero-text {
                font-size: 2.5rem;
            }
            .curved-logo {
                width: 3rem;
                height: 3rem;
                border-radius: 10px;
            }
        }

        @keyframes logoPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
    </style>
</head>
<body class="text-white">
    <!-- Navbar -->
    <nav class="glass-nav fixed w-full z-50 transition-all duration-300" id="navbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex items-center justify-center logo-pulse mr-3">
                        <img src="{{ asset('images/2.jpeg') }}" alt="SWSS Logo" class="curved-logo">
                    </div>
                    <span class="text-xl font-bold font-space gradient-text">SWSS</span>
                </div>
                <div class="flex items-center space-x-6">
                    <a href="#features" class="text-white/80 hover:text-white transition-colors font-medium">Features</a>
                    <a href="#about" class="text-white/80 hover:text-white transition-colors font-medium">About</a>
                    <a href="#contact" class="text-white/80 hover:text-white transition-colors font-medium">Contact</a>
                    <a href="/login" class="btn-primary text-white px-6 py-2 rounded-full font-semibold">Login</a>
                    <a href="/register" class="btn-secondary text-white px-6 py-2 rounded-full font-semibold">Register</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-bg min-h-screen flex items-center justify-center text-center text-white relative">
        <div class="relative z-10 max-w-5xl mx-auto px-4">
            <h1 class="hero-text text-5xl md:text-7xl font-bold font-space mb-6 leading-tight">
                Smart <span class="gradient-text">Wheat</span><br>Supply <span class="gradient-text">Chain</span> System
            </h1>
            <p class="text-xl md:text-2xl mb-8 text-white/90 max-w-3xl mx-auto leading-relaxed">
                Revolutionary AI-powered platform that transforms wheat supply chains with 
                <span class="font-semibold text-cyan-300">real-time analytics</span>, 
                <span class="font-semibold text-purple-300">demand forecasting</span>, and 
                <span class="font-semibold text-pink-300">seamless automation</span>
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="/register" class="btn-primary text-white px-8 py-4 rounded-full text-lg font-semibold transform hover:scale-105 transition-all">
                    Start Your Journey
                </a>
                <a href="#features" class="text-white/80 hover:text-white border-2 border-white/30 hover:border-white px-8 py-4 rounded-full text-lg font-semibold transition-all">
                    Explore Features
                </a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 fade-in">
                <h2 class="text-4xl md:text-5xl font-bold font-space mb-4">
                    Cutting-Edge <span class="gradient-text">Features</span>
                </h2>
                <p class="text-xl text-gray-400 max-w-3xl mx-auto">
                    Experience the future of supply chain management with our revolutionary platform
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="glass-card p-8 fade-in">
                    <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-tachometer-alt text-2xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 font-space">Smart Order Management</h3>
                    <p class="text-gray-300 leading-relaxed">AI-driven optimization with real-time tracking and seamless coordination.</p>
                </div>
                <div class="glass-card p-8 fade-in">
                    <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-teal-600 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-warehouse text-2xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 font-space">AI-Powered Inventory</h3>
                    <p class="text-gray-300 leading-relaxed">Predict demand and optimize stock with automated replenishment.</p>
                </div>
                <div class="glass-card p-8 fade-in">
                    <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-pink-600 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-chart-line text-2xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 font-space">Predictive Analytics</h3>
                    <p class="text-gray-300 leading-relaxed">Leverage machine learning for forecasting and decision-making.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="parallax-section py-20 relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-indigo-900/50 to-purple-900/50"></div>
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center fade-in">
                <h2 class="text-4xl md:text-5xl font-bold font-space mb-8">
                    About <span class="gradient-text">SWSS</span>
                </h2>
                <div class="max-w-4xl mx-auto">
                    <p class="text-xl md:text-2xl text-white/90 leading-relaxed mb-8">
                        The Smart Wheat Supply System (SWSS) is the next evolution in agricultural supply chain management, 
                        developed by <span class="font-semibold text-cyan-300">The WheatChain Innovators</span>.
                    </p>
                    <p class="text-lg text-white/80 leading-relaxed">
                        Using AI, machine learning, and real-time analytics, SWSS ensures efficiency, transparency, and minimal waste from farm to retail.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-20 bg-gradient-to-b from-gray-900 to-black relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 fade-in">
                <h2 class="text-4xl md:text-5xl font-bold font-space mb-4">
                    Get In <span class="gradient-text">Touch</span>
                </h2>
                <p class="text-xl text-gray-400">Ready to revolutionize your supply chain? Let's talk.</p>
            </div>
            <div class="max-w-2xl mx-auto">
                <form action="/contact" method="POST" class="modern-form space-y-6 fade-in">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <input type="text" id="name" name="name" placeholder="Your Name" class="w-full p-4 rounded-2xl" required>
                        <input type="email" id="email" name="email" placeholder="Your Email" class="w-full p-4 rounded-2xl" required>
                    </div>
                    <textarea id="message" name="message" placeholder="Tell us about your project..." class="w-full p-4 rounded-2xl" rows="6" required></textarea>
                    <div class="text-center">
                        <button type="submit" class="btn-primary text-white px-8 py-4 rounded-full text-lg font-semibold transform hover:scale-105 transition-all">
                            Send Message
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-black py-12 border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-400 mb-4 md:mb-0">© 2025 The WheatChain Innovators. All rights reserved.</p>
                <div class="flex space-x-6">
                    <a href="https://github.com/Kayearasoftware/The-Bread-Chain-Innovators-G-24.git" class="text-gray-400 hover:text-white transition-colors">GitHub</a>
                    <a href="#privacy" class="text-gray-400 hover:text-white transition-colors">Privacy Policy</a>
                    <a href="#terms" class="text-gray-400 hover:text-white transition-colors">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            });
        });

        // Navbar scroll effect
        window.addEventListener('scroll', () => {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 100) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Scroll animations
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

        document.querySelectorAll('.fade-in').forEach(el => observer.observe(el));
    </script>
</body>
</html>