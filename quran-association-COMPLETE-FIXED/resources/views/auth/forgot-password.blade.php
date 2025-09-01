@extends('layouts.guest')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="text-center mb-4">
                <h1 class="logo-text">نظام إدارة جمعية تحفيظ القرآن</h1>
                <p class="text-white-50">استعادة كلمة المرور</p>
            </div>
            
            <div class="auth-card p-4">
                <div class="text-center mb-4">
                    <h3 class="text-dark mb-1">نسيت كلمة المرور؟</h3>
                    <p class="text-muted">أدخل بريدك الإلكتروني وسنرسل لك رابط إعادة تعيين كلمة المرور</p>
                </div>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <!-- Email Address -->
                    <div class="mb-3">
                        <label for="email" class="form-label">البريد الإلكتروني</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                               name="email" value="{{ old('email') }}" required autofocus 
                               placeholder="أدخل بريدك الإلكتروني">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid gap-2 mb-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>
                            إرسال رابط إعادة التعيين
                        </button>
                    </div>

                    <div class="text-center">
                        <a href="{{ route('login') }}" class="text-decoration-none">
                            <i class="fas fa-arrow-left me-2"></i>
                            العودة إلى تسجيل الدخول
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

