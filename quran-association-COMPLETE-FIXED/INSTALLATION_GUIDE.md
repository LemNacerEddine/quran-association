# 🚀 دليل تثبيت النسخة الكاملة - نظام إدارة جمعية تحفيظ القرآن

## 📋 **المتطلبات:**
- PHP 8.1 أو أحدث
- MySQL 8.0 أو أحدث
- Composer
- Node.js & NPM

## 🛠️ **خطوات التثبيت:**

### **1. إعداد المشروع:**
```bash
# فك ضغط المشروع
tar -xzf quran-association-COMPLETE-FIXED.tar.gz
cd quran-association-COMPLETE-FIXED

# تثبيت dependencies
composer install
npm install
```

### **2. إعداد قاعدة البيانات:**
```bash
# إنشاء قاعدة بيانات جديدة
mysql -u root -p
CREATE DATABASE quran_association CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;

# نسخ ملف البيئة
cp .env.example .env

# تحديث إعدادات قاعدة البيانات في .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=quran_association
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### **3. إعداد Laravel:**
```bash
# توليد مفتاح التطبيق
php artisan key:generate

# تشغيل migrations
php artisan migrate

# تشغيل seeders (اختياري)
php artisan db:seed
```

### **4. إعداد الأصول:**
```bash
# بناء الأصول
npm run build

# أو للتطوير
npm run dev
```

### **5. إعداد الصلاحيات:**
```bash
# إعداد صلاحيات المجلدات
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chown -R www-data:www-data storage
chown -R www-data:www-data bootstrap/cache
```

### **6. تشغيل الخادم:**
```bash
# للتطوير
php artisan serve

# للإنتاج - إعداد Apache/Nginx
```

## ✅ **التحقق من التثبيت:**

1. **افتح المتصفح:** `http://localhost:8000`
2. **سجل حساب جديد** أو استخدم البيانات الافتراضية
3. **اذهب إلى الجدولة:** `/schedules/create`
4. **تحقق من ظهور الإعدادات المتقدمة**

## 🎯 **الميزات المتاحة:**

### **✅ نموذج الجدولة المحسن:**
- الإعدادات المتقدمة تظهر بزر في الأعلى
- اختيار القاعة من 5 خيارات
- 3 مفاتيح تبديل للإعدادات
- التعبئة التلقائية عند اختيار الحلقة
- عرض معلومات الحلقة المفصلة

### **✅ التكرار الذكي:**
- شهري: ينشئ جلسات لشهر واحد
- أسبوعي: ينشئ جلسات للفترة كاملة

### **✅ فحص التعارضات:**
- فحص تعارض الوقت
- فحص تعارض القاعة
- عرض القاعات المتاحة/المحجوزة

## 🔧 **استكشاف الأخطاء:**

### **خطأ Migration:**
```bash
# إذا واجهت مشاكل في migrations
php artisan migrate:fresh --seed
```

### **خطأ الصلاحيات:**
```bash
# إعادة تعيين الصلاحيات
sudo chown -R $USER:www-data storage
sudo chown -R $USER:www-data bootstrap/cache
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### **خطأ الكاش:**
```bash
# مسح جميع أنواع الكاش
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

## 📞 **الدعم:**
إذا واجهت أي مشاكل، تحقق من:
- `storage/logs/laravel.log` للأخطاء
- إعدادات قاعدة البيانات في `.env`
- صلاحيات المجلدات

## 🎉 **تم الانتهاء!**
النظام جاهز للاستخدام مع جميع الميزات المحسنة!

