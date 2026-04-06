<?php

use App\Enums\Consultation\AIProvider;
use App\Services\AI\AIManager;
use Tests\TestCase;

uses(TestCase::class);

$sampleTranscript = 'المريض يشكو من ألم في الرأس منذ يومين، درجة الحرارة 38، التشخيص صداع وتوتر عضلي، العلاج باراسيتامول 500 ملج 3 مرات يومياً.';

$expectedNotes = [
    'chief_complaint' => 'ألم في الرأس',
    'examination' => 'درجة الحرارة 38',
    'diagnosis' => 'صداع وتوتر عضلي',
    'treatment_plan' => 'باراسيتامول 500 ملج 3 مرات يومياً',
];

it('extracts medical notes from a transcript', function () use ($sampleTranscript, $expectedNotes) {
    $manager = Mockery::mock(AIManager::class)
        ->makePartial()
        ->shouldAllowMockingProtectedMethods();

    $manager->shouldReceive('extractWithProvider')
        ->once()
        ->withArgs(fn ($transcript, $provider) => $transcript === $sampleTranscript && $provider === AIProvider::Gemini)
        ->andReturn($expectedNotes);

    config(['medical.ai.primary_provider' => 'gemini']);

    $result = $manager->extractMedicalNotes($sampleTranscript);

    expect($result)
        ->toBeArray()
        ->toHaveKey('chief_complaint')
        ->toHaveKey('diagnosis')
        ->toHaveKey('treatment_plan')
        ->and($result['diagnosis'])->toBe('صداع وتوتر عضلي');
});

it('falls back to secondary provider when primary fails', function () use ($sampleTranscript, $expectedNotes) {
    $manager = Mockery::mock(AIManager::class)
        ->makePartial()
        ->shouldAllowMockingProtectedMethods();

    config([
        'medical.ai.primary_provider' => 'gemini',
        'medical.ai.fallback_provider' => 'groq',
    ]);

    $manager->shouldReceive('extractWithProvider')
        ->with($sampleTranscript, AIProvider::Gemini)
        ->once()
        ->andThrow(new Exception('Gemini API unavailable'));

    $manager->shouldReceive('extractWithProvider')
        ->with($sampleTranscript, AIProvider::Groq)
        ->once()
        ->andReturn($expectedNotes);

    $result = $manager->extractMedicalNotes($sampleTranscript);

    expect($result)
        ->toBeArray()
        ->toHaveKey('chief_complaint')
        ->and($result['treatment_plan'])->toBe('باراسيتامول 500 ملج 3 مرات يومياً');
});

it('uses explicitly passed provider instead of config', function () use ($sampleTranscript, $expectedNotes) {
    $manager = Mockery::mock(AIManager::class)
        ->makePartial()
        ->shouldAllowMockingProtectedMethods();

    $manager->shouldReceive('extractWithProvider')
        ->with($sampleTranscript, AIProvider::Groq)
        ->once()
        ->andReturn($expectedNotes);

    $result = $manager->extractMedicalNotes($sampleTranscript, AIProvider::Groq);

    expect($result['diagnosis'])->toBe('صداع وتوتر عضلي');
});

it('throws when both primary and fallback providers fail', function () use ($sampleTranscript) {
    $manager = Mockery::mock(AIManager::class)
        ->makePartial()
        ->shouldAllowMockingProtectedMethods();

    config([
        'medical.ai.primary_provider' => 'gemini',
        'medical.ai.fallback_provider' => 'gemini',
    ]);

    $manager->shouldReceive('extractWithProvider')
        ->andThrow(new Exception('All providers unavailable'));

    expect(fn () => $manager->extractMedicalNotes($sampleTranscript))
        ->toThrow(Exception::class, 'All providers unavailable');
});
