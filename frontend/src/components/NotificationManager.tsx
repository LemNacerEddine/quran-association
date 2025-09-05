import React, { useEffect, useState } from 'react';
import {
  View,
  Text,
  StyleSheet,
  Switch,
  TouchableOpacity,
  ScrollView,
  Alert,
} from 'react-native';
import { useTranslation } from 'react-i18next';
import { SafeAreaView } from 'react-native-safe-area-context';
import { Ionicons } from '@expo/vector-icons';
import { colors } from '../theme/colors';
import { typography } from '../theme/fonts';
import notificationService from '../services/notificationService';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { showMessage } from 'react-native-flash-message';

interface NotificationSettings {
  attendance: boolean;
  messages: boolean;
  sessions: boolean;
  general: boolean;
  reminders: boolean;
  sound: boolean;
  vibration: boolean;
}

export default function NotificationManager() {
  const { t } = useTranslation();
  const [settings, setSettings] = useState<NotificationSettings>({
    attendance: true,
    messages: true,
    sessions: true,
    general: true,
    reminders: true,
    sound: true,
    vibration: true,
  });
  const [isEnabled, setIsEnabled] = useState(false);
  const [pushToken, setPushToken] = useState<string | null>(null);

  useEffect(() => {
    loadSettings();
    checkNotificationStatus();
  }, []);

  const loadSettings = async () => {
    try {
      const savedSettings = await AsyncStorage.getItem('notification_settings');
      if (savedSettings) {
        setSettings(JSON.parse(savedSettings));
      }
    } catch (error) {
      console.error('Failed to load notification settings:', error);
    }
  };

  const saveSettings = async (newSettings: NotificationSettings) => {
    try {
      await AsyncStorage.setItem('notification_settings', JSON.stringify(newSettings));
      setSettings(newSettings);
      
      showMessage({
        message: t('success'),
        description: 'تم حفظ إعدادات الإشعارات',
        type: 'success',
      });
    } catch (error) {
      console.error('Failed to save notification settings:', error);
    }
  };

  const checkNotificationStatus = async () => {
    const notificationSettings = await notificationService.getNotificationSettings();
    setIsEnabled(notificationSettings?.granted || false);
    setPushToken(notificationService.getExpoPushToken());
  };

  const toggleSetting = (key: keyof NotificationSettings) => {
    const newSettings = { ...settings, [key]: !settings[key] };
    saveSettings(newSettings);
  };

  const requestPermissions = async () => {
    try {
      await notificationService.registerForPushNotifications();
      await checkNotificationStatus();
      
      if (isEnabled) {
        showMessage({
          message: t('success'),
          description: 'تم تفعيل الإشعارات بنجاح',
          type: 'success',
        });
      }
    } catch (error) {
      showMessage({
        message: t('error'),
        description: 'فشل في تفعيل الإشعارات',
        type: 'danger',
      });
    }
  };

  const testNotification = () => {
    notificationService.sendLocalNotification({
      title: 'اختبار الإشعارات',
      body: 'هذا إشعار تجريبي للتأكد من عمل النظام',
      priority: 'high',
      category: 'general',
    });
  };

  const clearAllNotifications = () => {
    Alert.alert(
      'مسح الإشعارات',
      'هل تريد مسح جميع الإشعارات؟',
      [
        { text: 'إلغاء', style: 'cancel' },
        {
          text: 'مسح',
          style: 'destructive',
          onPress: () => {
            notificationService.clearAllNotifications();
            showMessage({
              message: t('success'),
              description: 'تم مسح جميع الإشعارات',
              type: 'info',
            });
          },
        },
      ]
    );
  };

  const settingsItems = [
    {
      key: 'attendance' as keyof NotificationSettings,
      title: 'الحضور والغياب',
      description: 'إشعارات تسجيل حضور الطلاب',
      icon: 'checkmark-circle',
      color: colors.success,
    },
    {
      key: 'messages' as keyof NotificationSettings,
      title: 'الرسائل',
      description: 'رسائل من المعلمين والأولياء',
      icon: 'chatbubble',
      color: colors.info,
    },
    {
      key: 'sessions' as keyof NotificationSettings,
      title: 'الجلسات',
      description: 'تذكيرات الجلسات والمواعيد',
      icon: 'calendar',
      color: colors.warning,
    },
    {
      key: 'general' as keyof NotificationSettings,
      title: 'إشعارات عامة',
      description: 'إعلانات وأخبار الجمعية',
      icon: 'notifications',
      color: colors.secondary,
    },
    {
      key: 'reminders' as keyof NotificationSettings,
      title: 'التذكيرات',
      description: 'تذكيرات المراجعة والأنشطة',
      icon: 'alarm',
      color: colors.primary,
    },
  ];

  const soundSettings = [
    {
      key: 'sound' as keyof NotificationSettings,
      title: 'الصوت',
      description: 'تشغيل صوت مع الإشعارات',
      icon: 'volume-high',
    },
    {
      key: 'vibration' as keyof NotificationSettings,
      title: 'الاهتزاز',
      description: 'اهتزاز الجهاز مع الإشعارات',
      icon: 'phone-portrait',
    },
  ];

  return (
    <SafeAreaView style={styles.container}>
      <ScrollView contentContainerStyle={styles.scrollContent}>
        {/* Header */}
        <View style={styles.header}>
          <Text style={styles.headerTitle}>إعدادات الإشعارات</Text>
          <Text style={styles.headerSubtitle}>
            تحكم في الإشعارات والتنبيهات
          </Text>
        </View>

        {/* Notification Status */}
        <View style={styles.statusContainer}>
          <View style={styles.statusCard}>
            <View style={styles.statusIcon}>
              <Ionicons 
                name={isEnabled ? 'notifications' : 'notifications-off'} 
                size={32} 
                color={isEnabled ? colors.success : colors.error} 
              />
            </View>
            <View style={styles.statusInfo}>
              <Text style={styles.statusTitle}>
                {isEnabled ? 'الإشعارات مفعلة' : 'الإشعارات معطلة'}
              </Text>
              <Text style={styles.statusDescription}>
                {isEnabled 
                  ? 'ستصلك الإشعارات حسب الإعدادات المحددة'
                  : 'قم بتفعيل الإشعارات لتلقي التنبيهات'
                }
              </Text>
            </View>
            {!isEnabled && (
              <TouchableOpacity
                style={styles.enableButton}
                onPress={requestPermissions}
              >
                <Text style={styles.enableButtonText}>تفعيل</Text>
              </TouchableOpacity>
            )}
          </View>
        </View>

        {/* Notification Categories */}
        <View style={styles.sectionContainer}>
          <Text style={styles.sectionTitle}>أنواع الإشعارات</Text>
          
          {settingsItems.map((item) => (
            <View key={item.key} style={styles.settingCard}>
              <View style={styles.settingContent}>
                <View 
                  style={[
                    styles.settingIcon,
                    { backgroundColor: `${item.color}20` }
                  ]}
                >
                  <Ionicons 
                    name={item.icon as any} 
                    size={24} 
                    color={item.color} 
                  />
                </View>
                <View style={styles.settingInfo}>
                  <Text style={styles.settingTitle}>{item.title}</Text>
                  <Text style={styles.settingDescription}>
                    {item.description}
                  </Text>
                </View>
                <Switch
                  value={settings[item.key]}
                  onValueChange={() => toggleSetting(item.key)}
                  trackColor={{ false: colors.gray300, true: `${item.color}40` }}
                  thumbColor={settings[item.key] ? item.color : colors.gray500}
                  disabled={!isEnabled}
                />
              </View>
            </View>
          ))}
        </View>

        {/* Sound & Vibration Settings */}
        <View style={styles.sectionContainer}>
          <Text style={styles.sectionTitle}>إعدادات الصوت</Text>
          
          {soundSettings.map((item) => (
            <View key={item.key} style={styles.settingCard}>
              <View style={styles.settingContent}>
                <View style={styles.settingIcon}>
                  <Ionicons 
                    name={item.icon as any} 
                    size={24} 
                    color={colors.primary} 
                  />
                </View>
                <View style={styles.settingInfo}>
                  <Text style={styles.settingTitle}>{item.title}</Text>
                  <Text style={styles.settingDescription}>
                    {item.description}
                  </Text>
                </View>
                <Switch
                  value={settings[item.key]}
                  onValueChange={() => toggleSetting(item.key)}
                  trackColor={{ false: colors.gray300, true: `${colors.primary}40` }}
                  thumbColor={settings[item.key] ? colors.primary : colors.gray500}
                  disabled={!isEnabled}
                />
              </View>
            </View>
          ))}
        </View>

        {/* Action Buttons */}
        <View style={styles.actionsContainer}>
          <TouchableOpacity
            style={styles.actionButton}
            onPress={testNotification}
            disabled={!isEnabled}
          >
            <Ionicons name="send" size={20} color={colors.white} />
            <Text style={styles.actionButtonText}>اختبار الإشعارات</Text>
          </TouchableOpacity>

          <TouchableOpacity
            style={[styles.actionButton, styles.clearButton]}
            onPress={clearAllNotifications}
          >
            <Ionicons name="trash" size={20} color={colors.error} />
            <Text style={[styles.actionButtonText, { color: colors.error }]}>
              مسح الإشعارات
            </Text>
          </TouchableOpacity>
        </View>

        {/* Debug Info */}
        {__DEV__ && pushToken && (
          <View style={styles.debugContainer}>
            <Text style={styles.debugTitle}>معلومات المطور</Text>
            <Text style={styles.debugText}>
              Push Token: {pushToken.substring(0, 30)}...
            </Text>
          </View>
        )}
      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: colors.background,
  },
  scrollContent: {
    paddingBottom: 40,
  },
  header: {
    padding: 20,
    backgroundColor: colors.primary,
  },
  headerTitle: {
    ...typography.h2,
    color: colors.white,
    marginBottom: 4,
  },
  headerSubtitle: {
    ...typography.body2,
    color: colors.white,
    opacity: 0.8,
  },
  statusContainer: {
    padding: 20,
  },
  statusCard: {
    backgroundColor: colors.white,
    borderRadius: 16,
    padding: 20,
    flexDirection: 'row',
    alignItems: 'center',
    elevation: 4,
    shadowColor: colors.black,
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 8,
  },
  statusIcon: {
    marginRight: 16,
  },
  statusInfo: {
    flex: 1,
  },
  statusTitle: {
    ...typography.h4,
    color: colors.textPrimary,
    marginBottom: 4,
  },
  statusDescription: {
    ...typography.body2,
    color: colors.textSecondary,
  },
  enableButton: {
    backgroundColor: colors.primary,
    paddingHorizontal: 16,
    paddingVertical: 8,
    borderRadius: 8,
  },
  enableButtonText: {
    ...typography.body2,
    color: colors.white,
    fontWeight: '600',
  },
  sectionContainer: {
    paddingHorizontal: 20,
    marginBottom: 24,
  },
  sectionTitle: {
    ...typography.h4,
    color: colors.textPrimary,
    marginBottom: 16,
  },
  settingCard: {
    backgroundColor: colors.white,
    borderRadius: 12,
    padding: 16,
    marginBottom: 12,
    elevation: 2,
    shadowColor: colors.black,
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.1,
    shadowRadius: 2,
  },
  settingContent: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  settingIcon: {
    width: 50,
    height: 50,
    borderRadius: 25,
    backgroundColor: colors.gray100,
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 16,
  },
  settingInfo: {
    flex: 1,
  },
  settingTitle: {
    ...typography.body1,
    color: colors.textPrimary,
    fontWeight: '600',
    marginBottom: 4,
  },
  settingDescription: {
    ...typography.body2,
    color: colors.textSecondary,
  },
  actionsContainer: {
    paddingHorizontal: 20,
    gap: 12,
  },
  actionButton: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: colors.primary,
    paddingVertical: 16,
    borderRadius: 12,
    gap: 8,
  },
  clearButton: {
    backgroundColor: colors.white,
    borderWidth: 1,
    borderColor: colors.error,
  },
  actionButtonText: {
    ...typography.body1,
    color: colors.white,
    fontWeight: '600',
  },
  debugContainer: {
    marginTop: 20,
    paddingHorizontal: 20,
  },
  debugTitle: {
    ...typography.body2,
    color: colors.textSecondary,
    fontWeight: '600',
    marginBottom: 8,
  },
  debugText: {
    ...typography.caption,
    color: colors.textSecondary,
    fontFamily: 'monospace',
  },
});