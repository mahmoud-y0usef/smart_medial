# دليل الحصول على API Keys - الشفاء الذكي

## 📱 WhatsApp Business API من Meta

### الخطوات التفصيلية:

#### 1. إنشاء حساب Meta for Developers

1. **اذهب إلى:** https://developers.facebook.com/
2. **سجل الدخول** بحساب Facebook الخاص بك
3. **انقر على "My Apps"** في الزاوية اليمنى العليا
4. **انقر على "Create App"**

#### 2. اختيار نوع التطبيق

1. **اختر:** "Business"
2. **انقر:** "Next"
3. **املأ المعلومات:**
   - **Display Name:** الشفاء الذكي
   - **App Contact Email:** بريدك الإلكتروني
   - **Business Account:** اختر أو أنشئ واحد جديد
4. **انقر:** "Create App"

#### 3. إضافة WhatsApp Product

1. في لوحة التحكم، **ابحث عن "WhatsApp"**
2. **انقر على "Set Up"** بجوار WhatsApp
3. سيتم توجيهك لصفحة WhatsApp Business Platform

#### 4. الحصول على Credentials الأساسية

##### A. Phone Number ID (META_PHONE_ID)
```
1. في صفحة WhatsApp > Getting Started
2. ابحث عن "Phone number ID"
3. انسخ الرقم (مثال: 123456789012345)
```

##### B. Access Token (META_WHATSAPP_TOKEN)
```
1. في نفس الصفحة، ابحث عن "Temporary access token"
2. انقر على "Copy" لنسخ الـ token
3. ⚠️ هذا token مؤقت (24 ساعة)
```

##### C. إنشاء Permanent Access Token (للإنتاج)
```
1. اذهب إلى: Tools > System Users (في Business Manager)
   رابط مباشر: https://business.facebook.com/settings/system-users

2. انقر على "Add" لإنشاء System User جديد
3. معلومات System User:
   - Name: "WhatsApp Bot System User"
   - Role: Admin
4. انقر على "Create System User"

5. ⚠️ خطوة مهمة: إضافة Assets أولاً (قبل إنشاء Token)
   
   أ. إضافة التطبيق:
      - انقر على "Add Assets"
      - اختر "Apps"
      - حدد تطبيقك (مثلاً: الشفاء الذكي)
      - اختر "Full Control"
      - انقر "Save Changes"
   
   ب. إضافة WhatsApp Account:
      - انقر على "Add Assets" مرة أخرى
      - اختر "WhatsApp Accounts"
      - حدد حساب WhatsApp Business الخاص بك
      - اختر "Manage WhatsApp Account"
      - انقر "Save Changes"

6. الآن انشئ الـ Token:
   - انقر على "Generate New Token"
   - اختر التطبيق الخاص بك من القائمة
   - ⚠️ إذا ظهرت رسالة "No permissions available":
     * ارجع للخطوة 5 وتأكد من إضافة Assets
     * انتظر دقيقة واحدة ثم حاول مرة أخرى
   
7. اختر Permissions المطلوبة:
   - ✅ whatsapp_business_messaging
   - ✅ whatsapp_business_management
   - ✅ business_management (اختياري)

8. انقر "Generate Token"
9. ⚠️ انسخ الـ Token فوراً وخزنه بأمان (لن تستطيع رؤيته مرة أخرى)
   - يبدأ بـ: EAAG... أو EAAl...
   - طوله حوالي 200+ حرف
```

##### D. Verify Token (META_VERIFY_TOKEN)
```
هذا token تختاره أنت بنفسك (أي نص عشوائي معقد)
مثال: "my_secure_verify_token_12345"
```

##### E. Business Account ID (META_BUSINESS_ACCOUNT_ID)
```
1. اذهب إلى: Business Settings
2. في القائمة الجانبية، انقر على "Business Info"
3. انسخ "Business ID" من الأعلى
```

#### 5. إعداد Webhook

1. في صفحة WhatsApp Configuration
2. **انقر على "Configure webhooks"**
3. **Callback URL:** `https://yourdomain.com/api/whatsapp`
4. **Verify Token:** نفس الـ token الي اخترته (META_VERIFY_TOKEN)
5. **Webhook Fields:** اختر:
   - ✅ messages
   - ✅ message_status
6. **انقر على "Verify and Save"**

#### 6. الحصول على رقم WhatsApp للاختبار

```
1. في Getting Started page
2. ابحث عن "To" section
3. انقر على "Add phone number"
4. أدخل رقمك (+201234567890)
5. سيصلك كود تفعيل على WhatsApp
6. أدخل الكود للتفعيل
```

#### 7. ترقية للإنتاج (Production)

```
⚠️ للاستخدام مع عملاء حقيقيين:

1. أكمل "Business Verification"
2. قدم طلب للحصول على Official Business Account
3. اختر رقم WhatsApp Business خاص بك
4. انتظر الموافقة من Meta (2-5 أيام)
```

---

## 🤖 OpenAI API Key

### الخطوات:

#### 1. إنشاء حساب OpenAI

1. **اذهب إلى:** https://platform.openai.com/
2. **انقر على "Sign Up"**
3. **أكمل التسجيل** بالبريد الإلكتروني أو Google

#### 2. الحصول على API Key

1. **اذهب إلى:** https://platform.openai.com/api-keys
2. **انقر على "Create new secret key"**
3. **اختر اسم:** "Smart Medical Production"
4. **Permissions:** يفضل "All" للبداية
5. **انقر على "Create secret key"**
6. **⚠️ انسخ الـ key فوراً** (لن تستطيع رؤيته مرة أخرى)
   ```
   مثال: sk-proj-abc123def456...
   ```

#### 3. إضافة رصيد (Billing)

```
⚠️ مهم: يجب إضافة طريقة دفع لاستخدام الـ API

1. اذهب إلى: Settings > Billing
2. انقر على "Add payment method"
3. أضف بطاقة ائتمان
4. حدد ميزانية شهرية (مثلاً: $20)
```

#### 4. معرفة Organization ID (اختياري)

```
1. اذهب إلى: Settings > Organization
2. انسخ "Organization ID"
3. مثال: org-abc123def456
```

#### 5. الأسعار (تقريبية)

```
GPT-4 Turbo:
- Input: $0.01 / 1K tokens
- Output: $0.03 / 1K tokens

GPT-3.5 Turbo:
- Input: $0.0005 / 1K tokens
- Output: $0.0015 / 1K tokens

💡 تقدير: حوالي $0.05-0.10 لكل استشارة طبية
```

---

## 🧠 Anthropic (Claude) API Key

### الخطوات:

#### 1. إنشاء حساب Anthropic

1. **اذهب إلى:** https://console.anthropic.com/
2. **انقر على "Sign Up"**
3. **أكمل التسجيل**

#### 2. الحصول على API Key

1. **اذهب إلى:** https://console.anthropic.com/settings/keys
2. **انقر على "Create Key"**
3. **اختر اسم:** "Smart Medical Bot"
4. **انقر على "Create Key"**
5. **انسخ الـ key:**
   ```
   مثال: sk-ant-api03-abc123def456...
   ```

#### 3. إضافة رصيد (Billing)

```
1. اذهب إلى: Settings > Billing
2. أضف طريقة دفع
3. أضف رصيد (minimum: $5)
```

#### 4. الأسعار (تقريبية)

```
Claude 3 Sonnet:
- Input: $0.003 / 1K tokens
- Output: $0.015 / 1K tokens

Claude 3 Opus:
- Input: $0.015 / 1K tokens
- Output: $0.075 / 1K tokens

💡 أرخص من GPT-4 بحوالي 3X
```

---

## ⚙️ إعداد ملف .env

### نسخ البيانات إلى .env

1. **افتح الملف:** `.env` في مجلد المشروع
2. **أضف القيم التالية:**

```env
# Meta WhatsApp Business API
META_WHATSAPP_TOKEN=EAAxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
META_VERIFY_TOKEN=my_secure_verify_token_12345
META_PHONE_ID=123456789012345
META_BUSINESS_ACCOUNT_ID=987654321098765

# OpenAI API (Primary)
OPENAI_API_KEY=sk-proj-abc123def456ghi789jkl012mno345pqr678stu901vwx234yz
OPENAI_ORGANIZATION=org-abc123def456

# Anthropic API (Fallback)
ANTHROPIC_API_KEY=sk-ant-api03-abc123def456ghi789jkl012mno345pqr678
```

### مثال كامل لـ .env:

```env
APP_NAME="الشفاء الذكي - Smart Medical"
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=smart_medical
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

# Broadcasting (للـ Real-time)
BROADCAST_DRIVER=reverb
# أو pusher للإنتاج

QUEUE_CONNECTION=redis
# أو database

# Meta WhatsApp
META_WHATSAPP_TOKEN=EAAxxxxxxx...
META_VERIFY_TOKEN=my_secure_verify_token_12345
META_PHONE_ID=123456789012345
META_BUSINESS_ACCOUNT_ID=987654321098765

# AI Services
OPENAI_API_KEY=sk-proj-xxxxx...
OPENAI_ORGANIZATION=org-xxxxx
ANTHROPIC_API_KEY=sk-ant-api03-xxxxx...
```

---

## 🧪 اختبار الإعدادات

### 1. اختبار WhatsApp API

```bash
# في terminal
php artisan tinker
```

```php
// اختبر إرسال رسالة
$whatsapp = app(\App\Services\WhatsApp\MetaWhatsAppService::class);
$whatsapp->sendMessage('+201234567890', 'مرحباً من الشفاء الذكي! 🏥');

// يجب أن ترى:
// ✅ Message sent successfully
```

### 2. اختبار OpenAI API

```bash
php artisan tinker
```

```php
// اختبر OpenAI
$client = \OpenAI::client(config('services.openai.api_key'));

$response = $client->chat()->create([
    'model' => 'gpt-4-turbo-preview',
    'messages' => [
        ['role' => 'user', 'content' => 'قل مرحباً بالعربية']
    ]
]);

echo $response->choices[0]->message->content;
// يجب أن تظهر: "مرحباً! كيف يمكنني مساعدتك؟"
```

### 3. اختبار Triage Service (يستخدم AI)

```bash
php artisan tinker
```

```php
$patient = \App\Models\Patient::first();
$triage = app(\App\Services\Medical\TriageService::class);

$assessment = $triage->createAssessment($patient, [
    'temperature' => 38.5,
    'pain_level' => 7,
    'symptoms' => 'صداع شديد',
    'dangerous_symptoms' => [],
    'chronic_diseases' => []
]);

echo $assessment->ai_recommendation;
// يجب أن تظهر توصية AI بالعربية
```

---

## 🔒 أمان API Keys

### ⚠️ مهم جداً:

1. **لا تشارك الـ Keys أبداً:**
   - لا تضعها في Git
   - لا ترسلها في Slack/WhatsApp
   - لا تضعها في Screenshots

2. **استخدم .env فقط:**
   ```bash
   # تأكد أن .env في .gitignore
   echo ".env" >> .gitignore
   ```

3. **للـ Production:**
   - استخدم Environment Variables على السيرفر
   - لا تحفظ الـ keys في الكود
   - استخدم Laravel Forge أو AWS Secrets Manager

4. **تدوير الـ Keys:**
   - غير الـ keys كل 3-6 أشهر
   - إذا تسربت key، احذفها فوراً وأنشئ جديدة

---

## 💰 تقدير التكاليف الشهرية

### سيناريو: 1000 مريض شهرياً

#### WhatsApp:
```
مجاناً في الوضع التجريبي (Test Mode)
في Production:
- أول 1000 محادثة: مجاناً
- بعد ذلك: $0.005-0.02 لكل محادثة (حسب الدولة)
تقدير: $5-20/شهر
```

#### OpenAI (GPT-4 Turbo):
```
استخدام متوسط:
- Triage: 500 tokens × $0.01/1K = $0.005
- AI Scribe: 2000 tokens × $0.01/1K = $0.02
- إجمالي لكل مريض: ~$0.025

1000 مريض × $0.025 = $25/شهر
```

#### Anthropic (Claude):
```
استخدام متوسط (كـ backup):
- متوسط: $0.008 لكل استشارة
- 200 استشارة شهرياً (20% من الإجمالي)
= $1.6/شهر
```

#### **إجمالي متوقع: $30-50/شهر** 💵

---

## 🚨 حل المشاكل الشائعة

### WhatsApp: "Invalid access token"
```
✅ الحل:
1. تأكد أن الـ token لم ينتهي
2. أنشئ Permanent token (System User)
3. تأكد من Permissions الصحيحة
```

### WhatsApp: "Rate limit exceeded"
```
✅ الحل:
1. Meta تضع حد 80 رسالة/ثانية
2. استخدم Queue لإرسال الرسائل
3. أضف delay بين الرسائل
```

### OpenAI: "Insufficient quota"
```
✅ الحل:
1. تحقق من Billing settings
2. أضف رصيد كافي
3. تأكد من Payment method صالحة
```

### OpenAI: "Rate limit exceeded"
```
✅ الحل:
1. Tier 1: 500 requests/minute
2. زود الـ tier بإضافة رصيد أكثر
3. أو استخدم Anthropic كـ fallback
```

---

## 📞 الدعم الفني

### Meta WhatsApp:
- **Documentation:** https://developers.facebook.com/docs/whatsapp
- **Support:** https://business.facebook.com/business/help

### OpenAI:
- **Documentation:** https://platform.openai.com/docs
- **Community:** https://community.openai.com

### Anthropic:
- **Documentation:** https://docs.anthropic.com
- **Support:** support@anthropic.com

---

## ✅ Checklist النهائي

قبل البدء في الإنتاج، تأكد من:

- [ ] حصلت على WhatsApp Access Token (Permanent)
- [ ] أضفت Phone Number ID
- [ ] أنشأت Verify Token
- [ ] أضفت Webhook URL وتم التحقق منه
- [ ] حصلت على OpenAI API Key
- [ ] أضفت رصيد في OpenAI Billing
- [ ] حصلت على Anthropic API Key (optional)
- [ ] أضفت جميع الـ keys في .env
- [ ] اختبرت إرسال رسالة WhatsApp
- [ ] اختبرت AI Triage
- [ ] شغلت Queue Worker
- [ ] شغلت Schedule Worker (للإشعارات)

---

## 🎉 جاهز للانطلاق!

بعد إتمام جميع الخطوات، نظامك جاهز لاستقبال المرضى عبر WhatsApp!

**الخطوة التالية:**
```bash
# تشغيل Queue Worker
php artisan queue:work --tries=3

# تشغيل Schedule (في window آخر)
php artisan schedule:work

# تشغيل السيرفر
php artisan serve
```

**اختبر النظام:**
1. أرسل رسالة من WhatsApp للرقم المسجل
2. اتبع flow التقييم
3. احجز موعد
4. تتبع الطابور

---

**تم بحمد الله ✨**

*دليل معد بواسطة: فريق الشفاء الذكي*
*التاريخ: 4 مارس 2026*
