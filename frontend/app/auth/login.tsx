import React, { useState } from 'react';
import {
  View,
  Text,
  TextInput,
  TouchableOpacity,
  StyleSheet,
  SafeAreaView,
  Alert,
  KeyboardAvoidingView,
  Platform,
  ScrollView,
} from 'react-native';
import { useTranslation } from 'react-i18next';
import { useRouter, useLocalSearchParams } from 'expo-router';
import { useAuth } from '../../src/context/AuthContext';
import { colors } from '../../src/theme/colors';
import { typography } from '../../src/theme/fonts';
import { Ionicons } from '@expo/vector-icons';
import { LinearGradient } from 'expo-linear-gradient';
import { showMessage } from 'react-native-flash-message';
import LoadingScreen from '../../src/components/LoadingScreen';

export default function LoginScreen() {
  const { t } = useTranslation();
  const router = useRouter();
  const { type } = useLocalSearchParams<{ type: 'parent' | 'teacher' }>();
  const { login } = useAuth();

  const [phone, setPhone] = useState('');
  const [password, setPassword] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [showPassword, setShowPassword] = useState(false);

  const isParent = type === 'parent';

  const handleLogin = async () => {
    if (!phone.trim()) {
      showMessage({
        message: t('requiredField'),
        description: t('phoneNumber'),
        type: 'warning',
      });
      return;
    }

    if (!password.trim()) {
      showMessage({
        message: t('requiredField'),
        description: isParent ? t('accessCode') : t('password'),
        type: 'warning',
      });
      return;
    }

    setIsLoading(true);
    try {
      const success = await login(phone, password, type!);
      
      if (success) {
        showMessage({
          message: t('success'),
          description: 'تم تسجيل الدخول بنجاح',
          type: 'success',
        });
        
        // Navigate to appropriate dashboard
        if (type === 'parent') {
          router.replace('/parent');
        } else {
          router.replace('/teacher');
        }
      } else {
        showMessage({
          message: t('loginError'),
          description: t('invalidCredentials'),
          type: 'danger',
        });
      }
    } catch (error) {
      console.error('Login error:', error);
      showMessage({
        message: t('loginError'),
        description: t('networkError'),
        type: 'danger',
      });
    } finally {
      setIsLoading(false);
    }
  };

  if (isLoading) {
    return <LoadingScreen />;
  }

  return (
    <SafeAreaView style={styles.container}>
      <LinearGradient
        colors={[colors.primary, colors.primaryLight]}
        style={styles.gradient}
      >
        <KeyboardAvoidingView
          behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
          style={styles.keyboardView}
        >
          <ScrollView
            contentContainerStyle={styles.scrollContent}
            showsVerticalScrollIndicator={false}
          >
            {/* Header */}
            <View style={styles.header}>
              <TouchableOpacity
                style={styles.backButton}
                onPress={() => router.back()}
              >
                <Ionicons name="arrow-back" size={24} color={colors.white} />
              </TouchableOpacity>
              
              <View style={styles.logoContainer}>
                <Ionicons 
                  name={isParent ? "people" : "school"} 
                  size={50} 
                  color={colors.white} 
                />
              </View>
              
              <Text style={styles.title}>
                {t('login')} - {isParent ? t('parent') : t('teacher')}
              </Text>
            </View>

            {/* Login Form */}
            <View style={styles.formContainer}>
              <View style={styles.form}>
                {/* Phone Input */}
                <View style={styles.inputContainer}>
                  <Text style={styles.inputLabel}>{t('phoneNumber')}</Text>
                  <View style={styles.inputWrapper}>
                    <Ionicons name="call" size={20} color={colors.gray600} />
                    <TextInput
                      style={styles.input}
                      value={phone}
                      onChangeText={setPhone}
                      placeholder="05xxxxxxxx"
                      placeholderTextColor={colors.gray500}
                      keyboardType="phone-pad"
                      autoComplete="tel"
                      textAlign="right"
                    />
                  </View>
                </View>

                {/* Password/Access Code Input */}
                <View style={styles.inputContainer}>
                  <Text style={styles.inputLabel}>
                    {isParent ? t('accessCode') : t('password')}
                  </Text>
                  <View style={styles.inputWrapper}>
                    <TouchableOpacity
                      onPress={() => setShowPassword(!showPassword)}
                      style={styles.eyeButton}
                    >
                      <Ionicons 
                        name={showPassword ? "eye-off" : "eye"} 
                        size={20} 
                        color={colors.gray600} 
                      />
                    </TouchableOpacity>
                    <TextInput
                      style={styles.input}
                      value={password}
                      onChangeText={setPassword}
                      placeholder={isParent ? "كود الوصول" : "كلمة المرور"}
                      placeholderTextColor={colors.gray500}
                      secureTextEntry={!showPassword}
                      textAlign="right"
                    />
                    <Ionicons name="lock-closed" size={20} color={colors.gray600} />
                  </View>
                </View>

                {/* Login Button */}
                <TouchableOpacity
                  style={styles.loginButton}
                  onPress={handleLogin}
                  disabled={isLoading}
                >
                  <Text style={styles.loginButtonText}>{t('loginButton')}</Text>
                </TouchableOpacity>

                {/* Help Text */}
                <Text style={styles.helpText}>
                  {isParent 
                    ? "يمكنك الحصول على كود الوصول من إدارة الجمعية"
                    : "يمكنك الحصول على كلمة المرور من إدارة الجمعية"
                  }
                </Text>
              </View>
            </View>
          </ScrollView>
        </KeyboardAvoidingView>
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
  keyboardView: {
    flex: 1,
  },
  scrollContent: {
    flexGrow: 1,
  },
  header: {
    paddingTop: 20,
    paddingHorizontal: 20,
    alignItems: 'center',
    position: 'relative',
  },
  backButton: {
    position: 'absolute',
    right: 20,
    top: 20,
    zIndex: 1,
  },
  logoContainer: {
    width: 100,
    height: 100,
    borderRadius: 50,
    backgroundColor: 'rgba(255, 255, 255, 0.2)',
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 20,
  },
  title: {
    ...typography.h3,
    color: colors.white,
    textAlign: 'center',
  },
  formContainer: {
    flex: 1,
    paddingTop: 40,
    paddingHorizontal: 20,
  },
  form: {
    backgroundColor: colors.white,
    borderRadius: 20,
    padding: 24,
    elevation: 10,
    shadowColor: colors.black,
    shadowOffset: { width: 0, height: 5 },
    shadowOpacity: 0.15,
    shadowRadius: 10,
  },
  inputContainer: {
    marginBottom: 20,
  },
  inputLabel: {
    ...typography.body2,
    color: colors.textPrimary,
    marginBottom: 8,
    fontWeight: '600',
  },
  inputWrapper: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: colors.gray100,
    borderRadius: 12,
    paddingHorizontal: 16,
    borderWidth: 1,
    borderColor: colors.border,
  },
  input: {
    flex: 1,
    height: 50,
    ...typography.body1,
    color: colors.textPrimary,
    marginHorizontal: 12,
  },
  eyeButton: {
    padding: 4,
  },
  loginButton: {
    backgroundColor: colors.primary,
    borderRadius: 12,
    height: 50,
    justifyContent: 'center',
    alignItems: 'center',
    marginTop: 10,
    elevation: 2,
    shadowColor: colors.primary,
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.2,
    shadowRadius: 4,
  },
  loginButtonText: {
    ...typography.body1,
    color: colors.white,
    fontWeight: '600',
  },
  helpText: {
    ...typography.caption,
    color: colors.textSecondary,
    textAlign: 'center',
    marginTop: 16,
    lineHeight: 18,
  },
});