<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Circle;
use App\Models\News;

class HomeController extends Controller
{
    public function index()
    {
        // جلب الإحصائيات
        $stats = [
            'students_count' => Student::where('is_active', true)->count(),
            'teachers_count' => Teacher::where('is_active', true)->count(),
            'circles_count' => Circle::where('is_active', true)->count(),
            'total_memorized' => Student::where('is_active', true)->count() * 5, // متوسط افتراضي
        ];

        // جلب آخر الأخبار
        $latest_news = News::where('is_published', true)
            ->orderBy('publish_date', 'desc')
            ->take(3)
            ->get();

        return view('home', compact('stats', 'latest_news'));
    }
}
