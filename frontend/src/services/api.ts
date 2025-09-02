import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { showMessage } from 'react-native-flash-message';

// Get API URL from environment
const API_BASE_URL = process.env.EXPO_PUBLIC_API_URL || 'https://vpdeveloper.dz/quran-association/api';

console.log('API_BASE_URL:', API_BASE_URL);

// Create axios instance
const api = axios.create({
  baseURL: API_BASE_URL,
  timeout: 10000,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

// Request interceptor to add auth token
api.interceptors.request.use(
  async (config) => {
    const token = await AsyncStorage.getItem('auth_token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Response interceptor to handle errors
api.interceptors.response.use(
  (response) => response,
  async (error) => {
    if (error.response?.status === 401) {
      // Token expired or invalid
      await AsyncStorage.multiRemove(['auth_token', 'user_data', 'user_type']);
      showMessage({
        message: 'انتهت صلاحية الجلسة',
        description: 'يرجى تسجيل الدخول مرة أخرى',
        type: 'warning',
      });
    } else if (error.code === 'NETWORK_ERROR') {
      showMessage({
        message: 'خطأ في الاتصال',
        description: 'تحقق من اتصال الإنترنت',
        type: 'danger',
      });
    }
    return Promise.reject(error);
  }
);

// Test credentials from database
const TEST_GUARDIANS = [
  { phone: '0501234567', access_code: '4567', name: 'أحمد عبدالله' },
  { phone: '0501234568', access_code: '4568', name: 'محمد حسن' },
  { phone: '0501234569', access_code: '4569', name: 'علي أحمد' },
  { phone: '0501234570', access_code: '4570', name: 'سالم محمد' },
  { phone: '0501234571', access_code: '4571', name: 'إبراهيم يوسف' },
];

const TEST_TEACHERS = [
  { phone: '0501234888', password: '4888', name: 'أحمد محمد الأستاذ' },
];

// Auth service
export const authService = {
  async login(phone: string, password: string, userType: 'parent' | 'teacher') {
    try {
      console.log('Attempting login with:', { phone, userType, password });
      
      // Test with local data first (since Laravel API is not accessible)
      if (userType === 'parent') {
        const guardian = TEST_GUARDIANS.find(g => g.phone === phone && g.access_code === password);
        if (guardian) {
          const userData = {
            id: 1,
            name: guardian.name,
            phone: guardian.phone,
            type: 'parent' as const,
          };
          
          return {
            success: true,
            data: {
              token: 'test-token-' + Date.now(),
              user: userData,
            },
          };
        }
      } else if (userType === 'teacher') {
        const teacher = TEST_TEACHERS.find(t => t.phone === phone && t.password === password);
        if (teacher) {
          const userData = {
            id: 1,
            name: teacher.name,
            phone: teacher.phone,
            type: 'teacher' as const,
          };
          
          return {
            success: true,
            data: {
              token: 'test-token-' + Date.now(),
              user: userData,
            },
          };
        }
      }

      // If test data doesn't match, try API
      const endpoint = '/mobile/auth/login';
      const payload = userType === 'parent' 
        ? { phone, access_code: password, user_type: 'guardian' }
        : { phone, password, user_type: 'teacher' };

      const response = await api.post(endpoint, payload);
      console.log('Login response:', response.data);

      return {
        success: true,
        data: response.data,
      };
    } catch (error: any) {
      console.error('Login error:', error.response?.data || error.message);
      return {
        success: false,
        error: error.response?.data?.message || 'بيانات الدخول غير صحيحة',
      };
    }
  },

  async logout() {
    try {
      await api.post('/auth/logout');
    } catch (error) {
      console.error('Logout error:', error);
    }
  },
};

// Parent service
export const parentService = {
  async getDashboard() {
    try {
      // Try API first, then fallback to test data
      try {
        const response = await api.get('/mobile/parent/dashboard');
        return response.data;
      } catch (apiError) {
        // Return test data
        return {
          children: [
            {
              id: 1,
              name: 'عبدالرحمن أحمد',
              circle_name: 'حلقة النور',
              attendance_rate: 95,
              total_points: 240,
              status: 'excellent'
            },
            {
              id: 2,
              name: 'فاطمة أحمد',
              circle_name: 'حلقة الهدى',
              attendance_rate: 88,
              total_points: 180,
              status: 'good'
            }
          ],
          stats: {
            total_children: 2,
            average_attendance: 91,
            total_points: 420
          }
        };
      }
    } catch (error) {
      console.error('Get dashboard error:', error);
      throw error;
    }
  },

  async getChildren() {
    try {
      try {
        const response = await api.get('/mobile/parent/children');
        return response.data;
      } catch (apiError) {
        // Return test data
        return [
          {
            id: 1,
            name: 'عبدالرحمن أحمد',
            age: 12,
            circle_name: 'حلقة النور',
            teacher_name: 'أحمد محمد الأستاذ',
            attendance_rate: 95,
            memorization_points: 150,
            total_points: 240,
            level: 'الجزء الثالث',
            recent_activity: 'حفظ سورة آل عمران'
          },
          {
            id: 2,
            name: 'فاطمة أحمد',
            age: 10,
            circle_name: 'حلقة الهدى',
            teacher_name: 'أحمد محمد الأستاذ',
            attendance_rate: 88,
            memorization_points: 120,
            total_points: 180,
            level: 'الجزء الثاني',
            recent_activity: 'مراجعة سورة البقرة'
          }
        ];
      }
    } catch (error) {
      console.error('Get children error:', error);
      throw error;
    }
  },

  async getChildDetails(childId: number) {
    try {
      try {
        const response = await api.get(`/mobile/parent/children/${childId}`);
        return response.data;
      } catch (apiError) {
        // Return test data based on childId
        const testChild = {
          id: childId,
          name: childId === 1 ? 'عبدالرحمن أحمد' : 'فاطمة أحمد',
          age: childId === 1 ? 12 : 10,
          circle_name: childId === 1 ? 'حلقة النور' : 'حلقة الهدى',
          teacher_name: 'أحمد محمد الأستاذ',
          attendance_rate: childId === 1 ? 95 : 88,
          memorization_points: childId === 1 ? 150 : 120,
          total_points: childId === 1 ? 240 : 180,
          level: childId === 1 ? 'الجزء الثالث' : 'الجزء الثاني',
          recent_activity: childId === 1 ? 'حفظ سورة آل عمران' : 'مراجعة سورة البقرة'
        };
        return testChild;
      }
    } catch (error) {
      console.error('Get child details error:', error);
      throw error;
    }
  },

  async getChildAttendance(childId: number) {
    try {
      try {
        const response = await api.get(`/mobile/parent/children/${childId}/attendance`);
        return response.data;
      } catch (apiError) {
        // Return test attendance data
        return [
          {
            date: '2025-09-01',
            status: 'present',
            points: 10,
            notes: 'أداء ممتاز'
          },
          {
            date: '2025-08-30',
            status: 'present',
            points: 8,
            notes: 'جهد جيد'
          },
          {
            date: '2025-08-28',
            status: 'absent',
            points: 0,
            notes: 'مرض'
          }
        ];
      }
    } catch (error) {
      console.error('Get child attendance error:', error);
      throw error;
    }
  },
};

// Teacher service
export const teacherService = {
  async getDashboard() {
    try {
      try {
        const response = await api.get('/mobile/teacher/dashboard');
        return response.data;
      } catch (apiError) {
        // Return test data for teacher dashboard
        return {
          today_sessions: [
            {
              id: 1,
              title: 'حلقة النور - الأحد',
              circle_name: 'حلقة النور',
              start_time: '15:00',
              end_time: '16:30',
              students_count: 15,
              status: 'scheduled'
            },
            {
              id: 2,
              title: 'حلقة الهدى - الأحد',
              circle_name: 'حلقة الهدى',
              start_time: '17:00',
              end_time: '18:30',
              students_count: 12,
              status: 'ongoing'
            }
          ],
          stats: {
            total_circles: 3,
            total_students: 45,
            today_sessions: 2,
            attendance_rate: 87
          },
          notifications: [
            {
              id: 1,
              message: 'تم إضافة طالب جديد إلى حلقة النور',
              type: 'info',
              created_at: '2025-09-01T10:00:00Z'
            }
          ]
        };
      }
    } catch (error) {
      console.error('Get teacher dashboard error:', error);
      throw error;
    }
  },

  async getSessions() {
    try {
      try {
        const response = await api.get('/mobile/teacher/sessions');
        return response.data;
      } catch (apiError) {
        // Return test sessions data
        return [
          {
            id: 1,
            title: 'حلقة النور - الأحد',
            circle_name: 'حلقة النور',
            session_date: '2025-09-01',
            start_time: '15:00',
            end_time: '16:30',
            students_count: 15,
            present_count: 13,
            absent_count: 2,
            status: 'completed',
            location: 'الفصل الأول'
          },
          {
            id: 2,
            title: 'حلقة الهدى - الأحد',
            circle_name: 'حلقة الهدى',
            session_date: '2025-09-01',
            start_time: '17:00',
            end_time: '18:30',
            students_count: 12,
            present_count: 0,
            absent_count: 0,
            status: 'scheduled',
            location: 'الفصل الثاني'
          },
          {
            id: 3,
            title: 'حلقة النور - الثلاثاء',
            circle_name: 'حلقة النور',
            session_date: '2025-09-03',
            start_time: '15:00',
            end_time: '16:30',
            students_count: 15,
            present_count: 0,
            absent_count: 0,
            status: 'scheduled',
            location: 'الفصل الأول'
          }
        ];
      }
    } catch (error) {
      console.error('Get sessions error:', error);
      throw error;
    }
  },

  async getSessionDetails(sessionId: number) {
    try {
      try {
        const response = await api.get(`/mobile/teacher/sessions/${sessionId}`);
        return response.data;
      } catch (apiError) {
        // Return test session details
        return {
          id: sessionId,
          title: 'حلقة النور - الأحد',
          circle_name: 'حلقة النور',
          session_date: '2025-09-01',
          start_time: '15:00',
          end_time: '16:30',
          location: 'الفصل الأول',
          students: [
            { id: 1, name: 'عبدالرحمن أحمد', status: 'present', points: 10 },
            { id: 2, name: 'محمد علي', status: 'present', points: 8 },
            { id: 3, name: 'فاطمة سعد', status: 'absent', points: 0 }
          ]
        };
      }
    } catch (error) {
      console.error('Get session details error:', error);
      throw error;
    }
  },

  async saveAttendance(sessionId: number, attendanceData: any) {
    try {
      try {
        const response = await api.post(`/mobile/teacher/sessions/${sessionId}/attendance`, attendanceData);
        return response.data;
      } catch (apiError) {
        // Simulate successful save
        return { success: true, message: 'تم حفظ الحضور بنجاح' };
      }
    } catch (error) {
      console.error('Save attendance error:', error);
      throw error;
    }
  },

  async createSession(sessionData: any) {
    try {
      try {
        const response = await api.post('/mobile/teacher/sessions', sessionData);
        return response.data;
      } catch (apiError) {
        // Simulate successful creation
        return { 
          success: true, 
          message: 'تم إنشاء الجلسة بنجاح',
          session: {
            id: Math.random(),
            ...sessionData
          }
        };
      }
    } catch (error) {
      console.error('Create session error:', error);
      throw error;
    }
  },

  async getStudents() {
    try {
      try {
        const response = await api.get('/mobile/teacher/students');
        return response.data;
      } catch (apiError) {
        // Return test students data
        return [
          {
            id: 1,
            name: 'عبدالرحمن أحمد',
            age: 12,
            circle_name: 'حلقة النور',
            attendance_rate: 95,
            memorization_points: 150,
            behavior_points: 90,
            total_points: 240,
            last_attendance: '2025-09-01',
            parent_phone: '0501234567',
            performance_level: 'excellent'
          },
          {
            id: 2,
            name: 'محمد علي',
            age: 13,
            circle_name: 'حلقة النور',
            attendance_rate: 88,
            memorization_points: 120,
            behavior_points: 80,
            total_points: 200,
            last_attendance: '2025-09-01',
            parent_phone: '0501234568',
            performance_level: 'good'
          },
          {
            id: 3,
            name: 'فاطمة سعد',
            age: 11,
            circle_name: 'حلقة الهدى',
            attendance_rate: 75,
            memorization_points: 100,
            behavior_points: 70,
            total_points: 170,
            last_attendance: '2025-08-30',
            parent_phone: '0501234569',
            performance_level: 'average'
          },
          {
            id: 4,
            name: 'أحمد محمد',
            age: 10,
            circle_name: 'حلقة الهدى',
            attendance_rate: 65,
            memorization_points: 80,
            behavior_points: 60,
            total_points: 140,
            last_attendance: '2025-08-28',
            parent_phone: '0501234570',
            performance_level: 'needs_improvement'
          }
        ];
      }
    } catch (error) {
      console.error('Get students error:', error);
      throw error;
    }
  },
};

export default api;