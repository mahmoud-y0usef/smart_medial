<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GeminiService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta';

    public function __construct()
    {
        $this->apiKey = config('services.google_ai.api_key');
    }

    /**
     * Generate content using Gemini Flash
     */
    public function generateContent(string $prompt, array $options = []): ?string
    {
        // Use Gemini 2.5 Flash (2026 latest model)
        $model = $options['model'] ?? 'gemini-2.5-flash';
        $temperature = $options['temperature'] ?? 0.7;
        $maxTokens = $options['max_tokens'] ?? 1000;

        try {
            $response = Http::timeout(30)
                ->post("{$this->baseUrl}/models/{$model}:generateContent?key={$this->apiKey}", [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => $temperature,
                        'maxOutputTokens' => $maxTokens,
                    ],
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
            }

            \Log::error('Gemini API Error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return null;
        } catch (\Exception $e) {
            \Log::error('Gemini API Exception', [
                'message' => $e->getMessage()
            ]);

            return null;
        }
    }

    /**
     * Get medical consultation response
     */
    public function getMedicalConsultation(string $symptoms, string $patientInfo = ''): ?string
    {
        $prompt = "أنت طبيب مساعد ذكي. المريض يشتكي من: {$symptoms}";
        
        if ($patientInfo) {
            $prompt .= "\n\nمعلومات المريض: {$patientInfo}";
        }

        $prompt .= "\n\nقدم تقييماً طبياً أولياً واقتراحات للتعامل مع الحالة.";

        return $this->generateContent($prompt, [
            'temperature' => 0.7,
            'max_tokens' => 1500,
        ]);
    }

    /**
     * Check if API is working
     */
    public function test(): bool
    {
        $response = $this->generateContent('مرحباً، هل تعمل؟');
        return $response !== null;
    }
}
