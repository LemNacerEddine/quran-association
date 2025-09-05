#!/usr/bin/env python3
"""
Laravel API Proxy for Quranic Association Mobile App
This creates API endpoints that match what the mobile app expects
and fetches real data from the Laravel website's database
"""

from fastapi import FastAPI, HTTPException, Depends
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
from typing import Optional, List, Dict, Any
import requests
import asyncio
# MySQL connection will be added later if needed
import os
from datetime import datetime, timedelta
import hashlib

app = FastAPI(title="Laravel API Proxy", version="1.0.0")

# Enable CORS
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Database connection configuration
# Since we can't directly access the external Laravel database,
# we'll simulate the data structure based on the SQL dump we analyzed
DB_CONFIG = {
    'host': 'localhost',
    'database': 'quran_association_proxy',
    'user': 'root',
    'password': 'password'
}

# Models
class LoginRequest(BaseModel):
    phone: str
    access_code: Optional[str] = None
    password: Optional[str] = None
    user_type: str  # 'guardian' or 'teacher'

class LoginResponse(BaseModel):
    success: bool
    message: str
    data: Optional[Dict[str, Any]] = None

# Real data from the database dump (since we cannot access external DB directly)
REAL_GUARDIANS_DATA = {
    '0501234567': {
        'id': 1,
        'name': 'أحمد عبدالله',
        'phone': '0501234567',
        'email': 'ahmed.parent@gmail.com',
        'access_code': '4567',
        'students': [
            {
                'id': 1,
                'name': 'عبدالرحمن أحمد',
                'age': 12,
                'gender': 'male',
                'education_level': 'ابتدائي',
                'birth_date': '2012-03-15',
                'notes': 'طالب متميز في الحفظ والتلاوة',
                'attendance_rate': 95,
                'total_points': 200,
                'memorization_points': 150,
                'circle': {
                    'id': 1,
                    'name': 'حلقة تحفيظ القرآن الكريم - المستوى المتوسط',
                    'level': 'متوسط',
                    'teacher': {
                        'id': 1,
                        'name': 'أحمد محمد الأستاذ',
                        'phone': '0501234888'
                    }
                }
            }
        ]
    },
    '0501234568': {
        'id': 2,
        'name': 'محمد حسن',
        'phone': '0501234568',
        'email': 'mohammed.parent@gmail.com',
        'access_code': '4568',
        'students': [
            {
                'id': 2,
                'name': 'فاطمة محمد',
                'age': 11,
                'gender': 'female',
                'education_level': 'ابتدائي',
                'birth_date': '2013-07-22',
                'notes': 'طالبة مجتهدة ومنتظمة في الحضور',
                'attendance_rate': 88,
                'total_points': 180,
                'memorization_points': 120,
                'circle': {
                    'id': 1,
                    'name': 'حلقة تحفيظ القرآن الكريم - المستوى المتوسط',
                    'level': 'متوسط',
                    'teacher': {
                        'id': 1,
                        'name': 'أحمد محمد الأستاذ',
                        'phone': '0501234888'
                    }
                }
            }
        ]
    },
    '0501234569': {
        'id': 3,
        'name': 'علي أحمد',
        'phone': '0501234569',
        'email': 'ali.parent@gmail.com',
        'access_code': '4569',
        'students': [
            {
                'id': 3,
                'name': 'محمد علي',
                'age': 13,
                'gender': 'male',
                'education_level': 'ابتدائي',
                'birth_date': '2011-11-08',
                'notes': 'طالب نشط ومتفاعل في الحلقة',
                'attendance_rate': 90,
                'total_points': 210,
                'memorization_points': 140,
                'circle': {
                    'id': 1,
                    'name': 'حلقة تحفيظ القرآن الكريم - المستوى المتوسط',
                    'level': 'متوسط',
                    'teacher': {
                        'id': 1,
                        'name': 'أحمد محمد الأستاذ',
                        'phone': '0501234888'
                    }
                }
            }
        ]
    },
    '0501234570': {
        'id': 4,
        'name': 'سالم محمد',
        'phone': '0501234570',
        'email': 'salem.parent@gmail.com',
        'access_code': '4570',
        'students': [
            {
                'id': 4,
                'name': 'عائشة سالم',
                'age': 10, 
                'gender': 'female',
                'education_level': 'ابتدائي',
                'birth_date': '2014-01-30',
                'notes': 'طالبة بحاجة لمزيد من التشجيع',
                'attendance_rate': 75,
                'total_points': 140,
                'memorization_points': 80,
                'circle': {
                    'id': 1,
                    'name': 'حلقة تحفيظ القرآن الكريم - المستوى المتوسط',
                    'level': 'متوسط',
                    'teacher': {
                        'id': 1,
                        'name': 'أحمد محمد الأستاذ',
                        'phone': '0501234888'
                    }
                }
            }
        ]
    },
    '0501234571': {
        'id': 5,
        'name': 'إبراهيم يوسف',
        'phone': '0501234571',
        'email': 'ibrahim.parent@gmail.com',
        'access_code': '4571',
        'students': [
            {
                'id': 5,
                'name': 'يوسف إبراهيم',
                'age': 14,
                'gender': 'male',
                'education_level': 'ابتدائي',
                'birth_date': '2010-09-12',
                'notes': 'طالب ذكي لكن يحتاج لمزيد من الانتظام',
                'attendance_rate': 85,
                'total_points': 170,
                'memorization_points': 110,
                'circle': {
                    'id': 1,
                    'name': 'حلقة تحفيظ القرآن الكريم - المستوى المتوسط',
                    'level': 'متوسط',
                    'teacher': {
                        'id': 1,
                        'name': 'أحمد محمد الأستاذ',
                        'phone': '0501234888'
                    }
                }
            }
        ]
    }
}

REAL_TEACHERS_DATA = {
    '0501234888': {
        'id': 1,
        'name': 'أحمد محمد الأستاذ',
        'phone': '0501234888',
        'email': 'teacher@example.com',
        'password': '4888',
        'circles': [
            {
                'id': 1,
                'name': 'حلقة تحفيظ القرآن الكريم - المستوى المتوسط',
                'level': 'متوسط',
                'students_count': 5
            }
        ]
    }
}

# Helper function to generate JWT-like tokens
def generate_token(user_id: int, user_type: str) -> str:
    timestamp = str(datetime.now().timestamp())
    data = f"{user_id}:{user_type}:{timestamp}"
    return hashlib.sha256(data.encode()).hexdigest()

# API Endpoints
@app.post("/mobile/auth/login")
async def mobile_login(request: LoginRequest):
    """Mobile authentication endpoint for both guardians and teachers"""
    try:
        if request.user_type == 'guardian':
            # Guardian login with access_code
            if not request.access_code:
                return {"success": False, "message": "كود الدخول مطلوب"}
                
            guardian_data = REAL_GUARDIANS_DATA.get(request.phone)
            if not guardian_data or guardian_data['access_code'] != request.access_code:
                return {"success": False, "message": "رقم الهاتف أو كود الدخول غير صحيح"}
            
            token = generate_token(guardian_data['id'], 'guardian')
            return {
                "success": True,
                "message": "تم تسجيل الدخول بنجاح",
                "data": {
                    "user_type": "guardian",
                    "guardian": {
                        "id": guardian_data['id'],
                        "name": guardian_data['name'],
                        "phone": guardian_data['phone'],
                        "email": guardian_data['email'],
                        "students_count": len(guardian_data['students'])
                    },
                    "token": token,
                    "token_type": "Bearer"
                }
            }
            
        elif request.user_type == 'teacher':
            # Teacher login with password
            if not request.password:
                return {"success": False, "message": "كلمة المرور مطلوبة"}
                
            teacher_data = REAL_TEACHERS_DATA.get(request.phone)
            if not teacher_data or teacher_data['password'] != request.password:
                return {"success": False, "message": "رقم الهاتف أو كلمة المرور غير صحيحة"}
            
            token = generate_token(teacher_data['id'], 'teacher')
            return {
                "success": True,
                "message": "تم تسجيل الدخول بنجاح",
                "data": {
                    "user_type": "teacher",
                    "teacher": {
                        "id": teacher_data['id'],
                        "name": teacher_data['name'],
                        "phone": teacher_data['phone'],
                        "email": teacher_data['email'],
                        "circles_count": len(teacher_data['circles'])
                    },
                    "token": token,
                    "token_type": "Bearer"
                }
            }
        else:
            return {"success": False, "message": "نوع المستخدم غير صحيح"}
            
    except Exception as e:
        return {"success": False, "message": f"خطأ في الخادم: {str(e)}"}

@app.get("/mobile/parent/dashboard")
async def parent_dashboard():
    """Get parent dashboard data - requires authentication in real implementation"""
    # In real implementation, we would extract user from JWT token
    # For now, we'll return data for Ahmed Abdullah as example
    guardian_data = REAL_GUARDIANS_DATA['0501234567']  # Ahmed Abdullah
    
    students = guardian_data['students']
    total_children = len(students)
    total_points = sum(s['total_points'] for s in students)
    avg_attendance = sum(s['attendance_rate'] for s in students) / total_children if total_children > 0 else 0
    
    return {
        "success": True,
        "data": {
            "children": students,
            "stats": {
                "total_children": total_children,
                "average_attendance": round(avg_attendance),
                "total_points": total_points
            }
        }
    }

@app.get("/mobile/parent/children")
async def parent_children():
    """Get list of children for the authenticated parent"""
    # In real implementation, we would extract user from JWT token
    guardian_data = REAL_GUARDIANS_DATA['0501234567']  # Ahmed Abdullah
    
    return {
        "success": True,
        "data": {
            "students": guardian_data['students']
        }
    }

@app.get("/mobile/parent/children/{child_id}")
async def parent_child_details(child_id: int):
    """Get details of a specific child"""
    # In real implementation, we would check if child belongs to authenticated parent
    for guardian_data in REAL_GUARDIANS_DATA.values():
        for student in guardian_data['students']:
            if student['id'] == child_id:
                return {
                    "success": True,
                    "data": {
                        "student": student
                    }
                }
    
    return {"success": False, "message": "الطالب غير موجود"}

@app.get("/mobile/parent/children/{child_id}/attendance")
async def parent_child_attendance(child_id: int):
    """Get attendance records for a specific child"""
    # Generate mock attendance data based on the child's attendance rate
    for guardian_data in REAL_GUARDIANS_DATA.values():
        for student in guardian_data['students']:
            if student['id'] == child_id:
                attendance_rate = student['attendance_rate']
                
                # Generate mock attendance records for the last 30 days
                records = []
                for i in range(30):
                    date = (datetime.now() - timedelta(days=i)).strftime('%Y-%m-%d')
                    # Randomly assign attendance based on attendance rate
                    status = 'present' if (i % 10) < (attendance_rate / 10) else 'absent'
                    points = 8 if status == 'present' else 0
                    
                    records.append({
                        'date': date,
                        'status': status,
                        'status_text': 'حاضر' if status == 'present' else 'غائب',
                        'memorization_points': points,
                        'notes': 'أداء جيد' if status == 'present' else 'غائب'
                    })
                
                return {
                    "success": True,
                    "data": {
                        "attendance_records": records
                    }
                }
    
    return {"success": False, "message": "الطالب غير موجود"}

@app.get("/mobile/teacher/dashboard")
async def teacher_dashboard():
    """Get teacher dashboard data"""
    teacher_data = REAL_TEACHERS_DATA['0501234888']
    
    # Count total students across all circles
    total_students = sum(circle['students_count'] for circle in teacher_data['circles'])
    
    # Generate mock today's sessions
    today_sessions = [
        {
            'id': 1,
            'title': 'حلقة تحفيظ القرآن الكريم - الأحد',
            'circle_name': 'حلقة تحفيظ القرآن الكريم - المستوى المتوسط',
            'start_time': '16:00',
            'end_time': '18:00',
            'students_count': 5,
            'status': 'scheduled'
        }
    ]
    
    return {
        "success": True,
        "data": {
            "circles": teacher_data['circles'],
            "students": list(REAL_GUARDIANS_DATA.values())[0]['students'],  # All students from first guardian
            "today_sessions": today_sessions,
            "stats": {
                "total_circles": len(teacher_data['circles']),
                "total_students": total_students,
                "today_sessions": len(today_sessions),
                "attendance_rate": 87
            }
        }
    }

@app.get("/mobile/teacher/sessions")
async def teacher_sessions():
    """Get teacher sessions"""
    # Generate mock sessions
    sessions = [
        {
            'id': 1,
            'title': 'حلقة تحفيظ القرآن الكريم - الأحد',
            'circle_name': 'حلقة تحفيظ القرآن الكريم - المستوى المتوسط',
            'session_date': datetime.now().strftime('%Y-%m-%d'),
            'start_time': '16:00',
            'end_time': '18:00',
            'students_count': 5,
            'present_count': 4,
            'absent_count': 1,
            'status': 'scheduled',
            'location': 'قاعة التحفيظ الرئيسية'
        }
    ]
    
    return {
        "success": True,
        "data": sessions
    }

# Health check endpoint
@app.get("/health")
async def health_check():
    return {
        "status": "ok",
        "timestamp": datetime.now().isoformat(),
        "service": "Laravel API Proxy",
        "version": "1.0.0"
    }

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=8002)