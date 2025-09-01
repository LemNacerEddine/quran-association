<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'لوحة التحكم - جمعية تحفيظ القرآن الكريم')</title>
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts - Cairo for Arabic -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        :root {
            --primary-color: #2E7D32;
            --secondary-color: #4CAF50;
            --accent-color: #81C784;
            --dark-color: #1B5E20;
            --light-color: #E8F5E8;
            --sidebar-width: 280px;
            --header-height: 70px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Cairo', sans-serif;
            background: #f8f9fa;
            overflow-x: hidden;
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            right: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(180deg, var(--primary-color) 0%, var(--dark-color) 100%);
            z-index: 1000;
            transition: all 0.3s ease;
            box-shadow: -2px 0 10px rgba(0,0,0,0.1);
        }

        .sidebar.collapsed {
            width: 80px;
        }

        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-header .logo {
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .sidebar-header .logo i {
            font-size: 2rem;
            color: var(--accent-color);
        }

        .sidebar-nav {
            padding: 20px 0;
        }

        .nav-item {
            margin: 5px 15px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
            transform: translateX(-5px);
        }

        .nav-link.active {
            background: var(--accent-color);
            color: var(--dark-color);
            font-weight: 600;
        }

        .nav-link i {
            width: 20px;
            margin-left: 15px;
            font-size: 1.1rem;
        }

        .sidebar.collapsed .nav-link span {
            display: none;
        }

        .sidebar.collapsed .nav-link {
            justify-content: center;
            padding: 12px;
        }

        .sidebar.collapsed .nav-link i {
            margin: 0;
        }

        /* Main Content */
        .main-content {
            margin-right: var(--sidebar-width);
            transition: all 0.3s ease;
        }

        .main-content.expanded {
            margin-right: 80px;
        }

        /* Header */
        .header {
            background: white;
            height: var(--header-height);
            padding: 0 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .sidebar-toggle {
            background: none;
            border: none;
            font-size: 1.2rem;
            color: var(--primary-color);
            cursor: pointer;
            padding: 8px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .sidebar-toggle:hover {
            background: var(--light-color);
        }

        .breadcrumb {
            background: none;
            padding: 0;
            margin: 0;
            font-size: 0.9rem;
        }

        .breadcrumb-item a {
            color: var(--primary-color);
            text-decoration: none;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .notification-btn {
            position: relative;
            background: none;
            border: none;
            font-size: 1.2rem;
            color: #6c757d;
            cursor: pointer;
            padding: 8px;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .notification-btn:hover {
            background: var(--light-color);
            color: var(--primary-color);
        }

        .notification-badge {
            position: absolute;
            top: 0;
            right: 0;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            padding: 8px 15px;
            border-radius: 25px;
            transition: all 0.3s ease;
        }

        .user-menu:hover {
            background: var(--light-color);
        }

        .user-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        .user-info h6 {
            margin: 0;
            font-size: 0.9rem;
            color: #333;
        }

        .user-info small {
            color: #6c757d;
        }

        /* Content Area */
        .content {
            padding: 30px;
        }

        .page-title {
            color: #333;
            font-weight: 700;
            margin-bottom: 30px;
            font-size: 2rem;
        }

        /* Cards */
        .dashboard-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            border: none;
            transition: all 0.3s ease;
            height: 100%;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        .stats-card {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .stats-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            transform: rotate(45deg);
        }

        .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            position: relative;
            z-index: 2;
        }

        .stats-label {
            font-size: 1rem;
            opacity: 0.9;
            position: relative;
            z-index: 2;
        }

        .stats-icon {
            position: absolute;
            top: 20px;
            left: 20px;
            font-size: 2rem;
            opacity: 0.3;
        }

        /* Tables */
        .table-container {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }

        .table {
            margin: 0;
        }

        .table thead th {
            background: var(--light-color);
            border: none;
            font-weight: 600;
            color: var(--dark-color);
            padding: 15px;
        }

        .table tbody td {
            padding: 15px;
            border-color: #f1f3f4;
            vertical-align: middle;
        }

        /* Buttons */
        .btn-primary {
            background: var(--primary-color);
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: var(--dark-color);
            transform: translateY(-2px);
        }

        .btn-success {
            background: var(--secondary-color);
            border: none;
            border-radius: 8px;
        }

        .btn-outline-primary {
            border-color: var(--primary-color);
            color: var(--primary-color);
            border-radius: 8px;
        }

        .btn-outline-primary:hover {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-right: 0;
            }

            .content {
                padding: 20px 15px;
            }

            .page-title {
                font-size: 1.5rem;
            }
        }

        /* Custom Scrollbar */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.1);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.3);
            border-radius: 3px;
        }

        /* Animation */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .slide-in {
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from { transform: translateX(20px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    </style>
    
    @yield('styles')
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="{{ route('dashboard') }}" class="logo">
                <i class="fas fa-mosque"></i>
                <span>جمعية تحفيظ القرآن</span>
            </a>
        </div>
        
        <nav class="sidebar-nav">
            <div class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>لوحة التحكم</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="{{ route('students.index') }}" class="nav-link {{ request()->routeIs('students.*') ? 'active' : '' }}">
                    <i class="fas fa-user-graduate"></i>
                    <span>إدارة الطلاب</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="{{ route('guardians.index') }}" class="nav-link {{ request()->routeIs('guardians.*') ? 'active' : '' }}">
                    <i class="fas fa-user-friends"></i>
                    <span>إدارة أولياء الأمور</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="{{ route('teachers.index') }}" class="nav-link {{ request()->routeIs('teachers.*') ? 'active' : '' }}">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <span>إدارة المعلمين</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="{{ route('circles.index') }}" class="nav-link {{ request()->routeIs('circles.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                    <span>إدارة الحلقات</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="{{ route('schedules.index') }}" class="nav-link {{ request()->routeIs('schedules.*') ? 'active' : '' }}">
                    <i class="fas fa-calendar-alt"></i>
                    <span>إدارة الجدولة</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="{{ route('sessions.index') }}" class="nav-link {{ request()->routeIs('sessions.*') ? 'active' : '' }}">
                    <i class="fas fa-chalkboard"></i>
                    <span>إدارة الجلسات</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="{{ route('attendance.index') }}" class="nav-link {{ request()->routeIs('attendance.*') ? 'active' : '' }}">
                    <i class="fas fa-calendar-check"></i>
                    <span>تسجيل الحضور</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-chart-bar"></i>
                    <span>التقارير</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-bell"></i>
                    <span>الإشعارات</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-cog"></i>
                    <span>الإعدادات</span>
                </a>
            </div>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Header -->
        <header class="header">
            <div class="header-left">
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        @yield('breadcrumb')
                    </ol>
                </nav>
            </div>
            
            <div class="header-right">
                <button class="notification-btn">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">3</span>
                </button>
                
                <div class="user-menu">
                    <div class="user-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="user-info">
                        <h6>مدير النظام</h6>
                        <small>المشرف العام</small>
                    </div>
                    <i class="fas fa-chevron-down"></i>
                </div>
            </div>
        </header>

        <!-- Content -->
        <div class="content">
            @yield('content')
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
    <script>
        // Sidebar Toggle
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        });

        // Mobile Sidebar
        if (window.innerWidth <= 768) {
            document.getElementById('sidebarToggle').addEventListener('click', function() {
                const sidebar = document.getElementById('sidebar');
                sidebar.classList.toggle('show');
            });
        }

        // Add fade-in animation to content
        document.addEventListener('DOMContentLoaded', function() {
            const content = document.querySelector('.content');
            content.classList.add('fade-in');
        });
    </script>
    
    @yield('scripts')
    @stack('scripts')
</body>
</html>

