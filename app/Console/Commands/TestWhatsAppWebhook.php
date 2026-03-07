<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestWhatsAppWebhook extends Command
{
    protected $signature = 'whatsapp:test-webhook {--url=}';
    protected $description = 'Test WhatsApp webhook endpoint';

    public function handle(): int
    {
        $url = $this->option('url') ?: config('app.url') . '/api/webhooks/whatsapp';

        $this->info("Testing webhook at: {$url}");
        $this->newLine();

        // Test 1: Verify endpoint (GET)
        $this->info('1️⃣ Testing verification endpoint...');
        
        $verifyToken = config('services.whatsapp.verify_token');
        
        try {
            $response = Http::get($url, [
                'hub_mode' => 'subscribe',
                'hub_verify_token' => $verifyToken,
                'hub_challenge' => 'test_challenge_12345',
            ]);

            if ($response->successful() && $response->body() === 'test_challenge_12345') {
                $this->info('   ✅ Verification endpoint working!');
            } else {
                $this->error('   ❌ Verification failed');
                $this->error('   Status: ' . $response->status());
                $this->error('   Body: ' . $response->body());
            }
        } catch (\Exception $e) {
            $this->error('   ❌ Error: ' . $e->getMessage());
        }

        $this->newLine();

        // Test 2: Send test message (POST)
        $this->info('2️⃣ Testing message endpoint...');
        
        $testPayload = [
            'object' => 'whatsapp_business_account',
            'entry' => [
                [
                    'id' => 'test_entry_id',
                    'changes' => [
                        [
                            'value' => [
                                'messaging_product' => 'whatsapp',
                                'metadata' => [
                                    'display_phone_number' => '15551535723',
                                    'phone_number_id' => config('services.whatsapp.phone_id'),
                                ],
                                'messages' => [
                                    [
                                        'from' => '201234567890',
                                        'id' => 'test_message_id_' . time(),
                                        'timestamp' => time(),
                                        'type' => 'text',
                                        'text' => [
                                            'body' => 'مرحباً - رسالة اختبار'
                                        ]
                                    ]
                                ]
                            ],
                            'field' => 'messages',
                        ]
                    ]
                ]
            ]
        ];

        try {
            $response = Http::post($url, $testPayload);

            if ($response->successful()) {
                $this->info('   ✅ Message endpoint working!');
                $this->info('   Response: ' . $response->body());
            } else {
                $this->error('   ❌ Message endpoint failed');
                $this->error('   Status: ' . $response->status());
                $this->error('   Body: ' . $response->body());
            }
        } catch (\Exception $e) {
            $this->error('   ❌ Error: ' . $e->getMessage());
        }

        $this->newLine();

        // Test 3: Check logs
        $this->info('3️⃣ Recent webhook logs:');
        $logFile = storage_path('logs/laravel.log');
        
        if (file_exists($logFile)) {
            $logs = file($logFile);
            $recentLogs = array_slice($logs, -10);
            
            foreach ($recentLogs as $log) {
                if (str_contains($log, 'WhatsApp')) {
                    $this->line('   ' . trim($log));
                }
            }
        }

        $this->newLine();
        $this->info('✅ Test completed!');
        $this->newLine();
        
        // Instructions
        $this->warn('📋 Next steps:');
        $this->line('1. If verification passed, use this URL in Meta:');
        $this->line("   {$url}");
        $this->line('2. Verify Token: ' . $verifyToken);
        $this->line('3. Subscribe to: messages, message_status');

        return self::SUCCESS;
    }
}
