{{-- resources/views/admin/user-detail.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Detail User: {{ $user->name }} - Admin Panel</title>
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
            --sidebar-width: 280px;
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
        }

        .admin-container {
            display: flex;
            height: 100vh;
        }

        /* Sidebar */
        .admin-sidebar {
            width: var(--sidebar-width);
            background: var(--secondary-dark);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
        }

        .admin-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }

        .admin-logo {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .admin-logo .logo-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--accent-green), #00cc66);
            display: flex;
            align-items: center;
            justify-content: center;
            color: black;
            font-size: 1.2rem;
        }

        .admin-logo h1 {
            font-size: 1.25rem;
            font-weight: 600;
        }

        .admin-user {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            background: var(--primary-dark);
            border-radius: 10px;
        }

        .admin-user .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: linear-gradient(135deg, #333, #555);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.9rem;
        }

        .admin-nav {
            flex: 1;
            padding: 1rem;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.75rem 1rem;
            margin-bottom: 0.5rem;
            border-radius: 8px;
            color: var(--text-secondary);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .nav-item:hover, .nav-item.active {
            background: rgba(0, 255, 136, 0.1);
            color: var(--accent-green);
            border-left: 3px solid var(--accent-green);
        }

        .nav-item i {
            width: 20px;
            text-align: center;
        }

        .admin-actions {
            padding: 1rem;
            border-top: 1px solid var(--border-color);
        }

        .btn-logout {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem;
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid rgba(255, 68, 68, 0.3);
            color: #ff4444;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-logout:hover {
            background: rgba(255, 68, 68, 0.2);
            color: #ff6666;
        }

        /* Main Content */
        .admin-main {
            flex: 1;
            overflow-y: auto;
            padding: 2rem;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 600;
            background: linear-gradient(135deg, var(--accent-green), #00cc66);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .btn-back {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1rem;
            background: var(--primary-dark);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            border-color: var(--accent-green);
            color: var(--accent-green);
        }

        /* User Profile Card */
        .user-profile-card {
            background: var(--secondary-dark);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .user-profile-header {
            display: flex;
            align-items: center;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .user-profile-avatar {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            background: linear-gradient(135deg, #333, #555);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            font-weight: 600;
        }

        .user-profile-info h2 {
            font-size: 1.75rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .user-profile-info p {
            color: var(--text-secondary);
            margin-bottom: 0.25rem;
        }

        .user-badges {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }

        .badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .badge-role {
            background: rgba(0, 255, 136, 0.2);
            color: var(--accent-green);
        }

        .badge-status-active {
            background: rgba(34, 197, 94, 0.2);
            color: #22c55e;
        }

        .badge-status-banned {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
        }

        .user-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .btn-action {
            padding: 0.75rem 1.5rem;
            border: 1px solid var(--border-color);
            background: var(--primary-dark);
            color: var(--text-primary);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-action:hover {
            transform: translateY(-2px);
        }

        .btn-ban:hover {
            border-color: #f59e0b;
            color: #f59e0b;
        }

        .btn-unban:hover {
            border-color: #10b981;
            color: #10b981;
        }

        .btn-delete:hover {
            border-color: #ef4444;
            color: #ef4444;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--secondary-dark);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background: rgba(0, 255, 136, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            color: var(--accent-green);
            font-size: 1.5rem;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        /* Chat History */
        .chat-history-card {
            background: var(--secondary-dark);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 2rem;
        }

        .chat-history-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .chat-history-title {
            font-size: 1.5rem;
            font-weight: 600;
        }

        .btn-delete-chats {
            padding: 0.5rem 1rem;
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #ef4444;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-delete-chats:hover {
            background: rgba(239, 68, 68, 0.2);
        }

        .chat-item {
            background: var(--primary-dark);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .chat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .chat-session {
            font-weight: 500;
            color: var(--accent-green);
        }

        .chat-time {
            font-size: 0.85rem;
            color: var(--text-secondary);
        }

        .chat-content {
            color: var(--text-secondary);
            line-height: 1.5;
        }

        .chat-user-msg {
            margin-bottom: 0.5rem;
        }

        .chat-bot-msg {
            padding-left: 1rem;
            border-left: 2px solid var(--accent-green);
        }

        .no-chats {
            text-align: center;
            color: var(--text-secondary);
            padding: 3rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .admin-container {
                flex-direction: column;
            }

            .admin-sidebar {
                width: 100%;
                height: auto;
            }

            .admin-main {
                padding: 1rem;
            }

            .page-header {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }

            .user-profile-header {
                flex-direction: column;
                text-align: center;
            }

            .user-actions {
                flex-direction: column;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="admin-sidebar">
            <div class="admin-header">
                <div class="admin-logo">
                    <div class="logo-icon">
                        <i class="fas fa-robot"></i>
                    </div>
                    <h1>Admin Panel</h1>
                </div>
                
                <div class="admin-user">
                    <div class="user-avatar">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div>
                        <div style="font-weight: 500;">{{ Auth::user()->name }}</div>
                        <div style="font-size: 0.8rem; color: var(--text-secondary);">Administrator</div>
                    </div>
                </div>
            </div>
            
            <div class="admin-nav">
                <a href="{{ route('admin.dashboard') }}" class="nav-item">
                    <i class="fas fa-chart-line"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.users') }}" class="nav-item active">
                    <i class="fas fa-users"></i>
                    <span>Kelola User</span>
                </a>
                <a href="{{ route('chatbot.index') }}" class="nav-item">
                    <i class="fas fa-comments"></i>
                    <span>Lihat Chatbot</span>
                </a>
            </div>
            
            <div class="admin-actions">
                <a href="{{ route('logout') }}" class="btn-logout" 
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="admin-main">
            <div class="page-header">
                <h1 class="page-title">Detail User</h1>
                <a href="{{ route('admin.users') }}" class="btn-back">
                    <i class="fas fa-arrow-left"></i>
                    Kembali
                </a>
            </div>

            <!-- User Profile Card -->
            <div class="user-profile-card">
                <div class="user-profile-header">
                    <div class="user-profile-avatar">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div class="user-profile-info">
                        <h2>{{ $user->name }}</h2>
                        <p><strong>Email:</strong> {{ $user->email }}</p>
                        <p><strong>Bergabung:</strong> {{ $user->created_at->format('d M Y, H:i') }}</p>
                        <p><strong>Terakhir Login:</strong> {{ $user->updated_at->diffForHumans() }}</p>
                    </div>
                </div>

                <div class="user-badges">
                    <span class="badge badge-role">
                        <i class="fas fa-user-cog"></i> {{ ucfirst($user->role) }}
                    </span>
                    <span class="badge badge-status-{{ $user->status }}">
                        <i class="fas fa-{{ $user->status == 'active' ? 'check-circle' : 'ban' }}"></i>
                        {{ $user->status == 'active' ? 'Aktif' : 'Banned' }}
                    </span>
                </div>

                @if($user->id != Auth::id())
                <div class="user-actions">
                    @if($user->status == 'active' && $user->role != 'admin')
                    <button class="btn-action btn-ban" onclick="banUser({{ $user->id }})">
                        <i class="fas fa-ban"></i>
                        Ban User
                    </button>
                    @elseif($user->status == 'banned')
                    <button class="btn-action btn-unban" onclick="unbanUser({{ $user->id }})">
                        <i class="fas fa-check"></i>
                        Unban User
                    </button>
                    @endif

                    @if($user->role != 'admin')
                    <button class="btn-action btn-delete" onclick="deleteUser({{ $user->id }})">
                        <i class="fas fa-trash"></i>
                        Hapus User
                    </button>
                    @endif
                </div>
                @endif
            </div>

            <!-- User Statistics -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-comments"></i>
                    </div>
                    <div class="stat-value">{{ number_format($userStats['total_chats']) }}</div>
                    <div class="stat-label">Total Chat</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <div class="stat-value">{{ number_format($userStats['total_sessions']) }}</div>
                    <div class="stat-label">Total Session</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="stat-value">
                        {{ $userStats['first_chat'] ? $userStats['first_chat']->diffInDays(now()) : 0 }}
                    </div>
                    <div class="stat-label">Hari Aktif</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-value">
                        {{ $userStats['last_chat'] ? $userStats['last_chat']->diffForHumans(null, true) : 'Never' }}
                    </div>
                    <div class="stat-label">Chat Terakhir</div>
                </div>
            </div>

            <!-- Chat History -->
            <div class="chat-history-card">
                <div class="chat-history-header">
                    <h3 class="chat-history-title">Riwayat Chat Terbaru</h3>
                    @if($user->chatHistories->count() > 0)
                    <button class="btn-delete-chats" onclick="deleteUserChats({{ $user->id }})">
                        <i class="fas fa-trash"></i>
                        Hapus Semua Chat
                    </button>
                    @endif
                </div>

                @forelse($user->chatHistories as $chat)
                <div class="chat-item">
                    <div class="chat-header">
                        <span class="chat-session">Session: {{ Str::limit($chat->session_id, 20) }}</span>
                        <span class="chat-time">{{ $chat->created_at->format('d M Y, H:i') }}</span>
                    </div>
                    <div class="chat-content">
                        <div class="chat-user-msg">
                            <strong>User:</strong> {{ Str::limit($chat->user_message, 150) }}
                        </div>
                        <div class="chat-bot-msg">
                            <strong>Bot:</strong> {{ Str::limit($chat->bot_response, 150) }}
                        </div>
                    </div>
                </div>
                @empty
                <div class="no-chats">
                    <i class="fas fa-comments" style="font-size: 3rem; margin-bottom: 1rem; color: var(--text-secondary);"></i>
                    <p>User belum memiliki riwayat chat</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <script>
        function banUser(userId) {
            if (!confirm('Apakah Anda yakin ingin memblokir user ini?')) return;

            fetch(`/admin/users/${userId}/ban`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert(data.message, 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showAlert(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Terjadi kesalahan', 'error');
            });
        }

        function unbanUser(userId) {
            if (!confirm('Apakah Anda yakin ingin mengaktifkan kembali user ini?')) return;

            fetch(`/admin/users/${userId}/unban`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert(data.message, 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showAlert(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Terjadi kesalahan', 'error');
            });
        }

        function deleteUser(userId) {
            if (!confirm('Apakah Anda yakin ingin menghapus user ini? Semua chat akan ikut terhapus dan tidak dapat dikembalikan.')) return;

            fetch(`/admin/users/${userId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert(data.message, 'success');
                    setTimeout(() => window.location.href = '/admin/users', 1500);
                } else {
                    showAlert(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Terjadi kesalahan', 'error');
            });
        }

        function deleteUserChats(userId) {
            if (!confirm('Apakah Anda yakin ingin menghapus semua chat user ini? Data tidak dapat dikembalikan.')) return;

            fetch(`/admin/users/${userId}/chats`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert(data.message, 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showAlert(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Terjadi kesalahan', 'error');
            });
        }

        function showAlert(message, type) {
            const alert = document.createElement('div');
            alert.className = `alert alert-${type}`;
            alert.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 1rem 1.5rem;
                border-radius: 8px;
                color: white;
                font-weight: 500;
                z-index: 9999;
                animation: slideIn 0.3s ease-out;
                box-shadow: 0 4px 12px rgba(0,0,0,0.3);
                ${type === 'success' ? 'background: linear-gradient(135deg, #10b981, #059669);' : 'background: linear-gradient(135deg, #ef4444, #dc2626);'}
            `;
            alert.textContent = message;

            document.body.appendChild(alert);

            setTimeout(() => {
                alert.style.animation = 'slideOut 0.3s ease-in forwards';
                setTimeout(() => {
                    if (alert.parentNode) {
                        alert.parentNode.removeChild(alert);
                    }
                }, 300);
            }, 3000);
        }

        // Add CSS animations
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOut {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(100%); opacity: 0; }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>