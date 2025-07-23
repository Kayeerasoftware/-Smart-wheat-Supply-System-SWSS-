<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Delivery Report</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Delivery Report</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Client</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($deliveries as $delivery)
                <tr>
                    <td>{{ $delivery['id'] }}</td>
                    <td>{{ $delivery['client'] }}</td>
                    <td>{{ $delivery['status'] }}</td>
                    <td>{{ $delivery['date'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html> 