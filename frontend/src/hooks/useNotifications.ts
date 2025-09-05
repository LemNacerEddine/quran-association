import { useState, useEffect, useCallback } from 'react';
import { notificationService as apiNotificationService } from '../services/api';
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
      const response = await apiNotificationService.getNotificationHistory();
      
      if (response.success && response.data) {
        setNotifications(response.data);
        const unread = response.data.filter((n: Notification) => !n.isRead).length;
        setUnreadCount(unread);
      } else {
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

  const markAsRead = useCallback(async (notificationId: string) => {
    try {
      await apiNotificationService.markNotificationAsRead(notificationId);
      
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
    } catch (error) {
      console.error('Error marking notification as read:', error);
    }
  }, []);

  const markAllAsRead = useCallback(async () => {
    try {
      const unreadNotifications = notifications.filter(n => !n.isRead);
      
      // Mark all as read in parallel
      await Promise.all(
        unreadNotifications.map(notification => 
          apiNotificationService.markNotificationAsRead(notification.id)
        )
      );
      
      setNotifications(prev => 
        prev.map(notification => ({ ...notification, isRead: true }))
      );
      
      setUnreadCount(0);
      
      showMessage({
        message: 'تم',
        description: 'تم وضع علامة مقروء على جميع الإشعارات',
        type: 'success',
      });
    } catch (error) {
      console.error('Error marking all notifications as read:', error);
    }
  }, [notifications]);

  const sendTestNotification = useCallback(() => {
    notificationService.sendLocalNotification({
      title: 'اختبار الإشعارات',
      body: 'هذا إشعار تجريبي للتأكد من عمل النظام',
      priority: 'high',
      category: 'general',
    });
  }, []);

  const scheduleSessionReminder = useCallback((sessionTitle: string, sessionTime: Date) => {
    // Schedule reminder 1 hour before session
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
  }, [loadNotifications]);

  return {
    notifications,
    isLoading,
    unreadCount,
    loadNotifications,
    markAsRead,
    markAllAsRead,
    sendTestNotification,
    scheduleSessionReminder,
  };
}