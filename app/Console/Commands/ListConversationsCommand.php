<?php

namespace App\Console\Commands;

use App\Models\ConversationState;
use Illuminate\Console\Command;

class ListConversationsCommand extends Command
{
    protected $signature = 'check:conversations';
    protected $description = 'List all conversation states';

    public function handle(): int
    {
        $states = ConversationState::all();
        
        if ($states->isEmpty()) {
            $this->info("No conversation states found");
            return 0;
        }
        
        $this->info("All conversation states:");
        foreach ($states as $state) {
            $this->line("  Phone: {$state->phone}");
            $this->line("    State: {$state->current_state}");
            $this->line("    Expires: {$state->expires_at}");
            $this->line("");
        }
        
        return 0;
    }
}
