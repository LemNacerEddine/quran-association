<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Guardian;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * تسجيل دخول ولي الأمر
     */
    public function guardianLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
            'access_code' => 'required|string|size:4'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors()
            ], 422);
        }

        $guardian = Guardian::where('phone', $request->phone)
                           ->where('is_active', true)
                           ->first();

        if (!$guardian || !$guardian->verifyAccessCode($request->access_code)) {
            return response()->json([
                'success' => false,
                'message' => 'رقم الهاتف أو كود الدخول غير صحيح'
            ], 401);
        }

        // تحديث آخر تسجيل دخول
        $guardian->last_login_at = now();
        $guardian->save();

        // إنشاء token
        $token = $guardian->createToken('guardian-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الدخول بنجاح',
            'data' => [
                'user_type' => 'guardian',
                'guardian' => [
                    'id' => $guardian->id,
                    'name' => $guardian->name,
                    'phone' => $guardian->phone,
                    'email' => $guardian->email,
                    'relationship' => $guardian->relationship_text,
                    'students_count' => $guardian->students()->count()
                ],
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ]);
    }

    /**
     * تسجيل دخول المعلم
     */
    public function teacherLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors()
            ], 422);
        }

        $teacher = Teacher::where('phone', $request->phone)
                         ->where('is_active', true)
                         ->first();

        if (!$teacher || $teacher->password !== $request->password) {
            return response()->json([
                'success' => false,
                'message' => 'رقم الهاتف أو كلمة المرور غير صحيحة'
            ], 401);
        }

        // تحديث آخر تسجيل دخول
        $teacher->updateLastLogin();

        // إنشاء token
        $token = $teacher->createToken('teacher-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الدخول بنجاح',
            'data' => [
                'user_type' => 'teacher',
                'teacher' => [
                    'id' => $teacher->id,
                    'name' => $teacher->name,
                    'phone' => $teacher->phone,
                    'email' => $teacher->email,
                    'specialization' => $teacher->specialization,
                    'circles_count' => $teacher->circles()->count(),
                    'students_count' => $teacher->total_students
                ],
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ]);
    }

    /**
     * Legacy login method for backward compatibility
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
            'password' => 'required|string',
            'user_type' => 'required|in:teacher,parent,guardian'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors()
            ], 422);
        }

        if ($request->user_type === 'teacher') {
            return $this->teacherLogin($request);
        } elseif ($request->user_type === 'guardian' || $request->user_type === 'parent') {
            // For guardian login, expect access_code instead of password
            $request->merge(['access_code' => $request->password]);
            return $this->guardianLogin($request);
        }

        return response()->json([
            'success' => false,
            'message' => 'نوع المستخدم غير صحيح'
        ], 400);
    }

    /**
     * تسجيل خروج المستخدم
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الخروج بنجاح'
        ]);
    }

    /**
     * الحصول على معلومات المستخدم الحالي
     */
    public function profile(Request $request)
    {
        $user = $request->user();
        
        if ($user instanceof Guardian) {
            return response()->json([
                'success' => true,
                'data' => [
                    'user_type' => 'guardian',
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'phone' => $user->phone,
                        'email' => $user->email,
                        'relationship' => $user->relationship_text,
                        'students_count' => $user->students()->count()
                    ]
                ]
            ]);
        } elseif ($user instanceof Teacher) {
            return response()->json([
                'success' => true,
                'data' => [
                    'user_type' => 'teacher',
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'phone' => $user->phone,
                        'email' => $user->email,
                        'specialization' => $user->specialization,
                        'circles_count' => $user->circles()->count(),
                        'students_count' => $user->total_students
                    ]
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'مستخدم غير معروف'
        ], 401);
    }

    /**
     * تحديث معلومات المستخدم
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();
        
        if ($user instanceof Guardian) {
            return $this->updateGuardianProfile($request);
        } elseif ($user instanceof Teacher) {
            return $this->updateTeacherProfile($request);
        }

        return response()->json([
            'success' => false,
            'message' => 'نوع المستخدم غير مدعوم'
        ], 400);
    }

    /**
     * تحديث معلومات ولي الأمر
     */
    private function updateGuardianProfile(Request $request)
    {
        $guardian = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255',
            'address' => 'sometimes|string|max:500',
            'job' => 'sometimes|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors()
            ], 422);
        }

        $guardian->update($request->only(['name', 'email', 'address', 'job']));

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث المعلومات بنجاح',
            'data' => [
                'guardian' => [
                    'id' => $guardian->id,
                    'name' => $guardian->name,
                    'phone' => $guardian->phone,
                    'email' => $guardian->email,
                    'address' => $guardian->address,
                    'job' => $guardian->job,
                    'relationship' => $guardian->relationship_text
                ]
            ]
        ]);
    }

    /**
     * تحديث معلومات المعلم
     */
    private function updateTeacherProfile(Request $request)
    {
        $teacher = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255',
            'address' => 'sometimes|string|max:500',
            'qualification' => 'sometimes|string|max:255',
            'experience' => 'sometimes|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors()
            ], 422);
        }

        $teacher->update($request->only(['name', 'email', 'address', 'qualification', 'experience']));

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث المعلومات بنجاح',
            'data' => [
                'teacher' => [
                    'id' => $teacher->id,
                    'name' => $teacher->name,
                    'phone' => $teacher->phone,
                    'email' => $teacher->email,
                    'address' => $teacher->address,
                    'qualification' => $teacher->qualification,
                    'experience' => $teacher->experience,
                    'specialization' => $teacher->specialization
                ]
            ]
        ]);
    }
}
