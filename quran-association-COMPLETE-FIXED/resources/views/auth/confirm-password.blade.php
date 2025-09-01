@extends('layouts.guest')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="text-center mb-4">
                <h1 class="logo-text">نظام إدارة جمعية تحفيظ القرآن</h1>
                <p class="text-white-50">تأكيد كلمة المرور</p>
            </div>
            
            <div class="auth-card p-4">
                <div class="text-center mb-4">
                    <i class="fas fa-shield-alt text-warning" style="font-size: 3rem;"></i>
                    <h3 class="text-dark mb-1 mt-3">تأكيد كلمة المرور</h3>
                    <p class="text-muted">
                        هذه منطقة آمنة من التطبيق. يرجى تأكيد كلمة المرور قبل المتابعة.
                    </p>
                </div>

                <form method="POST" action="{{ route('password.confirm') }}">
                    @csrf

                    <!-- Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label">كلمة المرور</label>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                               name="password" required autocomplete="current-password" 
                               placeholder="أدخل كلمة المرور للتأكيد">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check me-2"></i>
                            تأكيد
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

