<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SWSS Supplier Dashboard - Create Inventory</title>
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

        .sidebar {
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(20px);
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .sidebar-item {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .sidebar-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: var(--primary-gradient);
            transition: left 0.3s ease;
            z-index: -1;
        }

        .sidebar-item:hover::before,
        .sidebar-item.active::before {
            left: 0;
        }

        .sidebar-item:hover,
        .sidebar-item.active {
            color: white;
            transform: translateX(5px);
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
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        .notification-dot {
            background: var(--secondary-gradient);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                position: fixed;
                z-index: 50;
                height: 100vh;
            }
            
            .sidebar.open {
                transform: translateX(0);
            }
        }

        .logo-pulse {
            animation: logoPulse 2s ease-in-out infinite;
        }

        @keyframes logoPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
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

        .form-input {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            backdrop-filter: blur(10px);
        }

        .form-input:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(79, 172, 254, 0.5);
            box-shadow: 0 0 0 3px rgba(79, 172, 254, 0.1);
        }

        .form-input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .radio-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .radio-card:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .radio-card.selected {
            background: rgba(79, 172, 254, 0.2);
            border-color: rgba(79, 172, 254, 0.5);
            box-shadow: 0 0 0 3px rgba(79, 172, 254, 0.1);
        }

        .alert {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border-color: rgba(239, 68, 68, 0.3);
        }

        .alert-success {
            background: rgba(34, 197, 94, 0.1);
            border-color: rgba(34, 197, 94, 0.3);
        }
    </style>
</head>
<body class="text-white overflow-x-hidden">
    <!-- Top Navigation -->
    <nav class="glass-card mx-4 mt-4 mb-6 px-6 py-4 flex justify-between items-center">
        <div class="flex items-center space-x-4">
            <button class="md:hidden text-white" id="sidebarToggle">
                <i class="fas fa-bars text-xl"></i>
            </button>
            <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-400 to-purple-500 flex items-center justify-center logo-pulse">
                <span class="text-white font-bold">S</span>
            </div>
            <div>
                <h1 class="text-xl font-bold font-space gradient-text">SWSS Supplier</h1>
                <p class="text-xs text-gray-400">Wheat Supply Chain Management</p>
            </div>
        </div>
        
        <div class="flex items-center space-x-6">
            <div class="relative">
                <i class="fas fa-bell text-gray-300 text-xl cursor-pointer hover:text-white transition-colors"></i>
                <span class="notification-dot absolute -top-1 -right-1 w-3 h-3 rounded-full"></span>
            </div>
            <div class="flex items-center space-x-3">
                <div class="text-right">
                    <p class="text-sm font-semibold">John Supplier</p>
                    <p class="text-xs text-gray-400">Supplier Portal</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-gradient-to-r from-purple-400 to-pink-400 flex items-center justify-center">
                    <span class="text-white font-bold text-sm">JS</span>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex">
        <!-- Sidebar -->
        <aside class="sidebar w-64 min-h-screen p-6" id="sidebar">
            <nav class="space-y-2">
                <a href="{{ route('supplier.dashboard') }}" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300">
                    <i class="fas fa-tachometer-alt w-5"></i>
                    <span class="font-medium">Dashboard</span>
                </a>
                <a href="{{ route('inventory.index') }}" class="sidebar-item active flex items-center space-x-3 px-4 py-3 rounded-lg text-white">
                    <i class="fas fa-seedling w-5"></i>
                    <span class="font-medium">Inventory</span>
                </a>
                <a href="#" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300">
                    <i class="fas fa-shopping-cart w-5"></i>
                    <span class="font-medium">Orders</span>
                </a>
                <a href="#" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300">
                    <i class="fas fa-truck w-5"></i>
                    <span class="font-medium">Deliveries</span>
                </a>
                <a href="/reports" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300">
                    <i class="fas fa-chart-bar w-5"></i>
                    <span class="font-medium">Reports</span>
                </a>
                <a href="/contracts" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300">
                    <i class="fas fa-handshake w-5"></i>
                    <span class="font-medium">Contracts</span>
                </a>
                <a href="/payments" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300">
                    <i class="fas fa-dollar-sign w-5"></i>
                    <span class="font-medium">Payments</span>
                </a>
                <a href="/profile-settings" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-300">
                    <i class="fas fa-user-cog w-5"></i>
                    <span class="font-medium">Profile Settings</span>
                </a>
                
                <div class="pt-6 mt-6 border-t border-gray-700">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="sidebar-item flex items-center space-x-3 px-4 py-3 rounded-lg text-red-400 hover:text-red-300 w-full text-left">
                            <i class="fas fa-sign-out-alt w-5"></i>
                            <span class="font-medium">Logout</span>
                        </button>
                    </form>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-6">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                        <h1 class="text-3xl font-bold font-space gradient-text">Create Inventory Adjustment</h1>
                        <p class="mt-2 text-sm text-gray-400">Add new inventory or adjust existing stock levels</p>
                </div>
                    <a href="{{ route('inventory.index') }}" class="btn-secondary inline-flex items-center px-6 py-3 rounded-xl font-semibold text-sm text-white uppercase tracking-wider">
                        <i class="fas fa-arrow-left mr-2"></i>
                    Back to Inventory
                </a>
            </div>
        </div>

        <!-- Error Messages -->
        @if($errors->any())
                <div class="mb-6 alert alert-error rounded-xl p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-400 text-xl"></i>
                    </div>
                    <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-300">There were errors with your submission</h3>
                            <div class="mt-2 text-sm text-red-200">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Success Messages -->
        @if(session('success'))
                <div class="mb-6 alert alert-success rounded-xl p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-400 text-xl"></i>
                    </div>
                    <div class="ml-3">
                            <p class="text-sm font-medium text-green-300">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('inventory.store') }}" method="POST" class="space-y-8">
            @csrf
            
            <!-- Adjustment Type Selection -->
                <div class="glass-card p-6 rounded-2xl">
                    <h3 class="text-lg font-medium font-space mb-6">Adjustment Type</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <label class="radio-card relative flex cursor-pointer rounded-xl p-4 transition-all duration-300">
                            <input type="radio" name="adjustment_type" value="receipt" {{ old('adjustment_type') == 'receipt' ? 'checked' : '' }} class="sr-only" required>
                            <span class="flex flex-1">
                                <span class="flex flex-col">
                                    <span class="block text-sm font-medium text-white">Receipt</span>
                                    <span class="mt-1 flex items-center text-sm text-gray-400">Add new stock to inventory</span>
                                </span>
                            </span>
                            <div class="absolute inset-0 rounded-xl border-2 border-transparent transition-all duration-300"></div>
                        </label>

                        <label class="radio-card relative flex cursor-pointer rounded-xl p-4 transition-all duration-300">
                            <input type="radio" name="adjustment_type" value="issue" {{ old('adjustment_type') == 'issue' ? 'checked' : '' }} class="sr-only">
                            <span class="flex flex-1">
                                <span class="flex flex-col">
                                    <span class="block text-sm font-medium text-white">Issue</span>
                                    <span class="mt-1 flex items-center text-sm text-gray-400">Remove stock from inventory</span>
                                </span>
                            </span>
                            <div class="absolute inset-0 rounded-xl border-2 border-transparent transition-all duration-300"></div>
                        </label>

                        <label class="radio-card relative flex cursor-pointer rounded-xl p-4 transition-all duration-300">
                            <input type="radio" name="adjustment_type" value="transfer" {{ old('adjustment_type') == 'transfer' ? 'checked' : '' }} class="sr-only">
                            <span class="flex flex-1">
                                <span class="flex flex-col">
                                    <span class="block text-sm font-medium text-white">Transfer</span>
                                    <span class="mt-1 flex items-center text-sm text-gray-400">Move stock between locations</span>
                                </span>
                            </span>
                            <div class="absolute inset-0 rounded-xl border-2 border-transparent transition-all duration-300"></div>
                        </label>

                        <label class="radio-card relative flex cursor-pointer rounded-xl p-4 transition-all duration-300">
                            <input type="radio" name="adjustment_type" value="adjustment" {{ old('adjustment_type') == 'adjustment' ? 'checked' : '' }} class="sr-only">
                            <span class="flex flex-1">
                                <span class="flex flex-col">
                                    <span class="block text-sm font-medium text-white">Adjustment</span>
                                    <span class="mt-1 flex items-center text-sm text-gray-400">Set exact stock level</span>
                                </span>
                            </span>
                            <div class="absolute inset-0 rounded-xl border-2 border-transparent transition-all duration-300"></div>
                        </label>
                </div>
            </div>

            <!-- Product and Warehouse Selection -->
                <div class="glass-card p-6 rounded-2xl">
                    <h3 class="text-lg font-medium font-space mb-6">Product & Location</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="product_id" class="block text-sm font-medium text-gray-300 mb-2">Product</label>
                            <select name="product_id" id="product_id" required class="form-input w-full rounded-lg px-3 py-2">
                                <option value="">Select a product</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }} ({{ $product->sku }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="warehouse_id" class="block text-sm font-medium text-gray-300 mb-2">Warehouse</label>
                            <select name="warehouse_id" id="warehouse_id" required class="form-input w-full rounded-lg px-3 py-2">
                                <option value="">Select a warehouse</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                        {{ $warehouse->name }}
                                    </option>
                                @endforeach
                            </select>
                    </div>
                </div>
            </div>

            <!-- Quantity and Details -->
                <div class="glass-card p-6 rounded-2xl">
                    <h3 class="text-lg font-medium font-space mb-6">Quantity & Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="quantity" class="block text-sm font-medium text-gray-300 mb-2">Quantity</label>
                            <input type="number" name="quantity" id="quantity" step="0.01" min="0.01" 
                                   class="form-input w-full rounded-lg px-3 py-2" 
                                   placeholder="Enter quantity" value="{{ old('quantity') }}" required>
                        </div>

                        <div>
                            <label for="reference_number" class="block text-sm font-medium text-gray-300 mb-2">Reference Number</label>
                            <input type="text" name="reference_number" id="reference_number" 
                                   class="form-input w-full rounded-lg px-3 py-2" 
                                   placeholder="PO, Invoice, etc." value="{{ old('reference_number') }}">
                        </div>

                        <div>
                            <label for="batch_number" class="block text-sm font-medium text-gray-300 mb-2">Batch Number</label>
                            <input type="text" name="batch_number" id="batch_number" 
                                   class="form-input w-full rounded-lg px-3 py-2" 
                                   placeholder="Batch/Lot number" value="{{ old('batch_number') }}">
                        </div>

                        <div>
                            <label for="expiry_date" class="block text-sm font-medium text-gray-300 mb-2">Expiry Date</label>
                            <input type="date" name="expiry_date" id="expiry_date" 
                                   class="form-input w-full rounded-lg px-3 py-2" 
                                   value="{{ old('expiry_date') }}">
                        </div>
                    </div>

                    <div class="mt-6">
                        <label for="notes" class="block text-sm font-medium text-gray-300 mb-2">Notes</label>
                        <textarea name="notes" id="notes" rows="4" 
                                  class="form-input w-full rounded-lg px-3 py-2" 
                                  placeholder="Additional notes about this adjustment...">{{ old('notes') }}</textarea>
                </div>
            </div>

            <!-- Action Buttons -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('inventory.index') }}" class="btn-secondary inline-flex items-center px-6 py-3 rounded-xl font-semibold text-sm text-white uppercase tracking-wider">
                    Cancel
                </a>
                    <button type="submit" class="btn-primary inline-flex items-center px-6 py-3 rounded-xl font-semibold text-sm text-white uppercase tracking-wider">
                        <i class="fas fa-check mr-2"></i>
                    Create Adjustment
                </button>
            </div>
        </form>
        </main>
</div>

<script>
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');

        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });

        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 768 && !sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                sidebar.classList.remove('open');
            }
        });

    // Handle radio button selection styling
    const radioButtons = document.querySelectorAll('input[type="radio"]');
    radioButtons.forEach(radio => {
        radio.addEventListener('change', function() {
            // Remove selected styling from all labels
                document.querySelectorAll('.radio-card').forEach(card => {
                    card.classList.remove('selected');
                    const border = card.querySelector('div');
                    border.classList.remove('border-blue-400');
                    border.classList.add('border-transparent');
            });
            
            // Add selected styling to the checked radio's label
            if (this.checked) {
                    const card = this.closest('.radio-card');
                    card.classList.add('selected');
                    const border = card.querySelector('div');
                    border.classList.remove('border-transparent');
                    border.classList.add('border-blue-400');
            }
        });
    });

    // Set initial styling for checked radio buttons
    radioButtons.forEach(radio => {
        if (radio.checked) {
                const card = radio.closest('.radio-card');
                card.classList.add('selected');
                const border = card.querySelector('div');
                border.classList.remove('border-transparent');
                border.classList.add('border-blue-400');
            }
        });

        // Add hover effects to form inputs
        document.querySelectorAll('.form-input').forEach(input => {
            input.addEventListener('focus', () => {
                input.parentElement.classList.add('ring-2', 'ring-blue-400', 'ring-opacity-50');
            });
            
            input.addEventListener('blur', () => {
                input.parentElement.classList.remove('ring-2', 'ring-blue-400', 'ring-opacity-50');
    });
});
</script>
</body>
</html> 