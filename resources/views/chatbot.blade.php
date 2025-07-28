<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gemini AI Chatbot - {{ Auth::user()->name }}</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-dark: #0a0a0a;
            --secondary-dark: #1a1a1a;
            --accent-green: #00ff88;
            --green-glow: #00ff8820;
            --text-primary: #ffffff;
            --text-secondary: #a0a0a0;
            --border-color: #333333;
            --sidebar-width: 300px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--primary-dark) 0%, #000 100%);
            color: var(--text-primary);
            min-height: 100vh;
            overflow-x: hidden;
        }

        .app-container {
            display: flex;
            height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--secondary-dark);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
        }

        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--accent-green), #00cc66);
            display: flex;
            align-items: center;
            justify-content: center;
            color: black;
            font-weight: 600;
        }

        .user-name {
            font-weight: 600;
        }

        .sidebar-actions {
            display: flex;
            gap: 0.5rem;
        }

        .btn-sidebar {
            background: var(--primary-dark);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.85rem;
        }

        .btn-sidebar:hover {
            border-color: var(--accent-green);
            color: var(--text-primary);
        }

        .history-section {
            flex: 1;
            overflow-y: auto;
            padding: 1rem;
        }

        .history-title {
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--text-secondary);
        }

        .history-item {
            background: var(--primary-dark);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .history-item:hover {
            border-color: var(--accent-green);
            background: rgba(0, 255, 136, 0.05);
        }

        .history-message {
            font-size: 0.85rem;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .history-time {
            font-size: 0.75rem;
            color: var(--text-secondary);
        }

        /* Chat Area */
        .chat-area {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .chat-header {
            background: linear-gradient(90deg, var(--secondary-dark) 0%, #1f1f1f 100%);
            padding: 1.5rem 2rem;
            border-bottom: 1px solid var(--border-color);
            position: relative;
            overflow: hidden;
        }

        .chat-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--accent-green), transparent);
            animation: pulse 2s ease-in-out infinite;
        }

        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .ai-avatar {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--accent-green), #00cc66);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 20px var(--green-glow);
            animation: glow 3s ease-in-out infinite;
        }

        .chat-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
        }

        .chat-subtitle {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin: 0;
        }

        .header-actions {
            display: flex;
            gap: 0.5rem;
        }

        /* Rest of the chat styles remain the same... */
        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 2rem;
            background: linear-gradient(180deg, rgba(0,0,0,0.3) 0%, rgba(0,0,0,0.1) 100%);
        }

        .message {
            margin-bottom: 1.5rem;
            display: flex;
            gap: 1rem;
            animation: slideIn 0.3s ease-out;
        }

        .message.user {
            flex-direction: row-reverse;
        }

        .message-avatar {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .message.user .message-avatar {
            background: linear-gradient(135deg, #333, #555);
            color: white;
        }

        .message.bot .message-avatar {
            background: linear-gradient(135deg, var(--accent-green), #00cc66);
            color: black;
            box-shadow: 0 0 15px var(--green-glow);
        }

        .message-content {
            max-width: 70%;
            padding: 1rem 1.5rem;
            border-radius: 18px;
            position: relative;
            line-height: 1.6;
        }

        .message.user .message-content {
            background: linear-gradient(135deg, #2a2a2a, #3a3a3a);
            border: 1px solid #444;
        }

        .message.bot .message-content {
            background: linear-gradient(135deg, rgba(0, 255, 136, 0.1), rgba(0, 204, 102, 0.1));
            border: 1px solid rgba(0, 255, 136, 0.3);
        }

        .message-time {
            font-size: 0.75rem;
            color: var(--text-secondary);
            margin-top: 0.5rem;
        }

        .chat-input-container {
            padding: 2rem;
            background: var(--secondary-dark);
            border-top: 1px solid var(--border-color);
        }

        .chat-input-wrapper {
            display: flex;
            gap: 1rem;
            align-items: center;
            background: var(--primary-dark);
            border: 1px solid var(--border-color);
            border-radius: 25px;
            padding: 0.8rem 1.5rem;
            transition: all 0.3s ease;
        }

        .chat-input-wrapper:focus-within {
            border-color: var(--accent-green);
            box-shadow: 0 0 20px var(--green-glow);
        }

        .chat-input {
            flex: 1;
            background: transparent;
            border: none;
            outline: none;
            color: var(--text-primary);
            font-size: 1rem;
            resize: none;
            min-height: 24px;
            max-height: 120px;
        }

        .send-btn {
            background: linear-gradient(135deg, var(--accent-green), #00cc66);
            color: black;
            border: none;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1.2rem;
        }

        .send-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 0 25px var(--green-glow);
        }

        /* Welcome message for new users */
        .welcome-message {
            text-align: center;
            padding: 3rem 2rem;
            color: var(--text-secondary);
        }

        .welcome-title {
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, var(--accent-green), #00cc66);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Animations */
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes pulse {
            0%, 100% { opacity: 0.5; }
            50% { opacity: 1; }
        }

        @keyframes glow {
            0%, 100% { box-shadow: 0 0 20px var(--green-glow); }
            50% { box-shadow: 0 0 30px var(--accent-green); }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                position: fixed;
                z-index: 1000;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .sidebar.open {
                transform: translateX(0);
            }
            
            .chat-area {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <div class="user-info">
                    <div class="user-avatar">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="user-name">{{ Auth::user()->name }}</div>
                        <div style="font-size: 0.8rem; color: var(--text-secondary);">
                            {{ Auth::user()->email }}
                        </div>
                    </div>
                </div>
                
                <div class="sidebar-actions">
                    <button class="btn-sidebar" onclick="clearHistory()">
                        <i class="fas fa-trash"></i> Clear
                    </button>
                    <a href="{{ route('logout') }}" class="btn-sidebar" 
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
            
            <div class="history-section">
                <div class="history-title">Chat History</div>
                <div id="historyContainer">
                    @foreach($chatHistory as $chat)
                    <div class="history-item" onclick="loadChat('{{ $chat->id }}')">
                        <div class="history-message">{{ Str::limit($chat->user_message, 40) }}</div>
                        <div class="history-time">{{ $chat->created_at->diffForHumans() }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Chat Area -->
        <div class="chat-area">
            <div class="chat-header">
                <div class="header-content">
                    <div class="header-left">
                        <div class="ai-avatar">
                            <i class="fas fa-robot"></i>
                        </div>
                        <div>
                            <h1 class="chat-title">Jales AI Assistant</h1>
                            <p class="chat-subtitle">Personal AI untuk {{ Auth::user()->name }}</p>
                        </div>
                    </div>
                    
                    <div class="header-actions">
                        <button class="btn-sidebar" onclick="newChat()">
                            <i class="fas fa-plus"></i> New Chat
                        </button>
                    </div>
                </div>
            </div>

            <div class="chat-messages" id="messagesContainer">
                <div class="welcome-message">
                    <h2 class="welcome-title">Selamat Datang, {{ Auth::user()->name }}!</h2>
                    <p>Mulai percakapan baru dengan AI assistant Anda.</p>
                </div>
            </div>

            <div class="chat-input-container">
                <div class="chat-input-wrapper">
                    <textarea 
                        id="messageInput" 
                        class="chat-input" 
                        placeholder="Ketik pesan Anda di sini..." 
                        rows="1"
                        maxlength="2000"
                    ></textarea>
                    <button type="button" id="sendBtn" class="send-btn">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <script>
        // Load chat history dari server saat halaman dimuat
        let currentChatHistory = @json($chatHistory);
        
        document.addEventListener('DOMContentLoaded', function() {
            // Existing JavaScript code for chat functionality...
            initializeChatFunctionality();
        });

        function initializeChatFunctionality() {
            const messageInput = document.getElementById('messageInput');
            const messagesContainer = document.getElementById('messagesContainer');
            const sendBtn = document.getElementById('sendBtn');

            messageInput.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = this.scrollHeight + 'px';
            });

            messageInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendMessage();
                }
            });

            sendBtn.addEventListener('click', sendMessage);
        }

        function sendMessage() {
            const messageInput = document.getElementById('messageInput');
            const message = messageInput.value.trim();
            if (!message) return;

            // Clear welcome message
            clearWelcomeMessage();

            // Add user message
            addMessage(message, 'user');
            
            // Clear input
            messageInput.value = '';
            messageInput.style.height = 'auto';
            
            // Show typing indicator
            showTypingIndicator();
            setInputState(false);

            // Send to server
            fetch('/chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ message: message })
            })
            .then(response => response.json())
            .then(data => {
                hideTypingIndicator();
                
                if (data.success) {
                    addMessage(data.response, 'bot');
                    updateHistorySidebar(); // Update sidebar dengan chat baru
                } else {
                    addMessage(data.error || 'Terjadi kesalahan', 'bot', true);
                }
                
                setInputState(true);
                document.getElementById('messageInput').focus();
            })
            .catch(error => {
                hideTypingIndicator();
                addMessage('Koneksi bermasalah. Silakan coba lagi.', 'bot', true);
                setInputState(true);
                console.error('Error:', error);
            });
        }

        function addMessage(content, sender, isError = false) {
            const messagesContainer = document.getElementById('messagesContainer');
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${sender}`;
            
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID', { 
                hour: '2-digit', 
                minute: '2-digit' 
            });
            
            const avatar = sender === 'user' ? 
                '<i class="fas fa-user"></i>' : 
                '<i class="fas fa-robot"></i>';
                
            const contentClass = isError ? 'message-content error' : 'message-content';
            
            messageDiv.innerHTML = `
                <div class="message-avatar">${avatar}</div>
                <div class="${contentClass}">
                    ${formatMessage(content)}
                    <div class="message-time">${timeString}</div>
                </div>
            `;
            
            messagesContainer.appendChild(messageDiv);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        function formatMessage(content) {
            return content
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                .replace(/\*(.*?)\*/g, '<em>$1</em>')
                .replace(/`(.*?)`/g, '<code style="background: rgba(0,255,136,0.1); padding: 2px 4px; border-radius: 3px;">$1</code>')
                .replace(/\n/g, '<br>');
        }

        function showTypingIndicator() {
            const messagesContainer = document.getElementById('messagesContainer');
            const typingDiv = document.createElement('div');
            typingDiv.className = 'message bot';
            typingDiv.id = 'typingIndicator';
            
            typingDiv.innerHTML = `
                <div class="message-avatar"><i class="fas fa-robot"></i></div>
                <div style="display: flex; align-items: center; gap: 0.5rem; padding: 1rem 1.5rem; background: linear-gradient(135deg, rgba(0, 255, 136, 0.1), rgba(0, 204, 102, 0.1)); border: 1px solid rgba(0, 255, 136, 0.3); border-radius: 18px; max-width: 70%;">
                    <div style="display: flex; gap: 4px;">
                        <div style="width: 8px; height: 8px; border-radius: 50%; background: var(--accent-green); animation: typing 1.4s ease-in-out infinite;"></div>
                        <div style="width: 8px; height: 8px; border-radius: 50%; background: var(--accent-green); animation: typing 1.4s ease-in-out infinite 0.2s;"></div>
                        <div style="width: 8px; height: 8px; border-radius: 50%; background: var(--accent-green); animation: typing 1.4s ease-in-out infinite 0.4s;"></div>
                    </div>
                    <span style="margin-left: 10px; color: var(--text-secondary);">AI sedang mengetik...</span>
                </div>
            `;
            
            messagesContainer.appendChild(typingDiv);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        function hideTypingIndicator() {
            const typingIndicator = document.getElementById('typingIndicator');
            if (typingIndicator) {
                typingIndicator.remove();
            }
        }

        function setInputState(enabled) {
            const messageInput = document.getElementById('messageInput');
            const sendBtn = document.getElementById('sendBtn');
            
            messageInput.disabled = !enabled;
            sendBtn.disabled = !enabled;
            
            if (enabled) {
                sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i>';
            } else {
                sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            }
        }

        function clearWelcomeMessage() {
            const welcomeMessage = document.querySelector('.welcome-message');
            if (welcomeMessage) {
                welcomeMessage.style.display = 'none';
            }
        }

        // Fungsi untuk clear history
        function clearHistory() {
            if (confirm('Apakah Anda yakin ingin menghapus semua chat history?')) {
                fetch('/chat/clear', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Clear sidebar history
                        document.getElementById('historyContainer').innerHTML = '<div style="text-align: center; color: var(--text-secondary); padding: 2rem;">Tidak ada history</div>';
                        
                        // Clear current chat
                        const messagesContainer = document.getElementById('messagesContainer');
                        messagesContainer.innerHTML = `
                            <div class="welcome-message">
                                <h2 class="welcome-title">Chat Baru Dimulai!</h2>
                                <p>History lama telah dihapus. Mulai percakapan baru.</p>
                            </div>
                        `;
                        
                        alert('History berhasil dihapus!');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Gagal menghapus history');
                });
            }
        }

        // Fungsi untuk memulai chat baru
        function newChat() {
            const messagesContainer = document.getElementById('messagesContainer');
            messagesContainer.innerHTML = `
                <div class="welcome-message">
                    <h2 class="welcome-title">Chat Baru</h2>
                    <p>Mulai percakapan baru dengan AI assistant Anda.</p>
                </div>
            `;
            
            document.getElementById('messageInput').focus();
        }

        // Update sidebar history setelah chat baru
        function updateHistorySidebar() {
            fetch('/chat/history')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const historyContainer = document.getElementById('historyContainer');
                        if (data.history.length === 0) {
                            historyContainer.innerHTML = '<div style="text-align: center; color: var(--text-secondary); padding: 2rem;">Tidak ada history</div>';
                        } else {
                            historyContainer.innerHTML = data.history.map(chat => `
                                <div class="history-item" onclick="loadChat('${chat.id}')">
                                    <div class="history-message">${chat.user_message.substring(0, 40)}${chat.user_message.length > 40 ? '...' : ''}</div>
                                    <div class="history-time">${formatTimeAgo(chat.created_at)}</div>
                                </div>
                            `).join('');
                        }
                    }
                })
                .catch(error => console.error('Error updating history:', error));
        }

        // Load specific chat (optional feature)
        function loadChat(chatId) {
            // Bisa diimplementasikan untuk load specific conversation
            console.log('Loading chat:', chatId);
        }

        // Helper function untuk format time
        function formatTimeAgo(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffInSeconds = Math.floor((now - date) / 1000);
            
            if (diffInSeconds < 60) return 'Baru saja';
            if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)} menit lalu`;
            if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)} jam lalu`;
            return `${Math.floor(diffInSeconds / 86400)} hari lalu`;
        }

        // Keyboard shortcut untuk new chat
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'n') {
                e.preventDefault();
                newChat();
            }
        });

        // CSS animation untuk typing indicator
        const style = document.createElement('style');
        style.textContent = `
            @keyframes typing {
                0%, 60%, 100% {
                    transform: translateY(0);
                    opacity: 0.4;
                }
                30% {
                    transform: translateY(-10px);
                    opacity: 1;
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>