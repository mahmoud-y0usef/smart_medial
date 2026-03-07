# Setup Quick Reference - الشفاء الذكي

## 🚀 البدء السريع (5 دقائق)

### 1. WhatsApp Test Mode (للتجربة الفورية)

```bash
# 1. افتح: https://developers.facebook.com/apps
# 2. Create App > Business
# 3. Add Product > WhatsApp
# 4. في Getting Started:
```

**انسخ هذه القيم:**
```env
META_PHONE_ID=123456789012345          # من WhatsApp > Getting Started
META_WHATSAPP_TOKEN=EAAxxxxx...         # Temporary token (انسخه)
META_VERIFY_TOKEN=test_verify_123       # اختره بنفسك (أي نص)
META_BUSINESS_ACCOUNT_ID=987654321      # من Business Settings
```

**أضف رقمك للاختبار:**
```
Getting Started > To > Add phone number
أدخل رقمك: +201234567890
أدخل الكود الذي وصلك في WhatsApp
```

---

### 2. OpenAI (للـ AI)

⚠️ **مدفوع - بدائل مجانية متاحة! راجع [FREE_AI_SOLUTIONS.md](./FREE_AI_SOLUTIONS.md)**

#### البديل المجاني (الموصى به):

```bash
# Google Gemini - مجاني تماماً!
# 1. افتح: https://aistudio.google.com/
# 2. Get API key
# 3. انسخ الـ key
```

**أضف في .env:**
```env
GOOGLE_AI_API_KEY=AIzaSy...
AI_PRIMARY_PROVIDER=gemini
```

**أو شغل script التثبيت:**
```bash
# Windows:
.\install-gemini.bat

# Linux/Mac:
./install-gemini.sh
```

#### OpenAI (إذا كنت تريد الدفع):

```bash
# 1. افتح: https://platform.openai.com/api-keys
# 2. Create new secret key
# 3. انسخ الـ key فوراً
```

**أضف في .env:**
```env
OPENAI_API_KEY=sk-proj-abc123...
AI_PRIMARY_PROVIDER=openai
```

**⚠️ لازم تضيف بطاقة ائتمان:**
```
Settings > Billing > Add payment method
حط ميزانية: $20/شهر (كافية للبداية)
```

---

### 3. Anthropic - اختياري (Backup AI)

⚠️ **مدفوع - بدائل مجانية أفضل! راجع [FREE_AI_SOLUTIONS.md](./FREE_AI_SOLUTIONS.md)**

#### البديل المجاني:

```bash
# Groq - مجاني وأسرع من Claude!
# 1. افتح: https://console.groq.com/
# 2. API Keys > Create Key
```

**أضف في .env:**
```env
GROQ_API_KEY=gsk_xxx...
AI_FALLBACK_PROVIDER=groq
```

#### Anthropic (إذا كنت تريد الدفع):

```bash
# 1. افتح: https://console.anthropic.com/settings/keys
# 2. Create Key
```

**أضف في .env:**
```env
ANTHROPIC_API_KEY=sk-ant-api03-xxx...
```

---

## ⚡ Testing Commands

### Test WhatsApp:
```bash
php artisan tinker
```
### Test Gemini (المجاني):
```bash
php artisan tinker
```
```php
$client = \Gemini::client(config('services.google_ai.api_key'));
$response = $client->geminiFlash()->generateContent('مرحباً');
echo $response->text();
```

```php
$whatsapp = app(\App\Services\WhatsApp\MetaWhatsAppService::class);
$whatsapp->sendMessage('+201234567890', 'تست 🚀');
```

### Test OpenAI:
```bash
php artisan tinker
```
```php
$client = \OpenAI::client(config('services.openai.api_key'));
$response = $client->chat()->create([
    'model' => 'gpt-3.5-turbo',
    'messages' => [['role' => 'user', 'content' => 'مرحباً']]
]);
echo $response->choices[0]->message->content;
```

---

## 🔧 Production Setup

### WhatsApp Permanent Token:

```bash
# في Meta Business Manager:
1. Business Settings > Users > System Users
2. Add > "WhatsApp Bot System User" > Admin
3. Add Assets > Apps > تطبيقك > Full Control
4. Generate Token > 
   ☑ whatsapp_business_messaging
   ☑ whatsapp_business_management
5. انسخ الـ token وخزنه بأمان
```

### Webhook Setup:

```env
Callback URL: https://yourdomain.com/api/whatsapp
Verify Token: نفس META_VERIFY_TOKEN في .env
```

**Subscribe to:**
- ☑ messages
- ☑ message_status

---

## 📋 .env Template

```env
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=smart_medical
DB_USERNAME=root
DB_PASSWORD=

# Queue (مهم للإشعارات!)
QUEUE_CONNECTION=redis  # أو database

# Broadcasting (للـ real-time updates)
BROADCAST_DRIVER=reverb  # أو pusher

# WhatsApp
META_WHATSAPP_TOKEN=EAAxxxxxxxxxxxxxxxx
META_VERIFY_TOKEN=your_secure_token_here
META_PHONE_ID=123456789012345
META_BUSINESS_ACCOUNT_ID=987654321

# AI - الخيار المجاني (الموصى به)
GOOGLE_AI_API_KEY=AIzaSyxxxxxxxx
AI_PRIMARY_PROVIDER=gemini
AI_FALLBACK_PROVIDER=groq
GROQ_API_KEY=gsk_xxxxxxxx  # backup مجاني

# أو الخيار المدفوع
# OPENAI_API_KEY=sk-proj-xxxxxxxx
# AI_PRIMARY_PROVIDER=openai
```

💡 **نصيحة:** استخدم Gemini المجاني بدلاً من OpenAI ووفر $40+/شهر!
📚 **دليل كامل:** [FREE_AI_SOLUTIONS.md](./FREE_AI_SOLUTIONS.md)

---

## 🎯 Run Commands

```bash
# تشغيل المشروع
php artisan serve

# تشغيل Queue (window جديد)
php artisan queue:work --tries=3

# تشغيل Schedule (window جديد)
php artisan schedule:work

# Google Gemini: **FREE** ✅ (1500 req/day)
- Total: **$0/month** 🎉

### Production (1000 patients) - خيار مجاني:
- WhatsApp: **$5-20/month** (بعد أول 1000)
- Google Gemini: **FREE** ✅
- Groq (backup): **FREE** ✅
- Total: **$5-20/month**

### Production (1000 patients) - خيار مدفوع:
- WhatsApp: **$5-20/month**
- OpenAI: **$25-40/month**
- Anthropic: **$2-5/month** (backup)
- Total: **$30-65/month**

💡 **التوصية: استخدم Gemini المجاني ووفر $40+/شهر!**
📚 **راجع:** [FREE_AI_SOLUTIONS.md](./FREE_AI_SOLUTIONS.md)
### Development (Test Mode):
- WhatsApp: **FREE** ✅
- OpenAI: **~$2-5/month** (testing)
- Total: **$5/month**

### Production (1000 patients):
- WhatsApp: **$5-20/month**
- OpenAI: **$25-40/month**
- Anthropic: **$2-5/month** (backup)
- Total: **$30-65/month**

---

## 🆘 Quick Fixes

### "Invalid access token"
```bash
# Token انتهى، احصل على Permanent token
# راجع: "WhatsApp Permanent Token" في الأعلى
```

### "Insufficient quota"
```bash
# OpenAI: أضف رصيد في Billing
# https://platform.openai.com/settings/organization/billing
```

### Messages لا تصل
```bash
# 1. تأكد من Queue Worker شغال
php artisan queue:work

# 2. تأكد من الرقم مسجل في Test Numbers
# WhatsApp > Getting Started > To > Add phone number
```

### Webhook لا يعمل
```bash
# 1. تأكد من HTTPS (مش HTTP)
# 2.🆓 بدائل AI المجانية:** [FREE_AI_SOLUTIONS.md](./FREE_AI_SOLUTIONS.md) ⭐
- **🔄 التبديل بين AI Services:** [AI_SWITCHING_GUIDE.md](./AI_SWITCHING_GUIDE.md)
- ** تأكد من META_VERIFY_TOKEN صحيح
# 3. Test webhook:
curl -X GET "https://yourdomain.com/api/whatsapp?hub.mode=subscribe&hub.verify_token=YOUR_TOKEN&hub.challenge=test"
```

---

## 📱 Test Flow

1. **أرسل رسالة** من WhatsApp للرقم المسجل
2. **اتبع التعليمات** (Triage questions)
3. **اختر عيادة**
4. **أكد الحجز**
5. **افتح tracking link** لمتابعة الطابور

---

## 📚 Full Documentation

- **API Setup:** [API_CREDENTIALS_GUIDE.md](./API_CREDENTIALS_GUIDE.md)
- **WhatsApp Booking:** [WHATSAPP_BOOKING_GUIDE.md](./WHATSAPP_BOOKING_GUIDE.md)
- **Claude Guidelines:** [CLAUDE.md](./CLAUDE.md)

---

## ✅ Ready Checklist

قبل Production:

- [ ] WhatsApp Permanent Token
- [ ] OpenAI Billing Setup + $20 budget
- [ ] Webhook Verified ✅
- [ ] Phone numbers added for testing
- [ ] Queue Worker running
- [ ] Schedule Worker running
- [ ] Database migrated & seeded
- [ ] Test message sent successfully
- [ ] Test booking flow completed

---

**🎉 You're ready to go!**

*راجع الدليل الكامل في `API_CREDENTIALS_GUIDE.md` لمزيد من التفاصيل*
