import * as Notifications from 'expo-notifications';
import * as Device from 'expo-device';
import Constants from 'expo-constants';
import { Platform } from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { showMessage } from 'react-native-flash-message';
import api from './api';

// Configure notification behavior
Notifications.setNotificationHandler({
  handleNotification: async () => ({
    shouldShowAlert: true,
    shouldPlaySound: true,
    shouldSetBadge: false,
  }),
});

export interface NotificationData {
  title: string;
  body: string;
  data?: any;
  userId?: string;
  userType?: 'parent' | 'teacher';
  priority: 'high' | 'normal' | 'low';
  category: 'attendance' | 'message' | 'session' | 'general';
}

class NotificationService {
  private expoPushToken: string | null = null;
  private notificationListener: any = null;
  private responseListener: any = null;

  async initialize(): Promise<void> {
    try {
      await this.registerForPushNotifications();
      this.setupNotificationListeners();
      console.log('🔔 Notification service initialized successfully');
    } catch (error) {
      console.error('❌ Failed to initialize notification service:', error);
    }
  }

  async registerForPushNotifications(): Promise<string | null> {
    try {
      let token = null;

      // Check if we're on a physical device
      if (!Device.isDevice) {
        console.log('📱 Must use physical device for Push Notifications');
        showMessage({
          message: 'تنبيه',
          description: 'الإشعارات تعمل فقط على الأجهزة الحقيقية، وليس المحاكي',
          type: 'info',
        });
        // For web/simulator, still create a mock token for testing
        this.expoPushToken = 'expo-mock-token-' + Date.now();
        await AsyncStorage.setItem('expo_push_token', this.expoPushToken);
        return this.expoPushToken;
      }

      console.log('📱 Device detected, requesting permissions...');

      // Check current permission status
      const { status: existingStatus } = await Notifications.getPermissionsAsync();
      console.log('📋 Current permission status:', existingStatus);

      let finalStatus = existingStatus;

      // If not granted, request permissions with specific options
      if (existingStatus !== 'granted') {
        console.log('🔔 Requesting notification permissions...');
        
        const { status } = await Notifications.requestPermissionsAsync({
          ios: {
            allowAlert: true,
            allowBadge: true,
            allowSound: true,
            allowAnnouncements: true,
          },
          android: {
            // Request all Android permissions
          }
        });
        
        finalStatus = status;
        console.log('📨 Permission request result:', status);
      }

      // Check if permission was granted
      if (finalStatus !== 'granted') {
        console.error('❌ Permission denied:', finalStatus);
        showMessage({
          message: 'إذن الإشعارات مرفوض',
          description: 'يرجى الذهاب إلى إعدادات الجهاز وتفعيل الإشعارات للتطبيق يدوياً',
          type: 'warning',
          duration: 5000,
        });
        return null;
      }

      console.log('✅ Permission granted, generating push token...');

      // Configure Android notification channels BEFORE getting token
      if (Platform.OS === 'android') {
        await this.createNotificationChannels();
      }

      try {
        // Get the Expo push token
        const projectId = Constants.expoConfig?.extra?.eas?.projectId || 
                          Constants.easConfig?.projectId || 
                          '631091388007';
        
        console.log('🔑 Using project ID:', projectId);
        
        const tokenResult = await Notifications.getExpoPushTokenAsync({
          projectId: projectId,
        });

        token = tokenResult.data;
        console.log('🎯 Push token generated:', token.substring(0, 30) + '...');

        this.expoPushToken = token;

        // Store token locally
        await AsyncStorage.setItem('expo_push_token', token);

        // Send token to server
        await this.sendTokenToServer(token);

        showMessage({
          message: 'تم تفعيل الإشعارات',
          description: 'سيتم إرسال الإشعارات إلى جهازك',
          type: 'success',
        });

        console.log('✅ Push token registered successfully');
      } catch (tokenError) {
        console.error('❌ Error getting push token:', tokenError);
        showMessage({
          message: 'خطأ في توليد رمز الإشعارات',
          description: 'تم تفعيل الإذن لكن فشل في توليد رمز الجهاز',
          type: 'warning',
        });
        return null;
      }

      return token;
    } catch (error) {
      console.error('❌ Error registering for push notifications:', error);
      showMessage({
        message: 'خطأ في تفعيل الإشعارات',
        description: 'حدث خطأ أثناء تفعيل الإشعارات: ' + error.message,
        type: 'danger',
      });
      return null;
    }
  }

  private async createNotificationChannels(): Promise<void> {
    const channels = [
      {
        id: 'attendance',
        name: 'الحضور والغياب',
        importance: Notifications.AndroidImportance.HIGH,
        vibrationPattern: [0, 250, 250, 250],
        sound: 'default',
      },
      {
        id: 'messages',
        name: 'الرسائل',
        importance: Notifications.AndroidImportance.HIGH,
        vibrationPattern: [0, 500],
        sound: 'default',
      },
      {
        id: 'sessions',
        name: 'الجلسات',
        importance: Notifications.AndroidImportance.MAX,
        vibrationPattern: [0, 250, 250, 250, 250, 250],
        sound: 'default',
      },
      {
        id: 'general',
        name: 'إشعارات عامة',
        importance: Notifications.AndroidImportance.DEFAULT,
        vibrationPattern: [0, 250],
        sound: 'default',
      },
    ];

    for (const channel of channels) {
      await Notifications.setNotificationChannelAsync(channel.id, channel);
    }
  }

  private setupNotificationListeners(): void {
    // Listener for notifications received while app is foregrounded
    this.notificationListener = Notifications.addNotificationReceivedListener(
      (notification) => {
        console.log('🔔 Notification received:', notification);
        this.handleForegroundNotification(notification);
      }
    );

    // Listener for when user taps on notification
    this.responseListener = Notifications.addNotificationResponseReceivedListener(
      (response) => {
        console.log('👆 Notification tapped:', response);
        this.handleNotificationResponse(response);
      }
    );
  }

  private handleForegroundNotification(notification: Notifications.Notification): void {
    const { title, body } = notification.request.content;
    
    showMessage({
      message: title || 'إشعار جديد',
      description: body || '',
      type: 'info',
      duration: 4000,
      icon: 'info',
    });
  }

  private handleNotificationResponse(response: Notifications.NotificationResponse): void {
    const { data } = response.notification.request.content;
    
    if (data) {
      // Handle different notification types
      switch (data.type) {
        case 'attendance':
          // Navigate to attendance screen
          console.log('Navigate to attendance for student:', data.studentId);
          break;
        case 'message':
          // Navigate to messages screen
          console.log('Navigate to messages for:', data.messageId);
          break;
        case 'session':
          // Navigate to session details
          console.log('Navigate to session:', data.sessionId);
          break;
        default:
          console.log('Handle general notification:', data);
      }
    }
  }

  async sendTokenToServer(token: string): Promise<void> {
    try {
      const userType = await AsyncStorage.getItem('user_type');
      const userData = await AsyncStorage.getItem('user_data');
      
      if (!userType || !userData) {
        console.log('⚠️ User not logged in, skipping token sync');
        return;
      }

      const user = JSON.parse(userData);

      // استخدام API endpoint الجديد
      const apiClient = (await import('./api')).default;
      await apiClient.post('/v1/fcm/register-token', {
        token,
        user_id: user.id,
        user_type: userType === 'parent' ? 'guardian' : 'teacher',
        device_type: Platform.OS,
        device_id: Constants.deviceId || 'unknown',
        app_version: Constants.expoConfig?.version || '1.0.0',
      });

      console.log('✅ Token registered successfully with backend');
    } catch (error) {
      console.error('❌ Failed to register token with server:', error);
      console.log('🔄 Will try again when user logs in');
    }
  }

  async sendLocalNotification(notificationData: NotificationData): Promise<void> {
    try {
      await Notifications.scheduleNotificationAsync({
        content: {
          title: notificationData.title,
          body: notificationData.body,
          data: notificationData.data,
          sound: 'default',
          priority: notificationData.priority === 'high' 
            ? Notifications.AndroidNotificationPriority.HIGH 
            : Notifications.AndroidNotificationPriority.DEFAULT,
        },
        trigger: null, // Show immediately
        identifier: `local_${Date.now()}`,
      });
    } catch (error) {
      console.error('❌ Failed to send local notification:', error);
    }
  }

  async scheduleReminder(
    title: string, 
    body: string, 
    scheduledDate: Date,
    data?: any
  ): Promise<void> {
    try {
      await Notifications.scheduleNotificationAsync({
        content: {
          title,
          body,
          data,
          sound: 'default',
        },
        trigger: {
          date: scheduledDate,
        },
        identifier: `reminder_${Date.now()}`,
      });

      console.log('⏰ Reminder scheduled for:', scheduledDate);
    } catch (error) {
      console.error('❌ Failed to schedule reminder:', error);
    }
  }

  async clearAllNotifications(): Promise<void> {
    try {
      await Notifications.dismissAllNotificationsAsync();
      console.log('🗑️ All notifications cleared');
    } catch (error) {
      console.error('❌ Failed to clear notifications:', error);
    }
  }

  async getNotificationSettings(): Promise<any> {
    try {
      const settings = await Notifications.getPermissionsAsync();
      const hasToken = this.expoPushToken !== null;
      
      return {
        granted: settings.status === 'granted' || hasToken,
        canAskAgain: settings.canAskAgain,
        status: settings.status,
        ios: settings.ios,
        android: settings.android,
        hasToken,
        platform: Platform.OS,
        isDevice: Device.isDevice,
      };
    } catch (error) {
      console.error('❌ Failed to get notification settings:', error);
      return {
        granted: false,
        canAskAgain: true,
        status: 'undetermined',
        hasToken: false,
        platform: Platform.OS,
        isDevice: Device.isDevice,
      };
    }
  }

  getExpoPushToken(): string | null {
    return this.expoPushToken;
  }

  cleanup(): void {
    if (this.notificationListener) {
      Notifications.removeNotificationSubscription(this.notificationListener);
    }
    if (this.responseListener) {
      Notifications.removeNotificationSubscription(this.responseListener);
    }
  }
}

// Create singleton instance
const notificationService = new NotificationService();

export default notificationService;