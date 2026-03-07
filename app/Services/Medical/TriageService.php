<?php

namespace App\Services\Medical;

use App\Enums\Triage\SeverityLevel;
use App\Models\Patient;
use App\Models\TriageAssessment;

class TriageService
{
    /**
     * Calculate priority score based on triage answers
     */
    public function calculatePriority(array $answers): int
    {
        $score = 0;
        $weights = config('medical.triage.scoring_weights');

        // Temperature scoring
        if (isset($answers['temperature'])) {
            $temp = (float) $answers['temperature'];
            if ($temp > 39) {
                $score += 3 * $weights['temperature'];
            } elseif ($temp >= 38) {
                $score += 2 * $weights['temperature'];
            } elseif ($temp >= 37.5) {
                $score += 1 * $weights['temperature'];
            }
        }

        // Pain level scoring
        if (isset($answers['pain_level'])) {
            $pain = (int) $answers['pain_level'];
            if ($pain >= 8) {
                $score += 3 * $weights['pain_level'];
            } elseif ($pain >= 5) {
                $score += 2 * $weights['pain_level'];
            } elseif ($pain >= 3) {
                $score += 1 * $weights['pain_level'];
            }
        }

        // Dangerous symptoms scoring
        $dangerousSymptoms = $answers['dangerous_symptoms'] ?? [];
        if (! empty($dangerousSymptoms)) {
            $score += count($dangerousSymptoms) * $weights['dangerous_symptoms'];
        }

        // Chronic disease interaction
        if (! empty($answers['chronic_diseases'])) {
            foreach ($answers['chronic_diseases'] as $disease) {
                if ($this->hasSymptomDiseaseInteraction($answers, $disease)) {
                    $score += $weights['chronic_disease_interaction'];
                }
            }
        }

        return min((int) round($score), 10); // Cap at 10
    }

    /**
     * Determine severity level from priority score
     */
    public function getSeverityLevel(int $priorityScore): SeverityLevel
    {
        return match (true) {
            $priorityScore >= config('medical.triage.high_priority_min', 8) => SeverityLevel::High,
            $priorityScore >= config('medical.triage.medium_priority_min', 4) => SeverityLevel::Medium,
            default => SeverityLevel::Low,
        };
    }

    /**
     * Create triage assessment
     */
    public function createAssessment(Patient $patient, array $answers): TriageAssessment
    {
        $priorityScore = $this->calculatePriority($answers);
        $severityLevel = $this->getSeverityLevel($priorityScore);

        return TriageAssessment::create([
            'patient_id' => $patient->id,
            'questions_answers' => $answers,
            'temperature' => $answers['temperature'] ?? null,
            'pain_level' => $answers['pain_level'] ?? null,
            'symptoms' => $answers['symptoms'] ?? [],
            'dangerous_symptoms' => $answers['dangerous_symptoms'] ?? [],
            'has_chronic_disease' => ! empty($answers['chronic_diseases']),
            'priority_score' => $priorityScore,
            'severity_level' => $severityLevel,
            'ai_recommendation' => $this->generateRecommendation($severityLevel),
        ]);
    }

    /**
     * Check if symptoms interact with chronic disease
     */
    protected function hasSymptomDiseaseInteraction(array $answers, string $disease): bool
    {
        $interactions = [
            'heart' => ['chest_pain', 'difficulty_breathing', 'palpitations'],
            'diabetes' => ['extreme_thirst', 'frequent_urination', 'blurred_vision'],
            'asthma' => ['difficulty_breathing', 'wheezing', 'chest_tightness'],
        ];

        $symptoms = $answers['symptoms'] ?? [];
        $dangerousSymptoms = $answers['dangerous_symptoms'] ?? [];
        $allSymptoms = array_merge($symptoms, $dangerousSymptoms);

        if (isset($interactions[$disease])) {
            foreach ($interactions[$disease] as $symptom) {
                if (in_array($symptom, $allSymptoms)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Generate AI recommendation based on severity
     */
    protected function generateRecommendation(SeverityLevel $severity): string
    {
        return match ($severity) {
            SeverityLevel::High, SeverityLevel::Emergency => 'يُنصح بالحصول على رعاية طبية فورية. سيتم إعطاؤك أولوية في الحجز.',
            SeverityLevel::Medium => 'يُنصح بزيارة الطبيب في أقرب وقت ممكن.',
            SeverityLevel::Low => 'حالتك غير خطيرة، لكن يُفضل استشارة الطبيب للاطمئنان.',
        };
    }
}
