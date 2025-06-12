<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Wheat Supply System (SWSS)</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .hero-bg {
            background-image: url('https://images.unsplash.com/photo-1507608617758-e63e19e7f3c7?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Navbar -->
    <nav class="bg-white shadow-lg fixed w-full z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <img src="https://via.placeholder.com/40" alt="SWSS Logo" class="h-10 w-10">
                    <span class="ml-3 text-xl font-bold text-gray-800">SWSS</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="#features" class="text-gray-600 hover:text-gray-900">Features</a>
                    <a href="#about" class="text-gray-600 hover:text-gray-900">About</a>
                    <a href="#contact" class="text-gray-600 hover:text-gray-900">Contact</a>
                    <a href="/login" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Login</a>
                    <a href="/register" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">Register</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-bg h-screen flex items-center justify-center text-center text-white">
        <div class="bg-black bg-opacity-50 p-8 rounded-lg max-w-3xl mx-auto">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Smart Wheat Supply System</h1>
            <p class="text-lg md:text-xl mb-6">Streamline your wheat supply chain with advanced inventory management, demand forecasting, and real-time analytics.</p>
            <a href="/register" class="bg-green-600 text-white px-6 py-3 rounded-md text-lg hover:bg-green-700">Get Started</a>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-gray-800 text-center mb-12">Key Features</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="p-6 bg-gray-50 rounded-lg shadow-md">
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Order Management</h3>
                    <p class="text-gray-600">Easily create, track, and manage orders across the supply chain with role-based access for farmers, suppliers, and retailers.</p>
                </div>
                <div class="p-6 bg-gray-50 rounded-lg shadow-md">
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Inventory Tracking</h3>
                    <p class="text-gray-600">Monitor stock levels in real-time and receive automated replenishment alerts based on demand forecasts.</p>
                </div>
                <div class="p-6 bg-gray-50 rounded-lg shadow-md">
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Machine Learning Analytics</h3>
                    <p class="text-gray-600">Leverage AI for demand forecasting and customer segmentation to optimize supply chain decisions.</p>
                </div>
                <div class="p-6 bg-gray-50 rounded-lg shadow-md">
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Vendor Validation</h3>
                    <p class="text-gray-600">Automate vendor application processing with financial and compliance checks for reliable partnerships.</p>
                </div>
                <div class="p-6 bg-gray-50 rounded-lg shadow-md">
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Real-Time Communication</h3>
                    <p class="text-gray-600">Connect stakeholders via secure chat for seamless coordination across the supply chain.</p>
                </div>
                <div class="p-6 bg-gray-50 rounded-lg shadow-md">
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Automated Reporting</h3>
                    <p class="text-gray-600">Generate tailored reports for stakeholders with insights into performance and operations.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-16 bg-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-gray-800 text-center mb-12">About SWSS</h2>
            <p class="text-lg text-gray-600 text-center max-w-3xl mx-auto">The Smart Wheat Supply System (SWSS) is designed to optimize the wheat supply chain from farm to retail. Built by The WheatChain Innovators, our platform integrates advanced analytics, machine learning, and real-time tracking to enhance efficiency and customer satisfaction.</p>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-gray-800 text-center mb-12">Get in Touch</h2>
            <form action="/contact" method="POST" class="max-w-lg mx-auto">
                <div class="mb-4">
                    <label for="name" class="block text-gray-700">Name</label>
                    <input type="text" id="name" name="name" class="w-full p-3 border rounded-md" required>
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-gray-700">Email</label>
                    <input type="email" id="email" name="email" class="w-full p-3 border rounded-md" required>
                </div>
                <div class="mb-4">
                    <label for="message" class="block text-gray-700">Message</label>
                    <textarea id="message" name="message" class="w-full p-3 border rounded-md" rows="5" required></textarea>
                </div>
                <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700">Send Message</button>
            </form>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div>
                    <p>&copy; 2025 The WheatChain Innovators. All rights reserved.</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <a href="https://github.com/Kayearasoftware/The-Bread-Chain-Innovators-G-24.git" class="text-gray-400 hover:text-white mr-4">GitHub</a>
                    <a href="#privacy" class="text-gray-400 hover:text-white mr-4">Privacy Policy</a>
                    <a href="#terms" class="text-gray-400 hover:text-white">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Smooth scroll for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html>