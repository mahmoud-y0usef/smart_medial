# 🏥 الشفاء الذكي - Smart Medical Platform

نظام إدارة عيادات طبية ذكي متكامل مع WhatsApp Bot و AI مجاني

## ✨ المميزات الرئيسية

### 🤖 WhatsApp Bot ذكي
- تقييم أولي للمرضى (Triage) عبر WhatsApp
- حجز المواعيد تلقائياً
- إدارة الطوابير بالوقت الفعلي
- إشعارات تلقائية للمرضى

### 🧠 ذكاء اصطناعي مجاني
- **Google Gemini Flash** (مجاني - 1500 طلب يومياً)
- **Groq** (مجاني - 7000 طلب يومياً)
- **Ollama** (محلي - لا محدود)
- AI Scribe لاستخراج الملاحظات الطبية
- تفريغ الصوت (Whisper)

### 📋 إدارة العيادات
- لوحات تحكم متعددة (Admin, Clinic, Pharmacy)
- إدارة المواعيد والطوابير
- الوصفات الطبية الإلكترونية (E-Prescriptions)
- رموز QR للأدوية

### 💊 إدارة الصيدليات
- مخزون الأدوية
- تنبيهات انتهاء الصلاحية
- تنبيهات نفاد الأدوية
- تقارير وإحصائيات

---

## 🚀 البدء السريع

### 1. التثبيت

```bash
# Clone المشروع
git clone [repository-url] smart_medical
cd smart_medical

# تثبيت Dependencies
composer install
npm install

# إعداد البيئة
cp .env.example .env
php artisan key:generate

# إعداد Database
php artisan migrate --seed
```

### 2. إعداد AI المجاني (30 ثانية)

**Windows:**
```bash
.\install-gemini.bat
```

**Linux/Mac:**
```bash
chmod +x install-gemini.sh
./install-gemini.sh
```

✅ **هذا كل شيء! النظام الآن يعمل بـ AI مجاني!**

📚 **البديل اليدوي:** [FREE_AI_SUMMARY.md](./FREE_AI_SUMMARY.md)

### 3. إعداد WhatsApp (5 دقائق)

راجع: [QUICK_SETUP.md](./QUICK_SETUP.md)

### 4. تشغيل النظام

```bash
# السيرفر
php artisan serve

# Queue Worker (window جديد)
php artisan queue:work

# Schedule Worker (window جديد)
php artisan schedule:work
```

### 5. تسجيل الدخول

افتح المتصفح على الرابط المعروض، ثم:

- **Admin:** admin@example.com / password
- **Doctor:** doctor@example.com / password
- **Pharmacist:** pharmacist@example.com / password

---

## 📚 التوثيق الكامل

| الملف | الوصف |
|------|-------|
| 🆓 [FREE_AI_SUMMARY.md](./FREE_AI_SUMMARY.md) | **ابدأ هنا!** دليل AI المجاني |
| 🔄 [AI_SWITCHING_GUIDE.md](./AI_SWITCHING_GUIDE.md) | التبديل بين خدمات AI |
| ⚡ [QUICK_SETUP.md](./QUICK_SETUP.md) | البدء السريع (5 دقائق) |
| 📱 [API_CREDENTIALS_GUIDE.md](./API_CREDENTIALS_GUIDE.md) | إعداد WhatsApp و AI بالتفصيل |
| 💬 [WHATSAPP_BOOKING_GUIDE.md](./WHATSAPP_BOOKING_GUIDE.md) | دليل WhatsApp Booking |
| 🎨 [CLAUDE.md](./CLAUDE.md) | Laravel Boost Guidelines |

---

## 💰 التكلفة

### الخيار المجاني (الموصى به):
```
WhatsApp Test Mode: مجاناً ✅
Google Gemini: مجاناً (1500 طلب/يوم) ✅
Groq (backup): مجاناً (7000 طلب/يوم) ✅
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
إجمالي: 0 دولار/شهر 🎉
```

### للإنتاج (1000 مريض/شهر):
```
WhatsApp: $5-20/شهر (بعد أول 1000)
Gemini: مجاناً ✅
Groq: مجاناً ✅
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
إجمالي: $5-20/شهر
```

💡 **توفير $840/سنة** مقارنة بـ OpenAI + Anthropic!

---

## 🛠 التقنيات المستخدمة

- **Backend:** Laravel 12 + PHP 8.4
- **Admin Panel:** Filament 5
- **Frontend:** Livewire 4 + Alpine.js 3 + Tailwind CSS 4
- **Database:** MySQL
- **Queue:** Redis
- **Broadcasting:** Reverb / Pusher
- **AI (مجاني):** Google Gemini + Groq
- **AI (مدفوع):** OpenAI + Anthropic (اختياري)
- **WhatsApp:** Meta Business API
- **Testing:** Pest 3

---

## 🎯 الميزات التقنية

### WhatsApp Integration
- ✅ Webhook handler للرسائل الواردة
- ✅ Conversation state management
- ✅ Triage assessment via chatbot
- ✅ Automated appointment booking
- ✅ Real-time queue tracking
- ✅ Auto-notifications

### AI Features
- ✅ Multi-provider support (Gemini/Groq/Ollama/OpenAI/Anthropic)
- ✅ AI Scribe (medical notes extraction)
- ✅ Audio transcription (Whisper)
- ✅ Prescription generation
- ✅ Automatic fallback

### Real-time Features
- ✅ Broadcasting events (QueueUpdated, PatientCalled)
- ✅ Live queue tracking page
- ✅ Auto-refresh every 15 seconds
- ✅ Instant notifications

### Security
- ✅ Multi-role authentication
- ✅ Policy-based authorization
- ✅ QR code verification for prescriptions
- ✅ Audit trails
- ✅ Encrypted sensitive data

---

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter=TriageServiceTest

# Coverage
php artisan test --coverage
```

---

## 📦 البنية

```
app/
├── Enums/              # Enumerations
├── Filament/           # Admin panels (Admin, Clinic, Pharmacy)
├── Http/               # Controllers & Middleware
├── Livewire/           # Livewire components
├── Models/             # Eloquent models
├── Services/           # Business logic
│   ├── AI/            # AI Manager (Gemini, Groq, OpenAI, etc.)
│   ├── Booking/       # Appointment booking service
│   ├── Chatbot/       # Conversation manager
│   ├── Medical/       # Triage & medical services
│   ├── Queue/         # Queue management
│   └── WhatsApp/      # Meta WhatsApp service
└── Providers/          # Service providers

database/
├── factories/          # Model factories
├── migrations/         # Database migrations
└── seeders/           # Demo data seeders

resources/
├── css/               # Styles
├── js/                # JavaScript
└── views/             # Blade templates

routes/
├── api.php            # API routes
├── console.php        # Scheduled commands
└── web.php            # Web routes

tests/
└── Feature/           # Pest tests
```

---

## 🤝 المساهمة

Pull requests مرحّب بها! للتغييرات الكبيرة، افتح issue أولاً.

---

## 📄 الترخيص

MIT License

---

## 🆘 الدعم

- **Documentation:** راجع الملفات في [التوثيق](#-التوثيق-الكامل)
- **Issues:** افتح issue في GitHub
- **WhatsApp Setup:** [API_CREDENTIALS_GUIDE.md](./API_CREDENTIALS_GUIDE.md)
- **AI Setup:** [FREE_AI_SUMMARY.md](./FREE_AI_SUMMARY.md)

---

## ⭐ نصيحة أخيرة

```
ابدأ بالمجاني أولاً!

1. شغل: .\install-gemini.bat
2. اختبر النظام كاملاً
3. إذا احتجت أداء أعلى → ترقية للمدفوع

💰 وفر $840/سنة مع الحل المجاني!
```

---

**تم بحمد الله ✨**

*نظام طبي ذكي متكامل... بتكلفة صفر!*

4 مارس 2026


## Patterns worth looking at

Here are some specific things to poke at if you're learning Filament:

| Pattern | Where to find it |
|---|---|
| Wizard form | Create a new order |
| Reactive calculations | Edit an expense's line items |
| Builder blocks | Edit a project's Plan tab |
| Action groups with custom actions | Any table row with "..." menu |
| Slide-over modals | Ship an order |
| Modal forms with actions | Send email to a customer |
| Infolist with repeatable entries | View an expense |
| Sub-navigation | View or edit any post |
| Conditional field visibility | Change employment type on an employee |
| Dashboard filters | Shop dashboard date range and customer type |
| Global search | Press Cmd+K anywhere |
| Keyboard shortcuts | Cmd+Shift+P to quick-publish a post |
| Navigation badges | Check sidebar counts on orders, leave requests, and expenses |
