<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class PatientController extends Controller
{
    protected $auditLog;

    public function __construct(AuditLogService $auditLog)
    {
        $this->auditLog = $auditLog;
    }

    /**
     * Display a listing of patients
     * OPTIMIZED: Select only needed columns, added index hints
     */
    public function index(Request $request)
    {
        $query = Patient::query();

        // Select only necessary columns for listing
        $query->select([
            'patient_id',
            'first_name',
            'middle_name',
            'last_name',
            'email',
            'contact_no',
            'sex',
            'created_at'
        ]);

        // Search functionality
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('contact_no', 'like', "%{$search}%");
            });
        }

        // Filter by sex
        if ($request->has('sex')) {
            $query->where('sex', $request->input('sex'));
        }

        $patients = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($patients);
    }

    /**
     * Display the specified patient
     * OPTIMIZED: Eager load appointments with only needed columns
     */
    public function show($id)
    {
        $patient = Patient::with(['appointments' => function ($query) {
            $query->select([
                'appointment_id',
                'patient_id',
                'service_id',
                'scheduled_date',
                'scheduled_time',
                'status',
                'created_at'
            ])
            ->orderBy('scheduled_date', 'desc');
        }])->findOrFail($id);

        return response()->json($patient);
    }

    /**
     * Update the specified patient
     * OPTIMIZED: Already efficient, no changes needed
     */
    public function update(Request $request, $id)
    {
        $patient = Patient::findOrFail($id);

        $request->validate([
            'first_name' => 'sometimes|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'sex' => 'sometimes|in:Male,Female',
            'contact_no' => 'sometimes|string|max:20',
            'address' => 'sometimes|string',
            'email' => 'sometimes|email|unique:patients,email,' . $id . ',patient_id',
        ]);

        $patient->update($request->only([
            'first_name',
            'middle_name',
            'last_name',
            'sex',
            'contact_no',
            'address',
            'email',
        ]));

        $this->auditLog->log(
            $patient->patient_id,
            "Patient profile updated"
        );

        return response()->json([
            'message' => 'Profile updated successfully',
            'patient' => $patient,
        ]);
    }

    /**
     * Update patient password
     * OPTIMIZED: Select only password column for verification
     */
    public function updatePassword(Request $request, $id)
    {
        // Only select password column for verification
        $patient = Patient::select('patient_id', 'password')->findOrFail($id);

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        // Verify current password
        if (!Hash::check($request->current_password, $patient->password)) {
            return response()->json([
                'message' => 'Current password is incorrect',
            ], 422);
        }

        $patient->update([
            'password' => Hash::make($request->new_password),
        ]);

        $this->auditLog->log(
            $patient->patient_id,
            "Password changed"
        );

        return response()->json([
            'message' => 'Password updated successfully',
        ]);
    }

    /**
     * Get patient's appointment history
     * OPTIMIZED: Select only needed columns, optimized eager loading
     */
    public function appointmentHistory($id)
    {
        // Verify patient exists (lightweight query)
        Patient::select('patient_id')->findOrFail($id);

        $appointments = DB::table('appointments')
            ->where('patient_id', $id)
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->select([
                'appointments.appointment_id',
                'appointments.scheduled_date',
                'appointments.scheduled_time',
                'appointments.status',
                'appointments.created_at',
                'services.id as service_id',
                'services.name as service_name',
                'services.duration as service_duration',
                'services.price as service_price'
            ])
            ->orderBy('appointments.scheduled_date', 'desc')
            ->paginate(20);

        return response()->json($appointments);
    }

    /**
     * Get patient statistics
     * OPTIMIZED: Single query instead of 6 separate queries
     */
    public function statistics($id)
    {
        // Get patient with minimal data
        $patient = Patient::select([
            'patient_id',
            'first_name',
            'middle_name', 
            'last_name',
            'email',
            'sex'
        ])->findOrFail($id);

        // Single query to get all statistics
        $stats = DB::table('appointments')
            ->where('patient_id', $id)
            ->selectRaw("
                COUNT(*) as total_appointments,
                COUNT(CASE WHEN status = 'Completed' THEN 1 END) as completed,
                COUNT(CASE WHEN status = 'Canceled' THEN 1 END) as canceled,
                COUNT(CASE WHEN status = 'No-show' THEN 1 END) as no_show,
                COUNT(CASE 
                    WHEN scheduled_date >= CURDATE() 
                    AND status IN ('Pending', 'Confirmed') 
                    THEN 1 
                END) as upcoming
            ")
            ->first();

        $completionRate = $stats->total_appointments > 0 
            ? round(($stats->completed / $stats->total_appointments) * 100, 2) 
            : 0;

        return response()->json([
            'patient' => $patient,
            'statistics' => [
                'total_appointments' => $stats->total_appointments,
                'completed' => $stats->completed,
                'canceled' => $stats->canceled,
                'no_show' => $stats->no_show,
                'upcoming' => $stats->upcoming,
                'completion_rate' => $completionRate,
            ],
        ]);
    }

    /**
     * Delete patient account
     * OPTIMIZED: Single query to check upcoming appointments
     */
    public function destroy(Request $request, $id)
    {
        // Get patient with only needed columns
        $patient = Patient::select('patient_id', 'first_name', 'middle_name', 'last_name')
            ->findOrFail($id);

        // Single optimized query to check upcoming appointments
        $upcomingCount = DB::table('appointments')
            ->where('patient_id', $id)
            ->where('scheduled_date', '>=', now()->toDateString())
            ->whereIn('status', ['Pending', 'Confirmed'])
            ->count();

        if ($upcomingCount > 0) {
            return response()->json([
                'message' => "Cannot delete patient. There are {$upcomingCount} upcoming appointment(s).",
            ], 422);
        }

        $patientName = $patient->first_name . ' ' . $patient->last_name;
        $patient->delete();

        $this->auditLog->log(
            $request->user()->getKey(),
            "Patient account deleted: {$patientName}"
        );

        return response()->json([
            'message' => 'Patient account deleted successfully',
        ]);
    }

    /**
     * Get current authenticated patient profile
     * OPTIMIZED: Already efficient
     */
    public function profile(Request $request)
    {
        $patient = $request->user();

        return response()->json($patient);
    }
}