import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  RefreshControl,
  Alert,
} from 'react-native';
import { useLocalSearchParams, useRouter } from 'expo-router';
import { useTranslation } from 'react-i18next';
import { SafeAreaView } from 'react-native-safe-area-context';
import { Ionicons } from '@expo/vector-icons';
import { colors } from '../../../src/theme/colors';
import { typography } from '../../../src/theme/fonts';
import { parentService } from '../../../src/services/api';
import { showMessage } from 'react-native-flash-message';
import LoadingScreen from '../../../src/components/LoadingScreen';

interface ChildDetails {
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
  address?: string;
  notes?: string;
}

interface AttendanceRecord {
  date: string;
  status: string;
  status_text: string;
  memorization_points: number;
  notes: string;
}

export default function ChildDetailsScreen() {
  const { id } = useLocalSearchParams();
  const router = useRouter();
  const { t } = useTranslation();
  const [child, setChild] = useState<ChildDetails | null>(null);
  const [attendanceRecords, setAttendanceRecords] = useState<AttendanceRecord[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [activeTab, setActiveTab] = useState<'details' | 'attendance'>('details');

  useEffect(() => {
    if (id) {
      loadChildDetails();
    }
  }, [id]);

  const loadChildDetails = async () => {
    try {
      const childId = parseInt(id as string);
      const [childData, attendanceData] = await Promise.all([
        parentService.getChildDetails(childId),
        parentService.getChildAttendance(childId),
      ]);
      
      setChild(childData);
      setAttendanceRecords(attendanceData);
    } catch (error) {
      console.error('Load child details error:', error);
      showMessage({
        message: t('error'),
        description: 'حدث خطأ في تحميل تفاصيل الطفل',
        type: 'danger',
      });
    } finally {
      setIsLoading(false);
      setRefreshing(false);
    }
  };

  const onRefresh = () => {
    setRefreshing(true);
    loadChildDetails();
  };

  const getAttendanceColor = (rate: number) => {
    if (rate >= 90) return colors.success;
    if (rate >= 75) return colors.warning;
    return colors.error;
  };

  const getStatusColor = (status: string) => {
    switch (status.toLowerCase()) {
      case 'present':
        return colors.success;
      case 'absent':
        return colors.error;
      case 'late':
        return colors.warning;
      default:
        return colors.gray500;
    }
  };

  const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('ar-SA', {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric',
    });
  };

  if (isLoading) {
    return <LoadingScreen />;
  }

  if (!child) {
    return (
      <SafeAreaView style={styles.container}>
        <View style={styles.errorContainer}>
          <Ionicons name="alert-circle" size={80} color={colors.error} />
          <Text style={styles.errorTitle}>خطأ في تحميل البيانات</Text>
          <Text style={styles.errorText}>
            لم نتمكن من العثور على بيانات هذا الطفل
          </Text>
          <TouchableOpacity style={styles.retryButton} onPress={() => router.back()}>
            <Text style={styles.retryButtonText}>العودة</Text>
          </TouchableOpacity>
        </View>
      </SafeAreaView>
    );
  }

  return (
    <SafeAreaView style={styles.container}>
      <View style={styles.header}>
        <TouchableOpacity onPress={() => router.back()} style={styles.backButton}>
          <Ionicons name="arrow-back" size={24} color={colors.white} />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>{child.name}</Text>
        <View style={styles.headerSpacer} />
      </View>

      <View style={styles.tabContainer}>
        <TouchableOpacity
          style={[styles.tab, activeTab === 'details' && styles.activeTab]}
          onPress={() => setActiveTab('details')}
        >
          <Text style={[styles.tabText, activeTab === 'details' && styles.activeTabText]}>
            التفاصيل
          </Text>
        </TouchableOpacity>
        <TouchableOpacity
          style={[styles.tab, activeTab === 'attendance' && styles.activeTab]}
          onPress={() => setActiveTab('attendance')}
        >
          <Text style={[styles.tabText, activeTab === 'attendance' && styles.activeTabText]}>
            سجل الحضور
          </Text>
        </TouchableOpacity>
      </View>

      <ScrollView
        contentContainerStyle={styles.scrollContent}
        refreshControl={
          <RefreshControl refreshing={refreshing} onRefresh={onRefresh} />
        }
        showsVerticalScrollIndicator={false}
      >
        {activeTab === 'details' ? (
          <View>
            {/* Basic Info Card */}
            <View style={styles.card}>
              <Text style={styles.cardTitle}>المعلومات الأساسية</Text>
              <View style={styles.infoGrid}>
                <View style={styles.infoRow}>
                  <View style={styles.infoItem}>
                    <Ionicons name="person" size={20} color={colors.primary} />
                    <Text style={styles.infoLabel}>الاسم</Text>
                    <Text style={styles.infoValue}>{child.name}</Text>
                  </View>
                  <View style={styles.infoItem}>
                    <Ionicons name="calendar" size={20} color={colors.secondary} />
                    <Text style={styles.infoLabel}>العمر</Text>
                    <Text style={styles.infoValue}>{child.age} سنة</Text>
                  </View>
                </View>
                <View style={styles.infoRow}>
                  <View style={styles.infoItem}>
                    <Ionicons name="school" size={20} color={colors.info} />
                    <Text style={styles.infoLabel}>الحلقة</Text>
                    <Text style={styles.infoValue}>{child.circle_name}</Text>
                  </View>
                  <View style={styles.infoItem}>
                    <Ionicons name="person-circle" size={20} color={colors.warning} />
                    <Text style={styles.infoLabel}>المعلم</Text>
                    <Text style={styles.infoValue}>{child.teacher_name}</Text>
                  </View>
                </View>
              </View>
            </View>

            {/* Performance Card */}
            <View style={styles.card}>
              <Text style={styles.cardTitle}>الأداء والإنجازات</Text>
              
              <View style={styles.performanceGrid}>
                <View style={styles.performanceItem}>
                  <View style={[styles.performanceBadge, { backgroundColor: `${getAttendanceColor(child.attendance_rate)}20` }]}>
                    <Ionicons name="checkmark-circle" size={24} color={getAttendanceColor(child.attendance_rate)} />
                  </View>
                  <Text style={styles.performanceValue}>{child.attendance_rate}%</Text>
                  <Text style={styles.performanceLabel}>معدل الحضور</Text>
                </View>

                <View style={styles.performanceItem}>
                  <View style={[styles.performanceBadge, { backgroundColor: `${colors.gold}20` }]}>
                    <Ionicons name="star" size={24} color={colors.gold} />
                  </View>
                  <Text style={styles.performanceValue}>{child.memorization_points}</Text>
                  <Text style={styles.performanceLabel}>نقاط الحفظ</Text>
                </View>

                <View style={styles.performanceItem}>
                  <View style={[styles.performanceBadge, { backgroundColor: `${colors.primary}20` }]}>
                    <Ionicons name="trophy" size={24} color={colors.primary} />
                  </View>
                  <Text style={styles.performanceValue}>{child.total_points}</Text>
                  <Text style={styles.performanceLabel}>إجمالي النقاط</Text>
                </View>
              </View>

              <View style={styles.levelContainer}>
                <Text style={styles.levelLabel}>المستوى الحالي:</Text>
                <Text style={styles.levelValue}>{child.level}</Text>
              </View>
            </View>

            {/* Recent Activity Card */}
            {child.recent_activity && (
              <View style={styles.card}>
                <Text style={styles.cardTitle}>النشاط الأخير</Text>
                <View style={styles.activityContainer}>
                  <Ionicons name="time" size={20} color={colors.primary} />
                  <Text style={styles.activityText}>{child.recent_activity}</Text>
                </View>
              </View>
            )}

            {/* Notes Card */}
            {child.notes && (
              <View style={styles.card}>
                <Text style={styles.cardTitle}>ملاحظات المعلم</Text>
                <Text style={styles.notesText}>{child.notes}</Text>
              </View>
            )}
          </View>
        ) : (
          /* Attendance Tab */
          <View>
            <Text style={styles.sectionTitle}>سجل الحضور (آخر 30 يوم)</Text>
            {attendanceRecords.length > 0 ? (
              attendanceRecords.map((record, index) => (
                <View key={index} style={styles.attendanceCard}>
                  <View style={styles.attendanceHeader}>
                    <Text style={styles.attendanceDate}>{formatDate(record.date)}</Text>
                    <View style={[styles.statusBadge, { backgroundColor: getStatusColor(record.status) }]}>
                      <Text style={styles.statusText}>{record.status_text}</Text>
                    </View>
                  </View>
                  
                  <View style={styles.attendanceBody}>
                    <View style={styles.attendanceRow}>
                      <Ionicons name="star" size={16} color={colors.gold} />
                      <Text style={styles.attendanceLabel}>نقاط الحفظ:</Text>
                      <Text style={styles.attendanceValue}>{record.memorization_points}</Text>
                    </View>
                    
                    {record.notes && (
                      <View style={styles.notesContainer}>
                        <Ionicons name="document-text" size={16} color={colors.gray600} />
                        <Text style={styles.attendanceNotes}>{record.notes}</Text>
                      </View>
                    )}
                  </View>
                </View>
              ))
            ) : (
              <View style={styles.emptyAttendance}>
                <Ionicons name="calendar-outline" size={60} color={colors.gray400} />
                <Text style={styles.emptyAttendanceText}>لا توجد سجلات حضور متاحة</Text>
              </View>
            )}
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
  header: {
    backgroundColor: colors.primary,
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 20,
    paddingVertical: 15,
  },
  backButton: {
    padding: 5,
  },
  headerTitle: {
    ...typography.h3,
    color: colors.white,
    flex: 1,
    textAlign: 'center',
    marginHorizontal: 10,
  },
  headerSpacer: {
    width: 34, // Same width as back button to center title
  },
  tabContainer: {
    flexDirection: 'row',
    backgroundColor: colors.white,
    elevation: 2,
    shadowColor: colors.black,
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.1,
    shadowRadius: 2,
  },
  tab: {
    flex: 1,
    paddingVertical: 16,
    alignItems: 'center',
    borderBottomWidth: 2,
    borderBottomColor: 'transparent',
  },
  activeTab: {
    borderBottomColor: colors.primary,
  },
  tabText: {
    ...typography.body1,
    color: colors.gray600,
    fontWeight: '500',
  },
  activeTabText: {
    color: colors.primary,
    fontWeight: '600',
  },
  scrollContent: {
    padding: 20,
    paddingBottom: 40,
  },
  card: {
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
  cardTitle: {
    ...typography.h4,
    color: colors.textPrimary,
    marginBottom: 16,
  },
  infoGrid: {
    gap: 16,
  },
  infoRow: {
    flexDirection: 'row',
    gap: 16,
  },
  infoItem: {
    flex: 1,
    alignItems: 'center',
    padding: 16,
    backgroundColor: colors.gray50,
    borderRadius: 12,
  },
  infoLabel: {
    ...typography.caption,
    color: colors.textSecondary,
    marginTop: 8,
    marginBottom: 4,
  },
  infoValue: {
    ...typography.body1,
    color: colors.textPrimary,
    fontWeight: '600',
    textAlign: 'center',
  },
  performanceGrid: {
    flexDirection: 'row',
    justifyContent: 'space-around',
    marginBottom: 20,
  },
  performanceItem: {
    alignItems: 'center',
  },
  performanceBadge: {
    width: 60,
    height: 60,
    borderRadius: 30,
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 8,
  },
  performanceValue: {
    ...typography.h4,
    color: colors.textPrimary,
    fontWeight: 'bold',
    marginBottom: 4,
  },
  performanceLabel: {
    ...typography.caption,
    color: colors.textSecondary,
    textAlign: 'center',
  },
  levelContainer: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    backgroundColor: colors.primary + '10',
    padding: 16,
    borderRadius: 12,
  },
  levelLabel: {
    ...typography.body1,
    color: colors.textPrimary,
    fontWeight: '600',
  },
  levelValue: {
    ...typography.body1,
    color: colors.primary,
    fontWeight: 'bold',
  },
  activityContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 12,
    backgroundColor: colors.info + '10',
    padding: 16,
    borderRadius: 12,
  },
  activityText: {
    ...typography.body2,
    color: colors.textPrimary,
    flex: 1,
  },
  notesText: {
    ...typography.body2,
    color: colors.textPrimary,
    lineHeight: 22,
    backgroundColor: colors.gray50,
    padding: 16,
    borderRadius: 12,
  },
  sectionTitle: {
    ...typography.h4,
    color: colors.textPrimary,
    marginBottom: 16,
  },
  attendanceCard: {
    backgroundColor: colors.white,
    borderRadius: 12,
    padding: 16,
    marginBottom: 12,
    elevation: 2,
    shadowColor: colors.black,
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
  },
  attendanceHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 12,
  },
  attendanceDate: {
    ...typography.body1,
    color: colors.textPrimary,
    fontWeight: '600',
  },
  statusBadge: {
    paddingHorizontal: 12,
    paddingVertical: 4,
    borderRadius: 12,
  },
  statusText: {
    ...typography.caption,
    color: colors.white,
    fontWeight: '600',
  },
  attendanceBody: {
    gap: 8,
  },
  attendanceRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
  },
  attendanceLabel: {
    ...typography.body2,
    color: colors.textSecondary,
  },
  attendanceValue: {
    ...typography.body2,
    color: colors.textPrimary,
    fontWeight: '600',
  },
  notesContainer: {
    flexDirection: 'row',
    alignItems: 'flex-start',
    gap: 8,
    backgroundColor: colors.gray50,
    padding: 12,
    borderRadius: 8,
  },
  attendanceNotes: {
    ...typography.body2,
    color: colors.textPrimary,
    flex: 1,
  },
  emptyAttendance: {
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 60,
  },
  emptyAttendanceText: {
    ...typography.body2,
    color: colors.textSecondary,
    marginTop: 16,
  },
  errorContainer: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
    padding: 40,
  },
  errorTitle: {
    ...typography.h4,
    color: colors.textPrimary,
    marginTop: 16,
    marginBottom: 8,
  },
  errorText: {
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