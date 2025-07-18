<div x-data="{ open: false }" class="relative">
    <button @click="open = !open" class="relative focus:outline-none">
        <i class="fas fa-bell text-gray-300 text-xl cursor-pointer hover:text-white transition-colors"></i>
        @if(auth()->user()->unreadNotifications->count() > 0)
            <span class="absolute -top-1 -right-1 w-3 h-3 rounded-full bg-red-500"></span>
        @endif
    </button>
    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-80 bg-gray-900 border border-gray-700 rounded-xl shadow-lg z-50 overflow-hidden" style="display: none;" x-transition>
        <div class="p-4 border-b border-gray-800 flex items-center justify-between">
            <span class="font-semibold text-white">Notifications</span>
            <button @click="open = false" class="text-gray-400 hover:text-white"><i class="fas fa-times"></i></button>
        </div>
        <div class="max-h-96 overflow-y-auto">
            @forelse(auth()->user()->notifications->take(10) as $notification)
                <a href="{{ $notification->data['url'] ?? '#' }}" onclick="markNotificationRead('{{ $notification->id }}')" class="block px-4 py-3 hover:bg-gray-800 border-b border-gray-800 {{ $notification->read_at ? 'text-gray-400' : 'text-white' }}">
                    <div class="font-medium">{{ $notification->data['title'] ?? 'Notification' }}</div>
                    <div class="text-sm">{{ $notification->data['body'] ?? '' }}</div>
                    <div class="text-xs text-gray-500 mt-1">{{ $notification->created_at->diffForHumans() }}</div>
                </a>
            @empty
                <div class="px-4 py-6 text-center text-gray-400">No notifications yet.</div>
            @endforelse
        </div>
    </div>
    <script>
        function markNotificationRead(id) {
            fetch('/notifications/mark-read/' + id, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content } })
                .then(() => window.location.reload());
        }
    </script>
</div> 