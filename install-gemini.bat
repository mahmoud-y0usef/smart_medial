@echo off
REM ==============================================
REM 🚀 تثبيت AI مجاني - الشفاء الذكي
REM Google Gemini Flash 1.5 - Free AI Setup (Windows)
REM ==============================================

echo ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
echo 🆓 تثبيت Google Gemini - AI مجاني للأبد!
echo ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
echo.

REM تثبيت Google Gemini PHP Package
echo 📦 تثبيت Google Generative AI PHP...
call composer require google/generative-ai-php --quiet

if %errorlevel% neq 0 (
    echo ❌ فشل التثبيت. تحقق من composer.json
    pause
    exit /b 1
)

echo ✅ تم تثبيت Gemini بنجاح!
echo.
echo ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
echo 🔑 الحصول على API Key (مجاناً)
echo ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
echo.
echo 1️⃣  افتح: https://aistudio.google.com/
echo 2️⃣  انقر على 'Get API key'
echo 3️⃣  انقر على 'Create API key'
echo 4️⃣  انسخ الـ API key
echo.

set /p got_key="هل حصلت على API key؟ (y/n): "

if /i not "%got_key%"=="y" (
    echo.
    echo 🌐 فتح المتصفح...
    start https://aistudio.google.com/
    echo.
    pause
)

echo.
set /p api_key="الصق API Key هنا: "

if "%api_key%"=="" (
    echo ❌ لم يتم إدخال API key
    pause
    exit /b 1
)

REM تحديث .env
echo.
echo ⚙️  تحديث ملف .env...

if not exist .env (
    echo ❌ ملف .env غير موجود!
    pause
    exit /b 1
)

REM إضافة أو تحديث Gemini API key باستخدام PowerShell
powershell -Command "(Get-Content .env) -replace 'GOOGLE_AI_API_KEY=.*', 'GOOGLE_AI_API_KEY=%api_key%' | Set-Content .env"
powershell -Command "if (!(Select-String -Path .env -Pattern 'GOOGLE_AI_API_KEY' -Quiet)) { Add-Content .env \"`nGOOGLE_AI_API_KEY=%api_key%\" }"

echo ✅ تم تحديث GOOGLE_AI_API_KEY

REM تحديث AI_PRIMARY_PROVIDER إلى gemini
powershell -Command "(Get-Content .env) -replace 'AI_PRIMARY_PROVIDER=.*', 'AI_PRIMARY_PROVIDER=gemini' | Set-Content .env"
powershell -Command "if (!(Select-String -Path .env -Pattern 'AI_PRIMARY_PROVIDER' -Quiet)) { Add-Content .env \"`nAI_PRIMARY_PROVIDER=gemini\" }"

echo ✅ تم تحديث AI_PRIMARY_PROVIDER إلى gemini

REM مسح Cache
echo.
echo 🧹 مسح Cache...
call php artisan config:clear >nul 2>&1
call php artisan cache:clear >nul 2>&1

echo.
echo ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
echo 🧪 اختبار الاتصال...
echo ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
echo.

REM اختبار بسيط
php artisan tinker --execute="$client = \Gemini::client(config('services.google_ai.api_key')); $response = $client->geminiFlash()->generateContent('قل: مرحباً'); echo 'Response: ' . $response->text() . PHP_EOL; echo '✅ Gemini يعمل بنجاح!' . PHP_EOL;"

if %errorlevel% equ 0 (
    echo.
    echo ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    echo ✨ نجح! نظامك الآن يعمل بـ AI مجاني!
    echo ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    echo.
    echo 📊 معلومات:
    echo    - Provider: Google Gemini Flash 1.5
    echo    - الحد اليومي: 1,500 طلب
    echo    - الحد بالدقيقة: 15 طلب
    echo    - التكلفة: 0 دولار/شهر 🎉
    echo.
    echo 📝 الخطوات التالية:
    echo    1. شغل السيرفر: php artisan serve
    echo    2. اختبر Triage عبر WhatsApp
    echo    3. راجع: FREE_AI_SOLUTIONS.md لمزيد من الخيارات
    echo.
    echo 💡 نصيحة: لسرعة أعلى، جرب Groq ^(مجاني أيضاً^)
    echo    راجع: FREE_AI_SOLUTIONS.md
    echo.
) else (
    echo.
    echo ⚠️  حدثت مشكلة في الاختبار
    echo تأكد من:
    echo   1. API key صحيح
    echo   2. اتصال الإنترنت يعمل
    echo   3. راجع: FREE_AI_SOLUTIONS.md
    echo.
)

echo ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
pause
