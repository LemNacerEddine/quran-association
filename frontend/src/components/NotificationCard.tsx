import React from 'react';
import {
  View,
  Text,
  StyleSheet,
  TouchableOpacity,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { colors } from '../theme/colors';
import { typography } from '../theme/fonts';

interface NotificationCardProps {
  notification: {
    id: string;
    title: string;
    body: string;
    type: 'attendance' | 'message' | 'session' | 'general';
    timestamp: string;
    isRead: boolean;
    data?: any;
  };
  onPress?: () => void;
  onMarkAsRead?: () => void;
}

export default function NotificationCard({ 
  notification, 
  onPress, 
  onMarkAsRead 
}: NotificationCardProps) {
  const getNotificationIcon = (type: string) => {
    switch (type) {
      case 'attendance':
        return { name: 'checkmark-circle', color: colors.success };
      case 'message':
        return { name: 'chatbubble', color: colors.info };
      case 'session':
        return { name: 'calendar', color: colors.warning };
      case 'general':
        return { name: 'notifications', color: colors.secondary };
      default:
        return { name: 'information-circle', color: colors.gray500 };
    }
  };

  const formatTimestamp = (timestamp: string) => {
    const date = new Date(timestamp);
    const now = new Date();
    const diff = now.getTime() - date.getTime();
    const minutes = Math.floor(diff / (1000 * 60));
    const hours = Math.floor(diff / (1000 * 60 * 60));
    const days = Math.floor(diff / (1000 * 60 * 60 * 24));

    if (minutes < 1) return 'الآن';
    if (minutes < 60) return `منذ ${minutes} دقيقة`;
    if (hours < 24) return `منذ ${hours} ساعة`;
    if (days < 7) return `منذ ${days} يوم`;
    
    return date.toLocaleDateString('ar-SA');
  };

  const iconInfo = getNotificationIcon(notification.type);

  return (
    <TouchableOpacity
      style={[
        styles.container,
        !notification.isRead && styles.unreadContainer
      ]}
      onPress={onPress}
      activeOpacity={0.7}
    >
      <View style={styles.content}>
        <View 
          style={[
            styles.iconContainer,
            { backgroundColor: `${iconInfo.color}20` }
          ]}
        >
          <Ionicons 
            name={iconInfo.name as any} 
            size={24} 
            color={iconInfo.color} 
          />
        </View>

        <View style={styles.textContainer}>
          <Text style={[
            styles.title,
            !notification.isRead && styles.unreadTitle
          ]}>
            {notification.title}
          </Text>
          <Text style={styles.body} numberOfLines={2}>
            {notification.body}
          </Text>
          <Text style={styles.timestamp}>
            {formatTimestamp(notification.timestamp)}
          </Text>
        </View>

        <View style={styles.actions}>
          {!notification.isRead && (
            <View style={styles.unreadDot} />
          )}
          
          {onMarkAsRead && !notification.isRead && (
            <TouchableOpacity
              style={styles.markReadButton}
              onPress={onMarkAsRead}
            >
              <Ionicons name="checkmark" size={16} color={colors.primary} />
            </TouchableOpacity>
          )}
        </View>
      </View>
    </TouchableOpacity>
  );
}

const styles = StyleSheet.create({
  container: {
    backgroundColor: colors.white,
    borderRadius: 12,
    marginBottom: 12,
    elevation: 2,
    shadowColor: colors.black,
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.1,
    shadowRadius: 2,
  },
  unreadContainer: {
    borderLeftWidth: 4,
    borderLeftColor: colors.primary,
  },
  content: {
    flexDirection: 'row',
    padding: 16,
    alignItems: 'flex-start',
  },
  iconContainer: {
    width: 50,
    height: 50,
    borderRadius: 25,
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 16,
  },
  textContainer: {
    flex: 1,
  },
  title: {
    ...typography.body1,
    color: colors.textPrimary,
    marginBottom: 4,
  },
  unreadTitle: {
    fontWeight: '600',
  },
  body: {
    ...typography.body2,
    color: colors.textSecondary,
    marginBottom: 8,
    lineHeight: 20,
  },
  timestamp: {
    ...typography.caption,
    color: colors.textSecondary,
  },
  actions: {
    alignItems: 'center',
    gap: 8,
  },
  unreadDot: {
    width: 8,
    height: 8,
    borderRadius: 4,
    backgroundColor: colors.primary,
  },
  markReadButton: {
    width: 24,
    height: 24,
    borderRadius: 12,
    backgroundColor: colors.gray100,
    justifyContent: 'center',
    alignItems: 'center',
  },
});