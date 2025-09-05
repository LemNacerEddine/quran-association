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
import { useRouter } from 'expo-router';
import { SafeAreaView } from 'react-native-safe-area-context';
import { Ionicons } from '@expo/vector-icons';
import { colors } from '../../src/theme/colors';
import { typography } from '../../src/theme/fonts';
import { parentService } from '../../src/services/api';
import { showMessage } from 'react-native-flash-message';
import LoadingScreen from '../../src/components/LoadingScreen';

interface Child {
  id: number;
  name: string;
  age: number;
  circle_name: string;
  teacher_name: string;
  attendance_rate: number;
  memorization_points: number;
  total_points: number;
  level: string;
  recent_activity: string;
}

export default function ChildrenScreen() {
  const { t } = useTranslation();
  const router = useRouter();
  const [children, setChildren] = useState<Child[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  useEffect(() => {
    loadChildren();
  }, []);

  const loadChildren = async () => {
    try {
      const data = await parentService.getChildren();
      setChildren(data);
    } catch (error) {
      console.error('Load children error:', error);
      showMessage({
        message: t('error'),
        description: 'حدث خطأ في تحميل بيانات الأبناء',
        type: 'danger',
      });
    } finally {
      setIsLoading(false);
      setRefreshing(false);
    }
  };

  const onRefresh = () => {
    setRefreshing(true);
    loadChildren();
  };

  const getAttendanceColor = (rate: number) => {
    if (rate >= 90) return colors.success;
    if (rate >= 75) return colors.warning;
    return colors.error;
  };

  const navigateToChildDetails = (childId: number) => {
    router.push(`/parent/child-details/${childId}`);
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
        <View style={styles.header}>
          <Text style={styles.headerTitle}>الأبناء</Text>
          <Text style={styles.headerSubtitle}>
            متابعة تقدم الأبناء في تحفيظ القرآن الكريم
          </Text>
        </View>

        {children.length > 0 ? (
          children.map((child) => (
            <TouchableOpacity key={child.id} style={styles.childCard}>
              <View style={styles.childHeader}>
                <View style={styles.childBasicInfo}>
                  <Text style={styles.childName}>{child.name}</Text>
                  <Text style={styles.childAge}>{child.age} سنة</Text>
                </View>
                <View style={styles.attendanceIndicator}>
                  <View 
                    style={[
                      styles.attendanceBadge,
                      { backgroundColor: getAttendanceColor(child.attendance_rate) }
                    ]}
                  >
                    <Text style={styles.attendanceText}>
                      {child.attendance_rate}%
                    </Text>
                  </View>
                </View>
              </View>

              <View style={styles.childDetails}>
                <View style={styles.detailRow}>
                  <Ionicons name="school" size={16} color={colors.primary} />
                  <Text style={styles.detailText}>{child.circle_name}</Text>
                </View>
                <View style={styles.detailRow}>
                  <Ionicons name="person" size={16} color={colors.secondary} />
                  <Text style={styles.detailText}>{child.teacher_name}</Text>
                </View>
                <View style={styles.detailRow}>
                  <Ionicons name="bar-chart" size={16} color={colors.info} />
                  <Text style={styles.detailText}>المستوى: {child.level}</Text>
                </View>
              </View>

              <View style={styles.statsRow}>
                <View style={styles.statItem}>
                  <Ionicons name="star" size={20} color={colors.gold} />
                  <View style={styles.statContent}>
                    <Text style={styles.statValue}>{child.memorization_points}</Text>
                    <Text style={styles.statLabel}>نقاط الحفظ</Text>
                  </View>
                </View>
                <View style={styles.statItem}>
                  <Ionicons name="trophy" size={20} color={colors.primary} />
                  <View style={styles.statContent}>
                    <Text style={styles.statValue}>{child.total_points}</Text>
                    <Text style={styles.statLabel}>إجمالي النقاط</Text>
                  </View>
                </View>
              </View>

              {child.recent_activity && (
                <View style={styles.recentActivity}>
                  <Text style={styles.activityLabel}>آخر نشاط:</Text>
                  <Text style={styles.activityText}>{child.recent_activity}</Text>
                </View>
              )}

              <View style={styles.cardFooter}>
                <TouchableOpacity style={styles.actionButton}>
                  <Text style={styles.actionButtonText}>عرض التفاصيل</Text>
                  <Ionicons name="chevron-forward" size={16} color={colors.primary} />
                </TouchableOpacity>
              </View>
            </TouchableOpacity>
          ))
        ) : (
          <View style={styles.emptyState}>
            <Ionicons name="people-outline" size={80} color={colors.gray400} />
            <Text style={styles.emptyTitle}>لا توجد بيانات</Text>
            <Text style={styles.emptyText}>
              لم يتم العثور على بيانات الأبناء
            </Text>
            <TouchableOpacity style={styles.retryButton} onPress={loadChildren}>
              <Text style={styles.retryButtonText}>إعادة المحاولة</Text>
            </TouchableOpacity>
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
    padding: 20,
    paddingBottom: 40,
  },
  header: {
    marginBottom: 24,
  },
  headerTitle: {
    ...typography.h2,
    color: colors.textPrimary,
    marginBottom: 4,
  },
  headerSubtitle: {
    ...typography.body2,
    color: colors.textSecondary,
  },
  childCard: {
    backgroundColor: colors.white,
    borderRadius: 16,
    padding: 20,
    marginBottom: 16,
    elevation: 4,
    shadowColor: colors.black,
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 8,
  },
  childHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
    marginBottom: 16,
  },
  childBasicInfo: {
    flex: 1,
  },
  childName: {
    ...typography.h4,
    color: colors.textPrimary,
    marginBottom: 4,
  },
  childAge: {
    ...typography.body2,
    color: colors.textSecondary,
  },
  attendanceIndicator: {
    alignItems: 'center',
  },
  attendanceBadge: {
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: 16,
  },
  attendanceText: {
    ...typography.caption,
    color: colors.white,
    fontWeight: '600',
  },
  childDetails: {
    marginBottom: 16,
    gap: 8,
  },
  detailRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
  },
  detailText: {
    ...typography.body2,
    color: colors.textPrimary,
    flex: 1,
  },
  statsRow: {
    flexDirection: 'row',
    justifyContent: 'space-around',
    paddingVertical: 16,
    borderTopWidth: 1,
    borderTopColor: colors.borderLight,
    borderBottomWidth: 1,
    borderBottomColor: colors.borderLight,
  },
  statItem: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
  },
  statContent: {
    alignItems: 'center',
  },
  statValue: {
    ...typography.body1,
    color: colors.textPrimary,
    fontWeight: '600',
  },
  statLabel: {
    ...typography.caption,
    color: colors.textSecondary,
  },
  recentActivity: {
    backgroundColor: colors.gray100,
    padding: 12,
    borderRadius: 8,
    marginTop: 12,
  },
  activityLabel: {
    ...typography.caption,
    color: colors.textSecondary,
    marginBottom: 4,
  },
  activityText: {
    ...typography.body2,
    color: colors.textPrimary,
  },
  cardFooter: {
    marginTop: 12,
    alignItems: 'flex-end',
  },
  actionButton: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 4,
  },
  actionButtonText: {
    ...typography.body2,
    color: colors.primary,
    fontWeight: '600',
  },
  emptyState: {
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 60,
  },
  emptyTitle: {
    ...typography.h4,
    color: colors.textPrimary,
    marginTop: 16,
    marginBottom: 8,
  },
  emptyText: {
    ...typography.body2,
    color: colors.textSecondary,
    textAlign: 'center',
    marginBottom: 24,
  },
  retryButton: {
    backgroundColor: colors.primary,
    paddingHorizontal: 24,
    paddingVertical: 12,
    borderRadius: 8,
  },
  retryButtonText: {
    ...typography.body2,
    color: colors.white,
    fontWeight: '600',
  },
});