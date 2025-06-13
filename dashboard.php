<?php
session_start();
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user information
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Get pending orders count
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM orders WHERE 'status' = 'pending'");
if ($role === 'supplier') {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM orders 
                           JOIN order_items ON orders.order_id = order_items.order_id
                           JOIN products ON order_items.product_id = products.product_id
                           WHERE orders.status = 'pending' AND products.supplier_id = ?");
    $stmt->bind_param("i", $user_id);
}
$stmt->execute();
$result = $stmt->get_result();
$pending_orders = $result->fetch_assoc()['count'];

// Get inventory alerts
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM inventory 
                       JOIN products ON inventory.product_id = product_id
                       WHERE inventory.quantity < IFNULL(products.min_stock_threshold, 0)");
if ($role === 'supplier') {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM inventory 
                           JOIN products ON inventory.product_id = product_id
                           WHERE inventory.quantity < products.min_stock_threshold 
                           AND products.supplier_id = ?");
    $stmt->bind_param("i", $user_id);
}
$stmt->execute();
$result = $stmt->get_result();
$inventory_alerts = $result->fetch_assoc()['count'];

// Get recent activity
$stmt = $conn->prepare("SELECT * FROM activity_log 
                       WHERE user_id = ? OR user_id = ?
                       ORDER BY created_at DESC LIMIT 5");
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$recent_activity = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wheat SCM - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            height: 100vh;
            background-color: #343a40;
            color: white;
            position: fixed;
            width: 250px;
            padding-top: 20px;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 5px;
        }
        .sidebar .nav-link:hover {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }
        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.2);
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            transition: transform 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .activity-item {
            padding: 10px;
            border-left: 3px solid #0d6efd;
            margin-bottom: 10px;
            background-color: white;
            border-radius: 5px;
        }
        .navbar-brand {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="text-center mb-4">
            <h4>SCM</h4>
            <p class="text-muted small">Wheat Supply Chain</p>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active" href="dashboard.php">
                    <i class="bi bi-speedometer2 me-2"></i>Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="orders.php">
                    <i class="bi bi-cart me-2"></i>Orders
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="inventory.php">
                    <i class="bi bi-box-seam me-2"></i>Inventory
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="reports.php">
                    <i class="bi bi-file-earmark-bar-graph me-2"></i>Reports
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="messages.php">
                    <i class="bi bi-chat-left-text me-2"></i>Messages
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="profile.php">
                    <i class="bi bi-person me-2"></i>Profile
                </a>
            </li>
            <li class="nav-item mt-4">
                <a class="nav-link text-danger" href="logout.php">
                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                </a>
            </li>
        </ul>
    </div>

    <div class="main-content">
        <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4 rounded">
            <div class="container-fluid">
                <span class="navbar-brand">Dashboard</span>
                <div class="d-flex align-items-center">
                    <span class="me-3">Welcome, <?php echo $_SESSION['username']; ?></span>
                    <span class="badge bg-primary"><?php echo ucfirst($role); ?></span>
                </div>
            </div>
        </nav>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title">Pending Orders</h5>
                                <h2 class="card-text"><?php echo $pending_orders; ?></h2>
                            </div>
                            <i class="bi bi-cart" style="font-size: 2.5rem;"></i>
                        </div>
                        <a href="orders.php?status=pending" class="text-white stretched-link"></a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title">Inventory Alerts</h5>
                                <h2 class="card-text"><?php echo $inventory_alerts; ?></h2>
                            </div>
                            <i class="bi bi-exclamation-triangle" style="font-size: 2.5rem;"></i>
                        </div>
                        <a href="inventory.php?alert=1" class="text-white stretched-link"></a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title">Forecasts & ML Insights</h5>
                                <h2 class="card-text">New</h2>
                            </div>
                            <i class="bi bi-graph-up" style="font-size: 2.5rem;"></i>
                        </div>
                        <a href="analytics.php" class="text-white stretched-link"></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Recent Activity Feed</h5>
                    </div>
                    <div class="card-body">
                        <?php if (count($recent_activity) > 0): ?>
                            <?php foreach ($recent_activity as $activity): ?>
                                <div class="activity-item">
                                    <p class="mb-1"><?php echo $activity['description']; ?></p>
                                    <small class="text-muted"><?php echo date('M d, Y H:i', strtotime($activity['timestamp'])); ?></small>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">No recent activity</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <?php if (in_array($role, ['farmer', 'supplier', 'manufacturer'])): ?>
                                <a href="new_order.php" class="btn btn-primary">
                                    <i class="bi bi-plus-circle me-2"></i>Create New Order
                                </a>
                            <?php endif; ?>
                            <?php if ($role === 'supplier'): ?>
                                <a href="inventory_replenishment.php" class="btn btn-warning">
                                    <i class="bi bi-arrow-repeat me-2"></i>Replenish Inventory
                                </a>
                            <?php endif; ?>
                            <a href="messages.php" class="btn btn-info">
                                <i class="bi bi-chat-left-text me-2"></i>Send Message
                            </a>
                            <a href="reports.php" class="btn btn-secondary">
                                <i class="bi bi-file-earmark-bar-graph me-2"></i>Generate Report
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>