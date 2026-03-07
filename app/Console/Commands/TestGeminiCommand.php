<?php

namespace App\Console\Commands;

use App\Services\GeminiService;
use Illuminate\Console\Command;

class TestGeminiCommand extends Command
{
    protected $signature = 'gemini:test {prompt=Hello}';
    protected $description = 'Test Google Gemini AI integration';

    public function handle(GeminiService $gemini): int
    {
        $prompt = $this->argument('prompt');

        $this->info("Testing Gemini AI with prompt: {$prompt}");
        $this->newLine();

        $response = $gemini->generateContent($prompt);

        if ($response) {
            $this->info('✅ Success! Response:');
            $this->line($response);
            return self::SUCCESS;
        }

        $this->error('❌ Failed to get response from Gemini API');
        $this->warn('Check logs for details: storage/logs/laravel.log');
        
        return self::FAILURE;
    }
}
