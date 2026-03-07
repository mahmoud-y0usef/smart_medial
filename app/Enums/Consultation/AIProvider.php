<?php

namespace App\Enums\Consultation;

enum AIProvider: string
{
    case OpenAI = 'openai';
    case Anthropic = 'anthropic';
    case Gemini = 'gemini';
    case Groq = 'groq';
    case Ollama = 'ollama';

    public function label(): string
    {
        return match ($this) {
            self::OpenAI => 'OpenAI GPT-4',
            self::Anthropic => 'Anthropic Claude',
            self::Gemini => 'Google Gemini Flash',
            self::Groq => 'Groq Llama 3',
            self::Ollama => 'Ollama (Local)',
        };
    }
}
