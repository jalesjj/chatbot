{{-- resources/views/admin/users.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kelola User - Admin Panel</title>
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

        /* Sidebar - Same as dashboard */
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

        /* Filters */
        .filters-container {
            background: var(--secondary-dark);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .filters-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr auto;
            gap: 1rem;
            align-items: end;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .filter-label {
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--text-secondary);
        }

        .filter-input, .filter-select {
            padding: 0.75rem;
            background: var(--primary-dark);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-primary);
            font-size: 1rem;
        }

        .filter-input:focus, .filter-select:focus {
            outline: none;
            border-color: var(--accent-green);
            box-shadow: 0 0 10px var(--green-glow);
        }

        .btn-filter {
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, var(--accent-green), #00cc66);
            color: black;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-filter:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px var(--green-glow);
        }

        /* Users Table */
        .users-container {
            background: var(--secondary-dark);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            overflow: hidden;
        }

        .users-table {
            width: 100%;
            border-collapse: collapse;
        }

        .users-table th,
        .users-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .users-table th {
            background: rgba(0, 255, 136, 0.05);
            font-weight: 600;
            color: var(--accent-green);
            position: sticky;
            top: 0;
        }

        .users-table tbody tr:hover {
            background: rgba(0, 255, 136, 0.03);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background: linear-gradient(135deg, #333, #555);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .user-details h4 {
            font-weight: 500;
            margin-bottom: 0.25rem;
        }

        .user-details p {
            font-size: 0.85rem;
            color: var(--text-secondary);
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-active {
            background: rgba(34, 197, 94, 0.2);
            color: #22c55e;
        }

        .status-banned {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
        }

        .role-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .role-admin {
            background: rgba(0, 255, 136, 0.2);
            color: var(--accent-green);
        }

        .role-user {
            background: rgba(156, 163, 175, 0.2);
            color: #9ca3af;
        }

        .actions-group {
            display: flex;
            gap: 0.5rem;
        }

        .btn-action {
            padding: 0.5rem;
            border: 1px solid var(--border-color);
            background: var(--primary-dark);
            color: var(--text-secondary);
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.85rem;
        }

        .btn-action:hover {
            transform: translateY(-1px);
        }

        .btn-view:hover {
            border-color: #3b82f6;
            color: #3b82f6;
        }

        .btn-ban:hover {
            border-color: #f59e0b;
            color: #f59e0b;
        }

        .btn-unban:hover {
            border-color: #10b981;
            color: #10b981;
        }

        .btn-role:hover {
            border-color: var(--accent-green);
            color: var(--accent-green);
        }

        .btn-delete:hover {
            border-color: #ef4444;
            color: #ef4444;
        }

        /* Pagination */
        .pagination-container {
            display: flex;
            justify-content: center;
            margin-top: 2rem;
        }

        .pagination {
            display: flex;
            gap: 0.5rem;
        }

        .pagination .page-link {
            padding: 0.5rem 0.75rem;
            background: var(--primary-dark);
            border: 1px solid var(--border-color);
            color: var(--text-secondary);
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .pagination .page-link:hover,
        .pagination .page-link.active {
            border-color: var(--accent-green);
            color: var(--accent-green);
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background: var(--secondary-dark);
            margin: 10% auto;
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

        .modal-body {
            margin-bottom: 1.5rem;
        }

        .modal-select {
            width: 100%;
            padding: 0.75rem;
            background: var(--primary-dark);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-primary);
            font-size: 1rem;
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

        /* Responsive */
        @media (max-width: 1024px) {
            .filters-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .users-table {
                font-size: 0.9rem;
            }

            .actions-group {
                flex-direction: column;
                gap: 0.25rem;
            }
        }

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

            .users-table th,
            .users-table td {
                padding: 0.5rem;
            }

            .user-info {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
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
                <h1 class="page-title">Kelola User</h1>
            </div>

            <!-- Filters -->
            <div class="filters-container">
                <form method="GET" action="{{ route('admin.users') }}">
                    <div class="filters-grid">
                        <div class="filter-group">
                            <label class="filter-label">Cari User</label>
                            <input type="text" name="search" class="filter-input" 
                                   placeholder="Nama atau email..." 
                                   value="{{ request('search') }}">
                        </div>
                        
                        <div class="filter-group">
                            <label class="filter-label">Role</label>
                            <select name="role" class="filter-select">
                                <option value="all" {{ request('role') == 'all' ? 'selected' : '' }}>Semua Role</option>
                                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>User</option>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label class="filter-label">Status</label>
                            <select name="status" class="filter-select">
                                <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Semua Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="banned" {{ request('status') == 'banned' ? 'selected' : '' }}>Banned</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn-filter">
                            <i class="fas fa-search"></i> Filter
                        </button>
                    </div>
                </form>
            </div>

            <!-- Users Table -->
            <div class="users-container">
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Chat Count</th>
                            <th>Bergabung</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>
                                <div class="user-info">
                                    <div class="user-avatar">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div class="user-details">
                                        <h4>{{ $user->name }}</h4>
                                        <p>{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="role-badge role-{{ $user->role }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td>
                                <span class="status-badge status-{{ $user->status }}">
                                    {{ $user->status == 'active' ? 'Aktif' : 'Banned' }}
                                </span>
                            </td>
                            <td>{{ number_format($user->chat_histories_count) }}</td>
                            <td>{{ $user->created_at->format('d M Y') }}</td>
                            <td>
                                <div class="actions-group">
                                    <button class="btn-action btn-view" 
                                            onclick="viewUser({{ $user->id }})" 
                                            title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    
                                    @if($user->status == 'active' && $user->role != 'admin')
                                    <button class="btn-action btn-ban" 
                                            onclick="banUser({{ $user->id }})" 
                                            title="Ban User">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                    @elseif($user->status == 'banned')
                                    <button class="btn-action btn-unban" 
                                            onclick="unbanUser({{ $user->id }})" 
                                            title="Unban User">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    @endif
                                    
                                    @if($user->id != Auth::id())
                                    <button class="btn-action btn-role" 
                                            onclick="changeRole({{ $user->id }}, '{{ $user->role }}')" 
                                            title="Ubah Role">
                                        <i class="fas fa-user-cog"></i>
                                    </button>
                                    @endif
                                    
                                    @if($user->role != 'admin' && $user->id != Auth::id())
                                    <button class="btn-action btn-delete" 
                                            onclick="deleteUser({{ $user->id }})" 
                                            title="Hapus User">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 3rem; color: var(--text-secondary);">
                                Tidak ada user ditemukan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($users->hasPages())
            <div class="pagination-container">
                {{ $users->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>

    <!-- Change Role Modal -->
    <div id="changeRoleModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Ubah Role User</h3>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <select id="newRole" class="modal-select">
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="modal-actions">
                <button class="btn-modal btn-secondary" onclick="closeModal()">Batal</button>
                <button class="btn-modal btn-primary" onclick="confirmRoleChange()">Simpan</button>
            </div>
        </div>
    </div>

    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <script>
        let currentUserId = null;

        function viewUser(userId) {
            window.location.href = `/admin/users/${userId}`;
        }

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

        function changeRole(userId, currentRole) {
            currentUserId = userId;
            document.getElementById('newRole').value = currentRole;
            document.getElementById('changeRoleModal').style.display = 'block';
        }

        function confirmRoleChange() {
            const newRole = document.getElementById('newRole').value;
            
            fetch(`/admin/users/${currentUserId}/role`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ role: newRole })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert(data.message, 'success');
                    closeModal();
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

        function closeModal() {
            document.getElementById('changeRoleModal').style.display = 'none';
            currentUserId = null;
        }

        function showAlert(message, type) {
            // Create alert element
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

        // Close modal when clicking outside
        window.addEventListener('click', function(e) {
            const modal = document.getElementById('changeRoleModal');
            if (e.target === modal) {
                closeModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });

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