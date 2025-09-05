import React from 'react';
import { View, StyleSheet } from 'react-native';
import { Tabs } from 'expo-router';
import { useTranslation } from 'react-i18next';
import { createMaterialTopTabNavigator } from '@react-navigation/material-top-tabs';
import NotificationsList from '../../src/components/NotificationsList';
import NotificationManager from '../../src/components/NotificationManager';
import { colors } from '../../src/theme/colors';
import { typography } from '../../src/theme/fonts';

const Tab = createMaterialTopTabNavigator();

export default function ParentNotifications() {
  const { t } = useTranslation();

  return (
    <View style={styles.container}>
      <Tab.Navigator
        screenOptions={{
          tabBarActiveTintColor: colors.primary,
          tabBarInactiveTintColor: colors.textSecondary,
          tabBarStyle: {
            backgroundColor: colors.white,
            elevation: 2,
          },
          tabBarLabelStyle: {
            ...typography.body2,
            fontWeight: '600',
          },
          tabBarIndicatorStyle: {
            backgroundColor: colors.primary,
            height: 3,
          },
        }}
      >
        <Tab.Screen
          name="NotificationsList"
          component={NotificationsList}
          options={{
            title: 'الإشعارات',
          }}
        />
        <Tab.Screen
          name="NotificationSettings"
          component={NotificationManager}
          options={{
            title: 'الإعدادات',
          }}
        />
      </Tab.Navigator>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: colors.background,
  },
});