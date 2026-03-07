<?php

namespace App\Console\Commands;

use App\Models\ConversationState;
use Illuminate\Console\Command;

class ResetConversationCommand extends Command
{
    protected $signature = 'conversation:reset {phone}';
    protected $description = 'Reset conversation state for a phone number';

    public function handle(): int
    {
        $phone = $this->argument('phone');
        $deleted = ConversationState::where('phone', $phone)->delete();
        
        if ($deleted) {
            $this->info("✅ Conversation state reset for {$phone}");
        } else {
            $this->warn("No conversation state found for {$phone}");
        }
        
        return 0;
    }
}
