<?php

namespace App\Jobs;

use App\Models\Clinic;
use App\Services\Queue\QueueManagementService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class NotifyQueuePatients implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $clinicId
    ) {}

    /**
     * Execute the job.
     */
    public function handle(QueueManagementService $queueService): void
    {
        $clinic = Clinic::find($this->clinicId);

        if (! $clinic) {
            return;
        }

        // Notify upcoming patients
        $queueService->notifyUpcomingPatients($this->clinicId);
    }
}
