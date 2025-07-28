<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gemini AI Chatbot</title>
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

        .chat-container {
            max-width: 1200px;
            margin: 0 auto;
            height: 100vh;
            display: flex;
            flex-direction: column;
            background: rgba(26, 26, 26, 0.8);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border-color);
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

        .message.bot .message-content.error {
            background: rgba(255, 59, 48, 0.1);
            border: 1px solid rgba(255, 59, 48, 0.3);
            color: #ff6b6b;
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

        .chat-input::placeholder {
            color: var(--text-secondary);
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

        .send-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        .typing-indicator {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 1.5rem;
            background: linear-gradient(135deg, rgba(0, 255, 136, 0.1), rgba(0, 204, 102, 0.1));
            border: 1px solid rgba(0, 255, 136, 0.3);
            border-radius: 18px;
            max-width: 70%;
        }

        .typing-dots {
            display: flex;
            gap: 4px;
        }

        .typing-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--accent-green);
            animation: typing 1.4s ease-in-out infinite;
        }

        .typing-dot:nth-child(2) {
            animation-delay: 0.2s;
        }

        .typing-dot:nth-child(3) {
            animation-delay: 0.4s;
        }

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

        .welcome-subtitle {
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }

        .welcome-features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            max-width: 800px;
            margin: 0 auto;
        }

        .feature-card {
            background: rgba(26, 26, 26, 0.5);
            border: 1px solid var(--border-color);
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            border-color: var(--accent-green);
            box-shadow: 0 0 20px var(--green-glow);
            transform: translateY(-5px);
        }

        .feature-icon {
            font-size: 2rem;
            color: var(--accent-green);
            margin-bottom: 1rem;
        }

        .feature-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .feature-description {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        /* Animations */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 0.5;
            }
            50% {
                opacity: 1;
            }
        }

        @keyframes glow {
            0%, 100% {
                box-shadow: 0 0 20px var(--green-glow);
            }
            50% {
                box-shadow: 0 0 30px var(--accent-green);
            }
        }

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

        /* Responsive */
        @media (max-width: 768px) {
            .chat-container {
                height: 100vh;
                border-radius: 0;
                border: none;
            }
            
            .message-content {
                max-width: 85%;
            }
            
            .chat-messages {
                padding: 1rem;
            }
            
            .chat-input-container {
                padding: 1rem;
            }
            
            .welcome-features {
                grid-template-columns: 1fr;
            }
        }

        /* Scrollbar styling */
        .chat-messages::-webkit-scrollbar {
            width: 6px;
        }

        .chat-messages::-webkit-scrollbar-track {
            background: var(--primary-dark);
        }

        .chat-messages::-webkit-scrollbar-thumb {
            background: var(--accent-green);
            border-radius: 3px;
        }

        .chat-messages::-webkit-scrollbar-thumb:hover {
            background: #00cc66;
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <!-- Header -->
        <div class="chat-header">
            <div class="header-content">
                <div class="ai-avatar">
                    <i class="fas fa-robot"></i>
                </div>
                <div>
                    <h1 class="chat-title">Gemini AI Assistant</h1>
                    <p class="chat-subtitle">Powered by Google's Gemini AI</p>
                </div>
            </div>
        </div>

        <!-- Messages Container -->
        <div class="chat-messages" id="messagesContainer">
            <div id="welcomeMessage" class="welcome-message">
                <h2 class="welcome-title">Selamat Datang!</h2>
                <p class="welcome-subtitle">Saya adalah asisten AI yang siap membantu Anda dengan berbagai pertanyaan dan tugas.</p>
                
                <div class="welcome-features">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-comments"></i>
                        </div>
                        <h3 class="feature-title">Percakapan Natural</h3>
                        <p class="feature-description">Berbicara dengan saya seperti berbicara dengan manusia</p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-lightbulb"></i>
                        </div>
                        <h3 class="feature-title">Jawaban Cerdas</h3>
                        <p class="feature-description">Mendapatkan solusi dan informasi yang akurat</p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h3 class="feature-title">Respon Cepat</h3>
                        <p class="feature-description">Jawaban instan untuk semua pertanyaan Anda</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Input Container -->
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const messageInput = document.getElementById('messageInput');
            const messagesContainer = document.getElementById('messagesContainer');
            const sendBtn = document.getElementById('sendBtn');
            const welcomeMessage = document.getElementById('welcomeMessage');

            // Auto-resize textarea
            messageInput.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = this.scrollHeight + 'px';
            });

            // Send message on Enter (but allow Shift+Enter for new line)
            messageInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendMessage();
                }
            });

            // Send button click
            sendBtn.addEventListener('click', sendMessage);

            function sendMessage() {
                const message = messageInput.value.trim();
                if (!message) return;

                // Hide welcome message
                if (welcomeMessage) {
                    welcomeMessage.style.display = 'none';
                }

                // Add user message
                addMessage(message, 'user');
                
                // Clear input
                messageInput.value = '';
                messageInput.style.height = 'auto';
                
                // Disable input
                setInputState(false);
                
                // Show typing indicator
                showTypingIndicator();

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
                    } else {
                        addMessage(data.error || 'Maaf, terjadi kesalahan. Silakan coba lagi.', 'bot', true);
                    }
                    
                    setInputState(true);
                    messageInput.focus();
                })
                .catch(error => {
                    hideTypingIndicator();
                    addMessage('Koneksi bermasalah. Silakan coba lagi.', 'bot', true);
                    setInputState(true);
                    console.error('Error:', error);
                });
            }

            function addMessage(content, sender, isError = false) {
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
                scrollToBottom();
            }

            function formatMessage(content) {
                // Enhanced markdown-like formatting dengan keamanan
                return content
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                    .replace(/\*(.*?)\*/g, '<em>$1</em>')
                    .replace(/`(.*?)`/g, '<code style="background: rgba(0,255,136,0.1); padding: 2px 4px; border-radius: 3px;">$1</code>')
                    .replace(/\n/g, '<br>')
                    .replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2" target="_blank" rel="noopener" style="color: var(--accent-green); text-decoration: underline;">$1</a>');
            }

            function showTypingIndicator() {
                const typingDiv = document.createElement('div');
                typingDiv.className = 'message bot';
                typingDiv.id = 'typingIndicator';
                
                typingDiv.innerHTML = `
                    <div class="message-avatar">
                        <i class="fas fa-robot"></i>
                    </div>
                    <div class="typing-indicator">
                        <div class="typing-dots">
                            <div class="typing-dot"></div>
                            <div class="typing-dot"></div>
                            <div class="typing-dot"></div>
                        </div>
                        <span style="margin-left: 10px; color: var(--text-secondary);">AI sedang mengetik...</span>
                    </div>
                `;
                
                messagesContainer.appendChild(typingDiv);
                scrollToBottom();
            }

            function hideTypingIndicator() {
                const typingIndicator = document.getElementById('typingIndicator');
                if (typingIndicator) {
                    typingIndicator.remove();
                }
            }

            function setInputState(enabled) {
                messageInput.disabled = !enabled;
                sendBtn.disabled = !enabled;
                
                if (enabled) {
                    sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i>';
                } else {
                    sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                }
            }

            function scrollToBottom() {
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }
        });
    </script>
</body>
</html>