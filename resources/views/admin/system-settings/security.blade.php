@extends('layouts.app')

@section('title', 'Security Settings')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#0f0f23] via-[#1a1a2e] to-[#16213e]">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold text-white mb-2 font-space gradient-text">Security Settings</h1>
                    <p class="text-gray-300">Monitor security threats and manage security settings</p>
                </div>
                <a href="{{ route('admin.system-settings.index') }}" class="bg-white/10 backdrop-blur-lg rounded-lg px-4 py-2 text-gray-200 hover:bg-white/20 transition-colors shadow-md border border-white/10">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Settings
                </a>
            </div>
        </div>

        <!-- Security Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="glass-card rounded-2xl p-6 shadow-lg border border-white/10">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-red-300">Failed Login Attempts</p>
                        <p class="text-3xl font-bold text-red-400">{{ $securityStats['failed_login_attempts'] }}</p>
                    </div>
                    <div class="p-3 bg-red-500/20 rounded-full">
                        <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="glass-card rounded-2xl p-6 shadow-lg border border-white/10">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-blue-300">Password Resets</p>
                        <p class="text-3xl font-bold text-blue-400">{{ $securityStats['password_resets'] }}</p>
                    </div>
                    <div class="p-3 bg-blue-500/20 rounded-full">
                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="glass-card rounded-2xl p-6 shadow-lg border border-white/10">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-yellow-300">Suspicious Activities</p>
                        <p class="text-3xl font-bold text-yellow-400">{{ $securityStats['suspicious_activities'] }}</p>
                    </div>
                    <div class="p-3 bg-yellow-500/20 rounded-full">
                        <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="glass-card rounded-2xl p-6 shadow-lg border border-white/10">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-300">Last Security Scan</p>
                        <p class="text-lg font-bold text-gray-200">{{ $securityStats['last_security_scan']->format('M d, H:i') }}</p>
                    </div>
                    <div class="p-3 bg-green-500/20 rounded-full">
                        <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security Settings -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Authentication Settings -->
            <div class="glass-card rounded-2xl p-6 shadow-lg border border-white/10">
                <h3 class="text-xl font-semibold text-white mb-6">Authentication Settings</h3>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-white/5 rounded-lg">
                        <div>
                            <h4 class="font-medium text-white">Two-Factor Authentication</h4>
                            <p class="text-sm text-gray-300">Require 2FA for all users</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-500 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 bg-white/5 rounded-lg">
                        <div>
                            <h4 class="font-medium text-white">Password Complexity</h4>
                            <p class="text-sm text-gray-300">Require strong passwords</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-500 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 bg-white/5 rounded-lg">
                        <div>
                            <h4 class="font-medium text-white">Session Timeout</h4>
                            <p class="text-sm text-gray-300">Auto-logout after inactivity</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-500 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 bg-white/5 rounded-lg">
                        <div>
                            <h4 class="font-medium text-white">Login Notifications</h4>
                            <p class="text-sm text-gray-300">Email notifications for logins</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-500 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Security Monitoring -->
            <div class="glass-card rounded-2xl p-6 shadow-lg border border-white/10">
                <h3 class="text-xl font-semibold text-white mb-6">Security Monitoring</h3>
                
                <div class="space-y-4">
                    <div class="p-4 bg-green-500/10 border border-green-500/20 rounded-lg">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <h4 class="font-medium text-green-300">System Status</h4>
                                <p class="text-sm text-green-400">All systems operational</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-4 bg-blue-500/10 border border-blue-500/20 rounded-lg">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-blue-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <h4 class="font-medium text-blue-300">Firewall Status</h4>
                                <p class="text-sm text-blue-400">Firewall is active and protecting</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-4 bg-yellow-500/10 border border-yellow-500/20 rounded-lg">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-yellow-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            <div>
                                <h4 class="font-medium text-yellow-300">SSL Certificate</h4>
                                <p class="text-sm text-yellow-400">Certificate expires in 30 days</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-4 bg-white/5 border border-gray-700 rounded-lg">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <div>
                                <h4 class="font-medium text-gray-300">Backup Status</h4>
                                <p class="text-sm text-gray-400">Last backup: 2 hours ago</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6">
                    <button id="runSecurityScanBtn" class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white px-4 py-2 rounded-lg hover:from-blue-700 hover:to-purple-700 transition-all font-semibold shadow-md flex items-center justify-center" type="button">
                        <span id="scanBtnText">Run Security Scan</span>
                        <svg id="scanSpinner" class="animate-spin ml-2 h-5 w-5 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Security Recommendations -->
        <div class="mt-8 glass-card rounded-2xl p-6 shadow-lg border border-white/10">
            <h3 class="text-xl font-semibold text-white mb-6">Security Recommendations</h3>
            <div class="space-y-4">
                @foreach($securityRecommendations as $rec)
                    <div class="flex items-start p-4 
                        @if($rec['type'] === 'warning') bg-yellow-50 border border-yellow-200 @elseif($rec['type'] === 'info') bg-blue-50 border border-blue-200 @elseif($rec['type'] === 'success') bg-green-50 border border-green-200 @else bg-gray-50 border border-gray-200 @endif
                        rounded-lg">
                        <svg class="w-5 h-5 mr-3 mt-0.5"
                            @if($rec['type'] === 'warning') fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #eab308;" @elseif($rec['type'] === 'info') fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #2563eb;" @elseif($rec['type'] === 'success') fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #22c55e;" @else fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #6b7280;" @endif>
                            @if($rec['type'] === 'warning')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            @elseif($rec['type'] === 'info')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            @elseif($rec['type'] === 'success')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            @else
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" />
                            @endif
                        </svg>
                        <div>
                            <h4 class="font-medium 
                                @if($rec['type'] === 'warning') text-yellow-800 @elseif($rec['type'] === 'info') text-blue-800 @elseif($rec['type'] === 'success') text-green-800 @else text-gray-800 @endif">
                                {{ $rec['title'] }}
                            </h4>
                            <p class="text-sm 
                                @if($rec['type'] === 'warning') text-yellow-600 @elseif($rec['type'] === 'info') text-blue-600 @elseif($rec['type'] === 'success') text-green-600 @else text-gray-600 @endif">
                                {{ $rec['message'] }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
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

<script>
document.getElementById('runSecurityScanBtn').addEventListener('click', function() {
    const btn = this;
    const spinner = document.getElementById('scanSpinner');
    const btnText = document.getElementById('scanBtnText');
    btn.disabled = true;
    spinner.classList.remove('hidden');
    btnText.textContent = 'Scanning...';

    fetch("{{ route('admin.system-settings.run-security-scan') }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({})
    })
    .then(async response => {
        btn.disabled = false;
        spinner.classList.add('hidden');
        btnText.textContent = 'Run Security Scan';

        if (!response.ok) {
            showToast('An error occurred while running the scan.', 'error');
            return;
        }

        let data;
        try {
            data = await response.json();
        } catch (e) {
            showToast('An error occurred while running the scan.', 'error');
            return;
        }

        if (data.success) {
            showToast('Security scan completed successfully!', 'success');
            if (data.last_scan_time) {
                const el = document.getElementById('lastSecurityScanTime');
                if (el) el.textContent = data.last_scan_time;
            }
        } else {
            showToast(data.message || 'Scan failed', 'error');
        }
    })
    .catch(() => {
        btn.disabled = false;
        spinner.classList.add('hidden');
        btnText.textContent = 'Run Security Scan';
        showToast('An error occurred while running the scan.', 'error');
    });
});

function showToast(message, type) {
    const toast = document.createElement('div');
    toast.className = `fixed bottom-4 right-4 px-6 py-3 rounded-lg shadow-2xl animate-fade-in-up transition-all duration-500 z-50 ${type === 'success' ? 'bg-green-500/90 text-white' : 'bg-red-500/90 text-white'}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 4000);
}
</script>
@endsection 