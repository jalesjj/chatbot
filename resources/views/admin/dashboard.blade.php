{{-- resources/views/admin/dashboard.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard - Jales ChatBot</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    
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
            margin-bottom: 2rem;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, var(--accent-green), #00cc66);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .page-subtitle {
            color: var(--text-secondary);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: linear-gradient(135deg, var(--secondary-dark), #252525);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--accent-green), #00cc66);
        }

        .stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        .stat-title {
            color: var(--text-secondary);
            font-size: 0.9rem;
            font-weight: 500;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: rgba(0, 255, 136, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--accent-green);
            font-size: 1.2rem;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .stat-change {
            font-size: 0.8rem;
            color: var(--accent-green);
        }

        /* Charts */
        .charts-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .chart-card {
            background: var(--secondary-dark);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1.5rem;
        }

        .chart-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--text-primary);
        }

        .chart-container {
            position: relative;
            height: 300px;
        }

        /* Recent Users */
        .recent-users {
            background: var(--secondary-dark);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1.5rem;
        }

        .recent-users-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--text-primary);
        }

        .user-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.75rem;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
        }

        .user-item:hover {
            background: rgba(0, 255, 136, 0.05);
        }

        .user-item .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: linear-gradient(135deg, #333, #555);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        .user-info {
            flex: 1;
        }

        .user-name {
            font-weight: 500;
            color: var(--text-primary);
        }

        .user-email {
            font-size: 0.85rem;
            color: var(--text-secondary);
        }

        .user-time {
            font-size: 0.8rem;
            color: var(--text-secondary);
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .charts-grid {
                grid-template-columns: 1fr;
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
                <a href="{{ route('admin.dashboard') }}" class="nav-item active">
                    <i class="fas fa-chart-line"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.users') }}" class="nav-item">
                    <i class="fas fa-users"></i>
                    <span>Kelola User</span>
                </a>
                <a href="{{ route('chatbot.index') }}" class="nav-item">
                    <i class="fas fa-comments"></i>
                    <span>Lihat Chatbot</span>
                </a>
            </div>
            
            <!-- FIXED: Only one logout button here -->
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
                <h1 class="page-title">Dashboard Admin</h1>
                <p class="page-subtitle">Selamat datang di panel administrasi Jales ChatBot</p>
            </div>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-title">Total Users</div>
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    <div class="stat-value">{{ number_format($totalUsers) }}</div>
                    <div class="stat-change">+{{ $newRegistrations }} bulan ini</div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-title">Registrasi Baru</div>
                        <div class="stat-icon">
                            <i class="fas fa-user-plus"></i>
                        </div>
                    </div>
                    <div class="stat-value">{{ number_format($newRegistrations) }}</div>
                    <div class="stat-change">30 hari terakhir</div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-title">Total Chat</div>
                        <div class="stat-icon">
                            <i class="fas fa-comments"></i>
                        </div>
                    </div>
                    <div class="stat-value">{{ number_format($totalChats) }}</div>
                    <div class="stat-change">Semua percakapan</div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-title">User Banned</div>
                        <div class="stat-icon">
                            <i class="fas fa-user-slash"></i>
                        </div>
                    </div>
                    <div class="stat-value">{{ number_format($bannedUsers) }}</div>
                    <div class="stat-change">User diblokir</div>
                </div>
            </div>

            <!-- Charts -->
            <div class="charts-grid">
                <div class="chart-card">
                    <h3 class="chart-title">Pertumbuhan User (7 Hari Terakhir)</h3>
                    <div class="chart-container">
                        <canvas id="userGrowthChart"></canvas>
                    </div>
                </div>

                <div class="recent-users">
                    <h3 class="recent-users-title">User Terbaru</h3>
                    @forelse($recentUsers as $user)
                    <div class="user-item">
                        <div class="user-avatar">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div class="user-info">
                            <div class="user-name">{{ $user->name }}</div>
                            <div class="user-email">{{ $user->email }}</div>
                        </div>
                        <div class="user-time">
                            {{ $user->created_at->diffForHumans() }}
                        </div>
                    </div>
                    @empty
                    <div style="text-align: center; color: var(--text-secondary); padding: 2rem;">
                        Belum ada user terbaru
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <script>
        // User Growth Chart
        const ctx = document.getElementById('userGrowthChart').getContext('2d');
        const userGrowthData = @json($userGrowth);
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: userGrowthData.map(item => item.label),
                datasets: [{
                    label: 'Registrasi Baru',
                    data: userGrowthData.map(item => item.count),
                    borderColor: '#00ff88',
                    backgroundColor: 'rgba(0, 255, 136, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#00ff88',
                    pointBorderColor: '#00cc66',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: '#a0a0a0',
                            stepSize: 1
                        },
                        grid: {
                            color: '#333333'
                        }
                    },
                    x: {
                        ticks: {
                            color: '#a0a0a0'
                        },
                        grid: {
                            color: '#333333'
                        }
                    }
                },
                elements: {
                    point: {
                        hoverBackgroundColor: '#00ff88'
                    }
                }
            }
        });
    </script>
</body>
</html>