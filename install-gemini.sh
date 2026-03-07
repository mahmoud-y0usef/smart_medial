#!/usr/bin/env bash

# ==============================================
# 🚀 تثبيت AI مجاني - الشفاء الذكي
# Google Gemini Flash 1.5 - Free AI Setup
# ==============================================

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🆓 تثبيت Google Gemini - AI مجاني للأبد!"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

# تثبيت Google Gemini PHP Package
echo "📦 تثبيت Google Generative AI PHP..."
composer require google/generative-ai-php --quiet

if [ $? -eq 0 ]; then
    echo "✅ تم تثبيت Gemini بنجاح!"
else
    echo "❌ فشل التثبيت. تحقق من composer.json"
    exit 1
fi

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🔑 الحصول على API Key (مجاناً)"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "1️⃣  افتح: https://aistudio.google.com/"
echo "2️⃣  انقر على 'Get API key'"
echo "3️⃣  انقر على 'Create API key'"
echo "4️⃣  انسخ الـ API key"
echo ""
read -p "هل حصلت على API key؟ (y/n): " got_key

if [ "$got_key" != "y" ]; then
    echo ""
    echo "🌐 فتح المتصفح..."
    if [[ "$OSTYPE" == "linux-gnu"* ]]; then
        xdg-open "https://aistudio.google.com/" 2>/dev/null
    elif [[ "$OSTYPE" == "darwin"* ]]; then
        open "https://aistudio.google.com/"
    elif [[ "$OSTYPE" == "msys" ]] || [[ "$OSTYPE" == "win32" ]]; then
        start "https://aistudio.google.com/"
    fi
    echo ""
    read -p "اضغط Enter بعد الحصول على API key..."
fi

echo ""
read -p "الصق API Key هنا: " api_key

if [ -z "$api_key" ]; then
    echo "❌ لم يتم إدخال API key"
    exit 1
fi

# تحديث .env
echo ""
echo "⚙️  تحديث ملف .env..."

if [ ! -f .env ]; then
    echo "❌ ملف .env غير موجود!"
    exit 1
fi

# إضافة أو تحديث Gemini API key
if grep -q "GOOGLE_AI_API_KEY=" .env; then
    # تحديث موجود
    sed -i.bak "s|GOOGLE_AI_API_KEY=.*|GOOGLE_AI_API_KEY=$api_key|g" .env
    echo "✅ تم تحديث GOOGLE_AI_API_KEY"
else
    # إضافة جديد
    echo "" >> .env
    echo "# Google Gemini (Free AI)" >> .env
    echo "GOOGLE_AI_API_KEY=$api_key" >> .env
    echo "✅ تم إضافة GOOGLE_AI_API_KEY"
fi

# تحديث AI_PRIMARY_PROVIDER إلى gemini
if grep -q "AI_PRIMARY_PROVIDER=" .env; then
    sed -i.bak "s|AI_PRIMARY_PROVIDER=.*|AI_PRIMARY_PROVIDER=gemini|g" .env
    echo "✅ تم تحديث AI_PRIMARY_PROVIDER إلى gemini"
else
    echo "AI_PRIMARY_PROVIDER=gemini" >> .env
    echo "✅ تم إضافة AI_PRIMARY_PROVIDER=gemini"
fi

# مسح Cache
echo ""
echo "🧹 مسح Cache..."
php artisan config:clear --quiet
php artisan cache:clear --quiet

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🧪 اختبار الاتصال..."
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

# اختبار بسيط
php artisan tinker --execute="
\$client = \Gemini::client(config('services.google_ai.api_key'));
\$response = \$client->geminiFlash()->generateContent('قل: مرحباً');
echo 'Response: ' . \$response->text() . PHP_EOL;
echo '✅ Gemini يعمل بنجاح!' . PHP_EOL;
"

if [ $? -eq 0 ]; then
    echo ""
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo "✨ نجح! نظامك الآن يعمل بـ AI مجاني!"
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo ""
    echo "📊 معلومات:"
    echo "   - Provider: Google Gemini Flash 1.5"
    echo "   - الحد اليومي: 1,500 طلب"
    echo "   - الحد بالدقيقة: 15 طلب"
    echo "   - التكلفة: 0 دولار/شهر 🎉"
    echo ""
    echo "📝 الخطوات التالية:"
    echo "   1. شغل السيرفر: php artisan serve"
    echo "   2. اختبر Triage عبر WhatsApp"
    echo "   3. راجع: FREE_AI_SOLUTIONS.md لمزيد من الخيارات"
    echo ""
    echo "💡 نصيحة: لسرعة أعلى، جرب Groq (مجاني أيضاً)"
    echo "   راجع: FREE_AI_SOLUTIONS.md"
    echo ""
else
    echo ""
    echo "⚠️  حدثت مشكلة في الاختبار"
    echo "تأكد من:"
    echo "  1. API key صحيح"
    echo "  2. اتصال الإنترنت يعمل"
    echo "  3. راجع: FREE_AI_SOLUTIONS.md"
    echo ""
fi

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
