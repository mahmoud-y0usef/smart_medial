# 🔄 دليل التبديل السريع بين خدمات AI

## البدء السريع (30 ثانية)

### ✅ الطريقة الأسهل - تشغيل Script التثبيت:

**Windows:**
```bash
.\install-gemini.bat
```

**Linux/Mac:**
```bash
chmod +x install-gemini.sh
./install-gemini.sh
```

---

## 🎯 التبديل اليدوي

### 1️⃣ Google Gemini (مجاني - الموصى به)

```bash
# تثبيت
composer require google/generative-ai-php

# إضافة في .env
GOOGLE_AI_API_KEY=AIzaSyxxxxxxxxx
AI_PRIMARY_PROVIDER=gemini

# مسح Cache
php artisan config:clear
```

**الحصول على API Key:**
https://aistudio.google.com/ → Get API key

---

### 2️⃣ Groq (مجاني + سريع جداً)

```bash
# تثبيت
composer require groq-php/groq-php

# إضافة في .env
GROQ_API_KEY=gsk_xxxxxxxxx
AI_PRIMARY_PROVIDER=groq

# مسح Cache
php artisan config:clear
```

**الحصول على API Key:**
https://console.groq.com/ → API Keys

---

### 3️⃣ Ollama (محلي - خصوصية كاملة)

```bash
# تثبيت Ollama
# Windows: حمل من https://ollama.com/download/windows
# Linux: curl -fsSL https://ollama.com/install.sh | sh

# تحميل نموذج عربي
ollama pull aya:8b

# تشغيل Ollama
ollama serve

# تثبيت Laravel package
composer require cloudstudio/ollama-laravel

# إضافة في .env
OLLAMA_HOST=http://localhost:11434
OLLAMA_MODEL=aya:8b
AI_PRIMARY_PROVIDER=ollama

# مسح Cache
php artisan config:clear
```

---

### 4️⃣ OpenAI (مدفوع)

```bash
# مثبت مسبقاً في المشروع

# إضافة في .env
OPENAI_API_KEY=sk-proj-xxxxxxxxx
AI_PRIMARY_PROVIDER=openai

# مسح Cache
php artisan config:clear
```

**الحصول على API Key:**
https://platform.openai.com/api-keys

---

## 🔄 استخدام Fallback (احتياطي)

```env
# في .env
AI_PRIMARY_PROVIDER=gemini      # الخيار الأول
AI_FALLBACK_PROVIDER=groq       # إذا فشل الأول

# إذا فشل gemini، سيستخدم groq تلقائياً
```

---

## 🧪 اختبار Provider

```bash
php artisan tinker
```

### اختبار Gemini:
```php
$client = \Gemini::client(config('services.google_ai.api_key'));
$response = $client->geminiFlash()->generateContent('قل مرحباً بالعربية');
echo $response->text();
```

### اختبار Groq:
```php
$client = \Groq::client(config('services.groq.api_key'));
$response = $client->chat()->create([
    'model' => 'llama-3.1-70b-versatile',
    'messages' => [['role' => 'user', 'content' => 'قل مرحباً بالعربية']]
]);
echo $response->choices[0]->message->content;
```

### اختبار Ollama:
```php
$ollama = app(\CloudStudio\Ollama\Facades\Ollama::class);
$response = $ollama->prompt('قل مرحباً بالعربية')
    ->model('aya:8b')
    ->generate();
echo $response['response'];
```

### اختبار عبر Triage Service:
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
```

---

## 📊 مقارنة سريعة

| Provider | مجاني؟ | السرعة | العربية | التثبيت |
|----------|--------|--------|---------|---------|
| **Gemini Flash** | ✅ | ⚡⚡⚡⚡ | ⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ |
| **Groq** | ✅ | ⚡⚡⚡⚡⚡ | ⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ |
| **Ollama** | ✅ | ⚡⚡ | ⭐⭐⭐ | ⭐⭐ |
| OpenAI | ❌ | ⚡⚡⚡⚡⚡ | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ |

---

## ⚠️ حل المشاكل

### "Class \Gemini not found"
```bash
composer require google/generative-ai-php
php artisan config:clear
```

### "Groq client not found"
```bash
composer require groq-php/groq-php
php artisan config:clear
```

### "Ollama connection refused"
```bash
# تأكد أن Ollama يعمل
ollama serve

# تأكد من OLLAMA_HOST في .env
OLLAMA_HOST=http://localhost:11434
```

### "Invalid API key"
```bash
# تأكد من API key صحيح في .env
# مسح cache
php artisan config:clear
php artisan cache:clear
```

---

## 💡 نصائح التوفير

### استراتيجية مجانية 100%:

```env
# للإنتاج
AI_PRIMARY_PROVIDER=gemini        # 1500 طلب/يوم مجاناً
AI_FALLBACK_PROVIDER=groq         # 7000 طلب/يوم مجاناً

# = 8500 طلب يومياً مجاناً! 🎉
```

### استراتيجية الخصوصية:

```env
# للبيانات الحساسة
AI_PRIMARY_PROVIDER=ollama        # محلي 100%
AI_FALLBACK_PROVIDER=gemini       # في حالة أن Ollama لا يعمل
```

### استراتيجية السرعة:

```env
# للاستجابة الفورية
AI_PRIMARY_PROVIDER=groq          # 450+ tokens/sec
AI_FALLBACK_PROVIDER=gemini       # سريع أيضاً
```

---

## 📝 التوثيق الكامل

- **جميع البدائل المجانية:** [FREE_AI_SOLUTIONS.md](./FREE_AI_SOLUTIONS.md)
- **إعداد WhatsApp و AI:** [API_CREDENTIALS_GUIDE.md](./API_CREDENTIALS_GUIDE.md)
- **البدء السريع:** [QUICK_SETUP.md](./QUICK_SETUP.md)

---

## ✅ Checklist

- [ ] اخترت AI provider (gemini موصى به)
- [ ] ثبت package المطلوب
- [ ] حصلت على API key (إذا لزم)
- [ ] أضفت credentials في .env
- [ ] غيرت AI_PRIMARY_PROVIDER في .env
- [ ] مسحت cache: `php artisan config:clear`
- [ ] اختبرت الاتصال بنجاح

---

**🎉 جاهز! الآن نظامك يعمل بـ AI مجاني!**

*راجع [FREE_AI_SOLUTIONS.md](./FREE_AI_SOLUTIONS.md) لمزيد من التفاصيل*
