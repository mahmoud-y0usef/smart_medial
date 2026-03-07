<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Medical Platform Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration options for Smart Medical Platform (الشفاء الذكي)
    |
    */

    /**
     * Queue management settings
     */
    'queue' => [
        'update_interval' => env('MEDICAL_QUEUE_UPDATE_INTERVAL', 300), // seconds
        'notification_before_turn' => 10, // minutes
        'urgent_notification_before_turn' => 3, // minutes
    ],

    /**
     * Consultation settings
     */
    'consultation' => [
        'default_duration' => env('MEDICAL_DEFAULT_CONSULTATION_DURATION', 15), // minutes
        'max_audio_size' => 50 * 1024, // KB (50MB)
        'allowed_audio_formats' => ['mp3', 'wav', 'ogg', 'm4a', 'webm'],
    ],

    /**
     * Triage settings
     */
    'triage' => [
        'emergency_threshold' => env('MEDICAL_EMERGENCY_PRIORITY_THRESHOLD', 8),
        'high_priority_min' => 8,
        'medium_priority_min' => 4,
        'low_priority_min' => 1,
        
        'dangerous_symptoms' => [
            'chest_pain',
            'difficulty_breathing',
            'loss_consciousness',
            'severe_bleeding',
        ],
        
        'scoring_weights' => [
            'temperature' => 2,
            'pain_level' => 1.5,
            'dangerous_symptoms' => 3,
            'chronic_disease_interaction' => 2,
        ],
    ],

    /**
     * Prescription settings
     */
    'prescription' => [
        'qr_expiry_days' => 30,
        'allow_multiple_dispense' => false,
        'signature_algorithm' => 'sha256',
    ],

    /**
     * Pharmacy settings
     */
    'pharmacy' => [
        'max_search_radius_km' => 10,
        'default_search_radius_km' => 5,
        'delivery_enabled' => true,
    ],

    /**
     * Approval workflow
     */
    'approval' => [
        'auto_approve' => false,
        'required_documents' => ['license'],
        'license_max_size' => 5120, // KB (5MB)
        'allowed_license_formats' => ['pdf', 'jpg', 'jpeg', 'png'],
    ],

    /**
     * AI Provider settings
     */
    'ai' => [
        'primary_provider' => env('AI_PRIMARY_PROVIDER', 'gemini'), // gemini, groq, ollama, openai, anthropic
        'fallback_provider' => env('AI_FALLBACK_PROVIDER', 'groq'),
        'max_retries' => 2,
        'timeout' => 30, // seconds
        
        'models' => [
            'gemini' => env('GEMINI_MODEL', 'gemini-1.5-flash-latest'),
            'groq' => env('GROQ_MODEL', 'llama-3.1-70b-versatile'),
            'ollama' => env('OLLAMA_MODEL', 'aya:8b'),
            'openai' => 'gpt-4-turbo-preview',
            'anthropic' => 'claude-3-sonnet-20240229',
            'whisper' => 'whisper-1',
        ],
    ],

    /**
     * Rate limiting
     */
    'rate_limits' => [
        'whatsapp' => [
            'per_phone' => 60, // per minute
        ],
        'api' => [
            'per_user' => 100, // per minute
        ],
        'ai' => [
            'per_clinic' => 20, // per minute
        ],
        'booking' => [
            'per_patient' => 10, // per minute
        ],
    ],

];
