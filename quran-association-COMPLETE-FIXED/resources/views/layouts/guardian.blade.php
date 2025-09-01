<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة تحكم ولي الأمر') - جمعية تحفيظ القرآن</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #f8f9fa;
        }
        
        .navbar-brand {
            font-weight: 700;
        }
        
        .navbar-nav .nav-link {
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .navbar-nav .nav-link:hover {
            color: #4CAF50 !important;
        }
        
        .dropdown-menu {
            border-radius: 10px;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .dropdown-item {
            padding: 10px 20px;
            transition: all 0.3s ease;
        }
        
        .dropdown-item:hover {
            background-color: #f8f9fa;
            color: #4CAF50;
        }
        
        .card {
            border-radius: 15px;
            border: none;
            transition: all 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .btn {
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            transform: translateY(-1px);
        }
        
        .alert {
            border-radius: 10px;
            border: none;
        }
        
        .footer {
            background-color: #343a40;
            color: white;
            padding: 2rem 0;
            margin-top: 3rem;
        }
        
        @media (max-width: 768px) {
            .navbar-nav {
                text-align: center;
            }
            
            .navbar-collapse {
                background-color: white;
                border-radius: 10px;
                margin-top: 10px;
                padding: 15px;
                box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand text-primary" href="{{ route('guardian.dashboard') }}">
                <i class="fas fa-mosque me-2"></i>
                جمعية تحفيظ القرآن
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('guardian.dashboard') ? 'active' : '' }}" 
                           href="{{ route('guardian.dashboard') }}">
                            <i class="fas fa-home me-1"></i>
                            الرئيسية
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('guardian.profile') ? 'active' : '' }}" 
                           href="{{ route('guardian.profile') }}">
                            <i class="fas fa-user me-1"></i>
                            الملف الشخصي
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" 
                           data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i>
                            أحمد محمد العلي
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="{{ route('guardian.profile') }}">
                                    <i class="fas fa-user me-2"></i>
                                    الملف الشخصي
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('guardian.logout') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="fas fa-sign-out-alt me-2"></i>
                                        تسجيل الخروج
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        <!-- Flash Messages -->
        @if(session('success'))
            <div class="container mt-3">
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="container mt-3">
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @if(session('warning'))
            <div class="container mt-3">
                <div class="alert alert-warning alert-dismissible fade show">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @if(session('info'))
            <div class="container mt-3">
                <div class="alert alert-info alert-dismissible fade show">
                    <i class="fas fa-info-circle me-2"></i>
                    {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>
                        <i class="fas fa-mosque me-2"></i>
                        جمعية تحفيظ القرآن الكريم
                    </h5>
                    <p class="mb-0">نحو جيل قرآني متميز</p>
                </div>
                <div class="col-md-6 text-end">
                    <p class="mb-0">
                        <small>
                            &copy; {{ date('Y') }} جميع الحقوق محفوظة
                        </small>
                    </p>
                    <p class="mb-0">
                        <small>
                            <i class="fas fa-code me-1"></i>
                            تم التطوير بواسطة فريق التقنية
                        </small>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Scripts -->
    <script>
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
        
        // Confirm logout
        document.addEventListener('DOMContentLoaded', function() {
            const logoutForms = document.querySelectorAll('form[action*="logout"]');
            logoutForms.forEach(function(form) {
                form.addEventListener('submit', function(e) {
                    if (!confirm('هل أنت متأكد من تسجيل الخروج؟')) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>

