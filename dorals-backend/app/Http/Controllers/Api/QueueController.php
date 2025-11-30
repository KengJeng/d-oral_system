<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Services\QueueService;
use App\Services\NotificationService;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class QueueController extends Controller
{
    protected $queueService;
    protected $notificationService;
    protected $auditLog;

    public function __construct(
        QueueService $queueService,
        NotificationService $notificationService,
        AuditLogService $auditLog
    ) {
        $this->queueService = $queueService;
        $this->notificationService = $notificationService;
        $this->auditLog = $auditLog;
    }

    /**
     * Get next patient in queue
     */
    public function getNext(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString());
        $next = $this->queueService->getNextInQueue($date);

        if (!$next) {
            return response()->json([
                'message' => 'No patients in queue',
                'next' => null,
            ]);
        }

        return response()->json($next);
    }

    /**
     * Call next patient and update status
     */
    public function callNext(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString());
        $next = $this->queueService->getNextInQueue($date);

        if (!$next) {
            return response()->json([
                'message' => 'No patients in queue',
            ], 404);
        }

        // Update status to confirmed if pending
        if ($next->status === 'Pending') {
            $next->update(['status' => 'Confirmed']);
        }

        // Send notification
        $this->notificationService->sendTurnNotification($next, 0);

        $this->auditLog->log(
            $request->user()->getKey(),
            "Called patient #{$next->patient_id} - Queue #{$next->queue_number}"
        );

        return response()->json([
            'message' => 'Patient called successfully',
            'patient' => $next->load(['patient', 'services']),
        ]);
    }

    /**
     * Get my queue position
     */
    public function myPosition(Request $request, $appointmentId)
    {
        try {
            $position = $this->queueService->getQueuePosition($appointmentId);

            return response()->json([
                'appointment_id' => $appointmentId,
                'position' => $position,
                'estimated_wait_minutes' => $position['people_ahead'] * 20, // Assuming 20 min per patient
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Appointment not found',
            ], 404);
        }
    }

    /**
     * Get queue statistics for a specific date
     */
    public function stats(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString());
        $stats = $this->queueService->getQueueStats($date);

        return response()->json($stats);
    }

    /**
     * Mark appointment as completed
     */
    public function markCompleted(Request $request, $appointmentId)
    {
        $appointment = Appointment::findOrFail($appointmentId);
        $appointment->update(['status' => 'Completed']);

        $this->notificationService->sendStatusUpdate($appointment);

        $this->auditLog->log(
            $request->user()->getKey(),
            "Marked appointment #{$appointmentId} as completed"
        );

        return response()->json([
            'message' => 'Appointment marked as completed',
            'appointment' => $appointment,
        ]);
    }

    /**
     * Mark appointment as no-show
     */
    public function markNoShow(Request $request, $appointmentId)
    {
        $appointment = Appointment::findOrFail($appointmentId);
        $appointment->update(['status' => 'No-show']);

        $this->notificationService->sendStatusUpdate($appointment);

        $this->auditLog->log(
            $request->user()->getKey(),
            "Marked appointment #{$appointmentId} as no-show"
        );

        return response()->json([
            'message' => 'Appointment marked as no-show',
            'appointment' => $appointment,
        ]);
    }

    /**
     * Reorder queue (change queue numbers)
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'appointments' => 'required|array',
            'appointments.*.appointment_id' => 'required|exists:appointments,appointment_id',
            'appointments.*.queue_number' => 'required|integer|min:1',
        ]);

        $date = $request->input('date');
        $appointments = $request->input('appointments');

        foreach ($appointments as $item) {
            Appointment::where('appointment_id', $item['appointment_id'])
                ->whereDate('scheduled_date', $date)
                ->update(['queue_number' => $item['queue_number']]);
        }

        $this->auditLog->log(
            $request->user()->getKey(),
            "Reordered queue for date: {$date}"
        );

        return response()->json([
            'message' => 'Queue reordered successfully',
        ]);
    }

    /**
     * Get queue history
     */
    public function history(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::today()->subDays(7));
        $endDate = $request->input('end_date', Carbon::today());

        $history = Appointment::with(['patient', 'services'])
            ->whereBetween('scheduled_date', [$startDate, $endDate])
            ->orderBy('scheduled_date', 'desc')
            ->orderBy('queue_number')
            ->paginate(50);

        return response()->json($history);
    }

    /**
     * Process queue notifications (notify patients when it's almost their turn)
     */
    public function processNotifications(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString());
        $notifications = $this->notificationService->processQueueNotifications($date);

        return response()->json([
            'message' => 'Queue notifications processed',
            'notifications_sent' => count($notifications),
        ]);
    }
}