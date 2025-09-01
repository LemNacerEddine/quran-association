@extends('layouts.guest')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="text-center mb-4">
                <h1 class="logo-text">نظام إدارة جمعية تحفيظ القرآن</h1>
                <p class="text-white-50">إنشاء حساب جديد في النظام</p>
            </div>
            
            <div class="auth-card p-4">
                <div class="text-center mb-4">
                    <h3 class="text-dark mb-1">إنشاء حساب جديد</h3>
                    <p class="text-muted">أدخل بياناتك لإنشاء حساب جديد</p>
                </div>

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <!-- Name -->
                    <div class="mb-3">
                        <label for="name" class="form-label">الاسم الكامل</label>
                        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" 
                               name="name" value="{{ old('name') }}" required autofocus autocomplete="name" 
                               placeholder="أدخل اسمك الكامل">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email Address -->
                    <div class="mb-3">
                        <label for="email" class="form-label">البريد الإلكتروني</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                               name="email" value="{{ old('email') }}" required autocomplete="username" 
                               placeholder="أدخل بريدك الإلكتروني">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label">كلمة المرور</label>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                               name="password" required autocomplete="new-password" 
                               placeholder="أدخل كلمة المرور">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">تأكيد كلمة المرور</label>
                        <input id="password_confirmation" type="password" class="form-control"
                               name="password_confirmation" required autocomplete="new-password" 
                               placeholder="أعد إدخال كلمة المرور">
                    </div>

                    <div class="d-grid gap-2 mb-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-user-plus me-2"></i>
                            إنشاء الحساب
                        </button>
                    </div>

                    <div class="text-center">
                        <p class="mb-0 text-muted">
                            لديك حساب بالفعل؟ 
                            <a href="{{ route('login') }}" class="text-decoration-none fw-bold">
                                تسجيل الدخول
                            </a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

