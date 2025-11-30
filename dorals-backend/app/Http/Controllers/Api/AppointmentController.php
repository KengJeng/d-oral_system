<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Appointment;
use App\Services\AuditLogService;
use App\Services\NotificationService;
use App\Services\QueueService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    /**
     * Admin creates an appointment (e.g., walk-in patient).
     */
    public function adminStore(Request $request)
    {
        // Ensure this is an admin
        $user = $request->user();
        if (! ($user instanceof Admin)) {
            abort(403, 'Admin access only.');
        }
        $adminId = $user->admin_id;

        $request->validate([
            'patient_id' => 'required|exists:patients,patient_id',
            'scheduled_date' => 'required|date|after_or_equal:today',
            'service_ids' => 'required|array|min:1',
            'service_ids.*' => 'exists:services,service_id',
        ]);

        $appointment = DB::transaction(function () use ($request, $adminId) {

            // 1. Admin creates appointment
            $appointment = Appointment::create([
                'patient_id' => $request->patient_id,
                'scheduled_date' => $request->scheduled_date,
                'status' => 'Pending',
                'created_by' => $adminId,
                'updated_by' => $adminId,
            ]);

            // 2. Attach services
            $appointment->services()->attach($request->service_ids);

            // 3. Assign queue number
            $queueNumber = $this->queueService->assignQueue($appointment);
            $appointment->queue_number = $queueNumber;
            $appointment->save();

            // 4. Audit log
            $this->auditLog->log(
                $adminId,
                "Admin #{$adminId} created walk-in appointment #{$appointment->appointment_id} for patient {$request->patient_id}"
            );

            return $appointment->load(['patient', 'services']);
        });

        return response()->json([
            'message' => 'Walk-in appointment created successfully',
            'appointment' => $appointment,
        ], 201);
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

    /**
     * Patient creates an appointment
     */
    /**
     * Patient creates an appointment
     */
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,patient_id',
            'scheduled_date' => 'required|date|after_or_equal:today',
            'service_ids' => 'required|array|min:1',
            'service_ids.*' => 'exists:services,service_id',
        ]);

        $appointment = DB::transaction(function () use ($request) {

            // 1. Create appointment
            $appointment = Appointment::create([
                'patient_id' => $request->patient_id,
                'scheduled_date' => $request->scheduled_date,
                'status' => 'Pending',
                // created_by / updated_by are NULL because this is a patient, not an admin
                'created_by' => null,
                'updated_by' => null,
            ]);

            // 2. Attach services
            $appointment->services()->attach($request->service_ids);

            // 3. Assign queue number
            $queueNumber = $this->queueService->assignQueue($appointment);
            $appointment->queue_number = $queueNumber;
            $appointment->save();

            // 4. Audit log (user_id = NULL because patient action)
            $this->auditLog->log(
                null,
                "Patient {$request->patient_id} created appointment #{$appointment->appointment_id}"
            );

            // 5. Return fully loaded appointment from inside the transaction
            return $appointment->load(['patient', 'services']);
        });

        return response()->json([
            'message' => 'Appointment created successfully',
            'appointment' => $appointment,
        ], 201);
    }

    public function show($id)
    {
        $appointment = Appointment::with(['patient', 'services', 'notifications'])
            ->findOrFail($id);

        return response()->json($appointment);
    }

    /**
     * Admin updates appointment (status/date/services)
     */
    public function update(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);

        // Ensure this is an admin
        $user = $request->user();
        if (! ($user instanceof Admin)) {
            abort(403, 'Admin access only.');
        }
        $adminId = $user->admin_id;

        $request->validate([
            'scheduled_date' => 'sometimes|date|after_or_equal:today',
            'status' => 'sometimes|in:Pending,Confirmed,Completed,Canceled,No-show',
            'service_ids' => 'sometimes|array|min:1',
            'service_ids.*' => 'exists:services,service_id',
        ]);

        $appointment = DB::transaction(function () use ($request, $appointment, $adminId) {

            if ($request->has('scheduled_date')) {
                $appointment->scheduled_date = $request->scheduled_date;

                // Reassign queue number if date changes
                $queueNumber = $this->queueService->assignQueue($appointment);
                $appointment->queue_number = $queueNumber;
            }

            if ($request->has('status')) {
                $oldStatus = $appointment->status;
                $appointment->status = $request->status;

                if ($oldStatus !== $request->status) {
                    // Notification is part of the logical transaction
                    $this->notificationService->sendStatusUpdate($appointment);
                }
            }

            // Mark which admin updated
            $appointment->updated_by = $adminId;
            $appointment->save();

            if ($request->has('service_ids')) {
                $appointment->services()->sync($request->service_ids);
            }

            // Audit log
            $this->auditLog->log(
                $adminId,
                "Appointment #{$appointment->appointment_id} updated by admin #{$adminId}"
            );

            return $appointment->load(['patient', 'services']);
        });

        return response()->json([
            'message' => 'Appointment updated successfully',
            'appointment' => $appointment,
        ]);
    }

    /**
     * Admin deletes appointment
     */
    public function destroy(Request $request, $id)
{
    $appointment = Appointment::findOrFail($id);
    $patientId   = $appointment->patient_id;

    $user = $request->user();
    if (!($user instanceof Admin)) {
        abort(403, 'Admin access only.');
    }
    $adminId = $user->admin_id;

    DB::transaction(function () use ($appointment, $patientId, $adminId, $id) {
        $appointment->delete();

        $this->auditLog->log(
            $adminId,
            "Appointment #{$id} for patient {$patientId} deleted by admin #{$adminId}"
        );
    });

    return response()->json([
        'message' => 'Appointment deleted successfully',
    ]);
}


    /**
     * Patient views own appointments
     */
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
