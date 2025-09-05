import { initializeApp, getApps } from 'firebase/app';
import { getMessaging, isSupported } from 'firebase/messaging';

// Firebase configuration from google-services.json
const firebaseConfig = {
  apiKey: "AIzaSyB1k-u22aVVwwoInBoPix4M1T9fmFwDzOk",
  authDomain: "quran-association.firebaseapp.com",
  projectId: "quran-association",
  storageBucket: "quran-association.firebasestorage.app",
  messagingSenderId: "631091388007",
  appId: "1:631091388007:android:f2bb6438bd160229247ec2"
};

// Initialize Firebase
let app;
if (getApps().length === 0) {
  app = initializeApp(firebaseConfig);
} else {
  app = getApps()[0];
}

// Initialize Firebase Cloud Messaging
let messaging: any = null;

// Check if messaging is supported (for web)
const initializeMessaging = async () => {
  try {
    const supported = await isSupported();
    if (supported) {
      messaging = getMessaging(app);
    }
  } catch (error) {
    console.log('Messaging not supported:', error);
  }
};

initializeMessaging();

export { app, messaging };
export default app;