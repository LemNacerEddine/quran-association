@extends('layouts.app')

@section('title', 'الرئيسية - جمعية تحفيظ القرآن الكريم')

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <h1 class="hero-title">
                    <i class="fas fa-quran me-3"></i>
                    جمعية تحفيظ القرآن الكريم
                </h1>
                <p class="hero-subtitle">
                    نسعى لخدمة كتاب الله وتعليم القرآن الكريم للجميع بأفضل الطرق والوسائل الحديثة
                </p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="{{ route('students.create') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-user-plus me-2"></i>
                        تسجيل طالب جديد
                    </a>
                    <a href="{{ route('circles.index') }}" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-users me-2"></i>
                        تصفح الحلقات
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section class="content-section">
    <div class="container">
        <h2 class="section-title">إحصائيات الجمعية</h2>
        <div class="row">
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="stats-card">
                    <div class="stats-number">{{ $stats['students_count'] }}</div>
                    <div class="stats-label">
                        <i class="fas fa-user-graduate me-2"></i>
                        طالب مسجل
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="stats-card">
                    <div class="stats-number">{{ $stats['teachers_count'] }}</div>
                    <div class="stats-label">
                        <i class="fas fa-chalkboard-teacher me-2"></i>
                        معلم متخصص
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="stats-card">
                    <div class="stats-number">{{ $stats['circles_count'] }}</div>
                    <div class="stats-label">
                        <i class="fas fa-users me-2"></i>
                        حلقة تحفيظ
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="stats-card">
                    <div class="stats-number">{{ $stats['total_memorized'] }}</div>
                    <div class="stats-label">
                        <i class="fas fa-book-open me-2"></i>
                        جزء محفوظ
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="content-section">
    <div class="container">
        <h2 class="section-title">خدماتنا</h2>
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-quran fa-3x text-primary"></i>
                        </div>
                        <h5 class="card-title">تحفيظ القرآن الكريم</h5>
                        <p class="card-text">
                            برامج متخصصة لتحفيظ القرآن الكريم بطرق علمية حديثة ومناهج مدروسة
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-graduation-cap fa-3x text-primary"></i>
                        </div>
                        <h5 class="card-title">تعليم التجويد</h5>
                        <p class="card-text">
                            دورات متخصصة في علم التجويد وتحسين الأداء والقراءة الصحيحة
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-users fa-3x text-primary"></i>
                        </div>
                        <h5 class="card-title">حلقات جماعية</h5>
                        <p class="card-text">
                            حلقات تحفيظ جماعية تشجع على التنافس الإيجابي والتعلم التفاعلي
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-chart-line fa-3x text-primary"></i>
                        </div>
                        <h5 class="card-title">متابعة التقدم</h5>
                        <p class="card-text">
                            نظام متطور لمتابعة تقدم الطلاب وتقييم مستواهم في الحفظ
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-calendar-alt fa-3x text-primary"></i>
                        </div>
                        <h5 class="card-title">جدولة مرنة</h5>
                        <p class="card-text">
                            مواعيد مرنة تناسب جميع الأعمار والظروف مع إمكانية التعديل
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-award fa-3x text-primary"></i>
                        </div>
                        <h5 class="card-title">شهادات معتمدة</h5>
                        <p class="card-text">
                            شهادات معتمدة للطلاب المتميزين والمكملين لحفظ أجزاء من القرآن
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@if($latest_news->count() > 0)
<!-- News Section -->
<section class="content-section">
    <div class="container">
        <h2 class="section-title">آخر الأخبار</h2>
        <div class="row">
            @foreach($latest_news as $news)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100">
                    @if($news->image)
                    <img src="{{ asset('storage/' . $news->image) }}" class="card-img-top" alt="{{ $news->title }}" style="height: 200px; object-fit: cover;">
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $news->title }}</h5>
                        <p class="card-text">{{ Str::limit($news->content, 100) }}</p>
                        <small class="text-muted">
                            <i class="fas fa-calendar me-1"></i>
                            {{ $news->publish_date->format('Y/m/d') }}
                        </small>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Call to Action -->
<section class="content-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <div class="card">
                    <div class="card-body p-5">
                        <h3 class="mb-4">انضم إلينا اليوم</h3>
                        <p class="lead mb-4">
                            ابدأ رحلتك في حفظ القرآن الكريم مع أفضل المعلمين والبرامج المتخصصة
                        </p>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="{{ route('students.create') }}" class="btn btn-primary btn-lg">
                                <i class="fas fa-user-plus me-2"></i>
                                سجل الآن
                            </a>
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-primary btn-lg">
                                <i class="fas fa-info-circle me-2"></i>
                                المزيد من المعلومات
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
    // Add smooth scrolling animation
    document.addEventListener('DOMContentLoaded', function() {
        // Animate stats numbers
        const statsNumbers = document.querySelectorAll('.stats-number');
        statsNumbers.forEach(stat => {
            const finalNumber = parseInt(stat.textContent);
            let currentNumber = 0;
            const increment = finalNumber / 50;
            
            const timer = setInterval(() => {
                currentNumber += increment;
                if (currentNumber >= finalNumber) {
                    stat.textContent = finalNumber;
                    clearInterval(timer);
                } else {
                    stat.textContent = Math.floor(currentNumber);
                }
            }, 30);
        });
        
        // Add hover effects to cards
        const cards = document.querySelectorAll('.card');
        cards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-10px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    });
</script>
@endsection

