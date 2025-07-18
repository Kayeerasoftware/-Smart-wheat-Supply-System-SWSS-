<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SWSS {{ ucfirst(Auth::user()->role) }} Dashboard - Chat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --accent-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --dark-gradient: linear-gradient(135deg, #2d1b69 0%, #11998e 100%);
            --card-gradient: linear-gradient(145deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 50%, #16213e 100%);
            min-height: 100vh;
        }

        .font-space {
            font-family: 'Space Grotesk', 'Inter', sans-serif;
        }

        /* Enhanced Glass Card Effect */
        .glass-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 1.25rem;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        }

        .glass-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
        }

        .glass-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.12);
            border-color: rgba(255, 255, 255, 0.2);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        /* Enhanced Chat Layout */
        .chat-container {
            display: flex;
            flex-direction: column;
            height: calc(100vh - 4rem);
            background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 100%);
        }

        /* Contacts Panel */
        .contacts-panel {
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(20px);
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            height: 100%;
            overflow-y: auto;
        }

        .contact-item {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            border-radius: 0.75rem;
        }

        .contact-item:hover {
            background: rgba(255, 255, 255, 0.05);
            transform: translateX(3px);
        }

        .contact-item.active {
            background: linear-gradient(90deg, rgba(59, 130, 246, 0.2) 0%, rgba(59, 130, 246, 0.1) 100%);
            border-left: 2px solid rgba(59, 130, 246, 0.5);
        }

        /* Chat Area */
        .chat-area {
            display: flex;
            flex-direction: column;
            flex: 1;
            background: linear-gradient(145deg, rgba(15, 15, 35, 0.9) 0%, rgba(26, 26, 46, 0.95) 100%);
        }

        /* Chat Header */
        .chat-header {
            background: rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 1rem;
            z-index: 10;
        }

        /* Messages Container */
        .messages-container {
            flex: 1;
            overflow-y: auto;
            padding: 1rem;
            background: linear-gradient(rgba(15, 15, 35, 0.7), rgba(15, 15, 35, 0.9));
        }

        /* Input Area */
        .input-area {
            background: rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding: 1rem;
        }

        /* Message Bubbles */
        .chat-message {
            margin-bottom: 0.7rem;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .chat-message .bubble {
            position: relative;
            display: inline-block;
            padding: 0.75rem 1.25rem;
            border-radius: 1.25rem;
            max-width: 70%;
            font-size: 0.95rem;
            line-height: 1.5;
            margin-bottom: 0.25rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: all 0.2s ease;
            word-break: break-word;
            white-space: pre-line;
        }

        .chat-message.sent .bubble {
            background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%);
            color: #fff;
            border-bottom-right-radius: 0.5rem;
            box-shadow: 0 4px 16px rgba(59, 130, 246, 0.2);
        }

        .chat-message.received .bubble {
            background: rgba(35, 39, 47, 0.9);
            color: #e5e7eb;
            border-bottom-left-radius: 0.5rem;
            border: 1px solid rgba(55, 65, 81, 0.5);
            box-shadow: 0 4px 12px rgba(30, 41, 59, 0.15);
        }

        .chat-message .bubble:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
        }

        .chat-message .bubble .timestamp {
            display: block;
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.6);
            margin-top: 0.25rem;
            text-align: right;
        }

        /* Input Styling */
        .message-input {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .message-input:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(79, 172, 254, 0.5);
            box-shadow: 0 0 0 3px rgba(79, 172, 254, 0.1);
        }

        /* Send Button */
        .send-button {
            background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%);
            transition: all 0.3s ease;
        }

        .send-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(59, 130, 246, 0.3);
            background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%);
        }

        /* Scrollbars */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 3px;
        }

        /* Avatar Colors */
        .avatar-blue { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
        .avatar-purple { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); }
        .avatar-pink { background: linear-gradient(135deg, #ec4899 0%, #db2777 100%); }
        .avatar-red { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
        .avatar-orange { background: linear-gradient(135deg, #f97316 0%, #ea580c 100%); }
        .avatar-green { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
        .avatar-teal { background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%); }

        /* Status Indicators */
        .status-online { background: #10b981; box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.3); }
        .status-offline { background: #6b7280; box-shadow: 0 0 0 2px rgba(107, 114, 128, 0.3); }
        .status-typing { background: #f59e0b; box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.3); }

        /* Typing Animation */
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .typing-indicator {
            animation: pulse 1.5s infinite;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .contacts-panel {
                width: 100%;
                position: absolute;
                z-index: 40;
                transform: translateX(-100%);
            }
            
            .contacts-panel.open {
                transform: translateX(0);
            }
            
            .chat-area {
                margin-left: 0;
            }
        }
    </style>
</head>
<body class="text-gray-100 overflow-hidden">
    <!-- Navigation Bar -->
    <div class="fixed-nav w-full h-16 flex items-center justify-between px-6 bg-gray-900/80 backdrop-blur-md border-b border-gray-800">
        <div class="flex items-center space-x-4">
            <button onclick="history.back()" class="flex items-center space-x-2 text-blue-400 hover:text-blue-300 transition-colors">
                <i class="fas fa-arrow-left"></i>
                <span class="font-medium">Back</span>
            </button>
        </div>
        <div class="flex items-center space-x-4">
            <div class="text-sm font-medium text-blue-400">
                SWSS {{ ucfirst(Auth::user()->role) }} Dashboard
            </div>
        </div>
    </div>

    <!-- Main Chat Container -->
    <div class="chat-container">
        <div class="flex flex-1 h-full">
            <!-- Contacts Panel -->
            <div class="contacts-panel w-1/3 md:w-1/4 flex flex-col">
                <div class="p-4 border-b border-gray-800">
                    <h2 class="text-xl font-bold text-blue-400 mb-2">Contacts</h2>
                    <div class="relative">
                        <input id="contact-search" type="text" 
                               class="message-input w-full px-4 py-2 rounded-lg bg-gray-800/70 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/30" 
                               placeholder="Search contacts...">
                        <i class="fas fa-search absolute right-3 top-3 text-gray-400"></i>
                    </div>
                </div>
                <div class="flex-1 overflow-y-auto p-2">
                    <ul id="contacts-list" class="space-y-2"></ul>
                </div>
            </div>

            <!-- Chat Area -->
            <div class="chat-area flex-1 flex flex-col">
                <!-- Chat Header -->
                <div id="chat-header" class="chat-header flex items-center space-x-4">
                    <div id="chat-avatar" class="w-10 h-10 rounded-full flex items-center justify-center text-lg font-bold text-white shadow-md"></div>
                    <div class="flex-1">
                        <div id="chat-contact-name" class="font-semibold text-lg"></div>
                        <div class="flex items-center space-x-2">
                            <span id="chat-contact-role" class="text-xs text-gray-400"></span>
                            <span id="chat-contact-status" class="w-2 h-2 rounded-full"></span>
                            <span id="chat-contact-status-text" class="text-xs text-gray-400"></span>
                            <span id="chat-typing-indicator" class="text-xs text-blue-400 hidden">typing...</span>
                        </div>
                    </div>
                </div>

                <!-- Messages Container -->
                <div id="chat-messages" class="messages-container space-y-4 p-4"></div>

                <!-- Input Area -->
                <div class="input-area flex items-center space-x-2">
                    <input id="message-input" type="text" 
                           class="message-input flex-1 px-4 py-3 rounded-lg bg-gray-800/70 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/30" 
                           placeholder="Type your message..." autocomplete="off">
                    <button id="send-btn" class="send-button px-5 py-3 rounded-lg text-white font-medium flex items-center space-x-2">
                        <i class="fas fa-paper-plane"></i>
                        <span>Send</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Mobile sidebar toggle
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('open');
        });

        // Chat functionality
        let selectedUserId = null;
        let selectedUsername = null;

        // Contact selection
        document.querySelectorAll('.contact-item').forEach(item => {
            item.addEventListener('click', function() {
                const userId = this.dataset.userId;
                const username = this.dataset.username;
                
                // Update selected contact
                selectedUserId = userId;
                selectedUsername = username;
                
                // Update UI
                document.getElementById('selectedUserInitials').textContent = username.substring(0, 2).toUpperCase();
                document.getElementById('selectedUsername').textContent = username;
                document.getElementById('selectedUserRole').textContent = 'Online';
                document.getElementById('selectedUserStatus').className = 'w-2 h-2 bg-green-400 rounded-full';
                document.getElementById('selectedUserStatusText').textContent = 'Online';
                
                // Enable chat input
                document.getElementById('messageInput').disabled = false;
                document.getElementById('sendMessage').disabled = false;
                
                // Update chat messages
                document.getElementById('chatMessages').innerHTML = `
                    <div class="text-center text-gray-400 py-8">
                        <i class="fas fa-comments text-4xl mb-4"></i>
                        <p class="text-lg font-medium">Chat with ${username}</p>
                        <p class="text-sm">Start typing to send a message</p>
                    </div>
                `;
                
                // Remove active class from all contacts
                document.querySelectorAll('.contact-item').forEach(contact => {
                    contact.classList.remove('bg-gray-700/50');
                });
                
                // Add active class to selected contact
                this.classList.add('bg-gray-700/50');
            });
        });

        // Send message functionality
        document.getElementById('sendMessage').addEventListener('click', function() {
            const messageInput = document.getElementById('messageInput');
            const message = messageInput.value.trim();
            
            if (message && selectedUserId) {
                // Add message to chat
                const chatMessages = document.getElementById('chatMessages');
                const messageElement = document.createElement('div');
                messageElement.className = 'chat-message mb-4';
                messageElement.innerHTML = `
                    <div class="flex justify-end">
                        <div class="bg-blue-500 text-white px-4 py-2 rounded-lg max-w-xs">
                            <p class="text-sm">${message}</p>
                            <p class="text-xs opacity-75 mt-1">${new Date().toLocaleTimeString()}</p>
                        </div>
                    </div>
                `;
                chatMessages.appendChild(messageElement);
            
                // Clear input
                messageInput.value = '';
            
                // Scroll to bottom
                chatMessages.scrollTop = chatMessages.scrollHeight;
            
                // Simulate reply (in real app, this would come from server)
                setTimeout(() => {
                    const replyElement = document.createElement('div');
                    replyElement.className = 'chat-message mb-4';
                    replyElement.innerHTML = `
                        <div class="flex justify-start">
                            <div class="bg-gray-600 text-white px-4 py-2 rounded-lg max-w-xs">
                                <p class="text-sm">Thank you for your message. An administrator will respond shortly.</p>
                                <p class="text-xs opacity-75 mt-1">${new Date().toLocaleTimeString()}</p>
                            </div>
                        </div>
                    `;
                    chatMessages.appendChild(replyElement);
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }, 1000);
            }
        });

        // Enter key to send message
        document.getElementById('messageInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.getElementById('sendMessage').click();
            }
        });
    </script>

    <script type="module">
        import Echo from 'laravel-echo';
        import Pusher from 'pusher-js';
        window.Pusher = Pusher;
        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: import.meta.env.VITE_PUSHER_APP_KEY,
            cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
            wsHost: import.meta.env.VITE_PUSHER_HOST ?? `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
            wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
            wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
            forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
            enabledTransports: ['ws', 'wss'],
        });
    </script>

    <script>
        const userId = {{ auth()->id() }};
        let currentContactId = null;
        let contacts = [];
        let messages = [];
        let contactMap = {};
        let filteredContacts = [];
        let onlineUserIds = [];
        let unreadCounts = {};
        let typingUsers = {};

        // Utility: get initials from name/email
        function getInitials(name) {
            if (!name) return '?';
            const parts = name.split(' ');
            if (parts.length === 1) return name.substring(0, 2).toUpperCase();
            return (parts[0][0] + parts[1][0]).toUpperCase();
        }

        // Utility: random color for avatar
        function getAvatarColor(id) {
            const colors = ['avatar-blue', 'avatar-purple', 'avatar-pink', 'avatar-green', 'avatar-teal', 'avatar-orange', 'avatar-red'];
            return colors[id % colors.length];
        }

        // Fetch contacts
        function fetchContacts() {
            fetch('/chat/contacts')
                .then(res => res.json())
                .then(data => {
                    contacts = data;
                    filteredContacts = contacts;
                    contactMap = {};
                    contacts.forEach(c => contactMap[c.id] = c);
                    renderContacts();
                });
        }

        // Render contacts in sidebar
        function renderContacts() {
            const list = document.getElementById('contacts-list');
            if (!list) return;
            list.innerHTML = '';
            filteredContacts.forEach(contact => {
                const li = document.createElement('li');
                li.className = `contact-item flex items-center p-3 rounded-lg transition-all ${currentContactId === contact.id ? 'active' : ''}`;
                li.onclick = () => selectContact(contact.id);
                
                // Avatar
                const avatar = document.createElement('div');
                avatar.className = `w-10 h-10 rounded-full flex items-center justify-center text-white font-bold ${getAvatarColor(contact.id)}`;
                avatar.textContent = getInitials(contact.username || contact.name || contact.email);
                
                // Info
                const info = document.createElement('div');
                info.className = 'flex-1 ml-3 overflow-hidden';
                const name = document.createElement('div');
                name.className = 'font-medium truncate';
                name.textContent = contact.username || contact.name || contact.email;
                const meta = document.createElement('div');
                meta.className = 'flex items-center text-xs text-gray-400 mt-1';
                
                // Role
                const role = document.createElement('span');
                role.className = 'truncate';
                role.textContent = contact.role;
                
                // Status dot
                const statusDot = document.createElement('span');
                statusDot.className = `w-2 h-2 rounded-full ml-2 ${isUserOnline(contact.id) ? 'status-online' : 'status-offline'}`;
                
                meta.appendChild(role);
                meta.appendChild(statusDot);
                info.appendChild(name);
                info.appendChild(meta);
                
                // Unread badge
                if (unreadCounts[contact.id]) {
                    const badge = document.createElement('span');
                    badge.className = 'ml-auto bg-red-500 text-white text-xs rounded-full px-2 py-0.5';
                    badge.textContent = unreadCounts[contact.id];
                    li.appendChild(badge);
                }
                
                li.appendChild(avatar);
                li.appendChild(info);
                list.appendChild(li);
            });
        }

        // Contact search
        document.addEventListener('DOMContentLoaded', function() {
            const search = document.getElementById('contact-search');
            if (search) {
                search.addEventListener('input', function() {
                    const val = search.value.toLowerCase();
                    filteredContacts = contacts.filter(c => (c.username || c.name || c.email).toLowerCase().includes(val));
                    renderContacts();
                });
            }
        });

        // Select a contact and fetch messages
        function selectContact(id) {
            currentContactId = id;
            renderContacts();
            fetchMessages(id);
            updateChatHeader();
        }

        // Update chat header
        function updateChatHeader() {
            const header = document.getElementById('chat-header');
            const avatar = document.getElementById('chat-avatar');
            const name = document.getElementById('chat-contact-name');
            const role = document.getElementById('chat-contact-role');
            const statusDot = document.getElementById('chat-contact-status');
            const statusText = document.getElementById('chat-contact-status-text');
            const typingIndicator = document.getElementById('chat-typing-indicator');
            
            if (!currentContactId || !contactMap[currentContactId]) {
                avatar.textContent = '';
                name.textContent = '';
                role.textContent = '';
                statusDot.className = 'w-2 h-2 rounded-full status-offline';
                statusText.textContent = '';
                typingIndicator.classList.add('hidden');
                return;
            }
            
            const contact = contactMap[currentContactId];
            avatar.className = `w-10 h-10 rounded-full flex items-center justify-center text-white font-bold ${getAvatarColor(contact.id)}`;
            avatar.textContent = getInitials(contact.username || contact.name || contact.email);
            name.textContent = contact.username || contact.name || contact.email;
            role.textContent = contact.role;
            
            if (isUserOnline(contact.id)) {
                statusDot.className = 'w-2 h-2 rounded-full status-online';
                statusText.textContent = 'Online';
            } else {
                statusDot.className = 'w-2 h-2 rounded-full status-offline';
                statusText.textContent = 'Offline';
            }
            
            if (typingUsers.includes(currentContactId)) {
                typingIndicator.classList.remove('hidden');
                typingIndicator.classList.add('typing-indicator');
            } else {
                typingIndicator.classList.add('hidden');
                typingIndicator.classList.remove('typing-indicator');
            }
        }

        // Fetch messages with a contact
        function fetchMessages(contactId) {
            fetch(`/chat/messages/${contactId}`)
                .then(res => res.json())
                .then(data => {
                    messages = data;
                    renderMessages();
                });
        }

        // Render messages in chat window
        function renderMessages() {
            const chatBox = document.getElementById('chat-messages');
            if (!chatBox) return;
            chatBox.innerHTML = '';
            
            if (messages.length === 0) {
                chatBox.innerHTML = `
                    <div class="flex flex-col items-center justify-center h-full text-gray-400 py-12">
                        <i class="fas fa-comments text-4xl mb-4 opacity-50"></i>
                        <p class="text-lg font-medium">No messages yet</p>
                        <p class="text-sm">Start the conversation with ${contactMap[currentContactId]?.username || contactMap[currentContactId]?.name || contactMap[currentContactId]?.email || 'this contact'}</p>
                    </div>
                `;
                return;
            }
            
            messages.forEach(msg => {
                const div = document.createElement('div');
                const isSent = msg.sender_id == userId;
                div.className = `chat-message flex w-full ${isSent ? 'justify-end sent' : 'justify-start received'}`;
                
                const bubble = document.createElement('div');
                bubble.className = 'bubble';
                bubble.innerHTML = `
                    ${msg.message}
                    <span class="timestamp">${new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</span>
                `;
                
                div.appendChild(bubble);
                chatBox.appendChild(div);
            });
            
            chatBox.scrollTop = chatBox.scrollHeight;
        }

        // Send a message
        function sendMessage() {
            const input = document.getElementById('message-input');
            if (!input.value.trim() || !currentContactId) return;
            
            const messageText = input.value.trim();
            input.value = '';
            
            // Show the message instantly (optimistic UI)
            const tempMsg = {
                sender_id: userId,
                receiver_id: currentContactId,
                message: messageText,
                created_at: new Date().toISOString(),
                _temp: true // mark as temporary
            };
            
            messages.push(tempMsg);
            renderMessages();
            
            fetch('/chat/send-message', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    recipient_id: currentContactId,
                    message: messageText
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Replace the temp message with the real one
                    messages = messages.filter(m => !m._temp);
                    messages.push(data.data);
                    renderMessages();
                } else {
                    alert(data.error || 'Failed to send message');
                    // Remove the temp message if failed
                    messages = messages.filter(m => !m._temp);
                    renderMessages();
                }
            });
        }

        // Initialize chat
        document.addEventListener('DOMContentLoaded', function() {
            fetchContacts();
            
            // Set up send button
            const sendBtn = document.getElementById('send-btn');
            if (sendBtn) sendBtn.onclick = sendMessage;
            
            // Enter key
            const input = document.getElementById('message-input');
            if (input) {
                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') sendMessage();
                });
            }
            
            // Listen for real-time messages
            if (window.Echo) {
                window.Echo.private('chat.' + userId)
                    .listen('MessageSent', (e) => {
                        if (currentContactId == e.sender_id) {
                            messages.push(e);
                            renderMessages();
                        } else {
                            fetchContacts();
                        }
                    });
            }
        });

        // Poll online status every 15 seconds
        setInterval(fetchOnlineStatus, 15000);
        function fetchOnlineStatus() {
            fetch('/chat/online-users')
                .then(res => res.json())
                .then(data => {
                    onlineUserIds = data.online_user_ids ? Object.keys(data.online_user_ids).map(Number) : [];
                    renderContacts();
                    updateChatHeader();
                });
        }

        // Update status dot in contacts and chat header
        function isUserOnline(userId) {
            return onlineUserIds.includes(userId);
        }

        // Poll unread counts every 15 seconds
        setInterval(fetchUnreadCounts, 15000);
        function fetchUnreadCounts() {
            fetch('/chat/unread-counts')
                .then(res => res.json())
                .then(data => {
                    unreadCounts = data.unread_counts || {};
                    renderContacts();
                });
        }

        // Typing indicator logic
        let typingTimeout = null;
        function sendTyping() {
            if (!currentContactId) return;
            fetch('/chat/typing', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ contact_id: currentContactId })
            });
        }
        
        // Listen for typing events
        document.addEventListener('DOMContentLoaded', function() {
            const input = document.getElementById('message-input');
            if (input) {
                input.addEventListener('input', function() {
                    sendTyping();
                    if (typingTimeout) clearTimeout(typingTimeout);
                    typingTimeout = setTimeout(sendTyping, 2000);
                });
            }
        });
        
        // Poll for typing status every 2 seconds
        setInterval(fetchTyping, 2000);
        function fetchTyping() {
            fetch('/chat/typing')
                .then(res => res.json())
                .then(data => {
                    typingUsers = data.typing_user_ids || [];
                    updateChatHeader();
                });
        }
    </script>
</body>
</html>