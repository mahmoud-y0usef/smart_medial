# 🚀 تشغيل Webhook بدون ngrok

## المشكلة:
ngrok يحتاج تسجيل و authtoken 😞

## ✅ الحل: استخدم **localhost.run** (أسهل وأسرع!)

---

## 📋 الخطوات:

### 1️⃣ شغّل Laravel

```bash
php artisan serve
```

✅ اترك Terminal ده مفتوح

---

### 2️⃣ شغّل السكريبت المجهز

**افتح PowerShell أو CMD جديد وشغّل:**

```bash
.\start-webhook-tunnel.bat
```

**أو يدوياً:**

```bash
ssh -R 80:localhost:8000 nokey@localhost.run
```

---

### 3️⃣ انسخ الـ URL

هتشوف رسالة زي:

```
abc123.lhr.life tunneled with tls termination
https://abc123.lhr.life
```

**انسخ:** `https://abc123.lhr.life`

---

### 4️⃣ حطه في Meta

**1. افتح:**
```
https://developers.facebook.com/apps
```

**2. اختار تطبيقك > WhatsApp > Configuration**

**3. Webhook Section:**

**Callback URL:**
```
https://abc123.lhr.life/api/webhooks/whatsapp
```

**Verify Token:**
```
smart_medical_verify_2026_secure_eHTu8GkFHljri4qEYFObLCv
```

**4. اضغط "Verify and Save"** ✅

**5. Subscribe to:**
- ✅ messages
- ✅ message_status

---

### 5️⃣ جرب البوت!

**ابعت رسالة على WhatsApp:**
```
مرحباً
```

**البوت المفروض يرد:**
```
أهلاً بك في الشفاء الذكي! 👋
```

---

## 🔍 لو مش شغال:

### اختبر الـ Webhook محلياً:

```bash
php artisan whatsapp:test-webhook --url=https://abc123.lhr.life
```

*(غيّر الـ URL بتاعك)*

### شوف الـ Logs:

```bash
Get-Content storage/logs/laravel-2026-03-05.log -Tail 50
```

---

## 💡 بدائل تانية:

### 1. **serveo.net**
```bash
ssh -R 80:localhost:8000 serveo.net
```

### 2. **Expose (Laravel Package)**
```bash
composer global require beyondcode/expose
expose share http://localhost:8000
```

### 3. **Cloudflare Tunnel** (للإنتاج)
```bash
cloudflared tunnel --url http://localhost:8000
```

---

## ⚠️ ملاحظات مهمة:

1. **اترك الـ Terminal مفتوح** طول ما بتختبر
2. **الـ URL بيتغير** كل مرة تشغّله
3. **للإنتاج:** استخدم سيرفر حقيقي مع domain ثابت

---

## 🎯 الخلاصة:

```bash
# Terminal 1: Laravel
php artisan serve

# Terminal 2: Tunnel  
ssh -R 80:localhost:8000 nokey@localhost.run

# انسخ الـ URL
# حطه في Meta Webhook
# جرب البوت!
```

---

**✅ دلوقتي البوت هيرد على WhatsApp!** 🎉
