<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new parent user
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:users',
            'email' => 'nullable|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'parent',
            'is_active' => true,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء الحساب بنجاح',
            'data' => [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ], 201);
    }

    /**
     * Login user (teachers and parents)
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
            'password' => 'required|string',
            'user_type' => 'required|in:teacher,parent'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors()
            ], 422);
        }

        $phone = $request->phone;
        $password = $request->password;
        $userType = $request->user_type;

        if ($userType === 'teacher') {
            // Teacher login
            $teacher = \App\Models\Teacher::where('phone', $phone)->first();
            
            if (!$teacher || $teacher->access_code !== $password) {
                return response()->json([
                    'success' => false,
                    'message' => 'رقم الهاتف أو كود المرور غير صحيح'
                ], 401);
            }

            // Create or update user record for teacher
            $user = User::firstOrCreate([
                'phone' => $phone
            ], [
                'name' => $teacher->name,
                'email' => $teacher->email,
                'password' => Hash::make($password),
                'role' => 'teacher',
                'is_active' => true
            ]);

            // Update FCM token if provided
            if ($request->has('fcm_token')) {
                $user->update(['fcm_token' => $request->fcm_token]);
            }

            $user->update(['last_login_at' => now()]);
            $token = $user->createToken('teacher-mobile-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'تم تسجيل الدخول بنجاح',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'phone' => $user->phone,
                        'email' => $user->email,
                        'user_type' => 'teacher',
                        'teacher_id' => $teacher->id
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer'
                ]
            ]);

        } else {
            // Parent login - check if phone exists in students table as parent phone
            $student = \App\Models\Student::where('parent_phone', $phone)->first();
            
            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'رقم الهاتف غير مسجل'
                ], 401);
            }

            // For demo purposes, use simple password check
            // In production, implement proper parent authentication
            if ($password !== '123456') {
                return response()->json([
                    'success' => false,
                    'message' => 'كود المرور غير صحيح'
                ], 401);
            }

            // Create or update user record for parent
            $user = User::firstOrCreate([
                'phone' => $phone
            ], [
                'name' => $student->parent_name ?? 'ولي الأمر',
                'email' => $phone . '@parent.local',
                'password' => Hash::make($password),
                'role' => 'parent',
                'is_active' => true
            ]);

            // Update FCM token if provided
            if ($request->has('fcm_token')) {
                $user->update(['fcm_token' => $request->fcm_token]);
            }

            $user->update(['last_login_at' => now()]);
            $token = $user->createToken('parent-mobile-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'تم تسجيل الدخول بنجاح',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'phone' => $user->phone,
                        'email' => $user->email,
                        'user_type' => 'parent'
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer'
                ]
            ]);
        }
    }

    /**
     * Logout user
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
     * Get authenticated user profile
     */
    public function profile(Request $request)
    {
        $user = $request->user();
        $user->load(['children', 'notificationSettings']);

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'fcm_token' => 'sometimes|string',
            'notification_preferences' => 'sometimes|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors()
            ], 422);
        }

        $user->update($request->only(['name', 'email', 'fcm_token', 'notification_preferences']));

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الملف الشخصي بنجاح',
            'data' => $user
        ]);
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'كلمة المرور الحالية غير صحيحة'
            ], 400);
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تغيير كلمة المرور بنجاح'
        ]);
    }
}
