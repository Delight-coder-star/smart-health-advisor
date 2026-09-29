<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Smart Health Advisor</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        * { box-sizing: border-box; }

        html, body { height: 100%; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background-image: url('{{ asset("images/background.png") }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(248, 250, 252, 0.88);
            z-index: -1;
        }

        .app-wrapper {
            display: flex;
            flex: 1;
            min-height: 100vh;
        }

        .sidebar {
            width: 260px;
            background: linear-gradient(180deg, #1f2937 0%, #111827 100%);
            color: white;
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-brand img {
            width: 44px;
            height: 44px;
            object-fit: contain;
            background: white;
            border-radius: 8px;
            padding: 4px;
        }

        .sidebar-brand-text h5 {
            font-size: 0.95rem;
            font-weight: 700;
            margin: 0;
            color: white;
            line-height: 1.2;
        }

        .sidebar-brand-text small {
            font-size: 0.7rem;
            color: rgba(255, 255, 255, 0.5);
        }

        .sidebar-nav {
            list-style: none;
            padding: 1rem 0;
            margin: 0;
            flex: 1;
        }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: rgba(255, 255, 255, 0.75);
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }

        .sidebar-nav a i {
            width: 24px;
            margin-right: 0.75rem;
            text-align: center;
            font-size: 1rem;
        }

        .sidebar-nav a:hover {
            background: rgba(79, 70, 229, 0.15);
            color: white;
        }

        .sidebar-nav a.active {
            background: rgba(79, 70, 229, 0.25);
            color: white;
            border-left-color: #4f46e5;
            font-weight: 500;
        }

        .sidebar-nav .nav-section {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: rgba(255, 255, 255, 0.4);
            padding: 1rem 1.5rem 0.5rem;
            font-weight: 600;
        }

        .sidebar-user {
            padding: 1rem 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .sidebar-user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
            flex-shrink: 0;
        }

        .sidebar-user-info {
            flex: 1;
            min-width: 0;
        }

        .sidebar-user-info strong {
            display: block;
            font-size: 0.85rem;
            color: white;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-user-info small {
            font-size: 0.7rem;
            color: rgba(255, 255, 255, 0.5);
            text-transform: capitalize;
        }

        .sidebar-user form button {
            background: none;
            border: none;
            color: rgba(255, 255, 255, 0.6);
            cursor: pointer;
            padding: 0.25rem;
            font-size: 0.9rem;
            transition: color 0.2s;
        }

        .sidebar-user form button:hover {
            color: #ef4444;
        }

        .main-wrapper {
            margin-left: 260px;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            width: calc(100% - 260px);
        }

        .main-content {
            flex: 1;
            padding: 2rem;
        }

        .app-footer {
            background: #1f2937;
            color: rgba(255, 255, 255, 0.7);
            padding: 1.5rem 2rem;
            text-align: center;
            font-size: 0.85rem;
            margin-top: auto;
        }

        .app-footer p { margin: 0; }

        .app-footer small {
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.75rem;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            margin-bottom: 1rem;
            background: white;
        }

        .card-header {
            background: white;
            border-bottom: 1px solid #e5e7eb;
            border-radius: 12px 12px 0 0 !important;
            font-weight: 600;
        }

        .btn-primary {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            border: none;
            border-radius: 8px;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #4338ca, #6d28d9);
        }

        .risk-badge {
            padding: 0.375rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            display: inline-block;
        }

        .risk-low { background: #d1fae5; color: #065f46; }
        .risk-moderate { background: #fef3c7; color: #92400e; }
        .risk-high { background: #fed7aa; color: #9a3412; }
        .risk-critical { background: #fecaca; color: #991b1b; }

        .stat-mini {
            background: white;
            border-radius: 12px;
            padding: 1rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
            text-align: center;
        }

        .stat-mini .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            line-height: 1.2;
        }

        .stat-mini .stat-label {
            font-size: 0.72rem;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .hero-card {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
            border-radius: 16px;
            padding: 2rem;
            position: relative;
            overflow: hidden;
        }

        .hero-card::after {
            content: '';
            position: absolute;
            top: -50%; right: -10%;
            width: 300px; height: 300px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
        }

        .hero-card .hero-inner {
            position: relative;
            z-index: 2;
        }

        .mode-card {
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 12px;
            padding: 1rem 1.25rem;
            color: white;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: left;
            width: 100%;
        }

        .mode-card:hover {
            background: rgba(255, 255, 255, 0.22);
            transform: translateY(-2px);
            color: white;
        }

        .mode-card .mode-title {
            font-weight: 600;
            font-size: 0.95rem;
        }

        .mode-card .mode-desc {
            font-size: 0.75rem;
            opacity: 0.85;
        }

        .resume-banner {
            background: linear-gradient(135deg, #f59e0b, #f97316);
            color: white;
            border-radius: 12px;
            padding: 1.25rem 1.5rem;
        }

        .session-row {
            padding: 0.85rem 1rem;
            border-bottom: 1px solid #f1f5f9;
            transition: background 0.15s ease;
        }

        .session-row:hover { background: #f9fafb; }
        .session-row:last-child { border-bottom: none; }

        .domain-chip {
            display: inline-block;
            font-size: 0.68rem;
            padding: 0.15rem 0.5rem;
            border-radius: 10px;
            background: #eef2ff;
            color: #4338ca;
            font-weight: 500;
            margin: 0.1rem;
        }

        .crisis-strip {
            background: linear-gradient(90deg, #dc2626, #b91c1c);
            color: white;
            border-radius: 12px;
            padding: 1rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .sidebar-toggle {
            display: none;
            position: fixed;
            top: 1rem; left: 1rem;
            z-index: 1100;
            background: #1f2937;
            color: white;
            border: none;
            width: 44px; height: 44px;
            border-radius: 10px;
            font-size: 1.1rem;
            cursor: pointer;
        }

        @media (max-width: 991px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            .sidebar.open { transform: translateX(0); }
            .main-wrapper { margin-left: 0; width: 100%; }
            .sidebar-toggle {
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .main-content { padding: 4rem 1rem 1rem; }
            .sidebar-backdrop {
                display: none;
                position: fixed;
                top: 0; left: 0; right: 0; bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 999;
            }
            .sidebar-backdrop.show { display: block; }
        }
    </style>

    @stack('styles')
</head>
<body>

@auth
<button class="sidebar-toggle" id="sidebarToggle" type="button">
    <i class="fas fa-bars"></i>
</button>

<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

<div class="app-wrapper">

    <aside class="sidebar" id="sidebar">

        <div class="sidebar-brand">
            <img src="{{ asset('images/logo.png') }}" alt="Harare Polytechnic">
            <div class="sidebar-brand-text">
                <h5>Smart Health Advisor</h5>
                <small>Harare Polytechnic</small>
            </div>
        </div>

        <ul class="sidebar-nav">

            <li>
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i>Dashboard
                </a>
            </li>

            @if(auth()->user()->isStudent())
                <li>
                    <a href="{{ route('assessment.index') }}" class="{{ request()->routeIs('assessment.*') ? 'active' : '' }}">
                        <i class="fas fa-clipboard-check"></i>Assessment
                    </a>
                </li>
            @endif

            <li class="nav-section">Health Tools</li>

            <li>
                <a href="{{ route('symptom.index') }}" class="{{ request()->routeIs('symptom.*') ? 'active' : '' }}">
                    <i class="fas fa-stethoscope"></i>Ask Health Advisor
                </a>
            </li>

            <li>
                <a href="{{ route('trends.index') }}" class="{{ request()->routeIs('trends.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i>Health Trends
                </a>
            </li>

            <li>
                <a href="{{ route('medications.index') }}" class="{{ request()->routeIs('medications.*') ? 'active' : '' }}">
                    <i class="fas fa-pills"></i>Medications
                </a>
            </li>

            <li>
                <a href="{{ route('reports.index') }}" class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <i class="fas fa-file-medical-alt"></i>Reports
                </a>
            </li>

            @if(auth()->user()->isAdvisor() || auth()->user()->isAdmin())
                <li class="nav-section">Advisor Panel</li>

                <li>
                    <a href="{{ route('advisor.dashboard') }}" class="{{ request()->routeIs('advisor.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt"></i>Overview
                    </a>
                </li>

                <li>
                    <a href="{{ route('advisor.students') }}" class="{{ request()->routeIs('advisor.students') || request()->routeIs('advisor.student.detail') ? 'active' : '' }}">
                        <i class="fas fa-users"></i>Students
                    </a>
                </li>
            @endif

            @if(auth()->user()->isAdmin())
                <li class="nav-section">Administration</li>

                <li>
                    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt"></i>Admin Dashboard
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users') ? 'active' : '' }}">
                        <i class="fas fa-user-cog"></i>Users
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.assessments') }}" class="{{ request()->routeIs('admin.assessments') ? 'active' : '' }}">
                        <i class="fas fa-clipboard-list"></i>Assessments
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.alerts') }}" class="{{ request()->routeIs('admin.alerts') ? 'active' : '' }}">
                        <i class="fas fa-bell"></i>Alerts
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.tips') }}" class="{{ request()->routeIs('admin.tips') ? 'active' : '' }}">
                        <i class="fas fa-lightbulb"></i>Health Tips
                    </a>
                </li>
            @endif

            <li class="nav-section">Account</li>

            <li>
                <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.*') ? 'active' : '' }}">
                    <i class="fas fa-user"></i>My Profile
                </a>
            </li>
        </ul>

        <div class="sidebar-user">
            <div class="sidebar-user-avatar">
                {{ auth()->user()->initials ?? strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="sidebar-user-info">
                <strong>{{ auth()->user()->name }}</strong>
                <small>{{ auth()->user()->role }}</small>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" title="Logout">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </form>
        </div>
    </aside>

    <div class="main-wrapper">
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

        <footer class="app-footer">
            <p>
                <i class="fas fa-heart-pulse me-2"></i>
                <strong>Smart Health Advisor</strong> · Harare Polytechnic College
            </p>
            <small>If you're in crisis, call: +263 717 923 021 · &copy; {{ date('Y') }} Harare Polytechnic</small>
        </footer>
    </div>

</div>
@endauth

@guest
<main style="flex:1; display:flex; align-items:center; justify-content:center;">
    @yield('content')
</main>
@endguest

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var toggle = document.getElementById('sidebarToggle');
    var sidebar = document.getElementById('sidebar');
    var backdrop = document.getElementById('sidebarBackdrop');

    if (toggle && sidebar) {
        toggle.addEventListener('click', function () {
            sidebar.classList.toggle('open');
            if (backdrop) backdrop.classList.toggle('show');
        });
    }

    if (backdrop && sidebar) {
        backdrop.addEventListener('click', function () {
            sidebar.classList.remove('open');
            backdrop.classList.remove('show');
        });
    }
});
</script>

@stack('scripts')

</body>
</html>