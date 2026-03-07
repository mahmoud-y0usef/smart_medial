<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConversationState extends Model
{
    protected $casts = [
        'context' => 'array',
        'last_interaction_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the patient that owns the conversation state
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Check if conversation is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at < now();
    }

    /**
     * Update context
     */
    public function updateContext(array $newData): void
    {
        $context = $this->context ?? [];
        $this->update([
            'context' => array_merge($context, $newData),
            'last_interaction_at' => now(),
        ]);
    }

    /**
     * Move to next state
     */
    public function transitionTo(string $newState): void
    {
        $this->update([
            'current_state' => $newState,
            'step' => $this->step + 1,
            'last_interaction_at' => now(),
        ]);
    }

    /**
     * Reset conversation to welcome state
     */
    public function reset(): void
    {
        $this->update([
            'current_state' => 'welcome',
            'step' => 0,
            'context' => [],
            'last_interaction_at' => now(),
            'expires_at' => now()->addHours(2),
        ]);
    }
}
