import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  RefreshControl,
} from 'react-native';
import { useTranslation } from 'react-i18next';
import { SafeAreaView } from 'react-native-safe-area-context';
import { Ionicons } from '@expo/vector-icons';
import { colors } from '../../src/theme/colors';
import { typography } from '../../src/theme/fonts';
import { teacherService } from '../../src/services/api';
import { useAuth } from '../../src/context/AuthContext';
import { showMessage } from 'react-native-flash-message';
import LoadingScreen from '../../src/components/LoadingScreen';
import { LinearGradient } from 'expo-linear-gradient';

interface TodaySession {
  id: number;
  title: string;
  circle_name: string;
  start_time: string;
  end_time: string;
  students_count: number;
  status: 'scheduled' | 'ongoing' | 'completed';
}

interface DashboardData {
  today_sessions: TodaySession[];
  stats: {
    total_circles: number;
    total_students: number;
    today_sessions: number;
    attendance_rate: number;
  };
  notifications: Array<{
    id: number;
    message: string;
    type: 'info' | 'warning' | 'success';
    created_at: string;
  }>;
}

export default function TeacherDashboard() {
  const { t } = useTranslation();
  const { user } = useAuth();
  const [dashboardData, setDashboardData] = useState<DashboardData | null>(null);
  const [isLoading, setIsLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  useEffect(() => {
    loadDashboardData();
  }, []);

  const loadDashboardData = async () => {
    try {
      const data = await teacherService.getDashboard();
      setDashboardData(data);
    } catch (error) {
      console.error('Load dashboard error:', error);
      showMessage({
        message: t('error'),
        description: 'حدث خطأ في تحميل البيانات',
        type: 'danger',
      });
    } finally {
      setIsLoading(false);
      setRefreshing(false);
    }
  };

  const onRefresh = () => {
    setRefreshing(true);
    loadDashboardData();
  };

  const getSessionStatusColor = (status: string) => {
    switch (status) {
      case 'ongoing': return colors.success;
      case 'completed': return colors.gray500;
      case 'scheduled': return colors.primary;
      default: return colors.gray500;
    }
  };

  const getSessionStatusText = (status: string) => {
    switch (status) {
      case 'ongoing': return 'جارية';
      case 'completed': return 'منتهية';
      case 'scheduled': return 'مجدولة';
      default: return status;
    }
  };

  if (isLoading) {
    return <LoadingScreen />;
  }

  return (
    <SafeAreaView style={styles.container}>
      <ScrollView
        contentContainerStyle={styles.scrollContent}
        refreshControl={
          <RefreshControl refreshing={refreshing} onRefresh={onRefresh} />
        }
        showsVerticalScrollIndicator={false}
      >
        {/* Header */}
        <LinearGradient
          colors={[colors.primary, colors.primaryLight]}
          style={styles.header}
        >
          <Text style={styles.welcomeText}>أهلاً وسهلاً</Text>
          <Text style={styles.userName}>الأستاذ {user?.name}</Text>
          <Text style={styles.headerSubtitle}>
            إدارة الحلقات والجلسات
          </Text>
        </LinearGradient>

        {/* Stats Cards */}
        {dashboardData?.stats && (
          <View style={styles.statsContainer}>
            <View style={styles.statsRow}>
              <View style={styles.statCard}>
                <Ionicons name="school" size={24} color={colors.primary} />
                <Text style={styles.statNumber}>{dashboardData.stats.total_circles}</Text>
                <Text style={styles.statLabel}>الحلقات</Text>
              </View>
              <View style={styles.statCard}>
                <Ionicons name="people" size={24} color={colors.secondary} />
                <Text style={styles.statNumber}>{dashboardData.stats.total_students}</Text>
                <Text style={styles.statLabel}>الطلاب</Text>
              </View>
            </View>
            <View style={styles.statsRow}>
              <View style={styles.statCard}>
                <Ionicons name="calendar" size={24} color={colors.info} />
                <Text style={styles.statNumber}>{dashboardData.stats.today_sessions}</Text>
                <Text style={styles.statLabel}>جلسات اليوم</Text>
              </View>
              <View style={styles.statCard}>
                <Ionicons name="checkmark-circle" size={24} color={colors.success} />
                <Text style={styles.statNumber}>
                  {dashboardData.stats.attendance_rate.toFixed(0)}%
                </Text>
                <Text style={styles.statLabel}>معدل الحضور</Text>
              </View>
            </View>
          </View>
        )}

        {/* Today's Sessions */}
        <View style={styles.sectionContainer}>
          <View style={styles.sectionHeader}>
            <Text style={styles.sectionTitle}>جلسات اليوم</Text>
            <TouchableOpacity>
              <Text style={styles.sectionLink}>عرض الكل</Text>
            </TouchableOpacity>
          </View>
          
          {dashboardData?.today_sessions && dashboardData.today_sessions.length > 0 ? (
            dashboardData.today_sessions.map((session) => (
              <TouchableOpacity key={session.id} style={styles.sessionCard}>
                <View style={styles.sessionHeader}>
                  <View style={styles.sessionInfo}>
                    <Text style={styles.sessionTitle}>{session.title}</Text>
                    <Text style={styles.sessionCircle}>{session.circle_name}</Text>
                  </View>
                  <View 
                    style={[
                      styles.sessionStatus,
                      { backgroundColor: getSessionStatusColor(session.status) }
                    ]}
                  >
                    <Text style={styles.sessionStatusText}>
                      {getSessionStatusText(session.status)}
                    </Text>
                  </View>
                </View>

                <View style={styles.sessionDetails}>
                  <View style={styles.sessionDetailItem}>
                    <Ionicons name="time" size={16} color={colors.gray600} />
                    <Text style={styles.sessionDetailText}>
                      {session.start_time} - {session.end_time}
                    </Text>
                  </View>
                  <View style={styles.sessionDetailItem}>
                    <Ionicons name="people" size={16} color={colors.gray600} />
                    <Text style={styles.sessionDetailText}>
                      {session.students_count} طالب
                    </Text>
                  </View>
                </View>

                <View style={styles.sessionActions}>
                  {session.status === 'scheduled' && (
                    <TouchableOpacity style={styles.actionButton}>
                      <Text style={styles.actionButtonText}>بدء الجلسة</Text>
                    </TouchableOpacity>
                  )}
                  {session.status === 'ongoing' && (
                    <TouchableOpacity style={[styles.actionButton, styles.ongoingButton]}>
                      <Text style={styles.actionButtonText}>تسجيل الحضور</Text>
                    </TouchableOpacity>
                  )}
                  {session.status === 'completed' && (
                    <TouchableOpacity style={[styles.actionButton, styles.viewButton]}>
                      <Text style={[styles.actionButtonText, styles.viewButtonText]}>
                        عرض التفاصيل
                      </Text>
                    </TouchableOpacity>
                  )}
                </View>
              </TouchableOpacity>
            ))
          ) : (
            <View style={styles.emptyState}>
              <Ionicons name="calendar-outline" size={64} color={colors.gray400} />
              <Text style={styles.emptyText}>لا توجد جلسات اليوم</Text>
            </View>
          )}
        </View>

        {/* Quick Actions */}
        <View style={styles.sectionContainer}>
          <Text style={styles.sectionTitle}>الإجراءات السريعة</Text>
          
          <View style={styles.quickActionsGrid}>
            <TouchableOpacity style={styles.quickActionCard}>
              <Ionicons name="add-circle" size={32} color={colors.primary} />
              <Text style={styles.quickActionText}>جلسة جديدة</Text>
            </TouchableOpacity>
            
            <TouchableOpacity style={styles.quickActionCard}>
              <Ionicons name="checkmark-circle" size={32} color={colors.success} />
              <Text style={styles.quickActionText}>تسجيل حضور</Text>
            </TouchableOpacity>
            
            <TouchableOpacity style={styles.quickActionCard}>
              <Ionicons name="people" size={32} color={colors.secondary} />
              <Text style={styles.quickActionText}>إدارة الطلاب</Text>
            </TouchableOpacity>
            
            <TouchableOpacity style={styles.quickActionCard}>
              <Ionicons name="bar-chart" size={32} color={colors.info} />
              <Text style={styles.quickActionText}>التقارير</Text>
            </TouchableOpacity>
          </View>
        </View>

        {/* Notifications */}
        {dashboardData?.notifications && dashboardData.notifications.length > 0 && (
          <View style={styles.sectionContainer}>
            <Text style={styles.sectionTitle}>الإشعارات</Text>
            
            {dashboardData.notifications.slice(0, 3).map((notification) => (
              <View key={notification.id} style={styles.notificationCard}>
                <View style={styles.notificationContent}>
                  <Ionicons 
                    name="notifications" 
                    size={20} 
                    color={colors.warning} 
                  />
                  <Text style={styles.notificationText}>
                    {notification.message}
                  </Text>
                </View>
                <Text style={styles.notificationTime}>
                  {new Date(notification.created_at).toLocaleDateString('ar-SA')}
                </Text>
              </View>
            ))}
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
    paddingBottom: 20,
  },
  header: {
    padding: 20,
    paddingTop: 40,
    marginBottom: 20,
  },
  welcomeText: {
    ...typography.body1,
    color: colors.white,
    opacity: 0.9,
  },
  userName: {
    ...typography.h2,
    color: colors.white,
    marginTop: 4,
  },
  headerSubtitle: {
    ...typography.body2,
    color: colors.white,
    opacity: 0.8,
    marginTop: 8,
  },
  statsContainer: {
    paddingHorizontal: 20,
    marginBottom: 20,
    gap: 12,
  },
  statsRow: {
    flexDirection: 'row',
    gap: 12,
  },
  statCard: {
    flex: 1,
    backgroundColor: colors.white,
    padding: 16,
    borderRadius: 12,
    alignItems: 'center',
    elevation: 2,
    shadowColor: colors.black,
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.1,
    shadowRadius: 2,
  },
  statNumber: {
    ...typography.h3,
    color: colors.textPrimary,
    marginTop: 8,
  },
  statLabel: {
    ...typography.caption,
    color: colors.textSecondary,
    marginTop: 4,
  },
  sectionContainer: {
    paddingHorizontal: 20,
    marginBottom: 20,
  },
  sectionHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 16,
  },
  sectionTitle: {
    ...typography.h4,
    color: colors.textPrimary,
  },
  sectionLink: {
    ...typography.body2,
    color: colors.primary,
  },
  sessionCard: {
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
  sessionHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
    marginBottom: 12,
  },
  sessionInfo: {
    flex: 1,
  },
  sessionTitle: {
    ...typography.body1,
    color: colors.textPrimary,
    fontWeight: '600',
    marginBottom: 4,
  },
  sessionCircle: {
    ...typography.body2,
    color: colors.textSecondary,
  },
  sessionStatus: {
    paddingHorizontal: 8,
    paddingVertical: 4,
    borderRadius: 12,
  },
  sessionStatusText: {
    ...typography.caption,
    color: colors.white,
    fontWeight: '600',
  },
  sessionDetails: {
    flexDirection: 'row',
    gap: 20,
    marginBottom: 16,
  },
  sessionDetailItem: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
  },
  sessionDetailText: {
    ...typography.body2,
    color: colors.textSecondary,
  },
  sessionActions: {
    alignItems: 'flex-end',
  },
  actionButton: {
    backgroundColor: colors.primary,
    paddingHorizontal: 16,
    paddingVertical: 8,
    borderRadius: 8,
  },
  ongoingButton: {
    backgroundColor: colors.success,
  },
  viewButton: {
    backgroundColor: colors.gray100,
  },
  actionButtonText: {
    ...typography.body2,
    color: colors.white,
    fontWeight: '600',
  },
  viewButtonText: {
    color: colors.textPrimary,
  },
  emptyState: {
    alignItems: 'center',
    paddingVertical: 40,
  },
  emptyText: {
    ...typography.body2,
    color: colors.textSecondary,
    marginTop: 16,
  },
  quickActionsGrid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 12,
  },
  quickActionCard: {
    backgroundColor: colors.white,
    padding: 16,
    borderRadius: 12,
    alignItems: 'center',
    width: '48%',
    elevation: 2,
    shadowColor: colors.black,
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.1,
    shadowRadius: 2,
  },
  quickActionText: {
    ...typography.caption,
    color: colors.textPrimary,
    marginTop: 8,
    textAlign: 'center',
  },
  notificationCard: {
    backgroundColor: colors.white,
    borderRadius: 8,
    padding: 12,
    marginBottom: 8,
    borderLeftWidth: 4,
    borderLeftColor: colors.warning,
  },
  notificationContent: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
    marginBottom: 4,
  },
  notificationText: {
    ...typography.body2,
    color: colors.textPrimary,
    flex: 1,
  },
  notificationTime: {
    ...typography.caption,
    color: colors.textSecondary,
    textAlign: 'right',
  },
});