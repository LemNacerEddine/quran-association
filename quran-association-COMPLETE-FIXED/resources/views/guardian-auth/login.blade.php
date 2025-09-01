<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>دخول أولياء الأمور - جمعية تحفيظ القرآن</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Tajawal', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 400px;
            width: 100%;
            margin: 20px;
        }
        
        .login-header {
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .login-header i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.9;
        }
        
        .login-body {
            padding: 2rem;
        }
        
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #4CAF50;
            box-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.25);
        }
        
        .btn-login {
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 500;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(76, 175, 80, 0.4);
        }
        
        .form-label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 8px;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
        }
        
        .footer-links {
            text-align: center;
            padding: 1rem 2rem 2rem;
            border-top: 1px solid #e9ecef;
        }
        
        .footer-links a {
            color: #6c757d;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s ease;
        }
        
        .footer-links a:hover {
            color: #4CAF50;
        }
        
        .input-group-text {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-left: none;
            border-radius: 0 10px 10px 0;
        }
        
        .input-group .form-control {
            border-left: none;
            border-radius: 10px 0 0 10px;
        }
        
        .input-group .form-control:focus + .input-group-text {
            border-color: #4CAF50;
        }
        
        @media (max-width: 576px) {
            .login-container {
                margin: 10px;
                border-radius: 15px;
            }
            
            .login-header {
                padding: 1.5rem;
            }
            
            .login-body {
                padding: 1.5rem;
            }
            
            .login-header i {
                font-size: 2.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Header -->
        <div class="login-header">
            <i class="fas fa-users"></i>
            <h3 class="mb-0">دخول أولياء الأمور</h3>
            <p class="mb-0 opacity-75">جمعية تحفيظ القرآن الكريم</p>
        </div>
        
        <!-- Body -->
        <div class="login-body">
            @if(session('success'))
                <div class="alert alert-success mb-3">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger mb-3">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                </div>
            @endif
            
            <form method="POST" action="{{ route('guardian.login') }}">
                @csrf
                
                <!-- Phone Number -->
                <div class="mb-3">
                    <label for="phone" class="form-label">
                        <i class="fas fa-phone me-1"></i>
                        رقم الهاتف
                    </label>
                    <div class="input-group">
                        <input type="tel" 
                               class="form-control @error('phone') is-invalid @enderror" 
                               id="phone" 
                               name="phone" 
                               value="{{ old('phone') }}" 
                               placeholder="05xxxxxxxx"
                               required
                               autocomplete="tel">
                        <span class="input-group-text">
                            <i class="fas fa-mobile-alt"></i>
                        </span>
                    </div>
                    @error('phone')
                        <div class="invalid-feedback d-block">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                
                <!-- Access Code -->
                <div class="mb-3">
                    <label for="access_code" class="form-label">
                        <i class="fas fa-key me-1"></i>
                        كود الدخول
                    </label>
                    <div class="input-group">
                        <input type="password" 
                               class="form-control @error('access_code') is-invalid @enderror" 
                               id="access_code" 
                               name="access_code" 
                               placeholder="****"
                               maxlength="4"
                               required
                               autocomplete="current-password">
                        <span class="input-group-text">
                            <i class="fas fa-lock"></i>
                        </span>
                    </div>
                    <small class="form-text text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        آخر 4 أرقام من رقم الهاتف (افتراضياً)
                    </small>
                    @error('access_code')
                        <div class="invalid-feedback d-block">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                
                <!-- Remember Me -->
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remember" name="remember">
                        <label class="form-check-label" for="remember">
                            تذكرني
                        </label>
                    </div>
                </div>
                
                <!-- Submit Button -->
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-login">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        دخول
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Footer -->
        <div class="footer-links">
            <a href="{{ route('guardian.reset-code') }}" class="d-block mb-2">
                <i class="fas fa-question-circle me-1"></i>
                نسيت كود الدخول؟
            </a>
            <a href="{{ url('/') }}">
                <i class="fas fa-arrow-right me-1"></i>
                العودة للموقع الرئيسي
            </a>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Auto-format phone number
        document.getElementById('phone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 10) {
                value = value.substring(0, 10);
            }
            e.target.value = value;
        });
        
        // Auto-format access code
        document.getElementById('access_code').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 4) {
                value = value.substring(0, 4);
            }
            e.target.value = value;
        });
        
        // Auto-focus next field
        document.getElementById('phone').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('access_code').focus();
            }
        });
    </script>
</body>
</html>

