<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --primary-color: #4f46e5;
            --secondary-color: #7c3aed;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --dark-color: #1f2937;
        }
        * { font-family: 'Inter', sans-serif; }
        body { background: #f8fafc; min-height: 100vh; display: flex; }
        .sidebar {
            width: 260px;
            background: linear-gradient(180deg, #1f2937 0%, #111827 100%);
            min-height: 100vh;
            position: fixed;
            left: 0; top: 0;
            padding: 1.5rem 0;
            color: white;
            z-index: 100;
        }
        .sidebar-brand {
            padding: 0 1.5rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 1rem;
        }
        .sidebar-brand h5 { font-weight: 700; margin: 0; color: white; }
        .sidebar-brand small { color: rgba(255,255,255,0.5); font-size: 0.75rem; }
        .sidebar-nav { list-style: none; padding: 0; margin: 0; }
        .sidebar-nav li a {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: all 0.2s;
            font-size: 0.9rem;
        }
        .sidebar-nav li a:hover,
        .sidebar-nav li a.active {
            background: rgba(79, 70, 229, 0.2);
            color: white;
            border-left: 3px solid var(--primary-color);
        }
        .sidebar-nav li a i { width: 20px; margin-right: 0.75rem; }
        .main-content {
            margin-left: 260px;
            flex: 1;
            padding: 2rem;
            width: calc(100% - 260px);
        }
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }
        .card-header {
            background: white;
            border-bottom: 1px solid #e5e7eb;
            border-radius: 12px 12px 0 0 !important;
            font-weight: 600;
            padding: 1rem 1.25rem;
        }
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }
        .stat-icon {
            width: 48px; height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }
        .risk-badge {
            padding: 0.375rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        .risk-low { background: #d1fae5; color: #065f46; }
        .risk-moderate { background: #fef3c7; color: #92400e; }
        .risk-high { background: #fed7aa; color: #9a3412; }
        .risk-critical { background: #fecaca; color: #991b1b; }
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
        }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .main-content { margin-left: 0; width: 100%; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-brand">
            <h5><i class="fas fa-heart-pulse me-2"></i>SHA Admin</h5>
            <small>Administration Panel</small>
        </div>
        <ul class="sidebar-nav">
            <li><a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i>Dashboard</a></li>
            <li><a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users') ? 'active' : '' }}">
                <i class="fas fa-users"></i>Users</a></li>
            <li><a href="{{ route('admin.assessments') }}" class="{{ request()->routeIs('admin.assessments') ? 'active' : '' }}">
                <i class="fas fa-clipboard-check"></i>Assessments</a></li>
            <li><a href="{{ route('admin.alerts') }}" class="{{ request()->routeIs('admin.alerts') ? 'active' : '' }}">
                <i class="fas fa-bell"></i>Alerts</a></li>
            <li><a href="{{ route('admin.tips') }}" class="{{ request()->routeIs('admin.tips') ? 'active' : '' }}">
                <i class="fas fa-lightbulb"></i>Health Tips</a></li>
            <li><hr style="border-color: rgba(255,255,255,0.1); margin: 1rem 1.5rem;"></li>
            <li><a href="{{ route('dashboard') }}"><i class="fas fa-home"></i>Main Dashboard</a></li>
            <li>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" style="background:none;border:none;color:rgba(255,255,255,0.7);padding:0.75rem 1.5rem;width:100%;text-align:left;font-size:0.9rem;display:flex;align-items:center;">
                        <i class="fas fa-sign-out-alt" style="width:20px;margin-right:0.75rem;"></i>Logout
                    </button>
                </form>
            </li>
        </ul>
    </aside>

    <main class="main-content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>