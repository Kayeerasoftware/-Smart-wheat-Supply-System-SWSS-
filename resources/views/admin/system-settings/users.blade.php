@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#0f0f23] via-[#1a1a2e] to-[#16213e]">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold text-white mb-2 font-space gradient-text">User Management</h1>
                    <p class="text-gray-300">Manage user accounts, roles, and permissions</p>
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
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-200 mb-2">Search</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               class="w-full px-3 py-2 border border-gray-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white/10 text-white shadow-sm pl-10 placeholder-gray-400"
                               placeholder="Name or email...">
                        <span class="absolute left-3 top-2.5 text-gray-400"><i class="fas fa-search"></i></span>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-200 mb-2">Role</label>
                    <select name="role" class="w-full px-3 py-2 border border-gray-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white/10 text-white shadow-sm">
                        <option value="">All Roles</option>
                        @foreach($roles as $role)
                        <option value="{{ $role }}" {{ request('role') === $role ? 'selected' : '' }}>
                            {{ ucfirst($role) }}
                        </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-200 mb-2">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white/10 text-white shadow-sm">
                        <option value="">All Status</option>
                        <option value="verified" {{ request('status') === 'verified' ? 'selected' : '' }}>Verified</option>
                        <option value="unverified" {{ request('status') === 'unverified' ? 'selected' : '' }}>Unverified</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white px-4 py-2 rounded-lg hover:from-blue-700 hover:to-purple-700 transition-all shadow-md font-semibold">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Users Table -->
        <div class="glass-card rounded-2xl shadow-2xl border border-white/10 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-blue-900/60 to-purple-900/60">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-200 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-200 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-200 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-200 uppercase tracking-wider">Joined</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-200 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white/5 divide-y divide-gray-800">
                        @forelse($users as $user)
                        <tr class="hover:bg-gradient-to-r hover:from-blue-900/40 hover:to-purple-900/40 transition-all">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-12 w-12">
                                        <div class="h-12 w-12 rounded-full bg-gradient-to-r from-blue-400 to-purple-500 flex items-center justify-center shadow-lg border-2 border-white/20">
                                            @if($user->avatar)
                                                <img src="{{ $user->avatar }}" alt="Avatar" class="h-12 w-12 rounded-full object-cover">
                                            @else
                                                <span class="text-lg font-bold text-white"><i class="fas fa-user"></i></span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-base font-semibold text-white">{{ $user->name }}</div>
                                        <div class="text-sm text-gray-300">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <form method="POST" action="{{ route('admin.system-settings.update-user-role', $user) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <select name="role" onchange="this.form.submit()" 
                                            class="text-sm border-0 bg-transparent focus:ring-2 focus:ring-blue-500 rounded font-semibold text-white">
                                        @foreach($roles as $role)
                                        <option value="{{ $role }}" {{ $user->role === $role ? 'selected' : '' }}>
                                            {{ ucfirst($role) }}
                                        </option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($user->email_verified_at)
                                <span class="px-2 py-1 text-xs font-bold rounded-full bg-green-500/20 text-green-300 shadow">Verified</span>
                                @else
                                <span class="px-2 py-1 text-xs font-bold rounded-full bg-yellow-500/20 text-yellow-300 shadow">Pending</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                {{ $user->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    @if($user->email_verified_at)
                                    <form method="POST" action="{{ route('admin.system-settings.toggle-user-status', $user) }}" class="inline" onsubmit="return confirm('Are you sure you want to deactivate {{ $user->name }}?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="px-3 py-1 rounded shadow transition-all font-semibold bg-gradient-to-r from-red-500 to-pink-500 hover:from-red-600 hover:to-pink-600 text-white"
                                            title="Deactivate this user account">
                                            Deactivate
                                        </button>
                                    </form>
                                    @else
                                    <form method="POST" action="{{ route('admin.system-settings.toggle-user-status', $user) }}" class="inline" onsubmit="return confirm('Are you sure you want to activate {{ $user->name }}?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="px-3 py-1 rounded shadow transition-all font-semibold bg-gradient-to-r from-blue-500 to-purple-500 hover:from-blue-600 hover:to-purple-600 text-white"
                                            title="Activate this user account">
                                            Activate
                                        </button>
                                    </form>
                                    @endif
                                    @if($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.system-settings.delete-user', $user) }}" class="inline" 
                                          onsubmit="return confirm('Are you sure you want to delete this user?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-gradient-to-r from-red-500 to-pink-500 text-white px-3 py-1 rounded shadow hover:from-red-600 hover:to-pink-600 transition-all font-semibold">Delete</button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <svg class="w-12 h-12 text-gray-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                                <p class="text-gray-400">No users found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            @if($users->hasPages())
            <div class="bg-white/5 px-4 py-3 border-t border-gray-800 sm:px-6">
                {{ $users->links() }}
            </div>
            @endif
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
@endsection 