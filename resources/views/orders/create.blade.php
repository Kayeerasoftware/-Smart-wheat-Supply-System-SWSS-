<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Order - SWSS Supplier Dashboard</title>
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

        .form-input {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: white;
            transition: all 0.3s ease;
        }

        .form-input:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(79, 172, 254, 0.5);
            box-shadow: 0 0 0 3px rgba(79, 172, 254, 0.1);
        }

        .form-input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .form-select {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: white;
            transition: all 0.3s ease;
        }

        .form-select:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(79, 172, 254, 0.5);
            box-shadow: 0 0 0 3px rgba(79, 172, 254, 0.1);
        }

        .form-select option {
            background: #1a1a2e;
            color: white;
        }

        .radio-group {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .radio-option {
            position: relative;
            cursor: pointer;
        }

        .radio-option input[type="radio"] {
            position: absolute;
            opacity: 0;
            cursor: pointer;
        }

        .radio-option label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: rgba(255, 255, 255, 0.7);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .radio-option input[type="radio"]:checked + label {
            background: var(--primary-gradient);
            border-color: rgba(79, 172, 254, 0.5);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .item-row {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }

        .item-row:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.2);
        }

        .btn-remove {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
            border: none;
            border-radius: 8px;
            color: white;
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
        }

        .btn-remove:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 107, 107, 0.3);
        }

        .btn-add {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            border: none;
            border-radius: 12px;
            color: white;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
        }

        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(79, 172, 254, 0.3);
        }

        .total-section {
            background: var(--card-gradient);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 1.5rem;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.open {
                transform: translateX(0);
            }
        }
    </style>
</head>
<body>
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div id="sidebar" class="sidebar w-64 h-full fixed lg:relative z-50">
            <div class="p-6">
                <div class="flex items-center space-x-3 mb-8">
                    <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-leaf text-white"></i>
                    </div>
                    <h1 class="text-xl font-bold font-space gradient-text">SWSS</h1>
                </div>
                
                <nav class="space-y-2">
                    <a href="{{ route('supplier.dashboard') }}" class="sidebar-item flex items-center space-x-3 p-3 rounded-xl text-gray-300 hover:text-white transition-all">
                        <i class="fas fa-tachometer-alt w-5"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('orders.index') }}" class="sidebar-item active flex items-center space-x-3 p-3 rounded-xl text-white transition-all">
                        <i class="fas fa-shopping-cart w-5"></i>
                        <span>Orders</span>
                    </a>
                    <a href="{{ route('inventory.index') }}" class="sidebar-item flex items-center space-x-3 p-3 rounded-xl text-gray-300 hover:text-white transition-all">
                        <i class="fas fa-seedling w-5"></i>
                        <span>Inventory</span>
                    </a>
                    <a href="{{ route('products.index') }}" class="sidebar-item flex items-center space-x-3 p-3 rounded-xl text-gray-300 hover:text-white transition-all">
                        <i class="fas fa-tags w-5"></i>
                        <span>Products</span>
                    </a>
                    <a href="{{ route('reports.index') }}" class="sidebar-item flex items-center space-x-3 p-3 rounded-xl text-gray-300 hover:text-white transition-all">
                        <i class="fas fa-chart-bar w-5"></i>
                        <span>Reports</span>
                    </a>
                    <a href="{{ route('deliveries.index') }}" class="sidebar-item flex items-center space-x-3 p-3 rounded-xl text-gray-300 hover:text-white transition-all">
                        <i class="fas fa-truck w-5"></i>
                        <span>Deliveries</span>
                    </a>
                    <a href="{{ route('payments.index') }}" class="sidebar-item flex items-center space-x-3 p-3 rounded-xl text-gray-300 hover:text-white transition-all">
                        <i class="fas fa-credit-card w-5"></i>
                        <span>Payments</span>
                    </a>
                    <a href="{{ route('contracts.index') }}" class="sidebar-item flex items-center space-x-3 p-3 rounded-xl text-gray-300 hover:text-white transition-all">
                        <i class="fas fa-file-contract w-5"></i>
                        <span>Contracts</span>
                    </a>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col lg:ml-0">
            <!-- Header -->
            <header class="glass-card m-4 p-4 rounded-2xl">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <button id="sidebarToggle" class="lg:hidden text-white p-2 rounded-lg hover:bg-white/10">
                            <i class="fas fa-bars"></i>
                        </button>
                        <div>
                            <h1 class="text-2xl font-bold font-space text-white">Create New Order</h1>
                            <p class="text-gray-400">Add a new order to your system</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <div class="w-3 h-3 bg-red-500 rounded-full absolute -top-1 -right-1 notification-dot"></div>
                            <i class="fas fa-bell text-white text-xl"></i>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-white"></i>
                            </div>
                            <div class="hidden md:block">
                                <p class="text-white font-medium">{{ Auth::user()->name }}</p>
                                <p class="text-gray-400 text-sm">Supplier</p>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 p-4 space-y-6">
                @if(session('error'))
                    <div class="glass-card p-4 border-l-4 border-red-500">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                            <p class="text-red-300">{{ session('error') }}</p>
                        </div>
                    </div>
                @endif

                <form action="{{ route('orders.store') }}" method="POST" id="orderForm">
                    @csrf
                    
                    <!-- Order Information -->
                    <div class="glass-card p-6 rounded-2xl">
                        <h2 class="text-xl font-bold font-space mb-6 text-white">Order Information</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Customer</label>
                                <select name="customer_id" class="form-select w-full" required>
                                    <option value="">Select Customer</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->email }})</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Order Type</label>
                                <div class="radio-group">
                                    <div class="radio-option">
                                        <input type="radio" id="sale" name="order_type" value="sale" checked>
                                        <label for="sale">
                                            <i class="fas fa-shopping-cart"></i>
                                            Sale
                                        </label>
                                    </div>
                                    <div class="radio-option">
                                        <input type="radio" id="purchase" name="order_type" value="purchase">
                                        <label for="purchase">
                                            <i class="fas fa-shopping-bag"></i>
                                            Purchase
                                        </label>
                                    </div>
                                    <div class="radio-option">
                                        <input type="radio" id="return" name="order_type" value="return">
                                        <label for="return">
                                            <i class="fas fa-undo"></i>
                                            Return
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Expected Delivery Date</label>
                                <input type="date" name="expected_delivery_date" class="form-input w-full" required min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Payment Method</label>
                                <select name="payment_method" class="form-select w-full" required>
                                    <option value="">Select Payment Method</option>
                                    <option value="cash">Cash</option>
                                    <option value="credit_card">Credit Card</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="check">Check</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Address Information -->
                    <div class="glass-card p-6 rounded-2xl">
                        <h2 class="text-xl font-bold font-space mb-6 text-white">Address Information</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Shipping Address</label>
                                <textarea name="shipping_address" rows="3" class="form-input w-full" placeholder="Enter shipping address" required></textarea>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Billing Address</label>
                                <textarea name="billing_address" rows="3" class="form-input w-full" placeholder="Enter billing address" required></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="glass-card p-6 rounded-2xl">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-bold font-space text-white">Order Items</h2>
                            <button type="button" id="addItem" class="btn-add">
                                <i class="fas fa-plus mr-2"></i>
                                Add Item
                            </button>
                        </div>
                        
                        <div id="orderItems">
                            <!-- Order items will be added here dynamically -->
                        </div>
                        
                        <div class="total-section mt-6">
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                                <div>
                                    <p class="text-gray-400 text-sm">Subtotal</p>
                                    <p class="text-white font-bold text-lg" id="subtotal">$0.00</p>
                                </div>
                                <div>
                                    <p class="text-gray-400 text-sm">Tax (10%)</p>
                                    <p class="text-white font-bold text-lg" id="tax">$0.00</p>
                                </div>
                                <div>
                                    <p class="text-gray-400 text-sm">Shipping</p>
                                    <p class="text-white font-bold text-lg" id="shipping">$50.00</p>
                                </div>
                                <div>
                                    <p class="text-gray-400 text-sm">Total</p>
                                    <p class="text-white font-bold text-xl" id="total">$50.00</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="glass-card p-6 rounded-2xl">
                        <h2 class="text-xl font-bold font-space mb-6 text-white">Additional Notes</h2>
                        <textarea name="notes" rows="4" class="form-input w-full" placeholder="Enter any additional notes or special instructions"></textarea>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end space-x-4">
                        <a href="{{ route('orders.index') }}" class="px-6 py-3 bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition-all">
                            Cancel
                        </a>
                        <button type="submit" class="btn-primary px-8 py-3 rounded-xl font-semibold">
                            <i class="fas fa-save mr-2"></i>
                            Create Order
                        </button>
                    </div>
                </form>
            </main>
        </div>
    </div>

    <script>
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const addItemBtn = document.getElementById('addItem');
        const orderItemsContainer = document.getElementById('orderItems');
        const products = @json($products);

        // Sidebar toggle
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });

        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 768 && !sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                sidebar.classList.remove('open');
            }
        });

        // Add order item
        addItemBtn.addEventListener('click', () => {
            const itemIndex = orderItemsContainer.children.length;
            const itemHtml = `
                <div class="item-row" data-index="${itemIndex}">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Product</label>
                            <select name="items[${itemIndex}][product_id]" class="form-select w-full" required>
                                <option value="">Select Product</option>
                                ${products.map(product => `<option value="${product.id}" data-price="${product.price}">${product.name}</option>`).join('')}
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Quantity</label>
                            <input type="number" name="items[${itemIndex}][quantity]" class="form-input w-full" min="1" value="1" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Unit Price</label>
                            <input type="number" name="items[${itemIndex}][unit_price]" class="form-input w-full" min="0" step="0.01" required>
                        </div>
                        <div class="flex items-end">
                            <button type="button" class="btn-remove w-full" onclick="removeItem(${itemIndex})">
                                <i class="fas fa-trash mr-2"></i>
                                Remove
                            </button>
                        </div>
                    </div>
                </div>
            `;
            orderItemsContainer.insertAdjacentHTML('beforeend', itemHtml);
            updateTotals();
        });

        // Remove order item
        function removeItem(index) {
            const item = orderItemsContainer.querySelector(`[data-index="${index}"]`);
            if (item) {
                item.remove();
                updateTotals();
            }
        }

        // Update totals
        function updateTotals() {
            let subtotal = 0;
            const items = orderItemsContainer.querySelectorAll('.item-row');
            
            items.forEach(item => {
                const quantity = parseFloat(item.querySelector('input[name*="[quantity]"]').value) || 0;
                const unitPrice = parseFloat(item.querySelector('input[name*="[unit_price]"]').value) || 0;
                subtotal += quantity * unitPrice;
            });
            
            const tax = subtotal * 0.1;
            const shipping = 50;
            const total = subtotal + tax + shipping;
            
            document.getElementById('subtotal').textContent = `$${subtotal.toFixed(2)}`;
            document.getElementById('tax').textContent = `$${tax.toFixed(2)}`;
            document.getElementById('shipping').textContent = `$${shipping.toFixed(2)}`;
            document.getElementById('total').textContent = `$${total.toFixed(2)}`;
        }

        // Auto-fill unit price when product is selected
        orderItemsContainer.addEventListener('change', (e) => {
            if (e.target.name && e.target.name.includes('[product_id]')) {
                const item = e.target.closest('.item-row');
                const unitPriceInput = item.querySelector('input[name*="[unit_price]"]');
                const selectedOption = e.target.options[e.target.selectedIndex];
                const price = selectedOption.dataset.price;
                if (price) {
                    unitPriceInput.value = price;
                    updateTotals();
                }
            }
        });

        // Update totals when quantity or price changes
        orderItemsContainer.addEventListener('input', (e) => {
            if (e.target.name && (e.target.name.includes('[quantity]') || e.target.name.includes('[unit_price]'))) {
                updateTotals();
            }
        });

        // Add first item on page load
        document.addEventListener('DOMContentLoaded', () => {
            addItemBtn.click();
        });
    </script>
</body>
</html> 