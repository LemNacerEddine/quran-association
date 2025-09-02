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
      const response = await api.get('/mobile/parent/dashboard');
      return response.data;
    } catch (error) {
      console.error('Get dashboard error:', error);
      throw error;
    }
  },

  async getChildren() {
    try {
      const response = await api.get('/mobile/parent/children');
      return response.data;
    } catch (error) {
      console.error('Get children error:', error);
      throw error;
    }
  },

  async getChildDetails(childId: number) {
    try {
      const response = await api.get(`/mobile/parent/children/${childId}`);
      return response.data;
    } catch (error) {
      console.error('Get child details error:', error);
      throw error;
    }
  },

  async getChildAttendance(childId: number) {
    try {
      const response = await api.get(`/mobile/parent/children/${childId}/attendance`);
      return response.data;
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
      const response = await api.get('/mobile/teacher/dashboard');
      return response.data;
    } catch (error) {
      console.error('Get teacher dashboard error:', error);
      throw error;
    }
  },

  async getSessions() {
    try {
      const response = await api.get('/mobile/teacher/sessions');
      return response.data;
    } catch (error) {
      console.error('Get sessions error:', error);
      throw error;
    }
  },

  async getSessionDetails(sessionId: number) {
    try {
      const response = await api.get(`/mobile/teacher/sessions/${sessionId}`);
      return response.data;
    } catch (error) {
      console.error('Get session details error:', error);
      throw error;
    }
  },

  async saveAttendance(sessionId: number, attendanceData: any) {
    try {
      const response = await api.post(`/mobile/teacher/sessions/${sessionId}/attendance`, attendanceData);
      return response.data;
    } catch (error) {
      console.error('Save attendance error:', error);
      throw error;
    }
  },

  async createSession(sessionData: any) {
    try {
      const response = await api.post('/mobile/teacher/sessions', sessionData);
      return response.data;
    } catch (error) {
      console.error('Create session error:', error);
      throw error;
    }
  },
};

export default api;