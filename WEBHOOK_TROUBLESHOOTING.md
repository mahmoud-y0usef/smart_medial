# 🔧 حل مشكلة عدم رد البوت على WhatsApp

## ❌ المشكلة:
البوت مش بيرد لما تبعتله رسالة على WhatsApp

---

## ✅ الحل خطوة بخطوة:

### المشكلة الأساسية:
**Meta محتاج يعرف فين يبعت الرسائل (Webhook URL)**

---

## 🚀 الحل السريع (بـ ngrok):

### الخطوة 1: حمّل ngrok

**Windows:**
```
https://ngrok.com/download
```

سجل حساب مجاني وحمّل البرنامج

---

### الخطوة 2: شغّل Laravel

```bash
php artisan serve
```

أو إذا عندك Laravel Valet:
```bash
# موقعك شغال على smart_medical.test
```

---

### الخطوة 3: شغّل ngrok

**افتح terminal جديد واكتب:**

```bash
ngrok http 8000
```

**أو إذا عندك Valet:**
```bash
ngrok http smart_medical.test:80
```

---

### الخطوة 4: خد الـ URL

ngrok هيديك حاجة زي كده:

```
Forwarding  https://abc123def456.ngrok.io -> http://localhost:8000
```

**انسخ:** `https://abc123def456.ngrok.io`

---

### الخطوة 5: Setup في Meta

**1. افتح:**
```
https://developers.facebook.com/apps
```

**2. اختار تطبيقك**

**3. من القائمة الجانبية:**
- WhatsApp > Configuration

**4. في قسم "Webhook":**

**Callback URL:**
```
https://abc123def456.ngrok.io/api/webhooks/whatsapp
```
*(غيّر بالـ URL بتاعك)*

**Verify Token:**
```
smart_medical_verify_2026_secure_eHTu8GkFHljri4qEYFObLCv
```
*(نفس اللي في `.env` بتاعك)*

**5. اضغط "Verify and Save"**

✅ لو ظهرت ✓ يبقى تمام!

**6. Webhook Fields:**
- ✅ اختار **messages**
- ✅ اختار **message_status**

**7. اضغط "Subscribe"**

---

## 🧪 اختبار الـ Webhook:

### من Terminal:

```bash
php artisan whatsapp:test-webhook
```

ده هيختبر الـ webhook محلياً

---

### إضافة رقمك للاختبار:

**1. من Meta Developers:**
- WhatsApp > Getting Started

**2. في قسم "To":**
- اضغط **"Add phone number"**

**3. أدخل رقمك:**
```
+201234567890
```

**4. هيوصلك كود على WhatsApp**

**5. أدخل الكود** ✅

---

## 📱 جرب البوت:

**الآن بعت رسالة:**
```
مرحباً
```

**البوت المفروض يرد:**
```
أهلاً بك في الشفاء الذكي! 👋
```

---

## 🔍 استكشاف الأخطاء:

### 1️⃣ الـ Webhook مش بيتسجل؟

**تحقق من:**

```bash
# شوف الـ logs
tail -f storage/logs/laravel.log
```

**لو مفيش حاجة:**
- ngrok شغال؟
- الـ URL صحيح في Meta؟

---

### 2️⃣ Webhook بيشتغل بس البوت مش بيرد؟

**تحقق من:**

```bash
# اختبر الـ AI
php artisan gemini:test "مرحباً"
```

**لو مش شغال:**
- تأكد من `GOOGLE_AI_API_KEY` في `.env`

---

### 3️⃣ البوت بيرد بس الرسالة مش بتوصل؟

**تحقق من:**

```bash
php artisan tinker
```

```php
$whatsapp = app(\App\Services\WhatsApp\MetaWhatsAppService::class);
$whatsapp->sendMessage('201234567890', 'تجربة');
```

**لو طلع error:**
- تأكد من `META_WHATSAPP_TOKEN`
- تأكد من `META_PHONE_ID`

---

## 💡 نصائح:

### ngrok بينقطع؟

**مجاني بس LIMITED:**
- بيشتغل 2 ساعة
- URL بيتغير كل مرة

**الحل:**
1. **اشترك في ngrok Pro** ($8/شهر)
2. أو **استخدم serveo** (مجاني):
   ```bash
   ssh -R 80:localhost:8000 serveo.net
   ```

---

### للإنتاج (Production):

**محتاج:**
1. **سيرفر حقيقي** (Shared Hosting, VPS, Cloud)
2. **Domain** (مثلاً: smart-medical.com)
3. **SSL Certificate** (HTTPS)

**الـ Webhook URL يبقى:**
```
https://smart-medical.com/api/webhooks/whatsapp
```

---

## 📊 الخلاصة:

### ✅ عشان البوت يرد:

1. **ngrok يكون شغال** ✓
2. **Webhook متعمل setup في Meta** ✓
3. **رقمك مضاف في Test numbers** ✓
4. **الـ Token صحيح** ✓
5. **الـ AI شغال** ✓

---

## 🎯 الخطوات السريعة:

```bash
# 1. شغّل Laravel
php artisan serve

# 2. terminal تاني - شغّل ngrok
ngrok http 8000

# 3. خد الـ URL وحطه في Meta
# مثال: https://abc123.ngrok.io/api/webhooks/whatsapp

# 4. اختبر
php artisan whatsapp:test-webhook

# 5. بعت رسالة على WhatsApp
# "مرحباً"
```

---

**✅ دلوقتي البوت المفروض يرد!** 🎉

---

## 🆘 لو لسه مش شغال:

**ابعتلي:**
1. الـ logs من: `storage/logs/laravel.log`
2. screenshot من Meta Webhook settings
3. الـ URL بتاع ngrok

**وأنا أساعدك!** 💪
