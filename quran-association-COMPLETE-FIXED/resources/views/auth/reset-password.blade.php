@extends('layouts.guest')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="text-center mb-4">
                <h1 class="logo-text">نظام إدارة جمعية تحفيظ القرآن</h1>
                <p class="text-white-50">إعادة تعيين كلمة المرور</p>
            </div>
            
            <div class="auth-card p-4">
                <div class="text-center mb-4">
                    <h3 class="text-dark mb-1">إعادة تعيين كلمة المرور</h3>
                    <p class="text-muted">أدخل كلمة مرور جديدة لحسابك</p>
                </div>

                <form method="POST" action="{{ route('password.store') }}">
                    @csrf

                    <!-- Password Reset Token -->
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <!-- Email Address -->
                    <div class="mb-3">
                        <label for="email" class="form-label">البريد الإلكتروني</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                               name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username" 
                               placeholder="أدخل بريدك الإلكتروني">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label">كلمة المرور الجديدة</label>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                               name="password" required autocomplete="new-password" 
                               placeholder="أدخل كلمة المرور الجديدة">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">تأكيد كلمة المرور</label>
                        <input id="password_confirmation" type="password" class="form-control"
                               name="password_confirmation" required autocomplete="new-password" 
                               placeholder="أعد إدخال كلمة المرور الجديدة">
                    </div>

                    <div class="d-grid gap-2 mb-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-key me-2"></i>
                            إعادة تعيين كلمة المرور
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

