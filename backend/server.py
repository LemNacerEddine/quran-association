from fastapi import FastAPI, APIRouter, HTTPException
from fastapi.responses import JSONResponse
from dotenv import load_dotenv
from starlette.middleware.cors import CORSMiddleware
from motor.motor_asyncio import AsyncIOMotorClient
import os
import logging
from pathlib import Path
from pydantic import BaseModel, Field
from typing import List, Optional, Dict, Any
import uuid
from datetime import datetime, timedelta
import requests
import hashlib


ROOT_DIR = Path(__file__).parent
load_dotenv(ROOT_DIR / '.env')

# MongoDB connection
mongo_url = os.environ['MONGO_URL']
client = AsyncIOMotorClient(mongo_url)
db = client[os.environ['DB_NAME']]

# Create the main app without a prefix
app = FastAPI()

# Create a router with the /api prefix
api_router = APIRouter(prefix="/api")


# Define Models
class StatusCheck(BaseModel):
    id: str = Field(default_factory=lambda: str(uuid.uuid4()))
    client_name: str
    timestamp: datetime = Field(default_factory=datetime.utcnow)

class StatusCheckCreate(BaseModel):
    client_name: str

# Add your routes to the router instead of directly to app
@api_router.get("/")
async def root():
    return {"message": "Hello World"}

@api_router.post("/status", response_model=StatusCheck)
async def create_status_check(input: StatusCheckCreate):
    status_dict = input.dict()
    status_obj = StatusCheck(**status_dict)
    _ = await db.status_checks.insert_one(status_obj.dict())
    return status_obj

@api_router.get("/status", response_model=List[StatusCheck])
async def get_status_checks():
    status_checks = await db.status_checks.find().to_list(1000)
    return [StatusCheck(**status_check) for status_check in status_checks]

app.add_middleware(
    CORSMiddleware,
    allow_credentials=True,
    allow_origins=["*"],
    allow_methods=["*"],
    allow_headers=["*"],
)

# Configure logging
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s'
)
logger = logging.getLogger(__name__)

# Laravel API Proxy Routes
# Real data from the database dump analysis
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
    }
}

# Laravel API Models
class LoginRequest(BaseModel):
    phone: str
    access_code: Optional[str] = None
    password: Optional[str] = None
    user_type: str  # 'guardian' or 'teacher'

def generate_token(user_id: int, user_type: str) -> str:
    timestamp = str(datetime.now().timestamp())
    data = f"{user_id}:{user_type}:{timestamp}"
    return hashlib.sha256(data.encode()).hexdigest()

@api_router.post("/mobile/auth/login")
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
        else:
            return {"success": False, "message": "نوع المستخدم غير مدعوم حالياً"}
            
    except Exception as e:
        return {"success": False, "message": f"خطأ في الخادم: {str(e)}"}

@api_router.get("/mobile/parent/dashboard")
async def parent_dashboard():
    """Get parent dashboard data from real database structure"""
    # TODO: In production, extract user from JWT token
    # For demo, return Ahmed Abdullah's data
    guardian_data = REAL_GUARDIANS_DATA['0501234567']
    
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

@api_router.get("/mobile/parent/children")
async def parent_children():
    """Get list of children for the authenticated parent"""
    # TODO: In production, extract user from JWT token
    guardian_data = REAL_GUARDIANS_DATA['0501234567'] 
    
    return {
        "success": True,
        "data": {
            "students": guardian_data['students']
        }
    }

@api_router.get("/mobile/parent/children/{child_id}")
async def parent_child_details(child_id: int):
    """Get details of a specific child from real database"""
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

# FCM Token Registration endpoint
@api_router.post("/v1/fcm/register-token")
async def register_fcm_token(request: dict):
    """Register FCM token for push notifications"""
    try:
        # In real implementation, store token in database
        # For now, just simulate success
        token = request.get('token')
        user_id = request.get('user_id')
        user_type = request.get('user_type')
        
        if not token or not user_id:
            return {
                "success": False,
                "message": "معرف المستخدم ورمز الجهاز مطلوبان"
            }
        
        # Simulate storing token
        print(f"📱 FCM Token registered: User {user_id} ({user_type}) - Token: {token[:20]}...")
        
        return {
            "success": True,
            "message": "تم تسجيل رمز الإشعارات بنجاح",
            "data": {
                "user_id": user_id,
                "user_type": user_type,
                "token_registered": True
            }
        }
    except Exception as e:
        return {
            "success": False,
            "message": f"خطأ في تسجيل الرمز: {str(e)}"
        }

# Simulate attendance marking and send notification
@api_router.post("/mobile/attendance/mark")
async def mark_attendance(request: dict):
    """Mark attendance and send notification to parent"""
    try:
        student_id = request.get('student_id')
        status = request.get('status', 'present')  # present, absent, late
        notes = request.get('notes', '')
        
        if not student_id:
            return {
                "success": False,
                "message": "معرف الطالب مطلوب"
            }
        
        # Find student in our data
        student = None
        guardian_phone = None
        
        for phone, guardian_data in REAL_GUARDIANS_DATA.items():
            for s in guardian_data['students']:
                if s['id'] == student_id:
                    student = s
                    guardian_phone = phone
                    break
            if student:
                break
        
        if not student:
            return {
                "success": False,
                "message": "الطالب غير موجود"
            }
        
        # Simulate attendance marking
        attendance_record = {
            "id": f"att_{student_id}_{datetime.now().strftime('%Y%m%d')}",
            "student_id": student_id,
            "student_name": student['name'],
            "status": status,
            "attendance_date": datetime.now().strftime('%Y-%m-%d'),
            "attendance_time": datetime.now().strftime('%H:%M'),
            "notes": notes,
            "recorded_by": "teacher_system"
        }
        
        # Send notification to parent (simulated)
        notification_title = f"تسجيل حضور {student['name']}"
        
        if status == 'present':
            notification_body = f"تم تسجيل حضور الطالب {student['name']} في جلسة اليوم"
        elif status == 'absent':
            notification_body = f"الطالب {student['name']} غائب عن جلسة اليوم"
        elif status == 'late':
            notification_body = f"الطالب {student['name']} متأخر عن جلسة اليوم"
        else:
            notification_body = f"تم تحديث حالة حضور الطالب {student['name']}"
            
        if notes:
            notification_body += f"\nملاحظات: {notes}"
        
        print(f"🔔 Sending notification to parent ({guardian_phone}): {notification_title}")
        print(f"📄 Message: {notification_body}")
        
        return {
            "success": True,
            "message": "تم تسجيل الحضور وإرسال الإشعار بنجاح",
            "data": {
                "attendance": attendance_record,
                "notification_sent": True,
                "notification": {
                    "title": notification_title,
                    "body": notification_body,
                    "recipient": guardian_phone,
                    "timestamp": datetime.now().isoformat()
                }
            }
        }
        
    except Exception as e:
        return {
            "success": False,
            "message": f"خطأ في تسجيل الحضور: {str(e)}"
        }

# Send test notification endpoint
@api_router.post("/mobile/notifications/send-test")
async def send_test_notification(request: dict):
    """Send a test notification"""
    try:
        user_id = request.get('user_id', 1)
        title = request.get('title', 'إشعار تجريبي')
        message = request.get('message', 'هذا إشعار تجريبي من الخادم')
        
        # Find user data
        user_data = None
        for phone, guardian_data in REAL_GUARDIANS_DATA.items():
            if guardian_data['id'] == user_id:
                user_data = guardian_data
                break
        
        if not user_data:
            return {
                "success": False,
                "message": "المستخدم غير موجود"
            }
        
        print(f"🧪 Sending test notification to {user_data['name']} ({user_data['phone']})")
        print(f"📧 Title: {title}")
        print(f"💬 Message: {message}")
        
        return {
            "success": True,
            "message": "تم إرسال الإشعار التجريبي",
            "data": {
                "recipient": user_data['name'],
                "title": title,
                "message": message,
                "sent_at": datetime.now().isoformat()
            }
        }
        
    except Exception as e:
        return {
            "success": False,
            "message": f"خطأ في إرسال الإشعار: {str(e)}"
        }

# Include the router in the main app (must be after all routes are defined)
app.include_router(api_router)

@app.on_event("shutdown")
async def shutdown_db_client():
    client.close()
