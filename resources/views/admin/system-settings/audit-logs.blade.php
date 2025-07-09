@extends('layouts.app')

@section('title', 'Audit Logs')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#0f0f23] via-[#1a1a2e] to-[#16213e]">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold text-white mb-2 font-space gradient-text">Audit Logs</h1>
                    <p class="text-gray-300">System activity logs and audit trail</p>
                </div>
                <a href="{{ route('admin.system-settings.index') }}" class="bg-white/10 backdrop-blur-lg rounded-lg px-4 py-2 text-gray-200 hover:bg-white/20 transition-colors shadow-md border border-white/10">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Settings
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="glass-card rounded-2xl p-6 shadow-xl border border-white/10 mb-8">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-200 mb-2">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           class="w-full px-3 py-2 border border-gray-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white/10 text-white placeholder-gray-400"
                           placeholder="Activity description...">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-200 mb-2">Type</label>
                    <select name="type" class="w-full px-3 py-2 border border-gray-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-900 text-gray-100 appearance-none">
                        <option value="">All Types</option>
                        <option value="login" {{ request('type') === 'login' ? 'selected' : '' }}>Login</option>
                        <option value="logout" {{ request('type') === 'logout' ? 'selected' : '' }}>Logout</option>
                        <option value="user_created" {{ request('type') === 'user_created' ? 'selected' : '' }}>User Created</option>
                        <option value="user_updated" {{ request('type') === 'user_updated' ? 'selected' : '' }}>User Updated</option>
                        <option value="user_deleted" {{ request('type') === 'user_deleted' ? 'selected' : '' }}>User Deleted</option>
                        <option value="system_config_updated" {{ request('type') === 'system_config_updated' ? 'selected' : '' }}>System Config</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-200 mb-2">User</label>
                    <select name="user_id" class="w-full px-3 py-2 border border-gray-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-900 text-gray-100 appearance-none">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }} style="background-color: #18181b; color: #f3f4f6;">
                            {{ $user->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-200 mb-2">Date From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" 
                           class="w-full px-3 py-2 border border-gray-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white/10 text-white placeholder-gray-400">
                </div>
                
                <div class="flex items-end">
                    <div class="w-full">
                        <label class="block text-sm font-medium text-gray-200 mb-2">Date To</label>
                        <div class="flex space-x-2">
                            <input type="date" name="date_to" value="{{ request('date_to') }}" 
                                   class="flex-1 px-3 py-2 border border-gray-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white/10 text-white placeholder-gray-400">
                            <button type="submit" class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-4 py-2 rounded-lg hover:from-blue-700 hover:to-purple-700 transition-all font-semibold shadow-md">
                                Filter
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Audit Logs Table -->
        <div class="glass-card rounded-2xl shadow-lg border border-white/10 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-blue-900/60 to-purple-900/60">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-200 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-200 uppercase tracking-wider">Activity</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-200 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-200 uppercase tracking-wider">Timestamp</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-200 uppercase tracking-wider">Details</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white/5 divide-y divide-gray-800">
                        @forelse($activities as $activity)
                        <tr class="hover:bg-gradient-to-r hover:from-blue-900/40 hover:to-purple-900/40 transition-all">
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($activity->user)
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <div class="h-8 w-8 rounded-full bg-gradient-to-r from-blue-400 to-purple-500 flex items-center justify-center">
                                            <span class="text-xs font-medium text-white">{{ strtoupper(substr($activity->user->name, 0, 2)) }}</span>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-white">{{ $activity->user->name }}</div>
                                        <div class="text-sm text-gray-300">{{ $activity->user->email }}</div>
                                    </div>
                                </div>
                                @else
                                <span class="text-sm text-gray-400">System</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-white max-w-xs truncate">{{ $activity->description }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded-full 
                                    @if(in_array($activity->type, ['login', 'logout'])) bg-blue-500/20 text-blue-300
                                    @elseif(in_array($activity->type, ['user_created', 'user_updated'])) bg-green-500/20 text-green-300
                                    @elseif($activity->type === 'user_deleted') bg-red-500/20 text-red-300
                                    @else bg-gray-500/20 text-gray-300
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $activity->type)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                <div>{{ $activity->created_at->format('M d, Y') }}</div>
                                <div>{{ $activity->created_at->format('H:i:s') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-400">
                                <button class="hover:underline" onclick="showDetails('{{ $activity->id }}')">
                                    View Details
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <svg class="w-12 h-12 text-gray-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                </svg>
                                <p class="text-gray-400">No audit logs found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            @if($activities->hasPages())
            <div class="bg-white/5 px-4 py-3 border-t border-gray-800 sm:px-6">
                {{ $activities->links() }}
            </div>
            @endif
        </div>

        <!-- Export Options -->
        <div class="mt-6 flex justify-end space-x-4">
            <form method="GET" action="{{ route('admin.audit-logs.export.csv') }}">
                <input type="hidden" name="search" value="{{ request('search') }}">
                <input type="hidden" name="type" value="{{ request('type') }}">
                <input type="hidden" name="user_id" value="{{ request('user_id') }}">
                <input type="hidden" name="date_from" value="{{ request('date_from') }}">
                <input type="hidden" name="date_to" value="{{ request('date_to') }}">
                <button type="submit" class="bg-gradient-to-r from-green-600 to-emerald-600 text-white px-4 py-2 rounded-lg hover:from-green-700 hover:to-emerald-700 transition-all font-semibold shadow-md">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export CSV
            </button>
            </form>
            <form method="GET" action="{{ route('admin.audit-logs.export.pdf') }}">
                <input type="hidden" name="search" value="{{ request('search') }}">
                <input type="hidden" name="type" value="{{ request('type') }}">
                <input type="hidden" name="user_id" value="{{ request('user_id') }}">
                <input type="hidden" name="date_from" value="{{ request('date_from') }}">
                <input type="hidden" name="date_to" value="{{ request('date_to') }}">
                <button type="submit" class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-4 py-2 rounded-lg hover:from-blue-700 hover:to-purple-700 transition-all font-semibold shadow-md">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export PDF
            </button>
            </form>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
        <div class="fixed bottom-4 right-4 bg-green-500/90 text-white px-6 py-3 rounded-lg shadow-2xl animate-fade-in-up transition-all duration-500">
            {{ session('success') }}
        </div>
        @endif

        @if($errors->any())
        <div class="fixed bottom-4 right-4 bg-red-500/90 text-white px-6 py-3 rounded-lg shadow-2xl animate-fade-in-up transition-all duration-500">
            <ul>
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>
</div>

<!-- Activity Details Modal -->
<div id="activityModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Activity Details</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="activityDetails" class="text-sm text-gray-600">
                <!-- Activity details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.18);
        position: relative;
        overflow: hidden;
    }
    .gradient-text {
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    @keyframes fade-in-up {
        0% {
            opacity: 0;
            transform: translateY(40px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .animate-fade-in-up {
        animation: fade-in-up 0.7s cubic-bezier(0.25, 0.8, 0.25, 1);
    }
</style>

<script>
function showDetails(activityId) {
    // This would typically make an AJAX call to get activity details
    // For now, showing a placeholder
    document.getElementById('activityDetails').innerHTML = `
        <p><strong>Activity ID:</strong> ${activityId}</p>
        <p><strong>Description:</strong> This is a detailed description of the activity.</p>
        <p><strong>IP Address:</strong> 192.168.1.1</p>
        <p><strong>User Agent:</strong> Mozilla/5.0...</p>
        <p><strong>Additional Data:</strong> No additional data available.</p>
    `;
    document.getElementById('activityModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('activityModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('activityModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>
@endsection 