<?php

namespace App\Events;

use App\Models\QueueEntry;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QueueUpdated implements ShouldBroadcast
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
    public function broadcastOn(): Channel
    {
        return new Channel('clinic.' . $this->queueEntry->clinic_id . '.queue');
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
            'estimated_wait_time' => $this->queueEntry->estimated_wait_time,
            'patient_id' => $this->queueEntry->appointment->patient_id,
        ];
    }
}
