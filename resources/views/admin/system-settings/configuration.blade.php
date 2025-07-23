@extends('layouts.app')

@section('title', 'System Configuration')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#0f0f23] via-[#1a1a2e] to-[#16213e]">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold text-white mb-2 font-space gradient-text">System Configuration</h1>
                    <p class="text-gray-300">Manage system settings and application configuration</p>
                </div>
                <a href="{{ route('admin.system-settings.index') }}" class="bg-white/10 backdrop-blur-lg rounded-lg px-4 py-2 text-gray-200 hover:bg-white/20 transition-colors shadow-md border border-white/10">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Settings
                </a>
            </div>
        </div>

        <!-- Security Monitoring -->
        <div class="glass-card p-8 mb-8" id="security-monitoring">
            <h2 class="text-2xl font-bold font-space mb-6 gradient-text">Security Monitoring</h2>
            <div id="security-status-content">
                <div class="mb-4 flex items-center justify-between">
                    <span class="text-gray-200">System Status</span>
                    <span id="system-status" class="font-semibold text-green-400">Checking...</span>
                </div>
                <div class="mb-4 flex items-center justify-between">
                    <span class="text-gray-200">Firewall Status</span>
                    <span id="firewall-status" class="font-semibold text-blue-400">Checking...</span>
                </div>
                <div class="mb-4 flex items-center justify-between">
                    <span class="text-gray-200">SSL Certificate</span>
                    <span id="ssl-status" class="font-semibold text-yellow-400">Checking...</span>
                </div>
                <div class="mb-4 flex items-center justify-between">
                    <span class="text-gray-200">Backup Status</span>
                    <span id="backup-status" class="font-semibold text-purple-400">Checking...</span>
                </div>
            </div>
        </div>
        <script>
            function loadSecurityStatus() {
                fetch('{{ route('admin.security.status') }}')
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    })
                    .then(data => {
                        document.getElementById('system-status').textContent = data.system_status || 'Unable to check status';
                        document.getElementById('firewall-status').textContent = data.firewall_status || 'Unable to check status';
                        document.getElementById('ssl-status').textContent = data.ssl_status || 'Unable to check status';
                        document.getElementById('backup-status').textContent = data.backup_status || 'Unable to check status';
                    })
                    .catch(error => {
                        document.getElementById('system-status').textContent = 'Unable to check status';
                        document.getElementById('firewall-status').textContent = 'Unable to check status';
                        document.getElementById('ssl-status').textContent = 'Unable to check status';
                        document.getElementById('backup-status').textContent = 'Unable to check status';
                        console.error('Error loading security status:', error);
                    });
            }
            loadSecurityStatus();
            setInterval(loadSecurityStatus, 60000); // Refresh every minute
        </script>

        <!-- Configuration Form -->
        <div class="glass-card rounded-2xl p-6 shadow-xl border border-white/10">
            <form method="POST" action="{{ route('admin.system-settings.update-configuration') }}">
                @csrf
                @method('PATCH')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Application Settings -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-white mb-4">Application Settings</h3>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-200 mb-2">Application Name</label>
                            <input type="text" name="app_name" value="{{ $config['app_name'] }}" 
                                   class="w-full px-3 py-2 border border-gray-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white/10 text-white placeholder-gray-400">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-200 mb-2">Application URL</label>
                            <input type="url" name="app_url" value="{{ $config['app_url'] }}" 
                                   class="w-full px-3 py-2 border border-gray-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white/10 text-white placeholder-gray-400">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-200 mb-2">Timezone</label>
                            <select name="timezone" class="w-full px-3 py-2 border border-gray-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white/10 text-white">
                                <option value="UTC" {{ $config['timezone'] === 'UTC' ? 'selected' : '' }}>UTC</option>
                                <option value="America/New_York" {{ $config['timezone'] === 'America/New_York' ? 'selected' : '' }}>Eastern Time</option>
                                <option value="America/Chicago" {{ $config['timezone'] === 'America/Chicago' ? 'selected' : '' }}>Central Time</option>
                                <option value="America/Denver" {{ $config['timezone'] === 'America/Denver' ? 'selected' : '' }}>Mountain Time</option>
                                <option value="America/Los_Angeles" {{ $config['timezone'] === 'America/Los_Angeles' ? 'selected' : '' }}>Pacific Time</option>
                                <option value="Europe/London" {{ $config['timezone'] === 'Europe/London' ? 'selected' : '' }}>London</option>
                                <option value="Europe/Paris" {{ $config['timezone'] === 'Europe/Paris' ? 'selected' : '' }}>Paris</option>
                                <option value="Asia/Tokyo" {{ $config['timezone'] === 'Asia/Tokyo' ? 'selected' : '' }}>Tokyo</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-200 mb-2">Locale</label>
                            <select name="locale" class="w-full px-3 py-2 border border-gray-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white/10 text-white">
                                <option value="en" {{ $config['locale'] === 'en' ? 'selected' : '' }}>English</option>
                                <option value="es" {{ $config['locale'] === 'es' ? 'selected' : '' }}>Spanish</option>
                                <option value="fr" {{ $config['locale'] === 'fr' ? 'selected' : '' }}>French</option>
                                <option value="de" {{ $config['locale'] === 'de' ? 'selected' : '' }}>German</option>
                                <option value="it" {{ $config['locale'] === 'it' ? 'selected' : '' }}>Italian</option>
                                <option value="pt" {{ $config['locale'] === 'pt' ? 'selected' : '' }}>Portuguese</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- System Information -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-white mb-4">System Information</h3>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between items-center p-3 bg-white/5 rounded-lg">
                                <span class="text-sm font-medium text-gray-200">Environment</span>
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $config['app_env'] === 'production' ? 'bg-green-500/20 text-green-300' : 'bg-yellow-500/20 text-yellow-300' }}">
                                    {{ ucfirst($config['app_env']) }}
                                </span>
                            </div>
                            
                            <div class="flex justify-between items-center p-3 bg-white/5 rounded-lg">
                                <span class="text-sm font-medium text-gray-200">Debug Mode</span>
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $config['app_debug'] ? 'bg-red-500/20 text-red-300' : 'bg-green-500/20 text-green-300' }}">
                                    {{ $config['app_debug'] ? 'Enabled' : 'Disabled' }}
                                </span>
                            </div>
                            
                            <div class="flex justify-between items-center p-3 bg-white/5 rounded-lg">
                                <span class="text-sm font-medium text-gray-200">Mail Driver</span>
                                <span class="text-sm text-gray-400">{{ ucfirst($config['mail_driver']) }}</span>
                            </div>
                            
                            <div class="flex justify-between items-center p-3 bg-white/5 rounded-lg">
                                <span class="text-sm font-medium text-gray-200">Database</span>
                                <span class="text-sm text-gray-400">{{ ucfirst($config['database_connection']) }}</span>
                            </div>
                            
                            <div class="flex justify-between items-center p-3 bg-white/5 rounded-lg">
                                <span class="text-sm font-medium text-gray-200">Cache Driver</span>
                                <span class="text-sm text-gray-400">{{ ucfirst($config['cache_driver']) }}</span>
                            </div>
                            
                            <div class="flex justify-between items-center p-3 bg-white/5 rounded-lg">
                                <span class="text-sm font-medium text-gray-200">Session Driver</span>
                                <span class="text-sm text-gray-400">{{ ucfirst($config['session_driver']) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-8 flex justify-end">
                    <button type="submit" class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-2 rounded-lg hover:from-blue-700 hover:to-purple-700 transition-all font-semibold shadow-md">
                        Update Configuration
                    </button>
                </div>
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