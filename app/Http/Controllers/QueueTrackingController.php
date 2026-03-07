<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;

class QueueTrackingController extends Controller
{
    /**
     * Show queue tracking page
     */
    public function track(Appointment $appointment)
    {
        $appointment->load(['patient', 'clinic', 'doctor', 'queueEntry']);

        if (! $appointment->queueEntry) {
            abort(404, 'Queue entry not found');
        }

        return view('queue.track', [
            'appointment' => $appointment,
            'queue' => $appointment->queueEntry,
            'clinic' => $appointment->clinic,
            'patient' => $appointment->patient,
        ]);
    }

    /**
     * API endpoint for real-time queue status
     */
    public function status(Appointment $appointment)
    {
        $queue = $appointment->queueEntry;

        if (! $queue) {
            return response()->json(['error' => 'Queue entry not found'], 404);
        }

        return response()->json([
            'position' => $queue->position,
            'status' => $queue->status->value,
            'estimated_wait_time' => $queue->estimated_wait_time,
            'people_ahead' => $queue->position - 1,
            'updated_at' => $queue->updated_at->toIso8601String(),
        ]);
    }
}
