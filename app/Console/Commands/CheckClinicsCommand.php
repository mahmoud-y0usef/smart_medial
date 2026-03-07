<?php

namespace App\Console\Commands;

use App\Enums\ApprovalStatus;
use App\Models\Clinic;
use Illuminate\Console\Command;

class CheckClinicsCommand extends Command
{
    protected $signature = 'check:clinics';
    protected $description = 'Check clinics in database';

    public function handle(): int
    {
        $total = Clinic::count();
        $approved = Clinic::where('is_active', true)
            ->where('approval_status', ApprovalStatus::Approved)
            ->count();
        
        $this->info("Total clinics: {$total}");
        $this->info("Approved active clinics: {$approved}");
        
        if ($approved > 0) {
            $this->line("\nApproved active clinics:");
            Clinic::where('is_active', true)
                ->where('approval_status', ApprovalStatus::Approved)
                ->get()
                ->each(fn($c) => $this->line("  - {$c->name} (ID: {$c->id})"));
        }
        
        return 0;
    }
}
