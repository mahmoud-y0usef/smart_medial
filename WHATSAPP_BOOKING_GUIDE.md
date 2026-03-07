# نظام الحجز والطابور عبر WhatsApp - الشفاء الذكي

## نظرة عامة

تم إكمال نظام الحجز والطابور الذكي الذي يتيح للمرضى:
1. ✅ حجز مواعيد عبر WhatsApp بعد تقييم الحالة (Triage)
2. ✅ اختيار العيادة المناسبة
3. ✅ الدخول في طابور ذكي مع تقدير الوقت
4. ✅ استقبال إشعارات تلقائية عند اقتراب الدور
5. ✅ تتبع الطابور بشكل لحظي عبر صفحة ويب

---

## المكونات الرئيسية

### 1. خدمات الحجز (Booking Services)

#### `AppointmentBookingService`
**الموقع:** `app/Services/Booking/AppointmentBookingService.php`

**المسؤوليات:**
- حجز المواعيد للمرضى
- حساب موقع المريض في الطابور
- تحديد الأولويات (عاجل/عادي) بناءً على تقييم الحالة
- حساب الوقت المتوقع للانتظار
- إدارة إلغاء المواعيد وإعادة ترتيب الطابور

**Methods الرئيسية:**
```php
// حجز موعد جديد
bookAppointment(Patient $patient, Clinic $clinic, ?int $triageId, ?Carbon $scheduledAt): Appointment

// الحصول على العيادات المتاحة مع حالة الطابور
getAvailableClinics(): array

// إلغاء موعد
cancelAppointment(Appointment $appointment, string $reason): bool

// الحصول على تفاصيل الموعد
getAppointmentDetails(Appointment $appointment): array
```

---

### 2. إدارة الطابور (Queue Management)

#### `QueueManagementService`
**الموقع:** `app/Services/Queue/QueueManagementService.php`

**المسؤوليات:**
- إدارة الطابور بشكل لحظي
- استدعاء المريض التالي
- بدء وإنهاء الكشف
- تخطي المرضى
- إرسال الإشعارات التلقائية

**Methods الرئيسية:**
```php
// الحصول على حالة الطابور
getQueueStatus(Clinic $clinic): array

// استدعاء المريض التالي
callNextPatient(Clinic $clinic): ?QueueEntry

// بدء الكشف
startConsultation(QueueEntry $entry): void

// إنهاء الكشف
completeConsultation(QueueEntry $entry): void

// تخطي مريض
skipPatient(QueueEntry $entry): void

// إشعار المرضى القادمين
notifyUpcomingPatients(int $clinicId): void
```

---

### 3. مدير المحادثات (Conversation Manager)

#### `ConversationManager`
**الموقع:** `app/Services/Chatbot/ConversationManager.php`

**المسؤوليات:**
- إدارة flow المحادثة الكامل
- تقييم الحالة (Triage)
- اختيار العيادة
- تأكيد الحجز
- تتبع الطابور

**States المحادثة:**
- `welcome` - الترحيب
- `triage_start` - بدء التقييم
- `triage_temp` - درجة الحرارة
- `triage_pain` - مستوى الألم
- `triage_dangerous` - الأعراض الخطيرة
- `triage_chronic` - الأمراض المزمنة
- `triage_result` - نتيجة التقييم
- `select_clinic` - اختيار العيادة
- `confirm_booking` - تأكيد الحجز

---

## Real-Time Updates

### Events المستخدمة:

#### `QueueUpdated`
يُرسل عند تحديث الطابور (تغيير الموقع/الوقت)
```php
broadcast(new QueueUpdated($queueEntry))->toOthers();
```

#### `PatientCalled`
يُرسل عند استدعاء مريض
```php
broadcast(new PatientCalled($queueEntry))->toOthers();
```

### Channels:
- `clinic.{clinic_id}.queue` - للعيادة
- `patient.{patient_id}` - للمريض

---

## Automated Tasks

### Command: `queue:notify`
**الوظيفة:** إرسال إشعارات تلقائية للمرضى في الطابور

**الاستخدام:**
```bash
# إشعار عيادة محددة
php artisan queue:notify 1

# إشعار جميع العيادات النشطة
php artisan queue:notify
```

**Schedule:** كل 5 دقائق (routes/console.php)

### Job: `NotifyQueuePatients`
يتم dispatch تلقائياً لإرسال الإشعارات بشكل async

---

## Database Schema

### New Fields في `appointments`:
```php
'is_whatsapp_booking' => boolean  // إذا كان الحجز عبر WhatsApp
'priority_level' => string        // 'high', 'normal', 'low'
```

### New Fields في `queue_entries`:
```php
'estimated_wait_time' => integer  // الوقت المتوقع بالدقائق (alias)
```

---

## WhatsApp Integration

### Webhooks:
- **GET** `/api/whatsapp` - التحقق
- **POST** `/api/whatsapp` - استقبال الرسائل

### Message Types المدعومة:
- Text messages
- Button replies
- Interactive lists

### Notification Messages:
```php
// تأكيد الحجز
sendBookingConfirmation($phone, $data)

// تحديث الطابور
sendQueueUpdate($phone, $position, $estimatedWait)

// اقتراب الدور (3 مرضى أو أقل)
sendYourTurnSoon($phone, $minutes)

// حان دورك
sendYourTurnNow($phone)
```

---

## Queue Tracking Page

### URL:
```
/queue/track/{appointment_id}
```

### Features:
- عرض موقع المريض بشكل كبير
- عدد المرضى قبله
- الوقت المتوقع
- معلومات العيادة والطبيب
- تحديث تلقائي كل 15 ثانية
- تمييز الحالات العاجلة

### API Endpoint:
```
GET /queue/status/{appointment_id}
```

**Response:**
```json
{
  "position": 5,
  "status": "waiting",
  "estimated_wait_time": 45,
  "people_ahead": 4,
  "updated_at": "2026-03-04T18:30:00Z"
}
```

---

## Priority System

### High Priority:
- يُمنح للمرضى بناءً على تقييم Triage
- يُوضع قبل المرضى العاديين في الطابور
- يحصل على badge أحمر في tracking page

### Calculation:
```php
$priorityLevel = $triage?->isHighPriority() ? 'high' : 'normal';
```

---

## Testing Guide

### 1. اختبار Flow الكامل:
```bash
# 1. فتح WhatsApp Sandbox/Production
# 2. إرسال رسالة للبوت
# 3. اتباع flow التقييم
# 4. اختيار عيادة
# 5. تأكيد الحجز
# 6. فتح tracking link
```

### 2. اختبار Queue Management:
```bash
# في Filament Clinic Panel:
# 1. فتح Queue Management
# 2. استدعاء مريض (Call Next)
# 3. بدء الكشف (Start Consultation)
# 4. إنهاء الكشف (Complete)
# 5. مراقبة التحديثات
```

### 3. اختبار Notifications:
```bash
# تشغيل queue scheduler يدوياً
php artisan queue:notify

# تشغيل queue worker
php artisan queue:work
```

---

## Configuration

### WhatsApp Config (.env):
```env
WHATSAPP_PHONE_ID=your_phone_id
WHATSAPP_TOKEN=your_access_token
WHATSAPP_VERIFY_TOKEN=your_verify_token
```

### Broadcasting Config (.env):
```env
BROADCAST_CONNECTION=reverb
# أو pusher أو log للتطوير
```

---

## Next Steps

### للإنتاج:
1. ✅ إعداد Pusher/Reverb للـ broadcasting
2. ✅ إضافة SSL للـ webhooks
3. ✅ تفعيل queue worker persistent
4. ✅ إعداد supervisor لـ queue:work
5. ✅ تفعيل rate limiting للـ WhatsApp webhook

### Features إضافية مقترحة:
- إرسال SMS بالإضافة للـ WhatsApp
- دعم لغات متعددة
- تقييم رضا المرضى بعد الكشف
- إحصائيات الطابور اليومية
- تكامل مع Google Maps للعيادات
- حجز مواعيد مستقبلية (غير فورية)

---

## Troubleshooting

### المشكلة: الإشعارات لا تُرسل
**الحل:**
```bash
# التحقق من queue worker
php artisan queue:work --verbose

# التحقق من logs
tail -f storage/logs/laravel.log

# تشغيل job يدوياً
php artisan tinker
>>> NotifyQueuePatients::dispatch(1);
```

### المشكلة: Real-time updates لا تعمل
**الحل:**
```bash
# التحقق من broadcasting config
php artisan config:cache

# التحقق من websocket connection
# في browser console:
// Echo.channel('clinic.1.queue')
//   .listen('QueueUpdated', (e) => console.log(e));
```

### المشكلة: Queue position غير دقيق
**الحل:**
```bash
# إعادة ترتيب الطابور يدوياً
php artisan tinker
>>> app(\App\Services\Queue\QueueManagementService::class)->reorderQueue(1);
```

---

## Performance Tips

1. **Cache frequent queries:**
```php
Cache::remember("clinic_$clinicId_queue_status", 30, function() {
    return $this->getQueueStatus($clinic);
});
```

2. **Eager load relationships:**
```php
$entries = QueueEntry::with(['appointment.patient', 'appointment.clinic'])->get();
```

3. **Use queue for notifications:**
```php
// كل الإشعارات تُرسل عبر queue
NotifyQueuePatients::dispatch($clinicId);
```

---

## License & Credits

This system is part of **Smart Medical (الشفاء الذكي)** platform.
Built with Laravel 12, Filament 5, and WhatsApp Business API.

© 2026 All rights reserved.
