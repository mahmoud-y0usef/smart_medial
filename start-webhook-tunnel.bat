@echo off
REM =============================================
REM WhatsApp Webhook Tunnel - الشفاء الذكي
REM Using localhost.run (No Registration!)
REM =============================================

echo ==========================================
echo  WhatsApp Webhook Setup
echo  الشفاء الذكي - Smart Medical
echo ==========================================
echo.

REM Check if Laravel is running
echo [1/3] Checking if Laravel is running...
curl -s http://localhost:8000 >nul 2>&1
if %errorlevel% neq 0 (
    echo.
    echo ❌ Laravel is not running!
    echo.
    echo Please run in another terminal:
    echo   php artisan serve
    echo.
    pause
    exit /b 1
)

echo ✅ Laravel is running on port 8000
echo.

REM Start localhost.run tunnel
echo [2/3] Starting tunnel...
echo.
echo ⚠️  Keep this window open!
echo.
echo 📋 Copy the HTTPS URL that appears below
echo    Example: https://abc123.lhr.life
echo.
echo 📌 Use this in Meta Webhook:
echo    [YOUR_URL]/api/webhooks/whatsapp
echo.
echo 🔑 Verify Token:
echo    smart_medical_verify_2026_secure_eHTu8GkFHljri4qEYFObLCv
echo.
echo ==========================================
echo.

REM Run the tunnel
ssh -R 80:localhost:8000 nokey@localhost.run
