<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Chatbot\ConversationManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    public function __construct(
        protected ConversationManager $conversationManager
    ) {}

    /**
     * Verify webhook (GET request from Meta)
     */
    public function verify(Request $request)
    {
        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        $verifyToken = config('services.whatsapp.verify_token');

        if ($mode === 'subscribe' && $token === $verifyToken) {
            Log::info('WhatsApp webhook verified successfully', [
                'challenge' => $challenge,
            ]);

            // Return plain text response as Meta expects
            return response($challenge, 200)
                ->header('Content-Type', 'text/plain; charset=UTF-8');
        }

        Log::warning('WhatsApp webhook verification failed', [
            'mode' => $mode,
            'provided_token' => $token,
            'expected_token' => $verifyToken,
        ]);

        return response()->json(['error' => 'Forbidden'], 403);
    }

    /**
     * Handle webhook (POST request with messages)
     */
    public function handle(Request $request): JsonResponse
    {
        try {
            $data = $request->all();
            
            Log::info('WhatsApp webhook received', ['data' => $data]);

            // Extract message data
            $entry = $data['entry'][0] ?? null;
            $changes = $entry['changes'][0] ?? null;
            $value = $changes['value'] ?? null;

            if (! $value) {
                return response()->json(['status' => 'no_data'], 200);
            }

            // Handle messages
            if (isset($value['messages'])) {
                foreach ($value['messages'] as $message) {
                    $this->processMessage($message);
                }
            }

            // Handle status updates
            if (isset($value['statuses'])) {
                foreach ($value['statuses'] as $status) {
                    $this->processStatus($status);
                }
            }

            return response()->json(['status' => 'success'], 200);
        } catch (\Exception $e) {
            Log::error('WhatsApp webhook error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Always return 200 to prevent Meta from retrying
            return response()->json(['status' => 'error'], 200);
        }
    }

    /**
     * Process incoming message
     */
    protected function processMessage(array $message): void
    {
        $type = $message['type'] ?? 'text';
        $from = $message['from'];
        $messageId = $message['id'];

        // Handle different message types
        match ($type) {
            'text' => $this->handleTextMessage($from, $message['text']['body'], $messageId),
            'button' => $this->handleButtonReply($from, $message['button']['text'], $message['button']['id'], $messageId),
            'interactive' => $this->handleInteractiveReply($from, $message['interactive'], $messageId),
            default => Log::info('Unsupported message type', ['type' => $type]),
        };
    }

    /**
     * Handle text message
     */
    protected function handleTextMessage(string $from, string $text, string $messageId): void
    {
        // Pass to conversation manager
        $this->conversationManager->handleMessage($from, $text);
    }

    /**
     * Handle button reply
     */
    protected function handleButtonReply(string $from, string $text, string $buttonId, string $messageId): void
    {
        // Pass to conversation manager with button ID
        $this->conversationManager->handleMessage($from, $text, $buttonId);
    }

    /**
     * Handle interactive reply (list or button)
     */
    protected function handleInteractiveReply(string $from, array $interactive, string $messageId): void
    {
        $type = $interactive['type'];

        if ($type === 'button_reply') {
            $buttonId = $interactive['button_reply']['id'];
            $buttonText = $interactive['button_reply']['title'];
            $this->conversationManager->handleMessage($from, $buttonText, $buttonId);
        } elseif ($type === 'list_reply') {
            $listId = $interactive['list_reply']['id'];
            $listText = $interactive['list_reply']['title'];
            $this->conversationManager->handleMessage($from, $listText, $listId);
        }
    }

    /**
     * Process message status update
     */
    protected function processStatus(array $status): void
    {
        $messageId = $status['id'];
        $statusType = $status['status']; // sent, delivered, read, failed

        Log::info('Message status update', [
            'message_id' => $messageId,
            'status' => $statusType,
        ]);

        // You can store these statuses if needed for delivery tracking
    }
}
