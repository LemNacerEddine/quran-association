import React, { createContext, useContext, useState, useEffect } from 'react';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { authService } from '../services/api';

export type UserType = 'parent' | 'teacher' | null;

export interface User {
  id: number;
  name: string;
  phone: string;
  type: UserType;
  email?: string;
}

interface AuthContextType {
  user: User | null;
  userType: UserType;
  isLoading: boolean;
  login: (phone: string, password: string, type: UserType) => Promise<boolean>;
  logout: () => Promise<void>;
  setUserType: (type: UserType) => void;
}

const AuthContext = createContext<AuthContextType | undefined>(undefined);

export function useAuth() {
  const context = useContext(AuthContext);
  if (!context) {
    throw new Error('useAuth must be used within an AuthProvider');
  }
  return context;
}

interface AuthProviderProps {
  children: React.ReactNode;
}

export default function AuthProvider({ children }: AuthProviderProps) {
  const [user, setUser] = useState<User | null>(null);
  const [userType, setUserTypeState] = useState<UserType>(null);
  const [isLoading, setIsLoading] = useState(true);

  useEffect(() => {
    checkAuthState();
  }, []);

  const checkAuthState = async () => {
    try {
      const [token, userData, savedUserType] = await Promise.all([
        AsyncStorage.getItem('auth_token'),
        AsyncStorage.getItem('user_data'),
        AsyncStorage.getItem('user_type'),
      ]);

      if (token && userData && savedUserType) {
        setUser(JSON.parse(userData));
        setUserTypeState(savedUserType as UserType);
      }
    } catch (error) {
      console.error('Error checking auth state:', error);
    } finally {
      setIsLoading(false);
    }
  };

  const login = async (phone: string, password: string, type: UserType): Promise<boolean> => {
    try {
      setIsLoading(true);
      const response = await authService.login(phone, password, type);
      
      if (response.success && response.data) {
        const { token, user: userData } = response.data;
        
        await AsyncStorage.multiSet([
          ['auth_token', token],
          ['user_data', JSON.stringify(userData)],
          ['user_type', type],
        ]);

        setUser(userData);
        setUserTypeState(type);
        return true;
      }
      return false;
    } catch (error) {
      console.error('Login error:', error);
      return false;
    } finally {
      setIsLoading(false);
    }
  };

  const logout = async () => {
    try {
      setIsLoading(true);
      await AsyncStorage.multiRemove(['auth_token', 'user_data', 'user_type']);
      setUser(null);
      setUserTypeState(null);
    } catch (error) {
      console.error('Logout error:', error);
    } finally {
      setIsLoading(false);
    }
  };

  const setUserType = (type: UserType) => {
    setUserTypeState(type);
  };

  const value: AuthContextType = {
    user,
    userType,
    isLoading,
    login,
    logout,
    setUserType,
  };

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
}