{{-- resources/views/chatbot.blade.php --}}
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
            position: relative;
            group: hover;
        }

        .history-item:hover {
            border-color: var(--accent-green);
            background: rgba(0, 255, 136, 0.05);
        }

        .history-item:hover .history-actions {
            display: flex;
        }

        .history-message {
            font-size: 0.85rem;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            padding-right: 80px;
        }

        .history-time {
            font-size: 0.75rem;
            color: var(--text-secondary);
        }

        .history-actions {
            display: none;
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            gap: 0.25rem;
        }

        .history-btn {
            background: var(--secondary-dark);
            border: 1px solid var(--border-color);
            color: var(--text-secondary);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.7rem;
            transition: all 0.3s ease;
        }

        .history-btn:hover {
            border-color: var(--accent-green);
            color: var(--accent-green);
        }

        .history-btn.delete:hover {
            border-color: #ff4444;
            color: #ff4444;
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

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background: var(--secondary-dark);
            margin: 15% auto;
            padding: 2rem;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            width: 90%;
            max-width: 400px;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .modal-title {
            font-size: 1.25rem;
            font-weight: 600;
        }

        .close-btn {
            background: none;
            border: none;
            color: var(--text-secondary);
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0.25rem;
        }

        .close-btn:hover {
            color: var(--text-primary);
        }

        .modal-input {
            width: 100%;
            padding: 0.75rem;
            background: var(--primary-dark);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-primary);
            font-size: 1rem;
            margin-bottom: 1rem;
        }

        .modal-input:focus {
            outline: none;
            border-color: var(--accent-green);
        }

        .modal-actions {
            display: flex;
            gap: 0.5rem;
            justify-content: flex-end;
        }

        .btn-modal {
            padding: 0.5rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent-green), #00cc66);
            color: black;
            border-color: var(--accent-green);
        }

        .btn-secondary {
            background: var(--primary-dark);
            color: var(--text-primary);
        }

        .btn-modal:hover {
            transform: translateY(-1px);
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

        /* Mobile Toggle Button */
        .mobile-toggle {
            display: none;
            background: var(--primary-dark);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            padding: 0.75rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 1001;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }

        .mobile-toggle:hover {
            border-color: var(--accent-green);
            box-shadow: 0 0 15px var(--green-glow);
        }

        .mobile-toggle.sidebar-open {
            left: calc(var(--sidebar-width) + 1rem);
        }

        /* Overlay for mobile */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            backdrop-filter: blur(2px);
        }

        .sidebar-overlay.show {
            display: block;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .mobile-toggle {
                display: block;
            }

            .sidebar {
                width: var(--sidebar-width);
                position: fixed;
                z-index: 1000;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                height: 100vh;
            }
            
            .sidebar.open {
                transform: translateX(0);
            }
            
            .chat-area {
                width: 100%;
                margin-left: 0;
            }

            .chat-header {
                padding-left: 4rem;
            }

            .welcome-message {
                padding-top: 1rem;
            }
        }

        @media (max-width: 480px) {
            .sidebar {
                width: 280px;
            }

            .mobile-toggle.sidebar-open {
                left: calc(280px + 1rem);
            }

            .chat-header {
                padding: 1rem 1rem 1rem 4rem;
            }

            .header-left .chat-title {
                font-size: 1.25rem;
            }

            .header-left .chat-subtitle {
                font-size: 0.8rem;
            }

            .ai-avatar {
                width: 40px;
                height: 40px;
            }

            .chat-input-container {
                padding: 1rem;
            }

            .chat-messages {
                padding: 1rem;
            }

            .message-content {
                max-width: 85%;
            }
        }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- Mobile Toggle Button -->
        <button class="mobile-toggle" id="mobileToggle" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Sidebar Overlay for Mobile -->
        <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
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
                    <a href="{{ route('logout') }}" class="btn-sidebar" 
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
            
            <div class="history-section">
                <div class="history-title">Chat History</div>
                <div id="historyContainer">
                    @foreach($chatSessions as $session)
                    <div class="history-item" data-session-id="{{ $session->session_id }}">
                        <div class="history-message">{{ $session->getDisplayTitle() }}</div>
                        <div class="history-time">{{ $session->created_at->diffForHumans() }}</div>
                        <div class="history-actions">
                            <button class="history-btn" onclick="editChatTitle('{{ $session->session_id }}', '{{ $session->getDisplayTitle() }}')">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="history-btn delete" onclick="deleteChatSession('{{ $session->session_id }}')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
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

    <!-- Edit Title Modal -->
    <div id="editTitleModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Edit Judul Chat</h3>
                <button class="close-btn" onclick="closeEditModal()">&times;</button>
            </div>
            <input type="text" id="newTitleInput" class="modal-input" placeholder="Masukkan judul baru">
            <div class="modal-actions">
                <button class="btn-modal btn-secondary" onclick="closeEditModal()">Batal</button>
                <button class="btn-modal btn-primary" onclick="saveNewTitle()">Simpan</button>
            </div>
        </div>
    </div>
    
    <div class="sidebar-actions">
    @if(Auth::user()->role === 'admin')
    <a href="{{ route('admin.dashboard') }}" class="btn-sidebar" style="margin-bottom: 0.5rem;">
        <i class="fas fa-cog"></i> Admin Panel
    </a>
    @endif
    
    <a href="{{ route('logout') }}" class="btn-sidebar" 
        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <i class="fas fa-sign-out-alt"></i> Logout
    </a>
</div>

    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <script>
        // Global variables
        let currentSessionId = null;
        let editingSessionId = null;
        let isSidebarOpen = false;
        
        document.addEventListener('DOMContentLoaded', function() {
            initializeChatFunctionality();
            attachHistoryListeners();
            initializeMobileHandlers();
        });

        function initializeMobileHandlers() {
            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    closeSidebar();
                }
            });
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const toggle = document.getElementById('mobileToggle');
            
            isSidebarOpen = !isSidebarOpen;
            
            if (isSidebarOpen) {
                sidebar.classList.add('open');
                overlay.classList.add('show');
                toggle.classList.add('sidebar-open');
                toggle.innerHTML = '<i class="fas fa-times"></i>';
                document.body.style.overflow = 'hidden'; // Prevent scroll
            } else {
                closeSidebar();
            }
        }

        function closeSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const toggle = document.getElementById('mobileToggle');
            
            isSidebarOpen = false;
            sidebar.classList.remove('open');
            overlay.classList.remove('show');
            toggle.classList.remove('sidebar-open');
            toggle.innerHTML = '<i class="fas fa-bars"></i>';
            document.body.style.overflow = ''; // Restore scroll
        }

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

        function attachHistoryListeners() {
            const historyItems = document.querySelectorAll('.history-item');
            historyItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    // Don't trigger if clicking on action buttons
                    if (e.target.closest('.history-actions')) return;
                    
                    const sessionId = this.dataset.sessionId;
                    loadChatSession(sessionId);
                });
            });
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
                body: JSON.stringify({ 
                    message: message,
                    session_id: currentSessionId
                })
            })
            .then(response => response.json())
            .then(data => {
                hideTypingIndicator();
                
                if (data.success) {
                    addMessage(data.response, 'bot');
                    
                    // Update current session ID for continuing conversation
                    if (data.session_id) {
                        currentSessionId = data.session_id;
                    }
                    
                    updateHistorySidebar();
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

        function loadChatSession(sessionId) {
            currentSessionId = sessionId;
            
            // Close sidebar on mobile after selecting chat
            if (window.innerWidth <= 768) {
                closeSidebar();
            }
            
            // Clear current messages
            const messagesContainer = document.getElementById('messagesContainer');
            messagesContainer.innerHTML = '';
            
            // Show loading
            messagesContainer.innerHTML = '<div class="welcome-message"><h2 class="welcome-title">Memuat chat...</h2></div>';
            
            fetch(`/chat/session/${sessionId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        messagesContainer.innerHTML = '';
                        
                        // Add all messages from this session
                        data.chats.forEach(chat => {
                            addMessage(chat.user_message, 'user', false, chat.created_at);
                            addMessage(chat.bot_response, 'bot', false, chat.created_at);
                        });
                        
                        // Scroll to bottom
                        messagesContainer.scrollTop = messagesContainer.scrollHeight;
                    } else {
                        messagesContainer.innerHTML = '<div class="welcome-message"><h2 class="welcome-title">Gagal memuat chat</h2></div>';
                    }
                })
                .catch(error => {
                    console.error('Error loading chat session:', error);
                    messagesContainer.innerHTML = '<div class="welcome-message"><h2 class="welcome-title">Gagal memuat chat</h2></div>';
                });
        }

        function editChatTitle(sessionId, currentTitle) {
            editingSessionId = sessionId;
            document.getElementById('newTitleInput').value = currentTitle;
            document.getElementById('editTitleModal').style.display = 'block';
        }

        function closeEditModal() {
            document.getElementById('editTitleModal').style.display = 'none';
            editingSessionId = null;
        }

        function saveNewTitle() {
            const newTitle = document.getElementById('newTitleInput').value.trim();
            if (!newTitle || !editingSessionId) return;

            fetch(`/chat/session/${editingSessionId}/title`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ title: newTitle })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update sidebar
                    updateHistorySidebar();
                    closeEditModal();
                } else {
                    alert('Gagal mengupdate judul');
                }
            })
            .catch(error => {
                console.error('Error updating title:', error);
                alert('Gagal mengupdate judul');
            });
        }

        function deleteChatSession(sessionId) {
            if (!confirm('Apakah Anda yakin ingin menghapus chat ini?')) return;

            fetch(`/chat/session/${sessionId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove from sidebar
                    const historyItem = document.querySelector(`[data-session-id="${sessionId}"]`);
                    if (historyItem) {
                        historyItem.remove();
                    }
                    
                    // If this was the current session, start new chat
                    if (currentSessionId === sessionId) {
                        newChat();
                    }
                } else {
                    alert('Gagal menghapus chat');
                }
            })
            .catch(error => {
                console.error('Error deleting chat:', error);
                alert('Gagal menghapus chat');
            });
        }

        function addMessage(content, sender, isError = false, timestamp = null) {
            const messagesContainer = document.getElementById('messagesContainer');
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${sender}`;
            
            const now = timestamp ? new Date(timestamp) : new Date();
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

        function newChat() {
            currentSessionId = null;
            const messagesContainer = document.getElementById('messagesContainer');
            messagesContainer.innerHTML = `
                <div class="welcome-message">
                    <h2 class="welcome-title">Chat Baru</h2>
                    <p>Mulai percakapan baru dengan AI assistant Anda.</p>
                </div>
            `;
            
            document.getElementById('messageInput').focus();
        }

        function updateHistorySidebar() {
            fetch('/chat/sessions')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const historyContainer = document.getElementById('historyContainer');
                        if (data.sessions.length === 0) {
                            historyContainer.innerHTML = '<div style="text-align: center; color: var(--text-secondary); padding: 2rem;">Tidak ada history</div>';
                        } else {
                            historyContainer.innerHTML = data.sessions.map(session => `
                                <div class="history-item" data-session-id="${session.session_id}">
                                    <div class="history-message">${session.chat_title || session.user_message.substring(0, 30) + (session.user_message.length > 30 ? '...' : '')}</div>
                                    <div class="history-time">${formatTimeAgo(session.created_at)}</div>
                                    <div class="history-actions">
                                        <button class="history-btn" onclick="editChatTitle('${session.session_id}', '${(session.chat_title || session.user_message).replace(/'/g, "\\'")}')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="history-btn delete" onclick="deleteChatSession('${session.session_id}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            `).join('');
                            
                            // Re-attach listeners
                            attachHistoryListeners();
                        }
                    }
                })
                .catch(error => console.error('Error updating history:', error));
        }

        function formatTimeAgo(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffInSeconds = Math.floor((now - date) / 1000);
            
            if (diffInSeconds < 60) return 'Baru saja';
            if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)} menit lalu`;
            if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)} jam lalu`;
            return `${Math.floor(diffInSeconds / 86400)} hari lalu`;
        }

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'n') {
                e.preventDefault();
                newChat();
            }
            
            // Close modal with Escape
            if (e.key === 'Escape') {
                closeEditModal();
                if (window.innerWidth <= 768 && isSidebarOpen) {
                    closeSidebar();
                }
            }
            
            // Toggle sidebar with Ctrl+B on mobile
            if (e.ctrlKey && e.key === 'b' && window.innerWidth <= 768) {
                e.preventDefault();
                toggleSidebar();
            }
        });

        // Close modal when clicking outside
        window.addEventListener('click', function(e) {
            const modal = document.getElementById('editTitleModal');
            if (e.target === modal) {
                closeEditModal();
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