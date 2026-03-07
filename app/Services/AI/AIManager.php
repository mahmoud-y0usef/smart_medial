<?php

namespace App\Services\AI;

use App\Enums\Consultation\AIProvider;
use CloudStudio\Ollama\Facades\Ollama;
use Exception;
use Gemini;
use Groq;
use OpenAI\Laravel\Facades\OpenAI;

/**
 * AI Manager - Multi-Provider Support
 *
 * Supports multiple AI providers:
 * - Google Gemini (FREE) - composer require google/generative-ai-php
 * - Groq (FREE) - composer require groq-php/groq-php
 * - Ollama (FREE + Local) - composer require cloudstudio/ollama-laravel
 * - OpenAI (Paid) - already installed
 * - Anthropic (Paid) - composer require anthropic-ai/php-sdk
 *
 * @see https://ai.google.dev/gemini-api/docs
 * @see https://console.groq.com/docs
 */
class AIManager
{
    /**
     * Extract medical notes from transcript using AI
     */
    public function extractMedicalNotes(string $transcript, ?AIProvider $provider = null): array
    {
        $provider ??= AIProvider::from(config('medical.ai.primary_provider'));

        try {
            return $this->extractWithProvider($transcript, $provider);
        } catch (Exception $e) {
            // Fallback to secondary provider
            $fallbackProvider = AIProvider::from(config('medical.ai.fallback_provider'));

            if ($fallbackProvider !== $provider) {
                return $this->extractWithProvider($transcript, $fallbackProvider);
            }

            throw $e;
        }
    }

    /**
     * Extract using specific provider
     */
    protected function extractWithProvider(string $transcript, AIProvider $provider): array
    {
        return match ($provider) {
            AIProvider::OpenAI => $this->extractWithOpenAI($transcript),
            AIProvider::Anthropic => $this->extractWithAnthropic($transcript),
            AIProvider::Gemini => $this->extractWithGemini($transcript),
            AIProvider::Groq => $this->extractWithGroq($transcript),
            AIProvider::Ollama => $this->extractWithOllama($transcript),
        };
    }

    /**
     * Extract using OpenAI
     */
    protected function extractWithOpenAI(string $transcript): array
    {
        $prompt = $this->buildExtractionPrompt($transcript);

        $response = OpenAI::chat()->create([
            'model' => config('medical.ai.models.openai'),
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a medical AI assistant that extracts structured information from doctor-patient consultations in Arabic. Always respond with valid JSON only.',
                ],
                [
                    'role' => 'user',
                    'content' => $prompt,
                ],
            ],
            'temperature' => 0.3,
            'response_format' => ['type' => 'json_object'],
        ]);

        $content = $response->choices[0]->message->content;

        return json_decode($content, true);
    }

    /**
     * Extract using Anthropic Claude
     */
    protected function extractWithAnthropic(string $transcript): array
    {
        // Implementation for Anthropic API
        // Note: Would need anthropic-sdk-php package

        throw new Exception('Anthropic provider not yet implemented. Install: composer require anthropic-ai/php-sdk');
    }

    /**
     * Extract using Google Gemini (FREE!)
     */
    protected function extractWithGemini(string $transcript): array
    {
        if (! class_exists(Gemini::class)) {
            throw new Exception('Gemini not installed. Run: composer require google/generative-ai-php');
        }

        $prompt = $this->buildExtractionPrompt($transcript);

        $client = Gemini::client(config('services.google_ai.api_key'));

        $response = $client->geminiFlash()->generateContent($prompt);

        $content = $response->text();

        // Extract JSON from markdown code blocks if present
        if (preg_match('/```json\s*(.*?)\s*```/s', $content, $matches)) {
            $content = $matches[1];
        } elseif (preg_match('/```\s*(.*?)\s*```/s', $content, $matches)) {
            $content = $matches[1];
        }

        return json_decode($content, true) ?? [];
    }

    /**
     * Extract using Groq (FREE + FAST!)
     */
    protected function extractWithGroq(string $transcript): array
    {
        if (! function_exists('groq')) {
            throw new Exception('Groq not installed. Run: composer require groq-php/groq-php');
        }

        $prompt = $this->buildExtractionPrompt($transcript);

        $client = Groq::client(config('services.groq.api_key'));

        $response = $client->chat()->create([
            'model' => config('medical.ai.models.groq'),
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a medical AI assistant that extracts structured information from doctor-patient consultations in Arabic. Always respond with valid JSON only.',
                ],
                [
                    'role' => 'user',
                    'content' => $prompt,
                ],
            ],
            'temperature' => 0.3,
            'response_format' => ['type' => 'json_object'],
        ]);

        $content = $response->choices[0]->message->content;

        return json_decode($content, true) ?? [];
    }

    /**
     * Extract using Ollama (FREE + LOCAL!)
     */
    protected function extractWithOllama(string $transcript): array
    {
        if (! class_exists(Ollama::class)) {
            throw new Exception('Ollama not installed. Run: composer require cloudstudio/ollama-laravel');
        }

        $prompt = $this->buildExtractionPrompt($transcript);

        $ollama = app(Ollama::class);

        $response = $ollama->prompt($prompt)
            ->model(config('medical.ai.models.ollama'))
            ->options(['temperature' => 0.3])
            ->generate();

        $content = $response['response'] ?? '';

        // Extract JSON from response
        if (preg_match('/```json\s*(.*?)\s*```/s', $content, $matches)) {
            $content = $matches[1];
        } elseif (preg_match('/\{.*\}/s', $content, $matches)) {
            $content = $matches[0];
        }

        return json_decode($content, true) ?? [];
    }

    /**
     * Build extraction prompt
     */
    protected function buildExtractionPrompt(string $transcript): string
    {
        return <<<PROMPT
Extract the following information from this medical consultation transcript in Arabic:

Transcript:
{$transcript}

Extract and return JSON with these fields:
- chief_complaint: The main reason for visit (in Arabic)
- examination: Physical examination findings (in Arabic)
- diagnosis: The medical diagnosis (in Arabic)
- treatment_plan: Recommended treatment and medications (in Arabic)

Return ONLY valid JSON, no additional text.
PROMPT;
    }

    /**
     * Transcribe audio using Whisper
     */
    public function transcribeAudio(string $audioPath): string
    {
        $response = OpenAI::audio()->transcribe([
            'model' => config('medical.ai.models.whisper'),
            'file' => fopen($audioPath, 'r'),
            'language' => 'ar',
        ]);

        return $response->text;
    }

    /**
     * Generate prescription from treatment plan
     */
    public function generatePrescription(string $treatmentPlan): array
    {
        $prompt = <<<PROMPT
من خطة العلاج التالية، استخرج الأدوية بصيغة منظمة:

{$treatmentPlan}

أرجع JSON فقط يحتوي على مصفوفة medicines:
[
  {
    "medicine_name": "اسم الدواء",
    "dosage": "الجرعة",
    "frequency": "عدد المرات",
    "duration_days": رقم,
    "instructions": "تعليمات الاستخدام"
  }
]
PROMPT;

        $response = OpenAI::chat()->create([
            'model' => config('medical.ai.models.openai'),
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a medical AI that extracts medication information from Arabic treatment plans. Return JSON only.',
                ],
                [
                    'role' => 'user',
                    'content' => $prompt,
                ],
            ],
            'temperature' => 0.2,
            'response_format' => ['type' => 'json_object'],
        ]);

        $content = $response->choices[0]->message->content;
        $data = json_decode($content, true);

        return $data['medicines'] ?? [];
    }
}
