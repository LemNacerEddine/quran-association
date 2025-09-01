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
import { useTranslation } from 'react-i18next';
import { SafeAreaView } from 'react-native-safe-area-context';
import { Ionicons } from '@expo/vector-icons';
import { colors } from '../../src/theme/colors';
import { typography } from '../../src/theme/fonts';
import { teacherService } from '../../src/services/api';
import { showMessage } from 'react-native-flash-message';
import LoadingScreen from '../../src/components/LoadingScreen';

interface Session {
  id: number;
  title: string;
  circle_name: string;
  session_date: string;
  start_time: string;
  end_time: string;
  students_count: number;
  present_count: number;
  absent_count: number;
  status: 'scheduled' | 'ongoing' | 'completed' | 'cancelled';
  location: string;
}

export default function SessionsScreen() {
  const { t } = useTranslation();
  const [sessions, setSessions] = useState<Session[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [filter, setFilter] = useState<'all' | 'today' | 'upcoming'>('all');

  useEffect(() => {
    loadSessions();
  }, []);

  const loadSessions = async () => {
    try {
      const data = await teacherService.getSessions();
      setSessions(data);
    } catch (error) {
      console.error('Load sessions error:', error);
      showMessage({
        message: t('error'),
        description: 'حدث خطأ في تحميل الجلسات',
        type: 'danger',
      });
    } finally {
      setIsLoading(false);
      setRefreshing(false);
    }
  };

  const onRefresh = () => {
    setRefreshing(true);
    loadSessions();
  };

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'ongoing': return colors.success;
      case 'completed': return colors.primary;
      case 'scheduled': return colors.info;
      case 'cancelled': return colors.error;
      default: return colors.gray500;
    }
  };

  const getStatusText = (status: string) => {
    switch (status) {
      case 'ongoing': return 'جارية';
      case 'completed': return 'منتهية';
      case 'scheduled': return 'مجدولة';
      case 'cancelled': return 'ملغية';
      default: return status;
    }
  };

  const handleSessionAction = (session: Session, action: string) => {
    switch (action) {
      case 'start':
        Alert.alert(
          'بدء الجلسة',
          `هل تريد بدء جلسة ${session.title}؟`,
          [
            { text: 'إلغاء', style: 'cancel' },
            { 
              text: 'بدء', 
              onPress: () => {
                showMessage({
                  message: 'تم بدء الجلسة',
                  type: 'success',
                });
              }
            },
          ]
        );
        break;
      case 'attendance':
        showMessage({
          message: 'سيتم فتح شاشة تسجيل الحضور',
          type: 'info',
        });
        break;
      case 'end':
        Alert.alert(
          'إنهاء الجلسة',
          `هل تريد إنهاء جلسة ${session.title}؟`,
          [
            { text: 'إلغاء', style: 'cancel' },
            { 
              text: 'إنهاء', 
              onPress: () => {
                showMessage({
                  message: 'تم إنهاء الجلسة',
                  type: 'success',
                });
              }
            },
          ]
        );
        break;
    }
  };

  const filteredSessions = sessions.filter(session => {
    if (filter === 'today') {
      const today = new Date().toDateString();
      return new Date(session.session_date).toDateString() === today;
    } else if (filter === 'upcoming') {
      return new Date(session.session_date) > new Date() && session.status === 'scheduled';
    }
    return true;
  });

  if (isLoading) {
    return <LoadingScreen />;
  }

  return (
    <SafeAreaView style={styles.container}>
      <View style={styles.header}>
        <Text style={styles.headerTitle}>الجلسات</Text>
        <TouchableOpacity style={styles.addButton}>
          <Ionicons name="add" size={24} color={colors.white} />
        </TouchableOpacity>
      </View>

      {/* Filter Tabs */}
      <View style={styles.filterContainer}>
        <TouchableOpacity
          style={[styles.filterTab, filter === 'all' && styles.activeFilterTab]}
          onPress={() => setFilter('all')}
        >
          <Text style={[
            styles.filterTabText, 
            filter === 'all' && styles.activeFilterTabText
          ]}>
            الكل
          </Text>
        </TouchableOpacity>
        <TouchableOpacity
          style={[styles.filterTab, filter === 'today' && styles.activeFilterTab]}
          onPress={() => setFilter('today')}
        >
          <Text style={[
            styles.filterTabText, 
            filter === 'today' && styles.activeFilterTabText
          ]}>
            اليوم
          </Text>
        </TouchableOpacity>
        <TouchableOpacity
          style={[styles.filterTab, filter === 'upcoming' && styles.activeFilterTab]}
          onPress={() => setFilter('upcoming')}
        >
          <Text style={[
            styles.filterTabText, 
            filter === 'upcoming' && styles.activeFilterTabText
          ]}>
            القادمة
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
        {filteredSessions.length > 0 ? (
          filteredSessions.map((session) => (
            <View key={session.id} style={styles.sessionCard}>
              <View style={styles.sessionHeader}>
                <View style={styles.sessionMainInfo}>
                  <Text style={styles.sessionTitle}>{session.title}</Text>
                  <Text style={styles.sessionCircle}>{session.circle_name}</Text>
                </View>
                <View 
                  style={[
                    styles.statusBadge,
                    { backgroundColor: getStatusColor(session.status) }
                  ]}
                >
                  <Text style={styles.statusText}>
                    {getStatusText(session.status)}
                  </Text>
                </View>
              </View>

              <View style={styles.sessionDetails}>
                <View style={styles.detailRow}>
                  <Ionicons name="calendar" size={16} color={colors.gray600} />
                  <Text style={styles.detailText}>
                    {new Date(session.session_date).toLocaleDateString('ar-SA')}
                  </Text>
                </View>
                <View style={styles.detailRow}>
                  <Ionicons name="time" size={16} color={colors.gray600} />
                  <Text style={styles.detailText}>
                    {session.start_time} - {session.end_time}
                  </Text>
                </View>
                <View style={styles.detailRow}>
                  <Ionicons name="location" size={16} color={colors.gray600} />
                  <Text style={styles.detailText}>{session.location}</Text>
                </View>
              </View>

              {/* Attendance Stats */}
              {session.status === 'completed' && (
                <View style={styles.attendanceStats}>
                  <View style={styles.statItem}>
                    <View style={styles.statIndicator}>
                      <View style={[styles.statDot, { backgroundColor: colors.success }]} />
                      <Text style={styles.statCount}>{session.present_count}</Text>
                    </View>
                    <Text style={styles.statLabel}>حاضر</Text>
                  </View>
                  <View style={styles.statItem}>
                    <View style={styles.statIndicator}>
                      <View style={[styles.statDot, { backgroundColor: colors.error }]} />
                      <Text style={styles.statCount}>{session.absent_count}</Text>
                    </View>
                    <Text style={styles.statLabel}>غائب</Text>
                  </View>
                  <View style={styles.statItem}>
                    <View style={styles.statIndicator}>
                      <View style={[styles.statDot, { backgroundColor: colors.primary }]} />
                      <Text style={styles.statCount}>{session.students_count}</Text>
                    </View>
                    <Text style={styles.statLabel}>إجمالي</Text>
                  </View>
                </View>
              )}

              {/* Action Buttons */}
              <View style={styles.actionButtons}>
                {session.status === 'scheduled' && (
                  <TouchableOpacity
                    style={[styles.actionButton, styles.startButton]}
                    onPress={() => handleSessionAction(session, 'start')}
                  >
                    <Ionicons name="play" size={16} color={colors.white} />
                    <Text style={styles.actionButtonText}>بدء الجلسة</Text>
                  </TouchableOpacity>
                )}
                
                {session.status === 'ongoing' && (
                  <>
                    <TouchableOpacity
                      style={[styles.actionButton, styles.attendanceButton]}
                      onPress={() => handleSessionAction(session, 'attendance')}
                    >
                      <Ionicons name="checkmark-circle" size={16} color={colors.white} />
                      <Text style={styles.actionButtonText}>تسجيل الحضور</Text>
                    </TouchableOpacity>
                    <TouchableOpacity
                      style={[styles.actionButton, styles.endButton]}
                      onPress={() => handleSessionAction(session, 'end')}
                    >
                      <Ionicons name="stop" size={16} color={colors.white} />
                      <Text style={styles.actionButtonText}>إنهاء</Text>
                    </TouchableOpacity>
                  </>
                )}
                
                {session.status === 'completed' && (
                  <TouchableOpacity
                    style={[styles.actionButton, styles.viewButton]}
                  >
                    <Ionicons name="eye" size={16} color={colors.primary} />
                    <Text style={[styles.actionButtonText, { color: colors.primary }]}>
                      عرض التفاصيل
                    </Text>
                  </TouchableOpacity>
                )}
              </View>
            </View>
          ))
        ) : (
          <View style={styles.emptyState}>
            <Ionicons name="calendar-outline" size={80} color={colors.gray400} />
            <Text style={styles.emptyTitle}>لا توجد جلسات</Text>
            <Text style={styles.emptyText}>
              {filter === 'today' ? 'لا توجد جلسات اليوم' : 
               filter === 'upcoming' ? 'لا توجد جلسات قادمة' : 
               'لم يتم العثور على جلسات'}
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
  header: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: 20,
    paddingVertical: 16,
    backgroundColor: colors.primary,
  },
  headerTitle: {
    ...typography.h3,
    color: colors.white,
  },
  addButton: {
    width: 40,
    height: 40,
    borderRadius: 20,
    backgroundColor: colors.primaryDark,
    justifyContent: 'center',
    alignItems: 'center',
  },
  filterContainer: {
    flexDirection: 'row',
    paddingHorizontal: 20,
    paddingVertical: 16,
    gap: 12,
  },
  filterTab: {
    paddingHorizontal: 16,
    paddingVertical: 8,
    borderRadius: 20,
    backgroundColor: colors.gray200,
  },
  activeFilterTab: {
    backgroundColor: colors.primary,
  },
  filterTabText: {
    ...typography.body2,
    color: colors.textSecondary,
  },
  activeFilterTabText: {
    color: colors.white,
    fontWeight: '600',
  },
  scrollContent: {
    padding: 20,
    paddingBottom: 40,
  },
  sessionCard: {
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
  sessionHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
    marginBottom: 16,
  },
  sessionMainInfo: {
    flex: 1,
  },
  sessionTitle: {
    ...typography.h4,
    color: colors.textPrimary,
    marginBottom: 4,
  },
  sessionCircle: {
    ...typography.body2,
    color: colors.textSecondary,
  },
  statusBadge: {
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: 16,
  },
  statusText: {
    ...typography.caption,
    color: colors.white,
    fontWeight: '600',
  },
  sessionDetails: {
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
  },
  attendanceStats: {
    flexDirection: 'row',
    justifyContent: 'space-around',
    paddingVertical: 16,
    borderTopWidth: 1,
    borderTopColor: colors.borderLight,
    marginBottom: 16,
  },
  statItem: {
    alignItems: 'center',
  },
  statIndicator: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 4,
    marginBottom: 4,
  },
  statDot: {
    width: 8,
    height: 8,
    borderRadius: 4,
  },
  statCount: {
    ...typography.body1,
    color: colors.textPrimary,
    fontWeight: '600',
  },
  statLabel: {
    ...typography.caption,
    color: colors.textSecondary,
  },
  actionButtons: {
    flexDirection: 'row',
    gap: 12,
    justifyContent: 'flex-end',
  },
  actionButton: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 16,
    paddingVertical: 8,
    borderRadius: 8,
    gap: 6,
  },
  startButton: {
    backgroundColor: colors.primary,
  },
  attendanceButton: {
    backgroundColor: colors.success,
  },
  endButton: {
    backgroundColor: colors.error,
  },
  viewButton: {
    backgroundColor: colors.gray100,
    borderWidth: 1,
    borderColor: colors.primary,
  },
  actionButtonText: {
    ...typography.body2,
    color: colors.white,
    fontWeight: '600',
  },
  emptyState: {
    alignItems: 'center',
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
  },
});