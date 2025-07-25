<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ManufacturerDashboardController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\LogisticsRouteController;

Route::get('/', function () {
    return view('welcome');
});

// Temporary test route for distributor dashboard
Route::get('/test-distributor', function () {
    $stats = [
        'active_routes' => 12,
        'today_deliveries' => 8,
        'pending_shipments' => 3,
        'low_stock_items' => 2,
    ];
    
    $recentActivity = collect([
        (object)['action' => 'Route R001 completed', 'created_at' => now()->subMinutes(2)],
        (object)['action' => 'New route R005 created', 'created_at' => now()->subMinutes(15)],
        (object)['action' => 'Route R003 delayed', 'created_at' => now()->subHour()],
    ]);
    
    return view('dashboards.distributor', compact('stats', 'recentActivity'));
});

// Temporary test route for analytics
Route::get('/test-analytics', function () {
    return view('distributor.analytics');
});

Route::get('/dashboard', function () {
    $user = Auth::user();
    $role = $user->role;

    switch ($role) {
        case 'admin':
            return redirect()->route('admin.dashboard');
        case 'farmer':
            return redirect()->route('farmer.dashboard');
        case 'supplier':
            return redirect()->route('supplier.dashboard');
        case 'manufacturer':
            return redirect()->route('manufacturer.dashboard');
        case 'distributor':
            return redirect()->route('distributor.dashboard');
        case 'retailer':
            return redirect()->route('retailer.dashboard');
        case 'vendor':
            return redirect()->route('vendor.dashboard');
        default:
            // If role is not recognized, redirect to login
            Auth::logout();
            return redirect()->route('login')->with('error', 'Invalid user role. Please contact administrator.');
    }
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');
    Route::get('/farmer/dashboard', [DashboardController::class, 'farmer'])->name('farmer.dashboard');
    Route::get('/supplier/dashboard', [DashboardController::class, 'supplier'])->name('supplier.dashboard');
    Route::get('/manufacturer/dashboard', [DashboardController::class, 'manufacturer'])->name('manufacturer.dashboard');
    Route::get('/distributor/dashboard', [DashboardController::class, 'distributor'])->name('distributor.dashboard');
    Route::get('/retailer/dashboard', [DashboardController::class, 'retailer'])->name('retailer.dashboard');
    Route::get('/vendor/dashboard', [DashboardController::class, 'vendor'])->name('vendor.dashboard');
    Route::get('/vendor/application', [App\Http\Controllers\VendorController::class, 'showApplicationForm'])->name('vendor.application');
    Route::post('/vendor/application', [App\Http\Controllers\VendorController::class, 'submitApplication'])->name('vendor.application.submit');
    
    // Admin vendor management routes
    Route::prefix('admin/vendors')->group(function () {
        Route::post('/{vendor}/schedule-visit', [App\Http\Controllers\AdminController::class, 'scheduleFacilityVisit'])->name('admin.vendors.schedule-visit');
        Route::post('/{vendor}/approve', [App\Http\Controllers\AdminController::class, 'approveVendor'])->name('admin.vendors.approve');
        Route::post('/{vendor}/reject', [App\Http\Controllers\AdminController::class, 'rejectVendor'])->name('admin.vendors.reject');
        Route::get('/{vendor}/details', [App\Http\Controllers\AdminController::class, 'viewVendorDetails'])->name('admin.vendors.details');
        Route::put('/{vendor}/scores', [App\Http\Controllers\AdminController::class, 'updateVendorScores'])->name('admin.vendors.update-scores');
    });

    // Vendor routes
    Route::prefix('vendor')->name('vendor.')->group(function () {
        Route::get('/dashboard', [VendorController::class, 'showStatus'])->name('dashboard');
        Route::get('/application', [VendorController::class, 'showApplicationForm'])->name('application');
        Route::post('/application', [VendorController::class, 'submitApplication'])->name('application.submit');
        Route::post('/upload-pdf', [VendorController::class, 'uploadNewPdf'])->name('upload-pdf');
        Route::get('/document/{type}', [VendorController::class, 'downloadDocument'])->name('document.download');
    });

    // Product Management Routes
    Route::middleware('auth')->prefix('products')->name('products.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/create', [ProductController::class, 'create'])->name('create');
        Route::post('/', [ProductController::class, 'store'])->name('store');
        Route::get('/{product}', [ProductController::class, 'show'])->name('show');
        Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
        Route::put('/{product}', [ProductController::class, 'update'])->name('update');
        Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
        Route::get('/low-stock/alerts', [ProductController::class, 'lowStockAlerts'])->name('low-stock-alerts');
        Route::get('/analytics', [ProductController::class, 'analytics'])->name('analytics');
    });

    // Category Management Routes
    Route::middleware('auth')->prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::get('/create', [CategoryController::class, 'create'])->name('create');
        Route::post('/', [CategoryController::class, 'store'])->name('store');
        Route::get('/{category}', [CategoryController::class, 'show'])->name('show');
        Route::get('/{category}/edit', [CategoryController::class, 'edit'])->name('edit');
        Route::put('/{category}', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('destroy');
        Route::get('/tree', [CategoryController::class, 'tree'])->name('tree');
        Route::get('/analytics', [CategoryController::class, 'analytics'])->name('analytics');
    });

    // Warehouse Management Routes
    Route::middleware('auth')->prefix('warehouses')->name('warehouses.')->group(function () {
        Route::get('/', [WarehouseController::class, 'index'])->name('index');
        Route::get('/create', [WarehouseController::class, 'create'])->name('create');
        Route::post('/', [WarehouseController::class, 'store'])->name('store');
        Route::get('/{warehouse}', [WarehouseController::class, 'show'])->name('show');
        Route::get('/{warehouse}/edit', [WarehouseController::class, 'edit'])->name('edit');
        Route::put('/{warehouse}', [WarehouseController::class, 'update'])->name('update');
        Route::delete('/{warehouse}', [WarehouseController::class, 'destroy'])->name('destroy');
        Route::get('/analytics', [WarehouseController::class, 'analytics'])->name('analytics');
        Route::get('/{warehouse}/inventory-report', [WarehouseController::class, 'inventoryReport'])->name('inventory-report');
    });

    // Inventory Management Routes
    Route::middleware('auth')->prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('index');
        Route::get('/create', [InventoryController::class, 'create'])->name('create');
        Route::post('/', [InventoryController::class, 'store'])->name('store');
        Route::get('/{inventory}', [InventoryController::class, 'show'])->name('show');
        Route::get('/{inventory}/edit', [InventoryController::class, 'edit'])->name('edit');
        Route::put('/{inventory}', [InventoryController::class, 'update'])->name('update');
        Route::delete('/{inventory}', [InventoryController::class, 'destroy'])->name('destroy');
        Route::get('/low-stock/alerts', [InventoryController::class, 'lowStockAlerts'])->name('low-stock-alerts');
        Route::get('/analytics', [InventoryController::class, 'analytics'])->name('analytics');
        Route::post('/bulk-adjustment', [InventoryController::class, 'bulkAdjustment'])->name('bulk-adjustment');
    });

    // Supplier Orders Routes
    Route::middleware('auth')->prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/create', [OrderController::class, 'create'])->name('create');
        Route::post('/', [OrderController::class, 'store'])->name('store');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
        Route::get('/{order}/edit', [OrderController::class, 'edit'])->name('edit');
        Route::put('/{order}', [OrderController::class, 'update'])->name('update');
        Route::delete('/{order}', [OrderController::class, 'destroy'])->name('destroy');
    });

    // Supplier Deliveries Routes
    Route::middleware('auth')->prefix('deliveries')->name('deliveries.')->group(function () {
        Route::get('/', [DashboardController::class, 'deliveries'])->name('index');
    });

    // Supplier Reports Routes
    Route::middleware('auth')->prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [DashboardController::class, 'reports'])->name('index');
        Route::get('/export/pdf', [DashboardController::class, 'exportPdf'])->name('export.pdf');
        Route::get('/export/excel', [DashboardController::class, 'exportExcel'])->name('export.excel');
        Route::get('/export/csv', [DashboardController::class, 'exportCsv'])->name('export.csv');
        Route::get('/generate', [ReportController::class, 'showReportPage'])->name('generate');
        Route::get('/delivery', [ReportController::class, 'generateDeliveryReport'])->name('delivery');
        Route::post('/email', [ReportController::class, 'sendEmailReport'])->name('email');
    });

    // Supplier Contracts Routes
    Route::middleware('auth')->prefix('contracts')->name('contracts.')->group(function () {
        Route::get('/', [DashboardController::class, 'contracts'])->name('index');
    });

    // Supplier Payments Routes
    Route::middleware('auth')->prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [DashboardController::class, 'payments'])->name('index');
    });

    // Supplier Profile Settings Routes
    Route::middleware('auth')->prefix('profile-settings')->name('profile-settings.')->group(function () {
        Route::get('/', [DashboardController::class, 'profileSettings'])->name('index');
        Route::post('/save', [DashboardController::class, 'saveProfileSettings'])->name('save');
    });

    // Notification Routes
    Route::middleware('auth')->prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'getNotifications'])->name('index');
        Route::get('/unread-count', [NotificationController::class, 'getUnreadCount'])->name('unread-count');
        Route::patch('/{id}/read', [NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::patch('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
    });

    // Chat Routes
    Route::middleware('auth')->prefix('chat')->name('chat.')->group(function () {
        Route::get('/', [ChatController::class, 'index'])->name('index');
        Route::post('/send-message', [ChatController::class, 'sendMessage'])->name('send-message');
        Route::get('/user/{id}', [ChatController::class, 'getUserDetails'])->name('user-details');
        Route::get('/online-users', [ChatController::class, 'getOnlineUsers'])->name('online-users');
        // New chat API routes
        Route::get('/contacts', [ChatController::class, 'listChatUsers'])->name('contacts');
        Route::get('/messages/{userId}', [ChatController::class, 'fetchMessages'])->name('fetch-messages');
        Route::get('/unread-counts', [ChatController::class, 'getUnreadCounts'])->name('unread-counts');
        Route::post('/typing', [ChatController::class, 'setTyping'])->name('typing');
        Route::get('/typing', [ChatController::class, 'getTyping'])->name('get-typing');
    });

    // Admin routes
    Route::get('/vendors', [\App\Http\Controllers\DashboardController::class, 'vendors'])->name('vendors');
    Route::get('/admin/vendors', [App\Http\Controllers\AdminController::class, 'vendors'])->name('admin.vendors');
    Route::get('/admin/analytics', [App\Http\Controllers\AdminController::class, 'analytics'])->name('admin.analytics');

    // Admin Inventory Management Routes
    Route::middleware('auth')->prefix('admin/inventory')->name('admin.inventory.')->group(function () {
        Route::get('/', [App\Http\Controllers\AdminInventoryController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\AdminInventoryController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\AdminInventoryController::class, 'store'])->name('store');
        Route::get('/{inventory}', [App\Http\Controllers\AdminInventoryController::class, 'show'])->name('show');
        Route::get('/{inventory}/edit', [App\Http\Controllers\AdminInventoryController::class, 'edit'])->name('edit');
        Route::put('/{inventory}', [App\Http\Controllers\AdminInventoryController::class, 'update'])->name('update');
        Route::delete('/{inventory}', [App\Http\Controllers\AdminInventoryController::class, 'destroy'])->name('destroy');
        Route::get('/low-stock/alerts', [App\Http\Controllers\AdminInventoryController::class, 'lowStockAlerts'])->name('low-stock-alerts');
        Route::get('/analytics', [App\Http\Controllers\AdminInventoryController::class, 'analytics'])->name('analytics');
    });

    // Admin Supply Chain Management Routes
    Route::middleware('auth')->prefix('admin/supply-chain')->name('admin.supply-chain.')->group(function () {
        Route::get('/', [App\Http\Controllers\AdminSupplyChainController::class, 'index'])->name('index');
        Route::get('/orders', [App\Http\Controllers\AdminSupplyChainController::class, 'orders'])->name('orders');
        Route::get('/purchase-orders', [App\Http\Controllers\AdminSupplyChainController::class, 'purchaseOrders'])->name('purchase-orders');
        Route::get('/shipments', [App\Http\Controllers\AdminSupplyChainController::class, 'shipments'])->name('shipments');
        Route::get('/manufacturing-orders', [App\Http\Controllers\AdminSupplyChainController::class, 'manufacturingOrders'])->name('manufacturing-orders');
        Route::get('/analytics', [App\Http\Controllers\AdminSupplyChainController::class, 'analytics'])->name('analytics');
    });

    // Admin System Settings Routes
    Route::middleware('auth')->prefix('admin/system-settings')->name('admin.system-settings.')->group(function () {
        Route::get('/', [App\Http\Controllers\AdminSystemSettingsController::class, 'index'])->name('index');
        Route::get('/users', [App\Http\Controllers\AdminSystemSettingsController::class, 'users'])->name('users');
        Route::get('/configuration', [App\Http\Controllers\AdminSystemSettingsController::class, 'configuration'])->name('configuration');
        Route::get('/security', [App\Http\Controllers\AdminSystemSettingsController::class, 'security'])->name('security');
        Route::post('/security/scan', [App\Http\Controllers\AdminSystemSettingsController::class, 'runSecurityScan'])->name('run-security-scan');
        Route::get('/audit-logs', [App\Http\Controllers\AdminSystemSettingsController::class, 'auditLogs'])->name('audit-logs');
        
        // User management actions
        Route::patch('/users/{user}/role', [App\Http\Controllers\AdminSystemSettingsController::class, 'updateUserRole'])->name('update-user-role');
        Route::patch('/users/{user}/status', [App\Http\Controllers\AdminSystemSettingsController::class, 'toggleUserStatus'])->name('toggle-user-status');
        Route::delete('/users/{user}', [App\Http\Controllers\AdminSystemSettingsController::class, 'deleteUser'])->name('delete-user');
        
        // Configuration actions
        Route::patch('/configuration', [App\Http\Controllers\AdminSystemSettingsController::class, 'updateConfiguration'])->name('update-configuration');
    });

    // Admin Reports Routes
    Route::middleware('auth')->prefix('admin/reports')->name('admin.reports.')->group(function () {
        Route::get('/', [App\Http\Controllers\AdminReportsController::class, 'index'])->name('index');
        Route::get('/user-report', [App\Http\Controllers\AdminReportsController::class, 'userReport'])->name('user-report');
        Route::get('/activity-report', [App\Http\Controllers\AdminReportsController::class, 'activityReport'])->name('activity-report');
        Route::get('/vendor-report', [App\Http\Controllers\AdminReportsController::class, 'vendorReport'])->name('vendor-report');
        
        // Export routes
        Route::post('/export/pdf', [App\Http\Controllers\AdminReportsController::class, 'exportPdf'])->name('export-pdf');
        Route::post('/export/excel', [App\Http\Controllers\AdminReportsController::class, 'exportExcel'])->name('export-excel');
        Route::post('/export/csv', [App\Http\Controllers\AdminReportsController::class, 'exportCsv'])->name('export-csv');
    });

    // Admin Audit Logs Routes
    Route::middleware('auth')->prefix('admin/audit-logs')->name('admin.audit-logs.')->group(function () {
        Route::get('/', [App\Http\Controllers\AdminAuditLogsController::class, 'index'])->name('index');
        Route::get('/security-logs', [App\Http\Controllers\AdminAuditLogsController::class, 'securityLogs'])->name('security-logs');
        Route::get('/user-activity-logs', [App\Http\Controllers\AdminAuditLogsController::class, 'userActivityLogs'])->name('user-activity-logs');
        Route::get('/system-logs', [App\Http\Controllers\AdminAuditLogsController::class, 'systemLogs'])->name('system-logs');
        
        // Export routes
        Route::post('/export/pdf', [App\Http\Controllers\AdminAuditLogsController::class, 'exportPdf'])->name('export-pdf');
        Route::post('/export/excel', [App\Http\Controllers\AdminAuditLogsController::class, 'exportExcel'])->name('export-excel');
        Route::post('/export/csv', [App\Http\Controllers\AdminAuditLogsController::class, 'exportCsv'])->name('export-csv');
        
        // Real-time and maintenance routes
        Route::get('/real-time', [App\Http\Controllers\AdminAuditLogsController::class, 'realTimeLogs'])->name('real-time');
        Route::post('/clear-old', [App\Http\Controllers\AdminAuditLogsController::class, 'clearOldLogs'])->name('clear-old');
    });

    Route::get('/logistics', [DashboardController::class, 'logistics'])->name('logistics');
    Route::get('/analytics', [DashboardController::class, 'analytics'])->name('analytics');
});

// Manufacturer Routes
Route::middleware(['auth', 'role:manufacturer'])->prefix('manufacturer')->name('manufacturer.')->group(function () {
    Route::get('/dashboard', [ManufacturerDashboardController::class, 'index'])->name('dashboard');
    
    // Production Lines
    Route::get('/production-lines', [ManufacturerDashboardController::class, 'productionLines'])->name('production-lines');
    Route::get('/production-lines/create', [ManufacturerDashboardController::class, 'createProductionLine'])->name('production-lines.create');
    Route::post('/production-lines', [ManufacturerDashboardController::class, 'storeProductionLine'])->name('production-lines.store');
    Route::get('/production-lines/{productionLine}/edit', [ManufacturerDashboardController::class, 'editProductionLine'])->name('production-lines.edit');
    Route::put('/production-lines/{productionLine}', [ManufacturerDashboardController::class, 'updateProductionLine'])->name('production-lines.update');
    
    // Quality Checks
    Route::get('/quality-checks', [ManufacturerDashboardController::class, 'qualityChecks'])->name('quality-checks');
    Route::get('/quality-checks/create', [ManufacturerDashboardController::class, 'createQualityCheck'])->name('quality-checks.create');
    Route::post('/quality-checks', [ManufacturerDashboardController::class, 'storeQualityCheck'])->name('quality-checks.store');
    Route::get('/quality-checks/{qualityCheck}/edit', [ManufacturerDashboardController::class, 'editQualityCheck'])->name('quality-checks.edit');
    Route::put('/quality-checks/{qualityCheck}', [ManufacturerDashboardController::class, 'updateQualityCheck'])->name('quality-checks.update');
    
    // Raw Materials
    Route::get('/raw-materials', [ManufacturerDashboardController::class, 'rawMaterials'])->name('raw-materials');
    Route::get('/raw-materials/create', [ManufacturerDashboardController::class, 'createRawMaterial'])->name('raw-materials.create');
    Route::post('/raw-materials', [ManufacturerDashboardController::class, 'storeRawMaterial'])->name('raw-materials.store');
    Route::get('/raw-materials/{rawMaterial}/edit', [ManufacturerDashboardController::class, 'editRawMaterial'])->name('raw-materials.edit');
    Route::put('/raw-materials/{rawMaterial}', [ManufacturerDashboardController::class, 'updateRawMaterial'])->name('raw-materials.update');

    // Orders
    Route::get('/orders/create', [ManufacturerDashboardController::class, 'createOrder'])->name('orders.create');
    Route::post('/orders', [ManufacturerDashboardController::class, 'storeOrder'])->name('orders.store');
});

// Temporary test route for inventory create view
Route::get('/test-inventory-create', function () {
    return view('inventory.create', [
        'products' => collect([]),
        'warehouses' => collect([])
    ]);
})->name('test.inventory.create');

Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
Route::get('/chat/user/{userId}', [ChatController::class, 'getUserDetails'])->name('chat.user.details');
Route::get('/chat/online-users', [ChatController::class, 'getOnlineUserIds'])->name('chat.online.users');

// Audit Log Export Routes
Route::get('/admin/audit-logs/export/pdf', [App\Http\Controllers\AdminAuditLogsController::class, 'exportPdf'])->name('admin.audit-logs.export.pdf');
Route::get('/admin/audit-logs/export/excel', [App\Http\Controllers\AdminAuditLogsController::class, 'exportExcel'])->name('admin.audit-logs.export.excel');
Route::get('/admin/audit-logs/export/csv', [App\Http\Controllers\AdminAuditLogsController::class, 'exportCsv'])->name('admin.audit-logs.export.csv');

Route::post('/admin/settings', [App\Http\Controllers\AdminController::class, 'updateSettings'])->name('admin.settings.update')->middleware('admin');

Route::get('/admin/security-status', [App\Http\Controllers\AdminController::class, 'securityStatus'])->name('admin.security.status')->middleware('admin');

// Admin Inventory Analytics
Route::get('/admin/inventory/analytics', [App\Http\Controllers\AdminInventoryController::class, 'analytics'])->name('admin.inventory.analytics');

Route::get('/admin/vendors/export/csv', [App\Http\Controllers\AdminController::class, 'exportVendorsCsv'])->name('admin.vendors.export.csv');

Route::get('/admin/dashboard/export/csv', [App\Http\Controllers\AdminController::class, 'exportDashboardCsv'])->name('admin.dashboard.export.csv');

Route::get('/admin/dashboard/performance-data', [App\Http\Controllers\AdminController::class, 'getSystemPerformanceData'])->name('admin.dashboard.performance-data');

// Logistics Routes Routes
Route::middleware('auth')->prefix('logistics-routes')->name('logistics-routes.')->group(function () {
    Route::get('/', [LogisticsRouteController::class, 'index'])->name('index');
    Route::post('/', [LogisticsRouteController::class, 'store'])->name('store');
    Route::get('/optimize', [LogisticsRouteController::class, 'optimize'])->name('optimize');
});

// Logistics Routes for Distributor
Route::middleware('auth')->group(function () {
    Route::get('/logistics-routes', [LogisticsRouteController::class, 'index'])->name('logistics-routes.index');
    Route::post('/logistics-routes', [LogisticsRouteController::class, 'store'])->name('logistics-routes.store');
    Route::get('/logistics-routes/optimize', [LogisticsRouteController::class, 'optimize'])->name('logistics-routes.optimize');
    Route::patch('/logistics-routes/{route}', [LogisticsRouteController::class, 'update'])->name('logistics-routes.update');
    Route::delete('/logistics-routes/{route}', [LogisticsRouteController::class, 'destroy'])->name('logistics-routes.destroy');
});

require __DIR__.'/auth.php';