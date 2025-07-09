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

        .glass-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative;
            overflow: hidden;
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

        .sidebar {
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(20px);
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            position: fixed;
            top: 5rem;
            left: 0;
            height: 100vh;
            z-index: 40;
            overflow-y: auto;
        }

        .sidebar-item {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .sidebar-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: var(--primary-gradient);
            transition: left 0.3s ease;
            z-index: -1;
        }

        .sidebar-item:hover::before,
        .sidebar-item.active::before {
            left: 0;
        }

        .sidebar-item:hover,
        .sidebar-item.active {
            color: white;
            transform: translateX(5px);
        }

        .gradient-text {
            color: #60a5fa;
            background: none;
            -webkit-background-clip: initial;
            -webkit-text-fill-color: initial;
        }

        .chat-message {
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

        .notification-dot {
            background: var(--secondary-gradient);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        /* Fixed Navigation Bar */
        .fixed-nav {
                position: fixed;
            top: 0;
            left: 0;
            right: 0;
                z-index: 50;
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            }

            .main-content {
            margin-left: 16rem; /* 256px for sidebar width */
            margin-top: 5rem; /* 80px for navigation height */
            min-height: calc(100vh - 5rem);
        }

        .logo-pulse {
            animation: logoPulse 2s ease-in-out infinite;
        }

        @keyframes logoPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--accent-gradient);
            border-radius: 3px;
        }

        /* Custom scrollbar for contacts list */
        .contacts-scroll::-webkit-scrollbar {
            width: 4px;
        }

        .contacts-scroll::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 2px;
        }

        .contacts-scroll::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 2px;
        }

        .contacts-scroll::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }

        .form-input {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            backdrop-filter: blur(10px);
        }

        .form-input:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(79, 172, 254, 0.5);
            box-shadow: 0 0 0 3px rgba(79, 172, 254, 0.1);
        }

        .form-input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .btn-primary {
            background: var(--primary-gradient);
            border: none;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: var(--secondary-gradient);
            transition: left 0.3s ease;
            z-index: -1;
        }

        .btn-primary:hover::before {
            left: 0;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        }

        /* Mobile adjustments */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            position: fixed;
            z-index: 50;
                height: 100vh;
        }

            .sidebar.open {
                transform: translateX(0);
            }

        .main-content {
                margin-left: 0;
                margin-top: 5rem;
            }
        }

        .chat-message .bubble {
            position: relative;
            display: inline-block;
            padding: 0.7rem 1.1rem;
            border-radius: 1.2rem;
            max-width: 60%;
            font-size: 1rem;
            line-height: 1.45;
            margin-bottom: 0.15rem;
            box-shadow: 0 2px 8px 0 rgba(0,0,0,0.08);
            font-weight: 500;
        }
        .chat-message.sent .bubble {
            background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%);
            color: #fff;
            border-bottom-right-radius: 0.5rem;
            box-shadow: 0 2px 12px 0 rgba(59,130,246,0.15);
        }
        .chat-message.received .bubble {
            background: #23272f;
            color: #e5e7eb;
            border-bottom-left-radius: 0.5rem;
            border: 1.5px solid #374151;
            box-shadow: 0 2px 8px 0 rgba(30,41,59,0.10);
        }
        .chat-message .bubble::after {
            content: '';
            position: absolute;
            bottom: 0;
            width: 0;
            height: 0;
        }
        .chat-message.sent .bubble::after {
            right: -12px;
            border-left: 12px solid #6366f1;
            border-top: 12px solid transparent;
            border-bottom: 0 solid transparent;
        }
        .chat-message.received .bubble::after {
            left: -12px;
            border-right: 12px solid #23272f;
            border-top: 12px solid transparent;
            border-bottom: 0 solid transparent;
        }
        .chat-message .bubble .timestamp {
            display: block;
            font-size: 0.85rem;
            color: #a1a1aa;
            margin-top: 0.3rem;
            text-align: right;
            opacity: 0.8;
        }
        .chat-message {
            margin-bottom: 0.7rem !important;
        }
    </style>
</head>
<body class="text-white overflow-x-hidden">
    <div class="flex flex-col min-h-screen min-w-full justify-between bg-gray-900">
        <!-- Back Button -->
        <div class="w-full flex items-center justify-start p-4 bg-gray-900 border-b border-gray-800 shadow z-20">
            <button onclick="history.back()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 shadow">
                <i class="fas fa-arrow-left"></i>
                <span class="font-semibold text-base">Back</span>
            </button>
        </div>
        <!-- Chat Layout -->
        <div class="flex flex-1 w-full h-full min-h-0 min-w-0">
            <!-- Contacts List -->
            <div class="w-1/3 md:w-1/4 bg-gray-800 p-4 border-r border-gray-800 flex flex-col h-screen sticky top-0 left-0 z-30" style="max-height: 100vh;">
                <div class="flex-1 overflow-y-auto contacts-scroll">
                    <div class="mb-4">
                        <input id="contact-search" type="text" class="form-input w-full px-3 py-2 rounded-lg bg-gray-900 border border-gray-700 text-white placeholder-gray-400 focus:bg-gray-800 focus:ring-2 focus:ring-blue-400 transition" placeholder="Search contacts..." />
                    </div>
                    <h2 class="text-lg font-bold mb-4 text-blue-400 font-space tracking-wide">Contacts</h2>
                    <ul id="contacts-list" class="space-y-2"></ul>
                </div>
            </div>
            <!-- Chat Area -->
            <div class="flex-1 flex flex-col bg-gray-900 p-0">
                <!-- Chat Header -->
                <div id="chat-header" class="flex items-center gap-4 px-6 py-4 border-b border-gray-800 bg-gray-900 sticky top-0 z-10 min-h-[72px]" style="min-height:72px;">
                    <div id="chat-avatar" class="w-12 h-12 rounded-full flex items-center justify-center text-xl font-bold bg-blue-700 text-white shadow border-2 border-gray-800"></div>
                    <div>
                        <div id="chat-contact-name" class="font-semibold text-lg"></div>
                        <div class="flex items-center gap-2">
                            <span id="chat-contact-role" class="text-xs text-gray-400"></span>
                            <span id="chat-contact-status" class="w-2 h-2 rounded-full bg-gray-400 inline-block"></span>
                            <span id="chat-contact-status-text" class="text-xs text-gray-400 ml-1"></span>
                        </div>
                    </div>
                </div>
                <!-- Chat Messages -->
                <div id="chat-messages" class="flex-1 overflow-y-auto px-6 py-4 space-y-2 bg-gray-900"></div>
                <!-- Message Input -->
                <div class="flex gap-2 px-6 py-4 bg-gray-900 sticky bottom-0 z-10 border-t border-gray-800">
                    <input id="message-input" type="text" class="form-input flex-1 px-4 py-2 rounded-lg bg-gray-800 border border-gray-700 text-white placeholder-gray-400 focus:bg-gray-700 focus:ring-2 focus:ring-blue-400 transition" placeholder="Type your message..." autocomplete="off" />
                    <button id="send-btn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 shadow">
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

        // Utility: random color for avatar (based on user id)
        function getAvatarColor(id) {
            const colors = ['from-blue-500 to-purple-600','from-pink-500 to-yellow-500','from-green-500 to-teal-400','from-indigo-500 to-blue-400','from-red-500 to-orange-400'];
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
                li.className = 'flex items-center gap-3 p-2 rounded-lg cursor-pointer transition hover:bg-gray-700/50' + (currentContactId === contact.id ? ' bg-gradient-to-r from-blue-700/60 to-purple-700/60 text-white' : '');
                li.onclick = () => selectContact(contact.id);
                // Avatar
                const avatar = document.createElement('div');
                avatar.className = `w-10 h-10 rounded-full flex items-center justify-center font-bold text-lg bg-gradient-to-br ${getAvatarColor(contact.id)} text-white`;
                avatar.textContent = getInitials(contact.username || contact.name || contact.email);
                // Info
                const info = document.createElement('div');
                info.className = 'flex flex-col';
                const name = document.createElement('span');
                name.className = 'font-semibold';
                name.textContent = contact.username || contact.name || contact.email;
                const role = document.createElement('span');
                role.className = 'text-xs text-gray-400';
                role.textContent = contact.role;
                // Status dot
                const statusDot = document.createElement('span');
                statusDot.className = 'w-2 h-2 rounded-full mt-1 ' + (isUserOnline(contact.id) ? 'bg-green-400' : 'bg-gray-400');
                info.appendChild(name);
                info.appendChild(role);
                // Compose
                li.appendChild(avatar);
                li.appendChild(info);
                li.appendChild(statusDot);
                // Add unread badge if needed
                if (unreadCounts[contact.id]) {
                    const badge = document.createElement('span');
                    badge.className = 'ml-2 bg-red-500 text-white text-xs rounded-full px-2 py-0.5';
                    badge.textContent = unreadCounts[contact.id];
                    li.appendChild(badge);
                }
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
            // Update chat header
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
            if (!currentContactId || !contactMap[currentContactId]) {
                avatar.textContent = '';
                name.textContent = '';
                role.textContent = '';
                statusDot.className = 'w-2 h-2 rounded-full bg-gray-400';
                statusText.textContent = '';
                return;
            }
            const contact = contactMap[currentContactId];
            avatar.className = `w-12 h-12 rounded-full flex items-center justify-center text-xl font-bold bg-gradient-to-br ${getAvatarColor(contact.id)} text-white`;
            avatar.textContent = getInitials(contact.username || contact.name || contact.email);
            name.textContent = contact.username || contact.name || contact.email;
            role.textContent = contact.role;
            statusDot.className = 'w-2 h-2 rounded-full ' + (isUserOnline(contact.id) ? 'bg-green-400' : 'bg-gray-400');
            statusText.textContent = isUserOnline(contact.id) ? 'Online' : 'Offline';
            if (currentContactId && typingUsers.includes(currentContactId)) {
                let typingEl = document.getElementById('chat-typing-indicator');
                if (!typingEl) {
                    typingEl = document.createElement('span');
                    typingEl.id = 'chat-typing-indicator';
                    typingEl.className = 'ml-2 text-xs text-blue-400 animate-pulse';
                    statusText.parentNode.appendChild(typingEl);
                }
                typingEl.textContent = 'Typing...';
            } else {
                const typingEl = document.getElementById('chat-typing-indicator');
                if (typingEl) typingEl.remove();
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
            messages.forEach(msg => {
                const div = document.createElement('div');
                const isSent = msg.sender_id == userId;
                div.className = 'chat-message flex w-full animate-fadein ' + (isSent ? 'justify-end sent' : 'justify-start received');
                div.innerHTML = `<div class='bubble'>${msg.message}<span class='timestamp'>${new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</span></div>`;
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

        // Listen for Enter key on input and send button
        document.addEventListener('DOMContentLoaded', function() {
            fetchContacts();
            // Set up send button
            const sendBtn = document.getElementById('send-btn');
            if (sendBtn) sendBtn.onclick = sendMessage;
            // Enter key
            const input = document.getElementById('message-input');
            if (input) input.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') sendMessage();
            });
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
        // Animation for chat bubbles
        const style = document.createElement('style');
        style.innerHTML = `@keyframes fadein { from { opacity: 0; transform: translateY(10px);} to { opacity: 1; transform: translateY(0);} } .animate-fadein { animation: fadein 0.3s; }`;
        document.head.appendChild(style);

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