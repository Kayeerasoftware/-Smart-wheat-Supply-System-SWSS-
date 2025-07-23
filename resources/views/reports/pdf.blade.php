<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplier Analytics Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #667eea;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #667eea;
            margin: 0;
            font-size: 28px;
        }
        .header p {
            color: #666;
            margin: 5px 0;
        }
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        .metric-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
        }
        .metric-card h3 {
            margin: 0 0 10px 0;
            color: #667eea;
            font-size: 18px;
        }
        .metric-card .value {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        .section {
            margin-bottom: 30px;
        }
        .section h2 {
            color: #667eea;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #667eea;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            color: #666;
            font-size: 12px;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        .performance-bar {
            background-color: #e9ecef;
            border-radius: 10px;
            height: 20px;
            overflow: hidden;
            margin: 5px 0;
        }
        .performance-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea, #764ba2);
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>SWSS Supplier Analytics Report</h1>
        <p>Generated on: {{ date('F d, Y \a\t g:i A') }}</p>
        <p>Supplier: {{ $user->name ?? 'Unknown' }}</p>
    </div>

    <div class="metrics-grid">
        <div class="metric-card">
            <h3>Total Revenue</h3>
            <div class="value">${{ number_format($analytics['total_revenue'], 2) }}</div>
        </div>
        <div class="metric-card">
            <h3>Total Orders</h3>
            <div class="value">{{ number_format($analytics['total_orders']) }}</div>
        </div>
        <div class="metric-card">
            <h3>Inventory Value</h3>
            <div class="value">${{ number_format($analytics['total_inventory_value'], 2) }}</div>
        </div>
        <div class="metric-card">
            <h3>Total Shipments</h3>
            <div class="value">{{ number_format($analytics['total_shipments']) }}</div>
        </div>
    </div>

    <div class="section">
        <h2>Delivery Performance</h2>
        <table>
            <thead>
                <tr>
                    <th>Metric</th>
                    <th>Count</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Delivered Shipments</td>
                    <td>{{ $analytics['delivered_shipments'] }}</td>
                    <td>{{ $analytics['total_shipments'] > 0 ? round(($analytics['delivered_shipments'] / $analytics['total_shipments']) * 100, 1) : 0 }}%</td>
                </tr>
                <tr>
                    <td>In Transit Shipments</td>
                    <td>{{ $analytics['in_transit_shipments'] }}</td>
                    <td>{{ $analytics['total_shipments'] > 0 ? round(($analytics['in_transit_shipments'] / $analytics['total_shipments']) * 100, 1) : 0 }}%</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Inventory Status</h2>
        <table>
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Count</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Low Stock Items</td>
                    <td>{{ $analytics['low_stock_items'] }}</td>
                </tr>
                <tr>
                    <td>Out of Stock Items</td>
                    <td>{{ $analytics['out_of_stock_items'] }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    @if($topProducts->count() > 0)
    <div class="section">
        <h2>Top Products by Revenue</h2>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Total Quantity</th>
                    <th>Total Revenue</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topProducts as $product)
                <tr>
                    <td>{{ $product['product']->name ?? 'Unknown Product' }}</td>
                    <td>{{ number_format($product['total_quantity']) }}</td>
                    <td>${{ number_format($product['total_revenue'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($warehousePerformance->count() > 0)
    <div class="section">
        <h2>Warehouse Performance</h2>
        <table>
            <thead>
                <tr>
                    <th>Warehouse</th>
                    <th>Total Items</th>
                    <th>Inventory Value</th>
                    <th>Low Stock Items</th>
                    <th>Performance</th>
                </tr>
            </thead>
            <tbody>
                @foreach($warehousePerformance as $warehouse)
                @php
                    $performance = $warehouse['total_items'] > 0 ? (($warehouse['total_items'] - $warehouse['low_stock_items']) / $warehouse['total_items']) * 100 : 0;
                @endphp
                <tr>
                    <td>{{ $warehouse['warehouse']->name ?? 'Unknown Warehouse' }}</td>
                    <td>{{ number_format($warehouse['total_items']) }}</td>
                    <td>${{ number_format($warehouse['total_value'], 2) }}</td>
                    <td>{{ number_format($warehouse['low_stock_items']) }}</td>
                    <td>
                        <div class="performance-bar">
                            <div class="performance-fill" style="width: {{ $performance }}%"></div>
                        </div>
                        {{ number_format($performance, 0) }}%
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        <p>This report was generated automatically by the SWSS (Smart Wheat Supply System)</p>
        <p>For questions or support, please contact your system administrator</p>
    </div>
</body>
</html> 