<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تتبع الطابور - الشفاء الذكي</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Cairo', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Cairo', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen py-8 px-4">
        <div class="max-w-2xl mx-auto">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-t-2xl p-6 text-white">
                <h1 class="text-2xl font-bold mb-2">🏥 الشفاء الذكي</h1>
                <p class="text-blue-100">تتبع الطابور بشكل لحظي</p>
            </div>

            <!-- Main Card -->
            <div class="bg-white rounded-b-2xl shadow-lg p-6">
                <!-- Status Badge -->
                <div class="mb-6 text-center">
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold
                        @if($queue->status->value === 'waiting') bg-yellow-100 text-yellow-800
                        @elseif($queue->status->value === 'called') bg-green-100 text-green-800
                        @elseif($queue->status->value === 'in_consultation') bg-blue-100 text-blue-800
                        @else bg-gray-100 text-gray-800
                        @endif">
                        @if($queue->status->value === 'waiting')
                            ⏳ في الانتظار
                        @elseif($queue->status->value === 'called')
                            🔔 تم النداء عليك
                        @elseif($queue->status->value === 'in_consultation')
                            👨‍⚕️ جاري الكشف
                        @else
                            ✅ تم الكشف
                        @endif
                    </span>
                </div>

                <!-- Queue Position (Large) -->
                <div class="mb-8 text-center">
                    <div class="text-6xl font-bold text-blue-600 mb-2" id="position">
                        {{ $queue->position }}
                    </div>
                    <p class="text-gray-600 text-lg">رقمك في الطابور</p>
                </div>

                <!-- Patient Info -->
                <div class="bg-gray-50 rounded-xl p-4 mb-6">
                    <h3 class="font-semibold text-gray-800 mb-3 flex items-center">
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        معلومات المريض
                    </h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">الاسم:</span>
                            <span class="font-semibold">{{ $patient->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">رقم الهاتف:</span>
                            <span class="font-semibold">{{ $patient->phone }}</span>
                        </div>
                    </div>
                </div>

                <!-- Clinic Info -->
                <div class="bg-blue-50 rounded-xl p-4 mb-6">
                    <h3 class="font-semibold text-blue-900 mb-3 flex items-center">
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        معلومات العيادة
                    </h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-blue-700">العيادة:</span>
                            <span class="font-semibold text-blue-900">{{ $clinic->name_ar }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-blue-700">العنوان:</span>
                            <span class="font-semibold text-blue-900">{{ $clinic->full_address }}</span>
                        </div>
                        @if($appointment->doctor)
                        <div class="flex justify-between">
                            <span class="text-blue-700">الطبيب:</span>
                            <span class="font-semibold text-blue-900">{{ $appointment->doctor->name_ar }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Queue Stats -->
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="bg-green-50 rounded-xl p-4 text-center">
                        <div class="text-3xl font-bold text-green-600" id="people-ahead">
                            {{ $queue->position - 1 }}
                        </div>
                        <p class="text-sm text-green-700 mt-1">مريض قبلك</p>
                    </div>
                    <div class="bg-purple-50 rounded-xl p-4 text-center">
                        <div class="text-3xl font-bold text-purple-600" id="estimated-wait">
                            {{ $queue->estimated_wait_time }}
                        </div>
                        <p class="text-sm text-purple-700 mt-1">دقيقة متوقعة</p>
                    </div>
                </div>

                <!-- Priority Badge -->
                @if($appointment->priority_level === 'high')
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-center mb-6">
                    <div class="flex items-center justify-center text-red-700">
                        <svg class="w-5 h-5 ml-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        <span class="font-semibold">حالة عاجلة - لديك أولوية</span>
                    </div>
                </div>
                @endif

                <!-- Auto Refresh Info -->
                <div class="text-center text-sm text-gray-500 mb-4">
                    <svg class="w-4 h-4 inline-block ml-1 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    يتم التحديث تلقائياً كل 15 ثانية
                </div>

                <!-- Last Updated -->
                <div class="text-center text-xs text-gray-400" id="last-updated">
                    آخر تحديث: {{ $queue->updated_at->diffForHumans() }}
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-6 text-center text-sm text-gray-500">
                <p>💡 ستصلك إشعارات على WhatsApp عند اقتراب دورك</p>
            </div>
        </div>
    </div>

    <script>
        // Auto-refresh every 15 seconds
        setInterval(async function() {
            try {
                const response = await fetch('{{ route("queue.status", $appointment) }}');
                const data = await response.json();
                
                // Update UI
                document.getElementById('position').textContent = data.position;
                document.getElementById('people-ahead').textContent = data.position - 1;
                document.getElementById('estimated-wait').textContent = data.estimated_wait_time;
                document.getElementById('last-updated').textContent = 'آخر تحديث: منذ لحظات';
                
                // Update status badge based on data.status if needed
                // ... (you can add more sophisticated status handling here)
                
            } catch (error) {
                console.error('Failed to fetch queue status:', error);
            }
        }, 15000);
    </script>
</body>
</html>
