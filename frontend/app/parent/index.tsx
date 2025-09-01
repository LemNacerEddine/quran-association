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
import { parentService } from '../../src/services/api';
import { useAuth } from '../../src/context/AuthContext';
import { showMessage } from 'react-native-flash-message';
import LoadingScreen from '../../src/components/LoadingScreen';
import { LinearGradient } from 'expo-linear-gradient';

interface Child {
  id: number;
  name: string;
  circle_name: string;
  attendance_rate: number;
  total_points: number;
  status: 'excellent' | 'good' | 'average' | 'needs_improvement';
}

interface DashboardData {
  children: Child[];
  stats: {
    total_children: number;
    average_attendance: number;
    total_points: number;
  };
}

export default function ParentDashboard() {
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
      const data = await parentService.getDashboard();
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

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'excellent': return colors.success;
      case 'good': return colors.info;
      case 'average': return colors.warning;
      case 'needs_improvement': return colors.error;
      default: return colors.gray500;
    }
  };

  const getStatusText = (status: string) => {
    switch (status) {
      case 'excellent': return t('excellent');
      case 'good': return t('good');
      case 'average': return t('average');
      case 'needs_improvement': return t('needsImprovement');
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
          <Text style={styles.userName}>{user?.name}</Text>
          <Text style={styles.headerSubtitle}>
            متابعة تقدم الأبناء في تحفيظ القرآن الكريم
          </Text>
        </LinearGradient>

        {/* Stats Cards */}
        {dashboardData?.stats && (
          <View style={styles.statsContainer}>
            <View style={styles.statsRow}>
              <View style={styles.statCard}>
                <Ionicons name="people" size={24} color={colors.primary} />
                <Text style={styles.statNumber}>{dashboardData.stats.total_children}</Text>
                <Text style={styles.statLabel}>الأبناء</Text>
              </View>
              <View style={styles.statCard}>
                <Ionicons name="checkmark-circle" size={24} color={colors.success} />
                <Text style={styles.statNumber}>
                  {dashboardData.stats.average_attendance.toFixed(0)}%
                </Text>
                <Text style={styles.statLabel}>معدل الحضور</Text>
              </View>
              <View style={styles.statCard}>
                <Ionicons name="star" size={24} color={colors.gold} />
                <Text style={styles.statNumber}>{dashboardData.stats.total_points}</Text>
                <Text style={styles.statLabel}>إجمالي النقاط</Text>
              </View>
            </View>
          </View>
        )}

        {/* Children List */}
        <View style={styles.sectionContainer}>
          <Text style={styles.sectionTitle}>الأبناء</Text>
          
          {dashboardData?.children && dashboardData.children.length > 0 ? (
            dashboardData.children.map((child) => (
              <TouchableOpacity key={child.id} style={styles.childCard}>
                <View style={styles.childCardContent}>
                  <View style={styles.childInfo}>
                    <Text style={styles.childName}>{child.name}</Text>
                    <Text style={styles.circleName}>{child.circle_name}</Text>
                  </View>
                  
                  <View style={styles.childStats}>
                    <View style={styles.statItem}>
                      <Text style={styles.statValue}>{child.attendance_rate}%</Text>
                      <Text style={styles.statText}>الحضور</Text>
                    </View>
                    <View style={styles.statItem}>
                      <Text style={styles.statValue}>{child.total_points}</Text>
                      <Text style={styles.statText}>النقاط</Text>
                    </View>
                  </View>
                  
                  <View style={styles.childStatus}>
                    <View 
                      style={[
                        styles.statusBadge, 
                        { backgroundColor: getStatusColor(child.status) }
                      ]}
                    >
                      <Text style={styles.statusText}>
                        {getStatusText(child.status)}
                      </Text>
                    </View>
                    <Ionicons name="chevron-forward" size={20} color={colors.gray600} />
                  </View>
                </View>
              </TouchableOpacity>
            ))
          ) : (
            <View style={styles.emptyState}>
              <Ionicons name="people-outline" size={64} color={colors.gray400} />
              <Text style={styles.emptyText}>لا توجد بيانات للأبناء</Text>
            </View>
          )}
        </View>

        {/* Quick Actions */}
        <View style={styles.sectionContainer}>
          <Text style={styles.sectionTitle}>الإجراءات السريعة</Text>
          
          <View style={styles.quickActionsRow}>
            <TouchableOpacity style={styles.quickActionCard}>
              <Ionicons name="calendar" size={32} color={colors.primary} />
              <Text style={styles.quickActionText}>الجدول الأسبوعي</Text>
            </TouchableOpacity>
            
            <TouchableOpacity style={styles.quickActionCard}>
              <Ionicons name="notifications" size={32} color={colors.warning} />
              <Text style={styles.quickActionText}>الإشعارات</Text>
            </TouchableOpacity>
            
            <TouchableOpacity style={styles.quickActionCard}>
              <Ionicons name="bar-chart" size={32} color={colors.info} />
              <Text style={styles.quickActionText}>التقارير</Text>
            </TouchableOpacity>
          </View>
        </View>
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
  },
  statsRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
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
  sectionTitle: {
    ...typography.h4,
    color: colors.textPrimary,
    marginBottom: 16,
  },
  childCard: {
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
  childCardContent: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  childInfo: {
    flex: 1,
  },
  childName: {
    ...typography.body1,
    color: colors.textPrimary,
    fontWeight: '600',
  },
  circleName: {
    ...typography.body2,
    color: colors.textSecondary,
    marginTop: 2,
  },
  childStats: {
    flexDirection: 'row',
    gap: 16,
    marginHorizontal: 16,
  },
  statItem: {
    alignItems: 'center',
  },
  statValue: {
    ...typography.body1,
    color: colors.primary,
    fontWeight: '600',
  },
  statText: {
    ...typography.caption,
    color: colors.textSecondary,
  },
  childStatus: {
    alignItems: 'center',
    gap: 8,
  },
  statusBadge: {
    paddingHorizontal: 8,
    paddingVertical: 4,
    borderRadius: 12,
  },
  statusText: {
    ...typography.caption,
    color: colors.white,
    fontWeight: '600',
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
  quickActionsRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    gap: 12,
  },
  quickActionCard: {
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
  quickActionText: {
    ...typography.caption,
    color: colors.textPrimary,
    marginTop: 8,
    textAlign: 'center',
  },
});