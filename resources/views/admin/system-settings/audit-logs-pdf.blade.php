<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Audit Logs PDF Export</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; }
        h2 { margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #888; padding: 6px 8px; text-align: left; }
        th { background: #f3f3f3; }
        tr:nth-child(even) { background: #f9f9f9; }
    </style>
</head>
<body>
    <h2>Audit Logs (Page {{ $page }})</h2>
    <table>
        <thead>
            <tr>
                <th>User</th>
                <th>Email</th>
                <th>Type</th>
                <th>Description</th>
                <th>Timestamp</th>
            </tr>
        </thead>
        <tbody>
        @foreach($activities as $log)
            <tr>
                <td>{{ $log->user ? $log->user->name : 'System' }}</td>
                <td>{{ $log->user ? $log->user->email : '' }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $log->type)) }}</td>
                <td>{{ $log->description }}</td>
                <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <p style="margin-top:20px; font-size:11px; color:#888;">Exported at {{ now()->format('Y-m-d H:i:s') }}</p>
</body>
</html> 