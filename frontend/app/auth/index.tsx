import React from 'react';
import {
  View,
  Text,
  StyleSheet,
  TouchableOpacity,
  SafeAreaView,
  Image,
} from 'react-native';
import { useTranslation } from 'react-i18next';
import { useRouter } from 'expo-router';
import { useAuth } from '../../src/context/AuthContext';
import { colors } from '../../src/theme/colors';
import { typography } from '../../src/theme/fonts';
import { Ionicons } from '@expo/vector-icons';
import { LinearGradient } from 'expo-linear-gradient';

export default function AuthSelection() {
  const { t } = useTranslation();
  const router = useRouter();
  const { setUserType } = useAuth();

  const handleUserTypeSelection = (type: 'parent' | 'teacher') => {
    setUserType(type);
    router.push(`/auth/login?type=${type}`);
  };

  return (
    <SafeAreaView style={styles.container}>
      <LinearGradient
        colors={[colors.primary, colors.primaryLight]}
        style={styles.gradient}
      >
        {/* Header with Logo */}
        <View style={styles.header}>
          <View style={styles.logoContainer}>
            <Ionicons name="book" size={60} color={colors.white} />
          </View>
          <Text style={styles.title}>{t('welcome')}</Text>
          <Text style={styles.subtitle}>{t('chooseUserType')}</Text>
        </View>

        {/* User Type Selection Cards */}
        <View style={styles.cardContainer}>
          {/* Parent Card */}
          <TouchableOpacity
            style={styles.card}
            onPress={() => handleUserTypeSelection('parent')}
            activeOpacity={0.8}
          >
            <View style={styles.cardContent}>
              <View style={styles.iconContainer}>
                <Ionicons name="people" size={40} color={colors.primary} />
              </View>
              <Text style={styles.cardTitle}>{t('parent')}</Text>
              <Text style={styles.cardDescription}>
                متابعة تقدم الأبناء والحضور
              </Text>
              <View style={styles.cardArrow}>
                <Ionicons name="chevron-forward" size={24} color={colors.gray600} />
              </View>
            </View>
          </TouchableOpacity>

          {/* Teacher Card */}
          <TouchableOpacity
            style={styles.card}
            onPress={() => handleUserTypeSelection('teacher')}
            activeOpacity={0.8}
          >
            <View style={styles.cardContent}>
              <View style={styles.iconContainer}>
                <Ionicons name="school" size={40} color={colors.primary} />
              </View>
              <Text style={styles.cardTitle}>{t('teacher')}</Text>
              <Text style={styles.cardDescription}>
                إدارة الحلقات والجلسات
              </Text>
              <View style={styles.cardArrow}>
                <Ionicons name="chevron-forward" size={24} color={colors.gray600} />
              </View>
            </View>
          </TouchableOpacity>
        </View>

        {/* Footer */}
        <View style={styles.footer}>
          <Text style={styles.footerText}>
            جمعية تحفيظ القرآن الكريم
          </Text>
        </View>
      </LinearGradient>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
  },
  gradient: {
    flex: 1,
  },
  header: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    paddingHorizontal: 20,
  },
  logoContainer: {
    width: 120,
    height: 120,
    borderRadius: 60,
    backgroundColor: 'rgba(255, 255, 255, 0.2)',
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 20,
  },
  title: {
    ...typography.h2,
    color: colors.white,
    textAlign: 'center',
    marginBottom: 10,
  },
  subtitle: {
    ...typography.body1,
    color: colors.white,
    opacity: 0.9,
    textAlign: 'center',
  },
  cardContainer: {
    flex: 1,
    paddingHorizontal: 20,
    justifyContent: 'center',
    gap: 20,
  },
  card: {
    backgroundColor: colors.white,
    borderRadius: 16,
    padding: 20,
    elevation: 8,
    shadowColor: colors.black,
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.15,
    shadowRadius: 8,
  },
  cardContent: {
    alignItems: 'center',
    position: 'relative',
  },
  iconContainer: {
    width: 80,
    height: 80,
    borderRadius: 40,
    backgroundColor: colors.gray100,
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 16,
  },
  cardTitle: {
    ...typography.h3,
    color: colors.textPrimary,
    textAlign: 'center',
    marginBottom: 8,
  },
  cardDescription: {
    ...typography.body2,
    color: colors.textSecondary,
    textAlign: 'center',
  },
  cardArrow: {
    position: 'absolute',
    right: 0,
    top: 0,
  },
  footer: {
    padding: 20,
    alignItems: 'center',
  },
  footerText: {
    ...typography.body2,
    color: colors.white,
    opacity: 0.8,
  },
});