<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LogisticsRoute;
use Illuminate\Support\Str;

class LogisticsRouteController extends Controller
{
    // List all routes (JSON for AJAX)
    public function index()
    {
        $routes = LogisticsRoute::orderBy('created_at', 'desc')->get();
        return response()->json($routes);
    }

    // Store a new route
    public function store(Request $request)
    {
        $request->validate([
            'origin' => 'required',
            'destination' => 'required',
            'deliverer' => 'required',
        ]);
        $route = LogisticsRoute::create([
            'route_id' => 'R' . Str::padLeft(LogisticsRoute::max('id') + 1, 3, '0'),
            'origin' => $request->origin,
            'destination' => $request->destination,
            'deliverer' => $request->deliverer,
            'status' => 'In Transit',
            'eta' => '1h 00m', // Default ETA, can be updated
        ]);
        return response()->json($route);
    }

    // Optimize routes (sort by ETA, then status)
    public function optimize()
    {
        $routes = LogisticsRoute::orderByRaw("FIELD(status, 'In Transit', 'Delayed', 'Delivered')")
            ->orderBy('eta')
            ->get();
        return response()->json($routes);
    }
} 