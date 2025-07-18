@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <h2 class="text-2xl font-bold mb-4">Machine Learning Settings</h2>
    @if(session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
    <form method="POST" action="{{ route('admin.system-settings.update-ml-settings') }}">
        @csrf
        <div class="mb-4">
            <label for="forecast_interval_days" class="block text-gray-700 font-bold mb-2">Forecast Interval (days):</label>
            <input type="number" name="forecast_interval_days" id="forecast_interval_days" value="{{ old('forecast_interval_days', $interval) }}" min="1" max="365" class="border rounded px-3 py-2 w-32">
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
    </form>
</div>
@endsection 