<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>نظام إدارة جمعية تحفيظ القرآن الكريم</title>
        
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        
        <style>
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                direction: rtl;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
            }
            .hero-section {
                background: rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(10px);
                border-radius: 20px;
                border: 1px solid rgba(255, 255, 255, 0.2);
                color: white;
                padding: 3rem;
                margin: 2rem 0;
            }
            .feature-card {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                border-radius: 15px;
                border: 1px solid rgba(255, 255, 255, 0.2);
                transition: transform 0.3s ease, box-shadow 0.3s ease;
                height: 100%;
            }
            .feature-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
            }
            .feature-icon {
                width: 60px;
                height: 60px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 1.5rem;
                margin: 0 auto 1rem;
            }
            .btn-custom {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border: none;
                border-radius: 10px;
                padding: 12px 30px;
                color: white;
                font-weight: 600;
                text-decoration: none;
                display: inline-block;
                transition: all 0.3s ease;
            }
            .btn-custom:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
                color: white;
            }
            .logo-text {
                font-size: 3rem;
                font-weight: 700;
                text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
                margin-bottom: 1rem;
            }
            .auth-links {
                position: absolute;
                top: 20px;
                left: 20px;
            }
            .auth-links a {
                color: white;
                text-decoration: none;
                margin: 0 10px;
                padding: 8px 20px;
                border: 2px solid rgba(255, 255, 255, 0.3);
                border-radius: 25px;
                transition: all 0.3s ease;
            }
            .auth-links a:hover {
                background: rgba(255, 255, 255, 0.2);
                color: white;
            }
            
            /* Login Option Cards */
            .login-option-card {
                background: rgba(255, 255, 255, 0.15);
                backdrop-filter: blur(10px);
                border-radius: 20px;
                border: 2px solid rgba(255, 255, 255, 0.2);
                padding: 2rem;
                height: 100%;
                transition: all 0.3s ease;
                position: relative;
                overflow: hidden;
            }
            
            .login-option-card:hover {
                transform: translateY(-5px);
                border-color: rgba(255, 255, 255, 0.4);
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            }
            
            .login-icon {
                width: 80px;
                height: 80px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 2rem;
                margin: 0 auto 1.5rem;
                position: relative;
                z-index: 2;
            }
            
            .guardian-icon {
                background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
                box-shadow: 0 10px 20px rgba(76, 175, 80, 0.3);
            }
            
            .admin-icon {
                background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%);
                box-shadow: 0 10px 20px rgba(33, 150, 243, 0.3);
            }
            
            .btn-login {
                padding: 15px 30px;
                border-radius: 50px;
                border: none;
                color: white;
                font-weight: 600;
                text-decoration: none;
                display: inline-block;
                transition: all 0.3s ease;
                position: relative;
                overflow: hidden;
                z-index: 2;
            }
            
            .guardian-btn {
                background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
                box-shadow: 0 8px 15px rgba(76, 175, 80, 0.3);
            }
            
            .guardian-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 12px 25px rgba(76, 175, 80, 0.4);
                color: white;
            }
            
            .teacher-btn {
                background: linear-gradient(135deg, #FF9800 0%, #F57C00 100%);
                box-shadow: 0 8px 15px rgba(255, 152, 0, 0.3);
            }
            
            .teacher-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 12px 25px rgba(255, 152, 0, 0.4);
                color: white;
            }
            
            .admin-btn {
                background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%);
                box-shadow: 0 8px 15px rgba(33, 150, 243, 0.3);
            }
            
            .admin-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 12px 25px rgba(33, 150, 243, 0.4);
                color: white;
            }
            
            .login-info {
                border-top: 1px solid rgba(255, 255, 255, 0.2);
                padding-top: 1rem;
            }
            
            .demo-info {
                backdrop-filter: blur(10px);
            }
            
            @media (max-width: 768px) {
                .login-option-card {
                    margin-bottom: 2rem;
                }
                
                .logo-text {
                    font-size: 2rem;
                }
                
                .hero-section {
                    padding: 2rem 1rem;
                }
            }
        </style>
    </head>
    <body>
        <!-- Auth Links -->
        @if (Route::has('login'))
            <div class="auth-links">
                @auth
                    <a href="{{ url('/dashboard') }}">
                        <i class="fas fa-tachometer-alt me-2"></i>
                        لوحة التحكم
                    </a>
                @else
                    <a href="{{ route('login') }}">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        تسجيل الدخول
                    </a>
                        <!-- تم إزالة رابط التسجيل - الحسابات تُنشأ من قبل الإدارة -->
                @endauth
            </div>
        @endif

        <div class="container">
            <!-- Hero Section -->
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="hero-section text-center">
                        <h1 class="logo-text">نظام إدارة جمعية تحفيظ القرآن الكريم</h1>
                        <p class="lead mb-5">
                            نظام شامل ومتطور لإدارة جمعيات تحفيظ القرآن الكريم بكفاءة وسهولة
                        </p>
                        
                        <!-- Login Options -->
                        <div class="row g-4 justify-content-center">
                            <!-- Guardian Login -->
                            <div class="col-md-4">
                                <div class="login-option-card">
                                    <div class="login-icon guardian-icon">
                                        <i class="fas fa-user-friends"></i>
                                    </div>
                                    <h4 class="mb-3">دخول أولياء الأمور</h4>
                                    <p class="mb-4">
                                        متابعة أولادكم وحضورهم وتقاريرهم الدراسية
                                    </p>
                                    <a href="{{ route('guardian.login') }}" class="btn-login guardian-btn">
                                        <i class="fas fa-sign-in-alt me-2"></i>
                                        دخول ولي الأمر
                                    </a>
                                    <div class="login-info mt-3">
                                        <small class="text-light opacity-75">
                                            <i class="fas fa-phone me-1"></i>
                                            استخدم رقم الهاتف وكود الدخول
                                        </small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Teacher Login -->
                            <div class="col-md-4">
                                <div class="login-option-card">
                                    <div class="login-icon teacher-icon">
                                        <i class="fas fa-chalkboard-teacher"></i>
                                    </div>
                                    <h4 class="mb-3">دخول المعلمين</h4>
                                    <p class="mb-4">
                                        تسجيل الحضور وإعطاء النقاط للطلاب
                                    </p>
                                    <a href="{{ route('teacher.login') }}" class="btn-login teacher-btn">
                                        <i class="fas fa-sign-in-alt me-2"></i>
                                        دخول المعلم
                                    </a>
                                    <div class="login-info mt-3">
                                        <small class="text-light opacity-75">
                                            <i class="fas fa-phone me-1"></i>
                                            استخدم رقم الهاتف وكود الدخول
                                        </small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Administrator Login -->
                            <div class="col-md-4">
                                <div class="login-option-card">
                                    <div class="login-icon admin-icon">
                                        <i class="fas fa-user-shield"></i>
                                    </div>
                                    <h4 class="mb-3">دخول الإدارة</h4>
                                    <p class="mb-4">
                                        إدارة النظام والطلاب والمعلمين والحلقات
                                    </p>
                                    <a href="{{ route('login') }}" class="btn-login admin-btn">
                                        <i class="fas fa-cog me-2"></i>
                                        دخول الإدارة
                                    </a>
                                    <div class="login-info mt-3">
                                        <small class="text-light opacity-75">
                                            <i class="fas fa-envelope me-1"></i>
                                            استخدم البريد الإلكتروني وكلمة المرور
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Quick Demo Info -->
                        <div class="demo-info mt-5 p-4" style="background: rgba(255, 255, 255, 0.1); border-radius: 15px; border: 1px solid rgba(255, 255, 255, 0.2);">
                            <h6 class="mb-3">
                                <i class="fas fa-info-circle me-2"></i>
                                للتجربة السريعة
                            </h6>
                            <div class="row text-start">
                                <div class="col-md-6">
                                    <strong>ولي الأمر:</strong><br>
                                    <small>الهاتف: 0501234567</small><br>
                                    <small>الكود: 4567</small>
                                </div>
                                <div class="col-md-6">
                                    <strong>الإدارة:</strong><br>
                                    <small>البريد: admin@quran.com</small><br>
                                    <small>كلمة المرور: password</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Features Section -->
            <div class="row g-4 my-5">
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card p-4 text-center">
                        <div class="feature-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h5 class="fw-bold">إدارة الطلاب</h5>
                        <p class="text-muted">
                            نظام شامل لإدارة بيانات الطلاب وتتبع تقدمهم في الحفظ
                        </p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="feature-card p-4 text-center">
                        <div class="feature-icon">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <h5 class="fw-bold">إدارة المعلمين</h5>
                        <p class="text-muted">
                            تنظيم بيانات المعلمين وتوزيع الحلقات والمهام
                        </p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="feature-card p-4 text-center">
                        <div class="feature-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <h5 class="fw-bold">إدارة الجدولة</h5>
                        <p class="text-muted">
                            نظام متطور لجدولة الحلقات والجلسات مع فحص التعارضات
                        </p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="feature-card p-4 text-center">
                        <div class="feature-icon">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <h5 class="fw-bold">التقارير والإحصائيات</h5>
                        <p class="text-muted">
                            تقارير شاملة وإحصائيات مفصلة لمتابعة الأداء
                        </p>
                    </div>
                </div>
            </div>

            <!-- Additional Features -->
            <div class="row g-4 mb-5">
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="feature-icon me-3" style="width: 50px; height: 50px; font-size: 1.2rem;">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <h6 class="fw-bold mb-0">تسجيل الحضور</h6>
                        </div>
                        <p class="text-muted small">
                            نظام دقيق لتسجيل حضور الطلاب ومتابعة الانتظام
                        </p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="feature-card p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="feature-icon me-3" style="width: 50px; height: 50px; font-size: 1.2rem;">
                                <i class="fas fa-mosque"></i>
                            </div>
                            <h6 class="fw-bold mb-0">إدارة الحلقات</h6>
                        </div>
                        <p class="text-muted small">
                            تنظيم الحلقات حسب المستوى والعمر والمعلم
                        </p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="feature-card p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="feature-icon me-3" style="width: 50px; height: 50px; font-size: 1.2rem;">
                                <i class="fas fa-mobile-alt"></i>
                            </div>
                            <h6 class="fw-bold mb-0">تصميم متجاوب</h6>
                        </div>
                        <p class="text-muted small">
                            يعمل على جميع الأجهزة - الكمبيوتر والجوال واللوحي
                        </p>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center text-white py-4">
                <p class="mb-0">
                    <i class="fas fa-heart text-danger"></i>
                    تم تطوير هذا النظام بعناية لخدمة جمعيات تحفيظ القرآن الكريم
                </p>
                <small class="opacity-75">
                    جميع الحقوق محفوظة © {{ date('Y') }}
                </small>
            </div>
        </div>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>

