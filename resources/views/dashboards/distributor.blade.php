@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50">
    <!-- Header -->
    <div class="bg-white shadow-lg border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        <i class="fas fa-truck text-3xl text-indigo-600"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Distribution Hub</h1>
                        <p class="text-sm text-gray-500">Manage logistics, routes, and deliveries</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <p class="text-sm font-medium text-gray-900">Hi, {{ Auth::user()->username ?? 'Distributor' }}!</p>
                        <p class="text-xs text-gray-500">{{ now()->format('M d, Y') }}</p>
                    </div>
                    <!-- Profile Picture Upload -->
                    <div class="relative group">
                        <div class="w-12 h-12 rounded-full overflow-hidden border-2 border-white shadow-lg cursor-pointer transition-transform hover:scale-105" onclick="openProfileModal()">
                            <img id="profileImage" src="https://via.placeholder.com/48x48/6366f1/ffffff?text=U" alt="Profile" class="w-full h-full object-cover">
                        </div>
                        <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 rounded-full border-2 border-white cursor-pointer hover:bg-green-600 transition-colors" onclick="openProfileModal()" title="Upload Profile Picture">
                            <i class="fas fa-camera text-white text-xs flex items-center justify-center h-full"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Welcome Message -->
        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl shadow-lg p-6 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-white mb-2">
                        Welcome to the SWSS Distributor Dashboard
                    </h2>
                    <p class="text-indigo-100">
                        Manage your distribution operations, track deliveries, and optimize routes efficiently.
                    </p>
                </div>
                <div class="hidden md:block">
                    <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                        <i class="fas fa-shipping-fast text-white text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Active Routes</p>
                        <p class="text-3xl font-bold text-gray-900" id="activeRoutesStat">0</p>
                        <p class="text-xs text-green-600"><i class="fas fa-arrow-up"></i> +12% from last week</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-route text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Today's Deliveries</p>
                        <p class="text-3xl font-bold text-gray-900" id="todayDeliveries">0</p>
                        <p class="text-xs text-green-600"><i class="fas fa-arrow-up"></i> +8% from yesterday</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-shipping-fast text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Avg Delivery Time</p>
                        <p class="text-3xl font-bold text-gray-900">2.3<span class="text-lg text-gray-500">hrs</span></p>
                        <p class="text-xs text-red-600"><i class="fas fa-arrow-down"></i> -5% improvement</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">On-Time Rate</p>
                        <p class="text-3xl font-bold text-gray-900">94<span class="text-lg text-gray-500">%</span></p>
                        <p class="text-xs text-green-600"><i class="fas fa-arrow-up"></i> +3% this month</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-line text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column - Routes Management -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <button onclick="optimizeRoutes()" class="flex items-center justify-center space-x-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-3 rounded-lg transition-colors">
                            <i class="fas fa-route"></i>
                            <span class="text-sm font-medium">Optimize Routes</span>
                        </button>
                        <button onclick="scheduleDelivery()" class="flex items-center justify-center space-x-2 bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg transition-colors">
                            <i class="fas fa-calendar-plus"></i>
                            <span class="text-sm font-medium">Schedule Delivery</span>
                        </button>
                        <button onclick="viewAnalytics()" class="flex items-center justify-center space-x-2 bg-purple-600 hover:bg-purple-700 text-white px-4 py-3 rounded-lg transition-colors">
                            <i class="fas fa-chart-bar"></i>
                            <span class="text-sm font-medium">View Analytics</span>
                        </button>
                        <button onclick="manageInventory()" class="flex items-center justify-center space-x-2 bg-orange-600 hover:bg-orange-700 text-white px-4 py-3 rounded-lg transition-colors">
                            <i class="fas fa-boxes"></i>
                            <span class="text-sm font-medium">Manage Inventory</span>
                        </button>
                        <button onclick="generateReport()" class="flex items-center justify-center space-x-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg transition-colors">
                            <i class="fas fa-file-alt"></i>
                            <span class="text-sm font-medium">Generate Report</span>
                        </button>
                        <button onclick="trackShipments()" class="flex items-center justify-center space-x-2 bg-teal-600 hover:bg-teal-700 text-white px-4 py-3 rounded-lg transition-colors">
                            <i class="fas fa-search-location"></i>
                            <span class="text-sm font-medium">Track Shipments</span>
                        </button>
                    </div>
                </div>

                <!-- Add New Route -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Add New Route</h3>
                    <form id="routeForm" onsubmit="addRoute(event)" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="origin" class="block text-sm font-medium text-gray-700 mb-2">Origin</label>
                                <input type="text" id="origin" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter origin">
                            </div>
                            <div>
                                <label for="destination" class="block text-sm font-medium text-gray-700 mb-2">Destination</label>
                                <input type="text" id="destination" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter destination">
                            </div>
                            <div>
                                <label for="deliverer" class="block text-sm font-medium text-gray-700 mb-2">Deliverer</label>
                                <input type="text" id="deliverer" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Deliverer name">
                            </div>
                        </div>
                        <button type="submit" class="w-full md:w-auto bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg transition-colors">
                            <i class="fas fa-plus mr-2"></i>Add Route
                        </button>
                    </form>
                </div>

                <!-- Active Routes Table -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Active Routes</h3>
                        <button onclick="loadRoutes()" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">
                            <i class="fas fa-sync-alt mr-1"></i>Refresh
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Route ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Origin</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Destination</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deliverer</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ETA</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="routesTableBody" class="bg-white divide-y divide-gray-200">
                                <!-- Routes will be dynamically loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Column - Sidebar -->
            <div class="space-y-6">
                <!-- Today's Summary -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Today's Summary</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Scheduled Deliveries</span>
                            <span class="text-sm font-semibold text-gray-900" id="todayScheduled">0</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Completed Deliveries</span>
                            <span class="text-sm font-semibold text-green-600" id="todayCompleted">0</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Pending Shipments</span>
                            <span class="text-sm font-semibold text-orange-600" id="pendingShipments">0</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Low Stock Items</span>
                            <span class="text-sm font-semibold text-red-600" id="lowStockItems">0</span>
                        </div>
                    </div>
                </div>

                <!-- Quick Report Generator -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Report</h3>
                    <div class="space-y-4">
                        <div>
                            <label for="reportType" class="block text-sm font-medium text-gray-700 mb-2">Report Type</label>
                            <select id="reportType" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="delivery">Delivery Report</option>
                                <option value="inventory">Inventory Report</option>
                                <option value="route_efficiency">Route Efficiency</option>
                            </select>
                        </div>
                        <div>
                            <label for="reportEmail" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                            <input type="email" id="reportEmail" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter email">
                        </div>
                        <button onclick="sendReport()" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition-colors">
                            <i class="fas fa-paper-plane mr-2"></i>Send Report
                        </button>
                    </div>
                </div>

                <!-- Chat System -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Messages</h3>
                        <button onclick="openChatModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg text-sm transition-colors">
                            <i class="fas fa-comments mr-1"></i>Chat
                        </button>
                    </div>
                    
                    <!-- Chat Contacts -->
                    <div class="space-y-2" id="chatContacts">
                        <div class="flex items-center space-x-3 p-2 hover:bg-gray-50 rounded-lg cursor-pointer" onclick="openChat('administrator')">
                            <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-user-shield text-white text-xs"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">Administrator</p>
                                <p class="text-xs text-gray-500">Online</p>
                            </div>
                            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                        </div>
                        
                        <div class="flex items-center space-x-3 p-2 hover:bg-gray-50 rounded-lg cursor-pointer" onclick="openChat('supplier')">
                            <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-truck text-white text-xs"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">Supplier</p>
                                <p class="text-xs text-gray-500">2 new messages</p>
                            </div>
                            <div class="w-4 h-4 bg-blue-500 rounded-full flex items-center justify-center">
                                <span class="text-xs text-white font-bold">2</span>
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-3 p-2 hover:bg-gray-50 rounded-lg cursor-pointer" onclick="openChat('manufacturer')">
                            <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-industry text-white text-xs"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">Manufacturer</p>
                                <p class="text-xs text-gray-500">Last seen 1h ago</p>
                            </div>
                            <div class="w-2 h-2 bg-gray-400 rounded-full"></div>
                        </div>
                        
                        <div class="flex items-center space-x-3 p-2 hover:bg-gray-50 rounded-lg cursor-pointer" onclick="openChat('customer')">
                            <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-store text-white text-xs"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">Customer</p>
                                <p class="text-xs text-gray-500">1 new message</p>
                            </div>
                            <div class="w-4 h-4 bg-blue-500 rounded-full flex items-center justify-center">
                                <span class="text-xs text-white font-bold">1</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h3>
                    <div class="space-y-3" id="recentActivity">
                        <div class="flex items-start space-x-3">
                            <div class="w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-900">Route R001 completed</p>
                                <p class="text-xs text-gray-500">2 minutes ago</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-900">New route R005 created</p>
                                <p class="text-xs text-gray-500">15 minutes ago</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <div class="w-2 h-2 bg-yellow-500 rounded-full mt-2"></div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-900">Route R003 delayed</p>
                                <p class="text-xs text-gray-500">1 hour ago</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Profile Picture Upload Modal -->
<div id="profileModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Update Profile Picture</h3>
                <button onclick="closeProfileModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="text-center mb-6">
                <div class="w-24 h-24 mx-auto rounded-full overflow-hidden border-4 border-gray-200 mb-4">
                    <img id="previewImage" src="https://via.placeholder.com/96x96/6366f1/ffffff?text=U" alt="Preview" class="w-full h-full object-cover">
                </div>
                <p class="text-sm text-gray-600">Click to upload a new profile picture</p>
            </div>
            
            <div class="mb-4">
                <input type="file" id="profilePictureInput" accept="image/*" class="hidden" onchange="previewProfilePicture(event)">
                <button onclick="document.getElementById('profilePictureInput').click()" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 transition-colors">
                    <i class="fas fa-upload mr-2"></i>Choose Picture
                </button>
            </div>
            
            <div class="flex space-x-3">
                <button onclick="closeProfileModal()" class="flex-1 bg-gray-300 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-400 transition-colors">
                    Cancel
                </button>
                <button onclick="uploadProfilePicture()" class="flex-1 bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>Save
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Chat Modal -->
<div id="chatModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-10 mx-auto p-0 border w-full max-w-4xl h-5/6 shadow-lg rounded-lg bg-white">
        <div class="flex h-full">
            <!-- Chat Sidebar -->
            <div class="w-1/3 bg-gray-50 border-r border-gray-200 flex flex-col">
                <div class="p-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Messages</h3>
                        <button onclick="closeChatModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                
                <div class="flex-1 overflow-y-auto p-4">
                    <div class="space-y-2" id="chatContactsList">
                        <div class="chat-contact p-3 hover:bg-gray-100 rounded-lg cursor-pointer" data-contact="administrator" onclick="selectChat('administrator')">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-red-500 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user-shield text-white"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">Administrator</p>
                                    <p class="text-sm text-gray-500">System updates available</p>
                                </div>
                                <div class="text-xs text-gray-400">2m</div>
                            </div>
                        </div>
                        
                        <div class="chat-contact p-3 hover:bg-gray-100 rounded-lg cursor-pointer" data-contact="supplier" onclick="selectChat('supplier')">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                                    <i class="fas fa-truck text-white"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">Supplier</p>
                                    <p class="text-sm text-gray-500">New wheat shipment ready</p>
                                </div>
                                <div class="flex flex-col items-end">
                                    <div class="w-5 h-5 bg-blue-500 rounded-full flex items-center justify-center mb-1">
                                        <span class="text-xs text-white font-bold">2</span>
                                    </div>
                                    <div class="text-xs text-gray-400">15m</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="chat-contact p-3 hover:bg-gray-100 rounded-lg cursor-pointer" data-contact="manufacturer" onclick="selectChat('manufacturer')">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-purple-500 rounded-full flex items-center justify-center">
                                    <i class="fas fa-industry text-white"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">Manufacturer</p>
                                    <p class="text-sm text-gray-500">Production schedule update</p>
                                </div>
                                <div class="text-xs text-gray-400">1h</div>
                            </div>
                        </div>
                        
                        <div class="chat-contact p-3 hover:bg-gray-100 rounded-lg cursor-pointer" data-contact="customer" onclick="selectChat('customer')">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-orange-500 rounded-full flex items-center justify-center">
                                    <i class="fas fa-store text-white"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">Customer</p>
                                    <p class="text-sm text-gray-500">Order delivery inquiry</p>
                                </div>
                                <div class="flex flex-col items-end">
                                    <div class="w-5 h-5 bg-blue-500 rounded-full flex items-center justify-center mb-1">
                                        <span class="text-xs text-white font-bold">1</span>
                                    </div>
                                    <div class="text-xs text-gray-400">30m</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Chat Area -->
            <div class="flex-1 flex flex-col">
                <!-- Chat Header -->
                <div class="p-4 border-b border-gray-200 bg-white" id="chatHeader">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center" id="chatAvatar">
                            <i class="fas fa-user text-white"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900" id="chatContactName">Select a contact</p>
                            <p class="text-sm text-gray-500" id="chatContactStatus">to start messaging</p>
                        </div>
                    </div>
                </div>
                
                <!-- Messages Area -->
                <div class="flex-1 overflow-y-auto p-4 bg-gray-50" id="messagesArea">
                    <div class="text-center text-gray-500 mt-20">
                        <i class="fas fa-comments text-4xl mb-4"></i>
                        <p>Select a contact to start chatting</p>
                    </div>
                </div>
                
                <!-- Message Input -->
                <div class="p-4 border-t border-gray-200 bg-white" id="messageInput" style="display: none;">
                    <div class="flex space-x-3">
                        <input type="text" id="messageText" placeholder="Type your message..." class="flex-1 px-4 py-2 border border-gray-300 rounded-full focus:ring-2 focus:ring-blue-500 focus:border-blue-500" onkeypress="handleMessageKeyPress(event)">
                        <button onclick="sendMessage()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-full transition-colors">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
// Enhanced JavaScript for Distributor Dashboard

// Global variables
let routes = [];
let dashboardStats = {};

// Initialize dashboard when page loads
document.addEventListener('DOMContentLoaded', function() {
    loadDashboardData();
    loadRoutes();
    setupEventListeners();
    loadSavedProfilePicture();
});

function setupEventListeners() {
    // Auto-refresh data every 30 seconds
    setInterval(loadDashboardData, 30000);
    
    // Add form validation
    const routeForm = document.getElementById('routeForm');
    if (routeForm) {
        routeForm.addEventListener('submit', addRoute);
    }
}

function loadDashboardData() {
    // Simulate loading dashboard statistics
    // In a real app, this would fetch from your API
    updateDashboardStats({
        activeRoutes: Math.floor(Math.random() * 20) + 5,
        todayDeliveries: Math.floor(Math.random() * 15) + 3,
        todayScheduled: Math.floor(Math.random() * 25) + 8,
        todayCompleted: Math.floor(Math.random() * 12) + 2,
        pendingShipments: Math.floor(Math.random() * 8) + 1,
        lowStockItems: Math.floor(Math.random() * 5)
    });
}

function updateDashboardStats(stats) {
    dashboardStats = stats;
    
    // Update stat cards
    updateElementText('activeRoutesStat', stats.activeRoutes);
    updateElementText('todayDeliveries', stats.todayDeliveries);
    updateElementText('todayScheduled', stats.todayScheduled);
    updateElementText('todayCompleted', stats.todayCompleted);
    updateElementText('pendingShipments', stats.pendingShipments);
    updateElementText('lowStockItems', stats.lowStockItems);
}

function updateElementText(elementId, value) {
    const element = document.getElementById(elementId);
    if (element) {
        element.textContent = value;
    }
}

function renderRouteRow(route) {
    let statusBadge = getStatusBadge(route.status);
    let actionButtons = getActionButtons(route);
    
    return `
        <tr class="hover:bg-gray-50 transition-colors">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${route.route_id}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${route.origin}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${route.destination}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${route.deliverer}</td>
            <td class="px-6 py-4 whitespace-nowrap">${statusBadge}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${route.eta || '--'}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">${actionButtons}</td>
        </tr>
    `;
}

function getStatusBadge(status) {
    const badges = {
        'In Transit': '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">In Transit</span>',
        'Delayed': '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Delayed</span>',
        'Delivered': '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Delivered</span>'
    };
    return badges[status] || badges['In Transit'];
}

function getActionButtons(route) {
    let buttons = `
        <button onclick="showRouteDetails('${route.route_id}')" class="text-indigo-600 hover:text-indigo-900 mr-3">
            <i class="fas fa-eye"></i>
        </button>
    `;
    
    if (route.status !== 'Delivered') {
        buttons += `
            <button onclick="markDelivered('${route.route_id}')" class="text-green-600 hover:text-green-900 mr-3" title="Mark as Delivered">
                <i class="fas fa-check"></i>
            </button>
        `;
    }
    
    buttons += `
        <button onclick="deleteRoute('${route.route_id}')" class="text-red-600 hover:text-red-900" title="Delete Route">
            <i class="fas fa-trash"></i>
        </button>
    `;
    
    return buttons;
}

function loadRoutes() {
    const tbody = document.getElementById('routesTableBody');
    if (!tbody) return;
    
    tbody.innerHTML = '<tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">Loading routes...</td></tr>';
    
    // Simulate loading with sample data
    setTimeout(() => {
        if (!routes || routes.length === 0) {
            routes = [
                {
                    route_id: 'R001',
                    origin: 'Kampala Central',
                    destination: 'Entebbe Airport',
                    deliverer: 'John Doe',
                    status: 'In Transit',
                    eta: '2h 15m'
                },
                {
                    route_id: 'R002',
                    origin: 'Jinja Factory',
                    destination: 'Mbale Store',
                    deliverer: 'Jane Smith',
                    status: 'Pending',
                    eta: '3h 30m'
                }
            ];
        }
        
        updateElementText('activeRoutesStat', routes.filter(r => r.status !== 'Delivered').length);
        
        if (routes.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">No routes found.</td></tr>';
            return;
        }
        
        tbody.innerHTML = routes.map(renderRouteRow).join('');
    }, 800);
}

function addRoute(event) {
    event.preventDefault();
    
    const origin = document.getElementById('origin').value.trim();
    const destination = document.getElementById('destination').value.trim();
    const deliverer = document.getElementById('deliverer').value.trim();
    
    if (!origin || !destination || !deliverer) {
        showNotification('Please fill in all fields', 'error');
        return;
    }
    
    const submitButton = event.target.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Adding...';
    submitButton.disabled = true;
    
    // Simulate adding route
    setTimeout(() => {
        const newRoute = {
            route_id: 'R' + String(Math.floor(Math.random() * 1000)).padStart(3, '0'),
            origin: origin,
            destination: destination,
            deliverer: deliverer,
            status: 'Pending',
            eta: Math.floor(Math.random() * 4 + 1) + 'h ' + Math.floor(Math.random() * 60) + 'm'
        };
        
        // Add to routes array
        if (!routes) routes = [];
        routes.push(newRoute);
        
        // Update the table
        const tbody = document.getElementById('routesTableBody');
        if (tbody) {
            tbody.innerHTML = routes.map(renderRouteRow).join('');
        }
        
        // Update active routes count
        updateElementText('activeRoutesStat', routes.filter(r => r.status !== 'Delivered').length);
        
        showNotification('Route added successfully!', 'success');
        document.getElementById('routeForm').reset();
        
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
    }, 1000);
}

function optimizeRoutes() {
    const tbody = document.getElementById('routesTableBody');
    if (!tbody) {
        showNotification('Routes optimized successfully!', 'success');
        return;
    }
    
    tbody.innerHTML = '<tr><td colspan="7" class="px-6 py-4 text-center text-blue-500">Optimizing routes...</td></tr>';
    
    // Simulate optimization with sample data
    setTimeout(() => {
        const optimizedRoutes = [
            {
                route_id: 'R001',
                origin: 'Kampala Warehouse',
                destination: 'Entebbe Distribution',
                deliverer: 'John Doe',
                status: 'In Transit',
                eta: '1h 15m'
            },
            {
                route_id: 'R002',
                origin: 'Jinja Factory',
                destination: 'Mbale Store',
                deliverer: 'Jane Smith',
                status: 'In Transit',
                eta: '2h 30m'
            },
            {
                route_id: 'R003',
                origin: 'Mbarara Hub',
                destination: 'Fort Portal',
                deliverer: 'Mike Johnson',
                status: 'Delivered',
                eta: '--'
            }
        ];
        
        routes = optimizedRoutes;
        tbody.innerHTML = optimizedRoutes.map(renderRouteRow).join('');
        showNotification('Routes optimized successfully! Delivery times reduced by 15%', 'success');
        
        // Update stats
        updateElementText('activeRoutesStat', optimizedRoutes.filter(r => r.status !== 'Delivered').length);
    }, 1500);
}

function markDelivered(routeId) {
    if (!confirm('Mark this route as delivered?')) return;
    
    // Update UI immediately for better UX
    const row = document.querySelector(`tr:has(td:first-child:contains('${routeId}'))`);
    if (row) {
        const statusCell = row.querySelector('td:nth-child(5)');
        if (statusCell) {
            statusCell.innerHTML = getStatusBadge('Delivered');
        }
    }
    
    // In a real app, you would send a PATCH request to update the backend
    // fetch(`/logistics-routes/${routeId}`, { method: 'PATCH', ... })
    
    showNotification('Route marked as delivered!', 'success');
}

function deleteRoute(routeId) {
    if (!confirm('Are you sure you want to delete this route?')) return;
    
    // In a real app, you would send a DELETE request
    // For now, just remove from UI
    const row = document.querySelector(`tr:has(td:first-child:contains('${routeId}'))`);
    if (row) {
        row.remove();
        showNotification('Route deleted successfully!', 'success');
    }
}

function showRouteDetails(routeId) {
    const route = routes.find(r => r.route_id === routeId);
    if (!route) {
        showNotification('Route not found', 'error');
        return;
    }
    
    const details = `
        Route Details:
        Route ID: ${route.route_id}
        Origin: ${route.origin}
        Destination: ${route.destination}
        Deliverer: ${route.deliverer}
        Status: ${route.status}
        ETA: ${route.eta || 'Not specified'}
        Created: ${route.created_at || 'Unknown'}
    `;
    
    alert(details);
}

// Quick Action Functions
function scheduleDelivery() {
    // Create modal for scheduling delivery
    const modal = createModal('Schedule New Delivery', `
        <form id="scheduleForm" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Customer Name</label>
                <input type="text" id="customerName" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter customer name" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Address</label>
                <textarea id="deliveryAddress" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" rows="3" placeholder="Enter delivery address" required></textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Date</label>
                    <input type="date" id="deliveryDate" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                    <select id="priority" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="normal">Normal</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" onclick="closeModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</button>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg transition-colors">Schedule Delivery</button>
            </div>
        </form>
    `);
    
    document.getElementById('scheduleForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const customerName = document.getElementById('customerName').value;
        const deliveryAddress = document.getElementById('deliveryAddress').value;
        const deliveryDate = document.getElementById('deliveryDate').value;
        const priority = document.getElementById('priority').value;
        
        showNotification(`Delivery scheduled for ${customerName} on ${deliveryDate}`, 'success');
        closeModal();
        
        // Add to recent activity
        addRecentActivity(`New delivery scheduled for ${customerName}`, 'blue');
    });
}

function viewAnalytics() {
    // Redirect to analytics page
    window.location.href = '/test-analytics';
}

function manageInventory() {
    // Create inventory management modal
    const modal = createModal('Inventory Management', `
        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-red-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-red-800">Low Stock Items</h4>
                    <p class="text-2xl font-bold text-red-600">5</p>
                    <p class="text-sm text-red-600">Items need restocking</p>
                </div>
                <div class="bg-green-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-green-800">Total Inventory Value</h4>
                    <p class="text-2xl font-bold text-green-600">$45,230</p>
                    <p class="text-sm text-green-600">Current stock value</p>
                </div>
            </div>
            
            <div class="border-t pt-4">
                <h4 class="font-semibold text-gray-800 mb-3">Quick Actions</h4>
                <div class="grid grid-cols-2 gap-3">
                    <button onclick="showNotification('Stock adjustment initiated', 'info'); closeModal();" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-edit mr-2"></i>Adjust Stock
                    </button>
                    <button onclick="showNotification('Reorder alerts sent', 'success'); closeModal();" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-bell mr-2"></i>Reorder Alerts
                    </button>
                    <button onclick="showNotification('Inventory report generated', 'success'); closeModal();" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-file-alt mr-2"></i>Generate Report
                    </button>
                    <button onclick="showNotification('Audit scheduled', 'info'); closeModal();" class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-search mr-2"></i>Schedule Audit
                    </button>
                </div>
            </div>
            
            <div class="flex justify-end pt-4">
                <button onclick="closeModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Close</button>
            </div>
        </div>
    `);
}

function generateReport() {
    const modal = createModal('Generate Report', `
        <form id="reportForm" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Report Type</label>
                <select id="reportTypeModal" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="delivery">Delivery Performance Report</option>
                    <option value="inventory">Inventory Status Report</option>
                    <option value="route_efficiency">Route Efficiency Report</option>
                    <option value="customer_satisfaction">Customer Satisfaction Report</option>
                    <option value="financial">Financial Summary Report</option>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date From</label>
                    <input type="date" id="dateFrom" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date To</label>
                    <input type="date" id="dateTo" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Format</label>
                <div class="flex space-x-4">
                    <label class="flex items-center">
                        <input type="radio" name="format" value="pdf" checked class="mr-2"> PDF
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="format" value="excel" class="mr-2"> Excel
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="format" value="csv" class="mr-2"> CSV
                    </label>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email (Optional)</label>
                <input type="email" id="reportEmailModal" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Send report to email">
            </div>
            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" onclick="closeModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors">
                    <i class="fas fa-file-alt mr-2"></i>Generate Report
                </button>
            </div>
        </form>
    `);
    
    document.getElementById('reportForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const reportType = document.getElementById('reportTypeModal').value;
        const format = document.querySelector('input[name="format"]:checked').value;
        const email = document.getElementById('reportEmailModal').value;
        
        showNotification(`${reportType} report (${format.toUpperCase()}) is being generated...`, 'info');
        closeModal();
        
        setTimeout(() => {
            if (email) {
                showNotification(`Report sent to ${email}`, 'success');
            } else {
                showNotification('Report generated and ready for download', 'success');
            }
        }, 2000);
    });
}

function trackShipments() {
    const modal = createModal('Track Shipments', `
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Enter Tracking Number</label>
                <div class="flex space-x-2">
                    <input type="text" id="trackingNumber" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., TRK001234">
                    <button onclick="trackShipment()" class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            
            <div id="trackingResults" class="hidden">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-gray-800 mb-3">Shipment Status</h4>
                    <div class="space-y-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                            <div>
                                <p class="font-medium">Package Picked Up</p>
                                <p class="text-sm text-gray-500">Jan 20, 2024 - 09:30 AM</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                            <div>
                                <p class="font-medium">In Transit</p>
                                <p class="text-sm text-gray-500">Jan 20, 2024 - 11:45 AM</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-3 h-3 bg-blue-500 rounded-full animate-pulse"></div>
                            <div>
                                <p class="font-medium">Out for Delivery</p>
                                <p class="text-sm text-gray-500">Jan 21, 2024 - 08:15 AM</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-3 h-3 bg-gray-300 rounded-full"></div>
                            <div>
                                <p class="font-medium text-gray-500">Delivered</p>
                                <p class="text-sm text-gray-400">Expected: Jan 21, 2024 - 02:00 PM</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="border-t pt-4">
                <h4 class="font-semibold text-gray-800 mb-3">Recent Shipments</h4>
                <div class="space-y-2">
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium">TRK001234</p>
                            <p class="text-sm text-gray-500">To: Downtown Warehouse</p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            In Transit
                        </span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium">TRK001235</p>
                            <p class="text-sm text-gray-500">To: North Distribution Center</p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Delivered
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end pt-4">
                <button onclick="closeModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Close</button>
            </div>
        </div>
    `);
}

function sendReport() {
    const email = document.getElementById('reportEmail').value.trim();
    const reportType = document.getElementById('reportType').value;
    
    if (!email) {
        showNotification('Please enter an email address', 'error');
        return;
    }
    
    if (!isValidEmail(email)) {
        showNotification('Please enter a valid email address', 'error');
        return;
    }
    
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Sending...';
    button.disabled = true;
    
    // Simulate sending report
    setTimeout(() => {
        showNotification(`${reportType} report sent to ${email}!`, 'success');
        document.getElementById('reportEmail').value = '';
        button.innerHTML = originalText;
        button.disabled = false;
    }, 2000);
}

// Utility Functions
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full`;
    
    const colors = {
        success: 'bg-green-500 text-white',
        error: 'bg-red-500 text-white',
        info: 'bg-blue-500 text-white',
        warning: 'bg-yellow-500 text-black'
    };
    
    notification.className += ` ${colors[type] || colors.info}`;
    notification.innerHTML = `
        <div class="flex items-center space-x-2">
            <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'times' : 'info'}-circle"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// Modal Helper Functions
function createModal(title, content) {
    // Remove existing modal if any
    const existingModal = document.getElementById('customModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    const modal = document.createElement('div');
    modal.id = 'customModal';
    modal.className = 'fixed inset-0 z-50 overflow-y-auto';
    modal.innerHTML = `
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeModal()"></div>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">${title}</h3>
                        <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="mt-2">
                        ${content}
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    return modal;
}

function closeModal() {
    const modal = document.getElementById('customModal');
    if (modal) {
        modal.remove();
    }
}

function trackShipment() {
    const trackingNumber = document.getElementById('trackingNumber').value;
    if (!trackingNumber) {
        showNotification('Please enter a tracking number', 'error');
        return;
    }
    
    const resultsDiv = document.getElementById('trackingResults');
    resultsDiv.classList.remove('hidden');
    showNotification(`Tracking shipment ${trackingNumber}`, 'info');
}

function addRecentActivity(activity, color) {
    const activityContainer = document.getElementById('recentActivity');
    if (activityContainer) {
        const newActivity = document.createElement('div');
        newActivity.className = 'flex items-start space-x-3';
        newActivity.innerHTML = `
            <div class="w-2 h-2 bg-${color}-500 rounded-full mt-2"></div>
            <div class="flex-1">
                <p class="text-sm text-gray-900">${activity}</p>
                <p class="text-xs text-gray-500">Just now</p>
            </div>
        `;
        activityContainer.insertBefore(newActivity, activityContainer.firstChild);
        
        // Remove oldest activity if more than 5
        if (activityContainer.children.length > 5) {
            activityContainer.removeChild(activityContainer.lastChild);
        }
    }
}

// Profile Picture Functions
function openProfileModal() {
    document.getElementById('profileModal').classList.remove('hidden');
    // Sync preview with current profile image
    const currentImg = document.getElementById('profileImage').src;
    document.getElementById('previewImage').src = currentImg;
}

function closeProfileModal() {
    document.getElementById('profileModal').classList.add('hidden');
    // Reset file input
    document.getElementById('profilePictureInput').value = '';
}

function previewProfilePicture(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImage').src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
}

function uploadProfilePicture() {
    const fileInput = document.getElementById('profilePictureInput');
    const file = fileInput.files[0];
    
    if (!file) {
        showNotification('Please select a picture first', 'error');
        return;
    }
    
    // Simulate upload process
    const reader = new FileReader();
    reader.onload = function(e) {
        // Update main profile image
        document.getElementById('profileImage').src = e.target.result;
        
        // Store in localStorage for persistence
        localStorage.setItem('profilePicture', e.target.result);
        
        showNotification('Profile picture updated successfully!', 'success');
        closeProfileModal();
    };
    reader.readAsDataURL(file);
}

// Load saved profile picture on page load
function loadSavedProfilePicture() {
    const savedPicture = localStorage.getItem('profilePicture');
    if (savedPicture) {
        document.getElementById('profileImage').src = savedPicture;
    }
}

// Chat System Functions
let currentChat = null;
let chatMessages = {
    administrator: [
        { sender: 'administrator', message: 'System maintenance scheduled for tonight at 11 PM', time: '10:30 AM', type: 'received' },
        { sender: 'distributor', message: 'Understood. Will complete all deliveries before then.', time: '10:32 AM', type: 'sent' },
        { sender: 'administrator', message: 'Thank you for your cooperation.', time: '10:33 AM', type: 'received' }
    ],
    supplier: [
        { sender: 'supplier', message: 'New wheat shipment of 500 tons is ready for pickup', time: '9:15 AM', type: 'received' },
        { sender: 'supplier', message: 'Quality certificates are attached', time: '9:16 AM', type: 'received' },
        { sender: 'distributor', message: 'Great! When can we schedule the pickup?', time: '9:45 AM', type: 'sent' }
    ],
    manufacturer: [
        { sender: 'manufacturer', message: 'Production schedule for next week is ready', time: '8:30 AM', type: 'received' },
        { sender: 'distributor', message: 'Please send the details', time: '8:45 AM', type: 'sent' },
        { sender: 'manufacturer', message: 'Will email the production plan shortly', time: '8:47 AM', type: 'received' }
    ],
    customer: [
        { sender: 'customer', message: 'Hello! I wanted to check on my wheat order delivery status', time: '2:15 PM', type: 'received' },
        { sender: 'customer', message: 'Order #WH2024-001 was supposed to arrive today', time: '2:16 PM', type: 'received' },
        { sender: 'distributor', message: 'Let me check the status for you right away', time: '2:30 PM', type: 'sent' }
    ]
};

function openChatModal() {
    document.getElementById('chatModal').classList.remove('hidden');
}

function closeChatModal() {
    document.getElementById('chatModal').classList.add('hidden');
    currentChat = null;
}

function openChat(contact) {
    openChatModal();
    selectChat(contact);
}

function selectChat(contact) {
    currentChat = contact;
    
    // Update chat header
    const contactInfo = {
        administrator: { name: 'Administrator', status: 'Online', avatar: 'fas fa-user-shield', color: 'bg-red-500' },
        supplier: { name: 'Supplier', status: 'Online', avatar: 'fas fa-truck', color: 'bg-green-500' },
        manufacturer: { name: 'Manufacturer', status: 'Last seen 1h ago', avatar: 'fas fa-industry', color: 'bg-purple-500' },
        customer: { name: 'Customer', status: 'Online', avatar: 'fas fa-store', color: 'bg-orange-500' }
    };
    
    const info = contactInfo[contact];
    document.getElementById('chatContactName').textContent = info.name;
    document.getElementById('chatContactStatus').textContent = info.status;
    document.getElementById('chatAvatar').innerHTML = `<i class="${info.avatar} text-white"></i>`;
    document.getElementById('chatAvatar').className = `w-10 h-10 ${info.color} rounded-full flex items-center justify-center`;
    
    // Show message input
    document.getElementById('messageInput').style.display = 'block';
    
    // Load messages
    loadChatMessages(contact);
    
    // Highlight selected contact
    document.querySelectorAll('.chat-contact').forEach(el => el.classList.remove('bg-blue-100'));
    document.querySelector(`[data-contact="${contact}"]`).classList.add('bg-blue-100');
}

function loadChatMessages(contact) {
    const messagesArea = document.getElementById('messagesArea');
    const messages = chatMessages[contact] || [];
    
    if (messages.length === 0) {
        messagesArea.innerHTML = `
            <div class="text-center text-gray-500 mt-20">
                <i class="fas fa-comments text-4xl mb-4"></i>
                <p>No messages yet. Start the conversation!</p>
            </div>
        `;
        return;
    }
    
    messagesArea.innerHTML = messages.map(msg => {
        if (msg.type === 'sent') {
            return `
                <div class="flex justify-end mb-4">
                    <div class="max-w-xs lg:max-w-md px-4 py-2 bg-blue-600 text-white rounded-lg">
                        <p>${msg.message}</p>
                        <p class="text-xs text-blue-200 mt-1">${msg.time}</p>
                    </div>
                </div>
            `;
        } else {
            return `
                <div class="flex justify-start mb-4">
                    <div class="max-w-xs lg:max-w-md px-4 py-2 bg-white border border-gray-200 rounded-lg">
                        <p class="text-gray-800">${msg.message}</p>
                        <p class="text-xs text-gray-500 mt-1">${msg.time}</p>
                    </div>
                </div>
            `;
        }
    }).join('');
    
    // Scroll to bottom
    messagesArea.scrollTop = messagesArea.scrollHeight;
}

function sendMessage() {
    const messageText = document.getElementById('messageText');
    const message = messageText.value.trim();
    
    if (!message || !currentChat) return;
    
    // Add message to chat
    const newMessage = {
        sender: 'distributor',
        message: message,
        time: new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}),
        type: 'sent'
    };
    
    if (!chatMessages[currentChat]) {
        chatMessages[currentChat] = [];
    }
    
    chatMessages[currentChat].push(newMessage);
    
    // Clear input
    messageText.value = '';
    
    // Reload messages
    loadChatMessages(currentChat);
    
    // Show notification
    showNotification(`Message sent to ${currentChat}`, 'success');
    
    // Simulate response after 2 seconds
    setTimeout(() => {
        simulateResponse(currentChat);
    }, 2000);
}

function simulateResponse(contact) {
    const responses = {
        administrator: ['Message received. Will get back to you shortly.', 'Thank you for the update.', 'Noted. Proceeding as discussed.'],
        supplier: ['Confirmed. Will prepare the shipment.', 'Understood. Delivery scheduled.', 'Perfect! Everything is on track.'],
        manufacturer: ['Got it. Production team notified.', 'Thanks for the heads up.', 'Will update you on progress.'],
        customer: ['Thank you for the quick response!', 'That would be great, please keep me updated.', 'I appreciate your help with this order.']
    };
    
    const responseMessage = {
        sender: contact,
        message: responses[contact][Math.floor(Math.random() * responses[contact].length)],
        time: new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}),
        type: 'received'
    };
    
    chatMessages[contact].push(responseMessage);
    
    if (currentChat === contact) {
        loadChatMessages(contact);
    }
    
    // Update sidebar with new message indicator
    updateChatSidebar();
}

function handleMessageKeyPress(event) {
    if (event.key === 'Enter') {
        sendMessage();
    }
}

function updateChatSidebar() {
    // Update message counts and last messages in sidebar
    // This would typically sync with real-time data
}

// Add CSS for better animations
const style = document.createElement('style');
style.textContent = `
    .transition-colors {
        transition: background-color 0.2s ease, color 0.2s ease;
    }
    
    .hover\\:shadow-md:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    
    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: .5;
        }
    }
    
    #customModal {
        animation: fadeIn 0.3s ease-out;
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }
`;
document.head.appendChild(style);

</script>
@endsection