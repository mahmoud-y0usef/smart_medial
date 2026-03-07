<?php

namespace App\Events;

use App\Models\QueueEntry;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PatientCalled implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public QueueEntry $queueEntry
    ) {}

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('clinic.' . $this->queueEntry->clinic_id . '.queue'),
            new Channel('patient.' . $this->queueEntry->appointment->patient_id),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'queue_entry_id' => $this->queueEntry->id,
            'appointment_id' => $this->queueEntry->appointment_id,
            'position' => $this->queueEntry->position,
            'status' => $this->queueEntry->status->value,
            'patient_name' => $this->queueEntry->appointment->patient->name,
            'clinic_name' => $this->queueEntry->appointment->clinic->name_ar,
        ];
    }
}
