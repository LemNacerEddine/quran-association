import React from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
} from 'react-native';
import { useTranslation } from 'react-i18next';
import { SafeAreaView } from 'react-native-safe-area-context';
import { Ionicons } from '@expo/vector-icons';
import { colors } from '../../src/theme/colors';
import { typography } from '../../src/theme/fonts';

export default function ReportsScreen() {
  const { t } = useTranslation();

  const reportTypes = [
    {
      id: 'attendance',
      title: 'تقرير الحضور',
      description: 'تقرير شامل عن حضور الأبناء',
      icon: 'checkmark-circle',
      color: colors.success,
    },
    {
      id: 'memorization',
      title: 'تقرير الحفظ',
      description: 'تقرير تقدم الحفظ والمراجعة',
      icon: 'book',
      color: colors.primary,
    },
    {
      id: 'points',
      title: 'تقرير النقاط',
      description: 'تقرير النقاط المكتسبة والتقييمات',
      icon: 'star',
      color: colors.gold,
    },
    {
      id: 'monthly',
      title: 'التقرير الشهري',
      description: 'ملخص شامل للأداء الشهري',
      icon: 'calendar',
      color: colors.info,
    },
  ];

  return (
    <SafeAreaView style={styles.container}>
      <ScrollView contentContainerStyle={styles.scrollContent}>
        <View style={styles.header}>
          <Text style={styles.headerTitle}>التقارير</Text>
          <Text style={styles.headerSubtitle}>
            تقارير شاملة عن تقدم الأبناء
          </Text>
        </View>

        <View style={styles.reportsContainer}>
          {reportTypes.map((report) => (
            <TouchableOpacity
              key={report.id}
              style={styles.reportCard}
              activeOpacity={0.7}
            >
              <View style={styles.reportHeader}>
                <View 
                  style={[
                    styles.reportIcon,
                    { backgroundColor: `${report.color}20` }
                  ]}
                >
                  <Ionicons 
                    name={report.icon as any} 
                    size={24} 
                    color={report.color} 
                  />
                </View>
                <View style={styles.reportInfo}>
                  <Text style={styles.reportTitle}>{report.title}</Text>
                  <Text style={styles.reportDescription}>
                    {report.description}
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

        {/* Quick Stats */}
        <View style={styles.quickStatsContainer}>
          <Text style={styles.sectionTitle}>إحصائيات سريعة</Text>
          
          <View style={styles.statsGrid}>
            <View style={styles.quickStatCard}>
              <Text style={styles.quickStatNumber}>85%</Text>
              <Text style={styles.quickStatLabel}>معدل الحضور العام</Text>
            </View>
            <View style={styles.quickStatCard}>
              <Text style={styles.quickStatNumber}>12</Text>
              <Text style={styles.quickStatLabel}>الجلسات هذا الشهر</Text>
            </View>
            <View style={styles.quickStatCard}>
              <Text style={styles.quickStatNumber}>240</Text>
              <Text style={styles.quickStatLabel}>إجمالي النقاط</Text>
            </View>
            <View style={styles.quickStatCard}>
              <Text style={styles.quickStatNumber}>3</Text>
              <Text style={styles.quickStatLabel}>أجزاء محفوظة</Text>
            </View>
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
  reportsContainer: {
    marginBottom: 32,
  },
  reportCard: {
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
  reportHeader: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  reportIcon: {
    width: 50,
    height: 50,
    borderRadius: 25,
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 16,
  },
  reportInfo: {
    flex: 1,
  },
  reportTitle: {
    ...typography.body1,
    color: colors.textPrimary,
    fontWeight: '600',
    marginBottom: 4,
  },
  reportDescription: {
    ...typography.body2,
    color: colors.textSecondary,
  },
  quickStatsContainer: {
    marginTop: 20,
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
  quickStatCard: {
    backgroundColor: colors.white,
    borderRadius: 12,
    padding: 16,
    alignItems: 'center',
    flex: 1,
    minWidth: '45%',
    elevation: 2,
    shadowColor: colors.black,
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.1,
    shadowRadius: 2,
  },
  quickStatNumber: {
    ...typography.h3,
    color: colors.primary,
    fontWeight: 'bold',
    marginBottom: 4,
  },
  quickStatLabel: {
    ...typography.caption,
    color: colors.textSecondary,
    textAlign: 'center',
  },
});