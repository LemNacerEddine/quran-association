import i18n from 'i18next';
import { initReactI18next } from 'react-i18next';
import * as Localization from 'expo-localization';
import ar from './locales/ar.json';

const resources = {
  ar: {
    translation: ar,
  },
};

i18n
  .use(initReactI18next)
  .init({
    compatibilityJSON: 'v3',
    resources,
    lng: 'ar', // Always use Arabic
    fallbackLng: 'ar',
    debug: false,
    interpolation: {
      escapeValue: false,
    },
  });

export default i18n;