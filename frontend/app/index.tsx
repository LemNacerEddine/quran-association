import React from 'react';
import { View, StyleSheet, I18nManager } from 'react-native';
import { useAuth } from '../src/context/AuthContext';
import { Redirect } from 'expo-router';
import LoadingScreen from '../src/components/LoadingScreen';

// Enable RTL for Arabic
I18nManager.allowRTL(true);
I18nManager.forceRTL(true);

export default function Index() {
  const { user, userType, isLoading } = useAuth();

  if (isLoading) {
    return <LoadingScreen />;
  }

  // If user is authenticated, redirect to appropriate dashboard
  if (user && userType) {
    if (userType === 'parent') {
      return <Redirect href="/parent" />;
    } else if (userType === 'teacher') {
      return <Redirect href="/teacher" />;
    }
  }

  // If not authenticated, show auth flow
  return <Redirect href="/auth" />;
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
  },
});