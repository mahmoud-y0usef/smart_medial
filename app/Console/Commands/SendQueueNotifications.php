<?php

namespace App\Console\Commands;

use App\Jobs\NotifyQueuePatients;
use App\Models\Clinic;
use Illuminate\Console\Command;

class SendQueueNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue:notify {clinic_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send queue notifications to waiting patients';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $clinicId = $this->argument('clinic_id');

        if ($clinicId) {
            // Notify specific clinic
            $clinic = Clinic::find($clinicId);

            if (! $clinic) {
                $this->error("Clinic with ID {$clinicId} not found.");

                return self::FAILURE;
            }

            NotifyQueuePatients::dispatch($clinic->id);
            $this->info("Queued notifications for clinic: {$clinic->name_ar}");

            return self::SUCCESS;
        }

        // Notify all active clinics with waiting patients
        $clinics = Clinic::where('is_active', true)
            ->whereHas('queueEntries', function ($query) {
                $query->where('status', \App\Enums\QueueStatus::Waiting);
            })
            ->get();

        if ($clinics->isEmpty()) {
            $this->info('No clinics with waiting patients found.');

            return self::SUCCESS;
        }

        foreach ($clinics as $clinic) {
            NotifyQueuePatients::dispatch($clinic->id);
        }

        $this->info("Queued notifications for {$clinics->count()} clinics.");

        return self::SUCCESS;
    }
}
