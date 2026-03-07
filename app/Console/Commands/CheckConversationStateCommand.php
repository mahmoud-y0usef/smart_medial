<?php

namespace App\Console\Commands;

use App\Models\ConversationState;
use Illuminate\Console\Command;

class CheckConversationStateCommand extends Command
{
    protected $signature = 'check:conversation {phone}';
    protected $description = 'Check conversation state for a phone number';

    public function handle(): int
    {
        $phone = $this->argument('phone');
        $state = ConversationState::where('phone', $phone)->first();
        
        if (!$state) {
            $this->error("No conversation state found for {$phone}");
            return 1;
        }
        
        $this->info("Conversation state for {$phone}:");
        $this->line("  Current state: {$state->current_state}");
        $this->line("  Expires at: {$state->expires_at}");
        $this->line("  Context: " . json_encode($state->context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        return 0;
    }
}
