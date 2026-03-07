<?php

namespace App\Services\WhatsApp;

use Netflie\WhatsAppCloudApi\Message\ButtonReply\Button;
use Netflie\WhatsAppCloudApi\Message\ButtonReply\ButtonAction;
use Netflie\WhatsAppCloudApi\WhatsAppCloudApi;

class MetaWhatsAppService
{
    protected WhatsAppCloudApi $client;

    public function __construct()
    {
        $this->client = new WhatsAppCloudApi([
            'from_phone_number_id' => config('services.whatsapp.phone_id'),
            'access_token' => config('services.whatsapp.token'),
        ]);
    }

    /**
     * Send text message
     */
    public function sendMessage(string $to, string $message): mixed
    {
        return $this->client->sendTextMessage($to, $message);
    }

    /**
     * Send interactive buttons
     */
    public function sendButtons(string $to, string $bodyText, array $buttons): mixed
    {
        $buttonObjects = [];
        foreach ($buttons as $id => $title) {
            $buttonObjects[] = new Button($id, $title);
        }

        $action = new ButtonAction($buttonObjects);

        return $this->client->sendButton($to, $bodyText, $action);
    }

    /**
     * Send interactive list
     */
    public function sendList(string $to, string $bodyText, string $buttonText, array $sections): mixed
    {
        \Log::info('MetaWhatsAppService::sendList called', [
            'to' => $to,
            'bodyText' => $bodyText,
            'buttonText' => $buttonText,
            'sections_count' => count($sections),
            'sections' => $sections,
        ]);

        // Lists require specific Action/Section/Row classes
        // For now, fallback to text message with options listed
        $message = $bodyText . "\n\n";
        
        foreach ($sections as $section) {
            if (isset($section['title'])) {
                $message .= "*" . $section['title'] . "*\n";
            }
            
            if (isset($section['rows'])) {
                foreach ($section['rows'] as $row) {
                    $message .= "• " . $row['title'];
                    if (isset($row['description'])) {
                        $message .= ": " . $row['description'];
                    }
                    $message .= "\n";
                }
            }
            
            $message .= "\n";
        }
        
        $message .= "رد برقم الخيار أو النص.";
        
        \Log::info('MetaWhatsAppService::sendList formatted message', [
            'message' => $message,
        ]);

        return $this->sendMessage($to, $message);
    }

    /**
     * Send template message
     */
    public function sendTemplate(string $to, string $templateName, array $components = []): mixed
    {
        return $this->client->sendTemplate($to, $templateName, 'ar', $components);
    }

    /**
     * Mark message as read
     */
    public function markAsRead(string $messageId): mixed
    {
        return $this->client->markMessageAsRead($messageId);
    }

    /**
     * Send booking confirmation
     */
    public function sendBookingConfirmation(string $to, array $data): mixed
    {
        $message = "✅ *تم تأكيد حجزك بنجاح*\n";
        $message .= "━━━━━━━━━━━━━━━━━\n\n";
        $message .= "🏥 *العيادة:* {$data['clinic_name']}\n";
        $message .= "📍 *العنوان:* {$data['clinic_address']}\n";
        $message .= "📅 *التاريخ:* {$data['date']}\n";
        $message .= "🕐 *الوقت:* {$data['time']}\n";
        $message .= "🔢 *رقمك في الطابور:* {$data['queue_number']}\n";
        $message .= "⏱ *الوقت المتوقع:* {$data['estimated_wait']} دقيقة\n\n";
        $message .= "━━━━━━━━━━━━━━━━━\n";
        $message .= "يمكنك متابعة الطابور عبر الرابط:\n";
        $message .= $data['tracking_url'];

        return $this->sendMessage($to, $message);
    }

    /**
     * Send queue update notification
     */
    public function sendQueueUpdate(string $to, int $position, int $estimatedWait): mixed
    {
        $message = "📊 تحديث الطابور\n\n";
        $message .= "عدد المرضى قبلك: {$position}\n";
        $message .= "الوقت المتوقع: {$estimatedWait} دقيقة";

        return $this->sendMessage($to, $message);
    }

    /**
     * Send "your turn soon" notification
     */
    public function sendYourTurnSoon(string $to, int $minutes): mixed
    {
        $message = "⏰ تنبيه: دورك بعد {$minutes} دقائق\n\n";
        $message .= 'يرجى التأكد من وجودك في العيادة.';

        return $this->sendMessage($to, $message);
    }

    /**
     * Send "your turn now" notification
     */
    public function sendYourTurnNow(string $to): mixed
    {
        $message = "🚶‍♂️ حان دورك الآن!\n\n";
        $message .= 'يرجى التوجه للطبيب فوراً.';

        return $this->sendMessage($to, $message);
    }

    /**
     * Send prescription ready notification
     */
    public function sendPrescriptionReady(string $to, array $data): mixed
    {
        $message = "💊 روشتتك جاهزة!\n\n";
        $message .= "رقم الروشتة: {$data['prescription_number']}\n";
        $message .= "الصيدلية: {$data['pharmacy_name']}\n";
        $message .= "العنوان: {$data['pharmacy_address']}\n\n";
        $message .= 'يمكنك استلام الأدوية الآن.';

        return $this->sendMessage($to, $message);
    }
}
