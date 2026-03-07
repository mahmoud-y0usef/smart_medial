<?php

namespace App\Services\Chatbot;

use App\Models\Clinic;
use App\Models\ConversationState;
use App\Models\Patient;
use App\Services\Booking\AppointmentBookingService;
use App\Services\Medical\TriageService;
use App\Services\WhatsApp\MetaWhatsAppService;

class ConversationManager
{
    public function __construct(
        protected MetaWhatsAppService $whatsapp,
        protected TriageService $triage,
        protected AppointmentBookingService $booking
    ) {}

    /**
     * Handle incoming message
     */
    public function handleMessage(string $phone, string $message, ?string $buttonId = null): void
    {
        $state = $this->getOrCreateState($phone);

        \Log::info('ConversationManager::handleMessage', [
            'phone' => $phone,
            'message' => $message,
            'buttonId' => $buttonId,
            'current_state' => $state->current_state,
        ]);

        match ($state->current_state) {
            'welcome' => $this->handleWelcome($state, $phone),
            'triage_start' => $this->handleTriageStart($state, $phone, $message),
            'triage_temp' => $this->handleTriageTemp($state, $phone, $message),
            'triage_pain' => $this->handleTriagePain($state, $phone, $message),
            'triage_dangerous' => $this->handleTriageDangerous($state, $phone, $message, $buttonId),
            'triage_chronic' => $this->handleTriageChronic($state, $phone, $message, $buttonId),
            'triage_result' => $this->handleTriageResult($state, $phone),
            'select_clinic' => $this->handleSelectClinic($state, $phone, $message, $buttonId),
            'ask_patient_name' => $this->handleAskPatientName($state, $phone, $message),
            'confirm_booking' => $this->handleConfirmBooking($state, $phone, $message, $buttonId),
            default => $this->handleDefault($state, $phone,  $buttonId),
        };
    }

    /**
     * Get or create conversation state
     */
    protected function getOrCreateState(string $phone): ConversationState
    {
        return ConversationState::firstOrCreate(
            ['phone' => $phone],
            [
                'current_state' => 'welcome',
                'context' => [],
                'expires_at' => now()->addHours(2),
            ]
        );
    }

    /**
     * Handle welcome state
     */
    protected function handleWelcome(ConversationState $state, string $phone): void
    {
        $this->whatsapp->sendButtons(
            $phone,
            "أهلاً بك في الشفاء الذكي 👋\nسأساعدك في تقييم حالتك وحجز موعد الكشف.",
            [
                'start_triage' => 'ابدأ التقييم 🩺',
                'direct_booking' => 'حجز مباشر 📅',
                'support' => 'تحدث مع الدعم 💬',
            ]
        );

        $state->transitionTo('triage_start');
    }

    /**
     * Handle triage start
     */
    protected function handleTriageStart(ConversationState $state, string $phone, string $message): void
    {
        $this->whatsapp->sendButtons(
            $phone,
            'ما درجة حرارتك الآن؟ 🌡️',
            [
                'temp_low' => 'أقل من 38',
                'temp_medium' => '38 - 39',
                'temp_high' => 'أكثر من 39',
            ]
        );

        $state->transitionTo('triage_temp');
    }

    /**
     * Handle triage temperature
     */
    protected function handleTriageTemp(ConversationState $state, string $phone, string $message): void
    {
        $temp = match ($message) {
            'temp_low' => 37.5,
            'temp_medium' => 38.5,
            'temp_high' => 39.5,
            default => null,
        };

        $state->updateContext(['temperature' => $temp]);

        $this->whatsapp->sendButtons(
            $phone,
            'كيف تصف مستوى الألم من 1 إلى 10؟ 🤕',
            [
                'pain_low' => '1-3 خفيف',
                'pain_medium' => '4-6 متوسط',
                'pain_high' => '7-10 شديد',
            ]
        );

        $state->transitionTo('triage_pain');
    }

    /**
     * Handle triage pain
     */
    protected function handleTriagePain(ConversationState $state, string $phone, string $message): void
    {
        $pain = match ($message) {
            'pain_low' => 2,
            'pain_medium' => 5,
            'pain_high' => 8,
            default => 0,
        };

        $state->updateContext(['pain_level' => $pain]);

        $this->whatsapp->sendButtons(
            $phone,
            'هل تعاني من أي من الأعراض التالية؟',
            [
                'symp_chest' => 'ألم صدر 💔',
                'symp_breathing' => 'ضيق تنفس 😮‍💨',
                'symp_none' => 'لا يوجد ✅',
            ]
        );

        $state->transitionTo('triage_dangerous');
    }

    /**
     * Handle dangerous symptoms
     */
    protected function handleTriageDangerous(ConversationState $state, string $phone, string $message, ?string $buttonId): void
    {
        $symptoms = $state->context['dangerous_symptoms'] ?? [];

        if ($buttonId !== 'symp_none') {
            $symptomMap = [
                'symp_chest' => 'chest_pain',
                'symp_breathing' => 'difficulty_breathing',
            ];

            if (isset($symptomMap[$buttonId])) {
                $symptoms[] = $symptomMap[$buttonId];
            }
        }

        $state->updateContext(['dangerous_symptoms' => $symptoms]);

        $this->whatsapp->sendButtons(
            $phone,
            'هل لديك أمراض مزمنة؟',
            [
                'chronic_heart' => 'قلب ❤️',
                'chronic_diabetes' => 'سكري 🩸',
                'chronic_none' => 'لا يوجد ✅',
            ]
        );

        $state->transitionTo('triage_chronic');
    }

    /**
     * Handle chronic diseases
     */
    protected function handleTriageChronic(ConversationState $state, string $phone, string $message, ?string $buttonId): void
    {
        $diseases = [];

        if ($buttonId !== 'chronic_none') {
            $diseaseMap = [
                'chronic_heart' => 'heart',
                'chronic_diabetes' => 'diabetes',
            ];

            if (isset($diseaseMap[$buttonId])) {
                $diseases[] = $diseaseMap[$buttonId];
            }
        }

        $state->updateContext(['chronic_diseases' => $diseases]);

        // Create triage assessment
        $patient = $this->getOrCreatePatient($state->phone);
        $assessment = $this->triage->createAssessment($patient, $state->context);

        // Show result
        $severity = $assessment->severity_level;
        $icon = match ($severity) {
            'high' => '🔴',
            'medium' => '🟡',
            default => '🟢',
        };

        $message = "{$icon} تقييم الحالة: {$severity->label()}\n\n";
        $message .= $assessment->ai_recommendation . "\n\n";

        if ($assessment->isHighPriority()) {
            $message .= '⚠️ سيتم إعطاؤك أولوية في الحجز.';
        }

        $this->whatsapp->sendButtons(
            $phone,
            $message,
            [
                'book_now' => 'حجز عاجل 🚑',
                'view_clinics' => 'اختيار عيادة 🏥',
            ]
        );

        $state->updateContext(['triage_id' => $assessment->id]);
        $state->transitionTo('triage_result');
    }

    /**
     * Handle triage result
     */
    protected function handleTriageResult(ConversationState $state, string $phone): void
    {
        // Get available clinics
        $clinics = $this->booking->getAvailableClinics();

        \Log::info('handleTriageResult called', [
            'phone' => $phone,
            'clinics_count' => count($clinics),
            'clinics' => $clinics,
        ]);

        if (empty($clinics)) {
            $this->whatsapp->sendMessage(
                $phone,
                '😔 عذراً، لا توجد عيادات متاحة حالياً. يرجى المحاولة لاحقاً.'
            );

            return;
        }

        // Build clinic list message
        $message = "📍 *العيادات المتاحة:*\n\n";
        
        foreach ($clinics as $index => $clinic) {
            $emergency = $clinic['accepts_emergency'] ? '🚑 ' : '';
            $message .= "*{$index}.* {$emergency}*{$clinic['name']}*\n";
            $message .= "   👥 عدد المنتظرين: {$clinic['waiting_count']}\n";
            $message .= "   ⏱ الوقت المتوقع: {$clinic['estimated_wait']} دقيقة\n";
            $message .= "   📍 {$clinic['address']}\n\n";
        }

        $message .= "💬 *للحجز:* اكتب رقم العيادة (0، 1، 2...) أو اسم العيادة";

        $this->whatsapp->sendMessage($phone, $message);

        // Store clinics in context for later reference
        $state->updateContext(['available_clinics' => $clinics]);
        $state->transitionTo('select_clinic');
    }

    /**
     * Handle clinic selection
     */
    protected function handleSelectClinic(ConversationState $state, string $phone, string $message, ?string $buttonId = null): void
    {
        $clinicId = null;

        // Try to extract clinic ID from button/list selection
        if ($buttonId && str_starts_with($buttonId, 'clinic_')) {
            $clinicId = (int) str_replace('clinic_', '', $buttonId);
        }
        // Try to match text input (number or clinic name)
        else {
            $availableClinics = $state->context['available_clinics'] ?? [];
            
            if (empty($availableClinics)) {
                // Re-fetch clinics if not in context
                $availableClinics = $this->booking->getAvailableClinics();
                $state->updateContext(['available_clinics' => $availableClinics]);
            }

            // Check if message is a number (index)
            if (is_numeric($message)) {
                $index = (int) $message;
                if (isset($availableClinics[$index])) {
                    $clinicId = $availableClinics[$index]['id'];
                }
            }
            // Try to match clinic name
            else {
                foreach ($availableClinics as $clinic) {
                    if (stripos($clinic['name'], $message) !== false) {
                        $clinicId = $clinic['id'];
                        break;
                    }
                }
            }

            if (!$clinicId) {
                $this->whatsapp->sendMessage(
                    $phone,
                    "❌ لم أتمكن من فهم اختيارك.\n\nرد برقم العيادة (0، 1، 2...) أو اسم العيادة."
                );
                return;
            }
        }

        $clinic = Clinic::find($clinicId);

        if (! $clinic) {
            $this->whatsapp->sendMessage($phone, '❌ العيادة غير موجودة. يرجى المحاولة مرة أخرى.');

            return;
        }

        // Store clinic selection
        $state->updateContext(['clinic_id' => $clinicId]);

        // Ask for patient name
        $this->whatsapp->sendMessage(
            $phone,
            "✅ تم اختيار: {$clinic->name}\n\n" .
            "📝 من فضلك، أدخل اسمك الكامل لتأكيد الحجز:"
        );

        $state->transitionTo('ask_patient_name');
    }

    /**
     * Handle patient name input
     */
    protected function handleAskPatientName(ConversationState $state, string $phone, string $message): void
    {
        $name = trim($message);

        // Validate name
        if (empty($name) || strlen($name) < 3) {
            $this->whatsapp->sendMessage(
                $phone,
                "❌ الاسم قصير جداً. من فضلك أدخل اسمك الكامل (3 أحرف على الأقل):"
            );
            return;
        }

        // Store patient name
        $state->updateContext(['patient_name' => $name]);

        // Get clinic details
        $clinic = Clinic::find($state->context['clinic_id']);

        if (!$clinic) {
            $this->whatsapp->sendMessage($phone, '❌ حدث خطأ. يرجى البدء من جديد.');
            $state->reset();
            return;
        }

        // Show full confirmation with all details
        $confirmMessage = "📋 *تأكيد بيانات الحجز*\n";
        $confirmMessage .= "━━━━━━━━━━━━━━━━━\n\n";
        $confirmMessage .= "👤 *الاسم:* {$name}\n";
        $confirmMessage .= "📱 *الهاتف:* +{$phone}\n\n";
        $confirmMessage .= "🏥 *العيادة:* {$clinic->name}\n";
        $confirmMessage .= "📍 *العنوان:* {$clinic->address}\n";
        $confirmMessage .= "📞 *هاتف العيادة:* {$clinic->phone}\n\n";
        
        $waitingCount = $clinic->queueEntries()
            ->whereIn('status', [\App\Enums\QueueStatus::Waiting, \App\Enums\QueueStatus::Called])
            ->count();
        
        $confirmMessage .= "⏰ *المرضى في الانتظار:* {$waitingCount}\n";
        $confirmMessage .= "⏱️ *الوقت المتوقع:* " . ($waitingCount * 15) . " دقيقة\n\n";
        $confirmMessage .= "━━━━━━━━━━━━━━━━━\n";
        $confirmMessage .= "هل تريد تأكيد الحجز؟";

        $this->whatsapp->sendButtons(
            $phone,
            $confirmMessage,
            [
                'confirm_booking' => 'تأكيد الحجز ✅',
                'change_name' => 'تعديل الاسم ✏️',
                'cancel' => 'إلغاء ❌',
            ]
        );

        $state->transitionTo('confirm_booking');
    }

    /**
     * Handle booking confirmation
     */
    protected function handleConfirmBooking(ConversationState $state, string $phone, string $message, ?string $buttonId): void
    {
        if ($buttonId === 'change_clinic') {
            // Go back to clinic selection
            $this->handleTriageResult($state, $phone);
            return;
        }

        if ($buttonId === 'change_name') {
            // Go back to ask name
            $this->whatsapp->sendMessage(
                $phone,
                "📝 من فضلك، أدخل اسمك الكامل:"
            );
            $state->transitionTo('ask_patient_name');
            return;
        }

        if ($buttonId === 'cancel') {
            $this->whatsapp->sendMessage($phone, '❌ تم إلغاء الحجز.');
            $state->reset();

            return;
        }

        if ($buttonId === 'confirm_booking') {
            try {
                // Get or create patient with name
                $patientName = $state->context['patient_name'] ?? null;
                $patient = $this->getOrCreatePatient($phone, $patientName);
                $clinic = Clinic::find($state->context['clinic_id']);

                if (! $clinic) {
                    $this->whatsapp->sendMessage($phone, '❌ حدث خطأ. العيادة غير موجودة.');

                    return;
                }

                // Book appointment
                $appointment = $this->booking->bookAppointment(
                    patient: $patient,
                    clinic: $clinic,
                    triageId: $state->context['triage_id'] ?? null
                );

                // Get appointment details
                $details = $this->booking->getAppointmentDetails($appointment);

                // Send confirmation
                $this->whatsapp->sendBookingConfirmation($phone, $details);

                // Additional info
                $this->whatsapp->sendButtons(
                    $phone,
                    "يمكنك الآن:\n" .
                    "• متابعة الطابور بشكل لحظي\n" .
                    '• استقبال تنبيهات عند اقتراب دورك',
                    [
                        'track_queue' => 'تتبع الطابور 📊',
                        'new_booking' => 'حجز جديد 🆕',
                    ]
                );

                // Reset conversation
                $state->reset();
            } catch (\Exception $e) {
                logger()->error('Booking failed', [
                    'phone' => $phone,
                    'error' => $e->getMessage(),
                ]);

                $this->whatsapp->sendMessage(
                    $phone,
                    '❌ عذراً، حدث خطأ أثناء الحجز. يرجى المحاولة مرة أخرى.'
                );

                $state->reset();
            }
        }
    }

    /**
     * Handle default/unknown state
     */
    protected function handleDefault(ConversationState $state, string $phone, ?string $buttonId = null): void
    {
        // Handle special commands
        if ($buttonId === 'track_queue') {
            $this->handleTrackQueue($state, $phone);

            return;
        }

        if ($buttonId === 'new_booking') {
            $state->reset();
            $this->handleWelcome($state, $phone);

            return;
        }

        // Default to welcome
        $this->handleWelcome($state, $phone);
    }

    /**
     * Handle queue tracking request
     */
    protected function handleTrackQueue(ConversationState $state, string $phone): void
    {
        $patient = Patient::where('phone', $phone)->first();

        if (! $patient) {
            $this->whatsapp->sendMessage($phone, '❌ لم نجد لك حجوزات حالية.');

            return;
        }

        $queueService = app(\App\Services\Queue\QueueManagementService::class);
        $position = $queueService->getPatientQueuePosition($phone);

        if (! $position) {
            $this->whatsapp->sendMessage($phone, '📋 ليس لديك حجوزات في الطابور حالياً.');

            return;
        }

        $message = "📊 موقعك في الطابور:\n\n";
        $message .= "🏥 العيادة: {$position['clinic_name']}\n";
        $message .= "🔢 رقمك في الطابور: {$position['position']}\n";
        $message .= "👥 عدد المرضى قبلك: {$position['people_ahead']}\n";
        $message .= "⏱ الوقت المتوقع: {$position['estimated_wait']} دقيقة\n";

        $this->whatsapp->sendMessage($phone, $message);
    }

    /**
     * Get or create patient
     */
    protected function getOrCreatePatient(string $phone, ?string $name = null): Patient
    {
        $patient = Patient::where('phone', $phone)->first();
        
        if ($patient) {
            // Update existing patient
            $updateData = ['last_whatsapp_interaction' => now()];
            if ($name) {
                $updateData['name'] = $name;
            }
            $patient->update($updateData);
        } else {
            // Create new patient
            $patient = Patient::create([
                'phone' => $phone,
                'name' => $name,
                'last_whatsapp_interaction' => now(),
            ]);
        }
        
        return $patient;
    }
}
