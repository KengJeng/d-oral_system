<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Services\AuditLogService;
use App\Services\QueueService;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    protected $auditLog;
    protected $queueService;
    protected $notificationService;

    public function __construct(
        AuditLogService $auditLog,
        QueueService $queueService,
        NotificationService $notificationService
    ) {
        $this->auditLog = $auditLog;
        $this->queueService = $queueService;
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        $appointments = Appointment::with(['patient', 'services'])
            ->when($request->date, function ($query) use ($request) {
                return $query->whereDate('scheduled_date', $request->date);
            })
            ->when($request->status, function ($query) use ($request) {
                return $query->where('status', $request->status);
            })
            ->orderBy('scheduled_date')
            ->orderBy('queue_number')
            ->paginate(20);

        return response()->json($appointments);
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,patient_id',
            'scheduled_date' => 'required|date|after_or_equal:today',
            'service_ids' => 'required|array|min:1',
            'service_ids.*' => 'exists:services,service_id',
        ]);

        $appointment = Appointment::create([
            'patient_id' => $request->patient_id,
            'scheduled_date' => $request->scheduled_date,
            'status' => 'Pending',
        ]);

        $appointment->services()->attach($request->service_ids);

        // Assign queue number
        $queueNumber = $this->queueService->assignQueue($appointment);
        $appointment->update(['queue_number' => $queueNumber]);

        $this->auditLog->log(
            $request->patient_id,
            "Appointment created: #{$appointment->appointment_id}"
        );

        return response()->json([
            'message' => 'Appointment created successfully',
            'appointment' => $appointment->load(['patient', 'services']),
        ], 201);
    }

    public function show($id)
    {
        $appointment = Appointment::with(['patient', 'services', 'notifications'])
            ->findOrFail($id);

        return response()->json($appointment);
    }

    public function update(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);

        $request->validate([
            'scheduled_date' => 'sometimes|date|after_or_equal:today',
            'status' => 'sometimes|in:Pending,Confirmed,Completed,Canceled,No-show',
            'service_ids' => 'sometimes|array|min:1',
            'service_ids.*' => 'exists:services,service_id',
        ]);

        if ($request->has('scheduled_date')) {
            $appointment->scheduled_date = $request->scheduled_date;
            // Reassign queue number if date changes
            $queueNumber = $this->queueService->assignQueue($appointment);
            $appointment->queue_number = $queueNumber;
        }

        if ($request->has('status')) {
            $oldStatus = $appointment->status;
            $appointment->status = $request->status;

            // Send notification on status change
            if ($oldStatus !== $request->status) {
                $this->notificationService->sendStatusUpdate($appointment);
            }
        }

        $appointment->save();

        if ($request->has('service_ids')) {
            $appointment->services()->sync($request->service_ids);
        }

        $this->auditLog->log(
            $appointment->patient_id,
            "Appointment updated: #{$appointment->appointment_id}"
        );

        return response()->json([
            'message' => 'Appointment updated successfully',
            'appointment' => $appointment->load(['patient', 'services']),
        ]);
    }

    public function destroy($id)
    {
        $appointment = Appointment::findOrFail($id);
        $patientId = $appointment->patient_id;

        $appointment->delete();

        $this->auditLog->log($patientId, "Appointment deleted: #{$id}");

        return response()->json([
            'message' => 'Appointment deleted successfully',
        ]);
    }

    public function myAppointments(Request $request)
    {
        $patient = $request->user();

        $appointments = Appointment::with('services')
            ->where('patient_id', $patient->patient_id)
            ->orderBy('scheduled_date', 'desc')
            ->paginate(20);

        return response()->json($appointments);
    }

    public function todayQueue()
    {
        $appointments = Appointment::with(['patient', 'services'])
            ->today()
            ->whereIn('status', ['Pending', 'Confirmed'])
            ->orderBy('queue_number')
            ->get();

        return response()->json($appointments);
    }
}