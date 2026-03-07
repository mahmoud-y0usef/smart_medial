# 🎯 البدء فوراً - دليل سريع جداً

## للمبرمج المستعجل (30 ثانية)

```bash
# 1. شغل هذا Script
.\install-gemini.bat       # Windows
./install-gemini.sh        # Linux/Mac

# 2. خلاص! ✅
```

---

## ليش Gemini؟

| | Gemini | OpenAI |
|---|--------|--------|
| السعر | **مجاناً** ✅ | $40+/شهر ❌ |
| الحد اليومي | **1,500 طلب** | حسب الرصيد |
| السرعة | **0.8 ثانية** ⚡ | 2-3 ثواني |
| العربية | **ممتاز** ⭐⭐⭐⭐ | ممتاز جداً ⭐⭐⭐⭐⭐ |
| التثبيت | **script واحد** | يدوي + بطاقة |

**النتيجة: Gemini كافي 100% للمشروع!**

---

## التثبيت اليدوي (إذا ما اشتغل Script)

### 1. تثبيت Package
```bash
composer require google/generative-ai-php
```

### 2. الحصول على API Key
افتح: **https://aistudio.google.com/**
- انقر "Get API key"
- انقر "Create API key"
- انسخ الـ key

### 3. أضف في .env
```env
GOOGLE_AI_API_KEY=AIzaSyxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
AI_PRIMARY_PROVIDER=gemini
```

### 4. مسح Cache
```bash
php artisan config:clear
```

### 5. اختبر
```bash
php artisan tinker
```
```php
$client = \Gemini::client(config('services.google_ai.api_key'));
echo $client->geminiFlash()->generateContent('مرحباً')->text();
```

✅ **إذا شفت رد، يعني شغال!**

---

## مشاكل؟

### "Class \Gemini not found"
```bash
composer require google/generative-ai-php
```

### "Invalid API key"
- تأكد من نسخ الـ key صح
- احذف المسافات من أول وآخر
- مسح cache: `php artisan config:clear`

### "لسا ما اشتغل"
راجع: [FREE_AI_SOLUTIONS.md](./FREE_AI_SOLUTIONS.md) → قسم "حل المشاكل"

---

## بدائل أخرى مجانية

### Groq (أسرع من Gemini!)
```bash
composer require groq-php/groq-php

# API Key من: https://console.groq.com/
GROQ_API_KEY=gsk_xxxxxxxx
AI_PRIMARY_PROVIDER=groq
```

### Ollama (محلي - خصوصية 100%)
```bash
# تثبيت Ollama: https://ollama.com/download
ollama pull aya:8b
ollama serve

composer require cloudstudio/ollama-laravel

# في .env
OLLAMA_HOST=http://localhost:11434
OLLAMA_MODEL=aya:8b
AI_PRIMARY_PROVIDER=ollama
```

---

## التوثيق الكامل

- **الدليل الكامل:** [FREE_AI_SOLUTIONS.md](./FREE_AI_SOLUTIONS.md)
- **التبديل بين الخدمات:** [AI_SWITCHING_GUIDE.md](./AI_SWITCHING_GUIDE.md)
- **إعداد WhatsApp:** [QUICK_SETUP.md](./QUICK_SETUP.md)

---

## النصيحة النهائية

```
استخدم:
AI_PRIMARY_PROVIDER=gemini      ← مجاني + قوي
AI_FALLBACK_PROVIDER=groq       ← مجاني + سريع

= 9000 طلب يومياً مجاناً! 🎉
```

---

**تم! الآن شغل المشروع:**

```bash
php artisan serve
php artisan queue:work
```

**🎉 مبروك! نظامك شغال بـ AI مجاني!**
