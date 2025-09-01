import React from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  Alert,
} from 'react-native';
import { useTranslation } from 'react-i18next';
import { SafeAreaView } from 'react-native-safe-area-context';
import { Ionicons } from '@expo/vector-icons';
import { colors } from '../../src/theme/colors';
import { typography } from '../../src/theme/fonts';
import { useAuth } from '../../src/context/AuthContext';
import { useRouter } from 'expo-router';
import { showMessage } from 'react-native-flash-message';

export default function TeacherProfileScreen() {
  const { t } = useTranslation();
  const { user, logout } = useAuth();
  const router = useRouter();

  const handleLogout = () => {
    Alert.alert(
      'تسجيل الخروج',
      'هل تريد تسجيل الخروج من التطبيق؟',
      [
        { text: 'إلغاء', style: 'cancel' },
        {
          text: 'خروج',
          style: 'destructive',
          onPress: async () => {
            await logout();
            showMessage({
              message: 'تم تسجيل الخروج بنجاح',
              type: 'info',
            });
            router.replace('/auth');
          },
        },
      ]
    );
  };

  const teacherStats = [
    {
      label: 'عدد الحلقات',
      value: '3',
      icon: 'school',
      color: colors.primary,
    },
    {
      label: 'عدد الطلاب',
      value: '45',
      icon: 'people',
      color: colors.secondary,
    },
    {
      label: 'الجلسات هذا الشهر',
      value: '28',
      icon: 'calendar',
      color: colors.info,
    },
    {
      label: 'معدل الحضور',
      value: '87%',
      icon: 'checkmark-circle',
      color: colors.success,
    },
  ];

  const profileOptions = [
    {
      id: 'circles',
      title: 'الحلقات المُدارة',
      description: 'عرض وإدارة الحلقات',
      icon: 'school',
      color: colors.primary,
    },
    {
      id: 'schedule',
      title: 'الجدول الأسبوعي',
      description: 'عرض الجدول والمواعيد',
      icon: 'calendar',
      color: colors.info,
    },
    {
      id: 'notifications',
      title: 'الإشعارات',
      description: 'إعدادات الإشعارات والتنبيهات',
      icon: 'notifications',
      color: colors.warning,
    },
    {
      id: 'reports',
      title: 'التقارير',
      description: 'تقارير الحضور والأداء',
      icon: 'bar-chart',
      color: colors.secondary,
    },
    {
      id: 'help',
      title: 'المساعدة',
      description: 'الدعم الفني والأسئلة الشائعة',
      icon: 'help-circle',
      color: colors.info,
    },
    {
      id: 'about',
      title: 'عن التطبيق',
      description: 'معلومات التطبيق والإصدار',
      icon: 'information-circle',
      color: colors.gray600,
    },
  ];

  return (
    <SafeAreaView style={styles.container}>
      <ScrollView contentContainerStyle={styles.scrollContent}>
        {/* Profile Header */}
        <View style={styles.profileHeader}>
          <View style={styles.avatarContainer}>
            <Ionicons name="person" size={50} color={colors.white} />
          </View>
          <Text style={styles.userName}>الأستاذ {user?.name}</Text>
          <Text style={styles.userPhone}>{user?.phone}</Text>
          <Text style={styles.userRole}>معلم تحفيظ القرآن الكريم</Text>
        </View>

        {/* Teacher Stats */}
        <View style={styles.statsContainer}>
          <Text style={styles.sectionTitle}>إحصائياتي</Text>
          <View style={styles.statsGrid}>
            {teacherStats.map((stat, index) => (
              <View key={index} style={styles.statCard}>
                <View 
                  style={[
                    styles.statIcon,
                    { backgroundColor: `${stat.color}20` }
                  ]}
                >
                  <Ionicons 
                    name={stat.icon as any} 
                    size={24} 
                    color={stat.color} 
                  />
                </View>
                <Text style={styles.statValue}>{stat.value}</Text>
                <Text style={styles.statLabel}>{stat.label}</Text>
              </View>
            ))}
          </View>
        </View>

        {/* Profile Options */}
        <View style={styles.optionsContainer}>
          <Text style={styles.sectionTitle}>الإعدادات</Text>
          {profileOptions.map((option) => (
            <TouchableOpacity
              key={option.id}
              style={styles.optionCard}
              activeOpacity={0.7}
            >
              <View style={styles.optionContent}>
                <View 
                  style={[
                    styles.optionIcon,
                    { backgroundColor: `${option.color}20` }
                  ]}
                >
                  <Ionicons 
                    name={option.icon as any} 
                    size={24} 
                    color={option.color} 
                  />
                </View>
                <View style={styles.optionInfo}>
                  <Text style={styles.optionTitle}>{option.title}</Text>
                  <Text style={styles.optionDescription}>
                    {option.description}
                  </Text>
                </View>
                <Ionicons 
                  name="chevron-forward" 
                  size={20} 
                  color={colors.gray600} 
                />
              </View>
            </TouchableOpacity>
          ))}
        </View>

        {/* App Info */}
        <View style={styles.appInfoContainer}>
          <Text style={styles.appName}>تطبيق جمعية تحفيظ القرآن الكريم</Text>
          <Text style={styles.appVersion}>الإصدار 1.0.0</Text>
        </View>

        {/* Logout Button */}
        <TouchableOpacity
          style={styles.logoutButton}
          onPress={handleLogout}
          activeOpacity={0.8}
        >
          <Ionicons name="log-out" size={20} color={colors.error} />
          <Text style={styles.logoutText}>{t('logout')}</Text>
        </TouchableOpacity>
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
  profileHeader: {
    backgroundColor: colors.primary,
    alignItems: 'center',
    paddingVertical: 40,
    paddingHorizontal: 20,
    marginBottom: 20,
  },
  avatarContainer: {
    width: 100,
    height: 100,
    borderRadius: 50,
    backgroundColor: colors.primaryDark,
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 16,
  },
  userName: {
    ...typography.h3,
    color: colors.white,
    marginBottom: 4,
  },
  userPhone: {
    ...typography.body2,
    color: colors.white,
    opacity: 0.8,
    marginBottom: 4,
  },
  userRole: {
    ...typography.body2,
    color: colors.white,
    opacity: 0.9,
  },
  statsContainer: {
    paddingHorizontal: 20,
    marginBottom: 32,
  },
  sectionTitle: {
    ...typography.h4,
    color: colors.textPrimary,
    marginBottom: 16,
  },
  statsGrid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 12,
  },
  statCard: {
    backgroundColor: colors.white,
    borderRadius: 12,
    padding: 16,
    alignItems: 'center',
    width: '48%',
    elevation: 2,
    shadowColor: colors.black,
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.1,
    shadowRadius: 2,
  },
  statIcon: {
    width: 50,
    height: 50,
    borderRadius: 25,
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 8,
  },
  statValue: {
    ...typography.h4,
    color: colors.textPrimary,
    fontWeight: 'bold',
    marginBottom: 4,
  },
  statLabel: {
    ...typography.caption,
    color: colors.textSecondary,
    textAlign: 'center',
  },
  optionsContainer: {
    paddingHorizontal: 20,
    marginBottom: 32,
  },
  optionCard: {
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
  optionContent: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  optionIcon: {
    width: 50,
    height: 50,
    borderRadius: 25,
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 16,
  },
  optionInfo: {
    flex: 1,
  },
  optionTitle: {
    ...typography.body1,
    color: colors.textPrimary,
    fontWeight: '600',
    marginBottom: 4,
  },
  optionDescription: {
    ...typography.body2,
    color: colors.textSecondary,
  },
  appInfoContainer: {
    alignItems: 'center',
    paddingHorizontal: 20,
    marginBottom: 32,
  },
  appName: {
    ...typography.body2,
    color: colors.textSecondary,
    textAlign: 'center',
    marginBottom: 4,
  },
  appVersion: {
    ...typography.caption,
    color: colors.textSecondary,
  },
  logoutButton: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: colors.white,
    marginHorizontal: 20,
    padding: 16,
    borderRadius: 12,
    borderWidth: 1,
    borderColor: colors.error,
    gap: 8,
  },
  logoutText: {
    ...typography.body1,
    color: colors.error,
    fontWeight: '600',
  },
});