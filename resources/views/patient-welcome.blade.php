<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الشفاء الذكي - حجز موعد</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap');
        body { font-family: 'Cairo', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <div class="container mx-auto px-4 py-12">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl md:text-5xl font-bold text-indigo-900 mb-4">
                🏥 الشفاء الذكي
            </h1>
            <p class="text-xl text-gray-700">
                Smart Medical Platform
            </p>
        </div>

        <!-- Main Card -->
        <div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-2xl overflow-hidden">
            <!-- Hero Section -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white p-8 text-center">
                <div class="text-6xl mb-4">💬</div>
                <h2 class="text-3xl font-bold mb-2">احجز موعدك الآن!</h2>
                <p class="text-blue-100">عبر WhatsApp في دقائق معدودة</p>
            </div>

            <!-- Content -->
            <div class="p-8">
                <!-- Features -->
                <div class="grid md:grid-cols-2 gap-4 mb-8">
                    <div class="flex items-start space-x-3 space-x-reverse">
                        <span class="text-3xl">⚡</span>
                        <div>
                            <h3 class="font-bold text-gray-800">سريع وسهل</h3>
                            <p class="text-gray-600 text-sm">احجز في أقل من دقيقتين</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3 space-x-reverse">
                        <span class="text-3xl">🤖</span>
                        <div>
                            <h3 class="font-bold text-gray-800">AI مساعد ذكي</h3>
                            <p class="text-gray-600 text-sm">يساعدك في كل خطوة</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3 space-x-reverse">
                        <span class="text-3xl">⏰</span>
                        <div>
                            <h3 class="font-bold text-gray-800">تتبع دورك</h3>
                            <p class="text-gray-600 text-sm">اعرف دورك في العيادة لحظياً</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3 space-x-reverse">
                        <span class="text-3xl">💊</span>
                        <div>
                            <h3 class="font-bold text-gray-800">روشتة إلكترونية</h3>
                            <p class="text-gray-600 text-sm">استلم الروشتة على WhatsApp</p>
                        </div>
                    </div>
                </div>

                <!-- CTA Button -->
                <div class="text-center mb-6">
                    <a href="https://wa.me/{{ config('services.whatsapp.phone_number', '201234567890') }}?text={{ urlencode('مرحباً، أريد حجز موعد') }}" 
                       target="_blank"
                       class="inline-flex items-center justify-center gap-3 bg-green-500 hover:bg-green-600 text-white font-bold py-4 px-8 rounded-full text-xl transition-all transform hover:scale-105 shadow-lg">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        ابدأ المحادثة على WhatsApp
                    </a>
                </div>

                <!-- QR Code -->
                <div class="text-center py-6 border-t border-gray-200">
                    <p class="text-gray-600 mb-4">أو امسح الـ QR Code</p>
                    <div class="inline-block p-4 bg-white border-4 border-indigo-600 rounded-lg">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode('https://wa.me/' . config('services.whatsapp.phone_number', '201234567890') . '?text=مرحباً') }}" 
                             alt="WhatsApp QR Code"
                             class="w-48 h-48">
                    </div>
                </div>

                <!-- Instructions -->
                <div class="bg-blue-50 rounded-lg p-6 mt-6">
                    <h3 class="font-bold text-gray-800 mb-3 flex items-center gap-2">
                        <span>📋</span>
                        كيف تحجز موعدك؟
                    </h3>
                    <ol class="space-y-2 text-gray-700">
                        <li class="flex items-start gap-2">
                            <span class="font-bold text-blue-600">1.</span>
                            <span>اضغط على زر "ابدأ المحادثة" أعلاه</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="font-bold text-blue-600">2.</span>
                            <span>اتبع التعليمات التي يرسلها البوت</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="font-bold text-blue-600">3.</span>
                            <span>اختر العيادة والموعد المناسب</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="font-bold text-blue-600">4.</span>
                            <span>احصل على تأكيد فوري ورقم موعد</span>
                        </li>
                    </ol>
                </div>

                <!-- Contact Info -->
                <div class="mt-6 text-center text-gray-600 text-sm">
                    <p>للاستفسارات: {{ config('services.whatsapp.phone_number', '+201234567890') }}</p>
                    <p class="mt-2">متاح 24/7</p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8 text-gray-600">
            <p>&copy; {{ date('Y') }} الشفاء الذكي. جميع الحقوق محفوظة.</p>
        </div>
    </div>
</body>
</html>
