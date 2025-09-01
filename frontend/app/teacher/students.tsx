import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  RefreshControl,
  TextInput,
} from 'react-native';
import { useTranslation } from 'react-i18next';
import { SafeAreaView } from 'react-native-safe-area-context';
import { Ionicons } from '@expo/vector-icons';
import { colors } from '../../src/theme/colors';
import { typography } from '../../src/theme/fonts';
import { teacherService } from '../../src/services/api';
import { showMessage } from 'react-native-flash-message';
import LoadingScreen from '../../src/components/LoadingScreen';

interface Student {
  id: number;
  name: string;
  age: number;
  circle_name: string;
  attendance_rate: number;
  memorization_points: number;
  behavior_points: number;
  total_points: number;
  last_attendance: string;
  parent_phone: string;
  performance_level: 'excellent' | 'good' | 'average' | 'needs_improvement';
}

export default function StudentsScreen() {
  const { t } = useTranslation();
  const [students, setStudents] = useState<Student[]>([]);
  const [filteredStudents, setFilteredStudents] = useState<Student[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [searchQuery, setSearchQuery] = useState('');

  useEffect(() => {
    loadStudents();
  }, []);

  useEffect(() => {
    filterStudents();
  }, [searchQuery, students]);

  const loadStudents = async () => {
    try {
      const data = await teacherService.getStudents();
      setStudents(data);
    } catch (error) {
      console.error('Load students error:', error);
      showMessage({
        message: t('error'),
        description: 'حدث خطأ في تحميل بيانات الطلاب',
        type: 'danger',
      });
    } finally {
      setIsLoading(false);
      setRefreshing(false);
    }
  };

  const filterStudents = () => {
    if (!searchQuery) {
      setFilteredStudents(students);
      return;
    }

    const filtered = students.filter(student =>
      student.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
      student.circle_name.toLowerCase().includes(searchQuery.toLowerCase())
    );
    setFilteredStudents(filtered);
  };

  const onRefresh = () => {
    setRefreshing(true);
    loadStudents();
  };

  const getPerformanceColor = (level: string) => {
    switch (level) {
      case 'excellent': return colors.success;
      case 'good': return colors.info;
      case 'average': return colors.warning;
      case 'needs_improvement': return colors.error;
      default: return colors.gray500;
    }
  };

  const getPerformanceText = (level: string) => {
    switch (level) {
      case 'excellent': return 'ممتاز';
      case 'good': return 'جيد';
      case 'average': return 'متوسط';
      case 'needs_improvement': return 'يحتاج تحسين';
      default: return level;
    }
  };

  const getAttendanceColor = (rate: number) => {
    if (rate >= 90) return colors.success;
    if (rate >= 75) return colors.warning;
    return colors.error;
  };

  if (isLoading) {
    return <LoadingScreen />;
  }

  return (
    <SafeAreaView style={styles.container}>
      <View style={styles.header}>
        <Text style={styles.headerTitle}>الطلاب</Text>
      </View>

      {/* Search Bar */}
      <View style={styles.searchContainer}>
        <View style={styles.searchInputWrapper}>
          <Ionicons name="search" size={20} color={colors.gray600} />
          <TextInput
            style={styles.searchInput}
            placeholder="البحث عن طالب أو حلقة..."
            placeholderTextColor={colors.gray500}
            value={searchQuery}
            onChangeText={setSearchQuery}
            textAlign="right"
          />
        </View>
      </View>

      {/* Stats Summary */}
      <View style={styles.statsContainer}>
        <View style={styles.statCard}>
          <Text style={styles.statNumber}>{students.length}</Text>
          <Text style={styles.statLabel}>إجمالي الطلاب</Text>
        </View>
        <View style={styles.statCard}>
          <Text style={styles.statNumber}>
            {students.filter(s => s.attendance_rate >= 90).length}
          </Text>
          <Text style={styles.statLabel}>حضور ممتاز</Text>
        </View>
        <View style={styles.statCard}>
          <Text style={styles.statNumber}>
            {students.filter(s => s.performance_level === 'excellent').length}
          </Text>
          <Text style={styles.statLabel}>أداء ممتاز</Text>
        </View>
      </View>

      <ScrollView
        contentContainerStyle={styles.scrollContent}
        refreshControl={
          <RefreshControl refreshing={refreshing} onRefresh={onRefresh} />
        }
        showsVerticalScrollIndicator={false}
      >
        {filteredStudents.length > 0 ? (
          filteredStudents.map((student) => (
            <TouchableOpacity key={student.id} style={styles.studentCard}>
              <View style={styles.studentHeader}>
                <View style={styles.studentBasicInfo}>
                  <Text style={styles.studentName}>{student.name}</Text>
                  <Text style={styles.studentAge}>{student.age} سنة</Text>
                  <Text style={styles.studentCircle}>{student.circle_name}</Text>
                </View>
                
                <View style={styles.performanceContainer}>
                  <View 
                    style={[
                      styles.performanceBadge,
                      { backgroundColor: getPerformanceColor(student.performance_level) }
                    ]}
                  >
                    <Text style={styles.performanceText}>
                      {getPerformanceText(student.performance_level)}
                    </Text>
                  </View>
                  
                  <View 
                    style={[
                      styles.attendanceBadge,
                      { backgroundColor: getAttendanceColor(student.attendance_rate) }
                    ]}
                  >
                    <Text style={styles.attendanceText}>
                      {student.attendance_rate}%
                    </Text>
                  </View>
                </View>
              </View>

              <View style={styles.studentStats}>
                <View style={styles.statItem}>
                  <Ionicons name="star" size={16} color={colors.gold} />
                  <Text style={styles.statValue}>{student.memorization_points}</Text>
                  <Text style={styles.statText}>حفظ</Text>
                </View>
                <View style={styles.statItem}>
                  <Ionicons name="happy" size={16} color={colors.success} />
                  <Text style={styles.statValue}>{student.behavior_points}</Text>
                  <Text style={styles.statText}>سلوك</Text>
                </View>
                <View style={styles.statItem}>
                  <Ionicons name="trophy" size={16} color={colors.primary} />
                  <Text style={styles.statValue}>{student.total_points}</Text>
                  <Text style={styles.statText}>المجموع</Text>
                </View>
              </View>

              <View style={styles.studentFooter}>
                <View style={styles.lastAttendance}>
                  <Ionicons name="time" size={14} color={colors.gray600} />
                  <Text style={styles.lastAttendanceText}>
                    آخر حضور: {new Date(student.last_attendance).toLocaleDateString('ar-SA')}
                  </Text>
                </View>
                
                <View style={styles.studentActions}>
                  <TouchableOpacity 
                    style={styles.contactButton}
                    onPress={() => {
                      showMessage({
                        message: 'جاري الاتصال',
                        description: `رقم ولي الأمر: ${student.parent_phone}`,
                        type: 'info',
                      });
                    }}
                  >
                    <Ionicons name="call" size={16} color={colors.primary} />
                  </TouchableOpacity>
                  
                  <TouchableOpacity 
                    style={styles.messageButton}
                    onPress={() => {
                      showMessage({
                        message: 'رسالة لولي الأمر',
                        description: 'سيتم فتح شاشة إرسال الرسائل',
                        type: 'info',
                      });
                    }}
                  >
                    <Ionicons name="chatbubble" size={16} color={colors.secondary} />
                  </TouchableOpacity>
                  
                  <TouchableOpacity style={styles.detailsButton}>
                    <Ionicons name="chevron-forward" size={16} color={colors.gray600} />
                  </TouchableOpacity>
                </View>
              </View>
            </TouchableOpacity>
          ))
        ) : (
          <View style={styles.emptyState}>
            <Ionicons name="people-outline" size={80} color={colors.gray400} />
            <Text style={styles.emptyTitle}>
              {searchQuery ? 'لا توجد نتائج' : 'لا توجد بيانات'}
            </Text>
            <Text style={styles.emptyText}>
              {searchQuery 
                ? `لم يتم العثور على طلاب يحتوون على "${searchQuery}"`
                : 'لم يتم العثور على بيانات الطلاب'
              }
            </Text>
            {searchQuery && (
              <TouchableOpacity 
                style={styles.clearSearchButton}
                onPress={() => setSearchQuery('')}
              >
                <Text style={styles.clearSearchText}>مسح البحث</Text>
              </TouchableOpacity>
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
    paddingHorizontal: 20,
    paddingVertical: 16,
    backgroundColor: colors.primary,
  },
  headerTitle: {
    ...typography.h3,
    color: colors.white,
  },
  searchContainer: {
    paddingHorizontal: 20,
    paddingVertical: 16,
  },
  searchInputWrapper: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: colors.white,
    borderRadius: 12,
    paddingHorizontal: 16,
    elevation: 2,
    shadowColor: colors.black,
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.1,
    shadowRadius: 2,
  },
  searchInput: {
    flex: 1,
    height: 50,
    ...typography.body1,
    color: colors.textPrimary,
    marginLeft: 12,
  },
  statsContainer: {
    flexDirection: 'row',
    paddingHorizontal: 20,
    marginBottom: 16,
    gap: 12,
  },
  statCard: {
    flex: 1,
    backgroundColor: colors.white,
    padding: 12,
    borderRadius: 8,
    alignItems: 'center',
    elevation: 2,
    shadowColor: colors.black,
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.1,
    shadowRadius: 2,
  },
  statNumber: {
    ...typography.h4,
    color: colors.primary,
    fontWeight: 'bold',
  },
  statLabel: {
    ...typography.caption,
    color: colors.textSecondary,
    marginTop: 2,
  },
  scrollContent: {
    padding: 20,
    paddingBottom: 40,
  },
  studentCard: {
    backgroundColor: colors.white,
    borderRadius: 16,
    padding: 16,
    marginBottom: 16,
    elevation: 4,
    shadowColor: colors.black,
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 8,
  },
  studentHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
    marginBottom: 12,
  },
  studentBasicInfo: {
    flex: 1,
  },
  studentName: {
    ...typography.h4,
    color: colors.textPrimary,
    marginBottom: 2,
  },
  studentAge: {
    ...typography.body2,
    color: colors.textSecondary,
    marginBottom: 2,
  },
  studentCircle: {
    ...typography.body2,
    color: colors.primary,
  },
  performanceContainer: {
    alignItems: 'flex-end',
    gap: 6,
  },
  performanceBadge: {
    paddingHorizontal: 8,
    paddingVertical: 4,
    borderRadius: 12,
  },
  performanceText: {
    ...typography.caption,
    color: colors.white,
    fontWeight: '600',
  },
  attendanceBadge: {
    paddingHorizontal: 8,
    paddingVertical: 4,
    borderRadius: 12,
  },
  attendanceText: {
    ...typography.caption,
    color: colors.white,
    fontWeight: '600',
  },
  studentStats: {
    flexDirection: 'row',
    justifyContent: 'space-around',
    paddingVertical: 12,
    borderTopWidth: 1,
    borderTopColor: colors.borderLight,
    borderBottomWidth: 1,
    borderBottomColor: colors.borderLight,
  },
  statItem: {
    alignItems: 'center',
    gap: 4,
  },
  statValue: {
    ...typography.body1,
    color: colors.textPrimary,
    fontWeight: '600',
  },
  statText: {
    ...typography.caption,
    color: colors.textSecondary,
  },
  studentFooter: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginTop: 12,
  },
  lastAttendance: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 4,
    flex: 1,
  },
  lastAttendanceText: {
    ...typography.caption,
    color: colors.textSecondary,
  },
  studentActions: {
    flexDirection: 'row',
    gap: 8,
  },
  contactButton: {
    width: 32,
    height: 32,
    borderRadius: 16,
    backgroundColor: colors.gray100,
    justifyContent: 'center',
    alignItems: 'center',
  },
  messageButton: {
    width: 32,
    height: 32,
    borderRadius: 16,
    backgroundColor: colors.gray100,
    justifyContent: 'center',
    alignItems: 'center',
  },
  detailsButton: {
    width: 32,
    height: 32,
    borderRadius: 16,
    backgroundColor: colors.gray100,
    justifyContent: 'center',
    alignItems: 'center',
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
    marginBottom: 24,
  },
  clearSearchButton: {
    backgroundColor: colors.primary,
    paddingHorizontal: 24,
    paddingVertical: 12,
    borderRadius: 8,
  },
  clearSearchText: {
    ...typography.body2,
    color: colors.white,
    fontWeight: '600',
  },
});