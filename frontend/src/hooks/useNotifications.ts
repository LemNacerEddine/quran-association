import { useState, useEffect, useCallback } from 'react';
import { notificationAPIService } from '../services/api';
import notificationService from '../services/notificationService';
import { showMessage } from 'react-native-flash-message';

interface Notification {
  id: string;
  title: string;
  body: string;
  type: 'attendance' | 'message' | 'session' | 'general';
  timestamp: string;
  isRead: boolean;
  data?: any;
}

export function useNotifications() {
  const [notifications, setNotifications] = useState<Notification[]>([]);
  const [isLoading, setIsLoading] = useState(false);
  const [unreadCount, setUnreadCount] = useState(0);

  const loadNotifications = useCallback(async () => {
    try {
      setIsLoading(true);
      
      // Try Laravel FCM API first
      const response = await notificationAPIService.getNotifications();
      
      if (response.success && response.data) {
        // Map Laravel response to our notification format
        const mappedNotifications = response.data.map((notification: any) => ({
          id: notification.id.toString(),
          title: notification.title,
          body: notification.body,
          type: notification.type || 'general',
          timestamp: notification.created_at,
          isRead: notification.is_read || false,
          data: notification.data ? JSON.parse(notification.data) : null,
        }));
        
        setNotifications(mappedNotifications);
        const unread = mappedNotifications.filter((n: Notification) => !n.isRead).length;
        setUnreadCount(unread);
        
        console.log('✅ Loaded notifications from Laravel API:', mappedNotifications.length);
      } else {
        console.log('⚠️ Laravel API failed, using mock data');
        
        // Use mock data for testing
        const mockNotifications: Notification[] = [
          {
            id: '1',
            title: 'تم تسجيل حضور عبدالرحمن',
            body: 'تم تسجيل حضور الطالب عبدالرحمن أحمد في جلسة اليوم',
            type: 'attendance',
            timestamp: new Date().toISOString(),
            isRead: false,
          },
          {
            id: '2',
            title: 'رسالة من المعلم',
            body: 'لديك رسالة جديدة من الأستاذ أحمد محمد',
            type: 'message',
            timestamp: new Date(Date.now() - 2 * 60 * 60 * 1000).toISOString(),
            isRead: false,
          },
          {
            id: '3',
            title: 'تذكير بالجلسة',
            body: 'ستبدأ جلسة حلقة النور خلال ساعة واحدة',
            type: 'session',
            timestamp: new Date(Date.now() - 24 * 60 * 60 * 1000).toISOString(),
            isRead: true,
          },
          {
            id: '4',
            title: 'إعلان من الإدارة',
            body: 'يسرنا إعلامكم بموعد المسابقة القرآنية السنوية',
            type: 'general',
            timestamp: new Date(Date.now() - 2 * 24 * 60 * 60 * 1000).toISOString(),
            isRead: true,
          },
        ];
        
        setNotifications(mockNotifications);
        const unread = mockNotifications.filter(n => !n.isRead).length;
        setUnreadCount(unread);
      }
    } catch (error) {
      console.error('Error loading notifications:', error);
      showMessage({
        message: 'خطأ',
        description: 'فشل في تحميل الإشعارات',
        type: 'danger',
      });
    } finally {
      setIsLoading(false);
    }
  }, []);

  const loadUnreadCount = useCallback(async () => {
    try {
      const response = await notificationAPIService.getUnreadCount();
      if (response.success) {
        setUnreadCount(response.count || 0);
      }
    } catch (error) {
      console.error('Error loading unread count:', error);
    }
  }, []);

  const markAsRead = useCallback(async (notificationId: string) => {
    try {
      const response = await notificationAPIService.markAsRead(notificationId);
      
      if (response.success) {
        setNotifications(prev => 
          prev.map(notification => 
            notification.id === notificationId 
              ? { ...notification, isRead: true }
              : notification
          )
        );
        
        setUnreadCount(prev => Math.max(0, prev - 1));
        
        showMessage({
          message: 'تم',
          description: 'تم وضع علامة مقروء على الإشعار',
          type: 'success',
          duration: 2000,
        });
      }
    } catch (error) {
      console.error('Error marking notification as read:', error);
    }
  }, []);

  const markAllAsRead = useCallback(async () => {
    try {
      const response = await notificationAPIService.markAllAsRead();
      
      if (response.success) {
        setNotifications(prev => 
          prev.map(notification => ({ ...notification, isRead: true }))
        );
        
        setUnreadCount(0);
        
        showMessage({
          message: 'تم',
          description: 'تم وضع علامة مقروء على جميع الإشعارات',
          type: 'success',
        });
      }
    } catch (error) {
      console.error('Error marking all notifications as read:', error);
    }
  }, []);

  const sendTestNotification = useCallback(async () => {
    try {
      // Get current push token
      const pushToken = notificationService.getExpoPushToken();
      
      if (pushToken) {
        // Test with Laravel FCM API
        const response = await notificationAPIService.testNotification(
          pushToken,
          'اختبار الإشعارات',
          'هذا إشعار تجريبي من Laravel FCM API'
        );
        
        if (response.success) {
          showMessage({
            message: 'تم إرسال الإشعار التجريبي',
            description: 'تحقق من الإشعارات',
            type: 'success',
          });
        } else {
          throw new Error('Laravel API test failed');
        }
      } else {
        // Fallback to local notification
        notificationService.sendLocalNotification({
          title: 'اختبار الإشعارات المحلية',
          body: 'هذا إشعار محلي للتأكد من عمل النظام',
          priority: 'high',
          category: 'general',
        });
      }
    } catch (error) {
      console.error('Test notification error:', error);
      
      // Fallback to local notification
      notificationService.sendLocalNotification({
        title: 'اختبار الإشعارات',
        body: 'هذا إشعار محلي - Laravel API غير متاح',
        priority: 'high',
        category: 'general',
      });
    }
  }, []);

  const scheduleSessionReminder = useCallback((sessionTitle: string, sessionTime: Date) => {
    // Schedule local reminder 1 hour before session
    const reminderTime = new Date(sessionTime.getTime() - 60 * 60 * 1000);
    
    notificationService.scheduleReminder(
      'تذكير بالجلسة',
      `ستبدأ ${sessionTitle} خلال ساعة واحدة`,
      reminderTime,
      { type: 'session_reminder', sessionTitle, sessionTime: sessionTime.toISOString() }
    );
  }, []);

  useEffect(() => {
    loadNotifications();
    loadUnreadCount();
  }, [loadNotifications, loadUnreadCount]);

  return {
    notifications,
    isLoading,
    unreadCount,
    loadNotifications,
    loadUnreadCount,
    markAsRead,
    markAllAsRead,
    sendTestNotification,
    scheduleSessionReminder,
  };
}