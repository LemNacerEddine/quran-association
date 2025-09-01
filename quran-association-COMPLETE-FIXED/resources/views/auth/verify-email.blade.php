@extends('layouts.guest')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="text-center mb-4">
                <h1 class="logo-text">نظام إدارة جمعية تحفيظ القرآن</h1>
                <p class="text-white-50">تحقق من البريد الإلكتروني</p>
            </div>
            
            <div class="auth-card p-4">
                <div class="text-center mb-4">
                    <i class="fas fa-envelope-open text-primary" style="font-size: 3rem;"></i>
                    <h3 class="text-dark mb-1 mt-3">تحقق من بريدك الإلكتروني</h3>
                    <p class="text-muted">
                        شكراً لك على التسجيل! قبل البدء، يرجى تحقق من بريدك الإلكتروني عبر النقر على الرابط الذي أرسلناه لك.
                        إذا لم تستلم البريد الإلكتروني، يمكننا إرسال آخر بكل سرور.
                    </p>
                </div>

                @if (session('status') == 'verification-link-sent')
                    <div class="alert alert-success" role="alert">
                        تم إرسال رابط تحقق جديد إلى عنوان البريد الإلكتروني الذي قدمته أثناء التسجيل.
                    </div>
                @endif

                <div class="d-flex justify-content-between">
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>
                            إعادة إرسال رابط التحقق
                        </button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary">
                            <i class="fas fa-sign-out-alt me-2"></i>
                            تسجيل الخروج
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

