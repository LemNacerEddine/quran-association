@extends('layouts.guest')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="text-center mb-4">
                <h1 class="logo-text">نظام إدارة جمعية تحفيظ القرآن</h1>
                <p class="text-white-50">مرحباً بك في نظام إدارة الجمعية</p>
            </div>
            
            <div class="auth-card p-4">
                <div class="text-center mb-4">
                    <h3 class="text-dark mb-1">تسجيل الدخول</h3>
                    <p class="text-muted">أدخل بياناتك للوصول إلى النظام</p>
                </div>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email Address -->
                    <div class="mb-3">
                        <label for="email" class="form-label">البريد الإلكتروني</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                               name="email" value="{{ old('email') }}" required autofocus autocomplete="username" 
                               placeholder="أدخل بريدك الإلكتروني">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label">كلمة المرور</label>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                               name="password" required autocomplete="current-password" 
                               placeholder="أدخل كلمة المرور">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember_me" name="remember">
                        <label class="form-check-label" for="remember_me">
                            تذكرني
                        </label>
                    </div>

                    <div class="d-grid gap-2 mb-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt me-2"></i>
                            تسجيل الدخول
                        </button>
                    </div>

                    <div class="text-center mt-3">
                        <p class="mb-0 text-muted small">
                            <i class="fas fa-info-circle me-1"></i>
                            يتم إنشاء الحسابات من قبل الإدارة
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

