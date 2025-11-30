<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Patient;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    protected $auditLog;

    public function __construct(AuditLogService $auditLog)
    {
        $this->auditLog = $auditLog;
    }

    public function patientRegister(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'sex' => 'required|in:Male,Female',
            'contact_no' => 'required|string|max:20',
            'address' => 'required|string',
            'email' => 'required|string|email|unique:patients',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $patient = Patient::create([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'sex' => $request->sex,
            'contact_no' => $request->contact_no,
            'address' => $request->address,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $patient->createToken('patient-token')->plainTextToken;

        $this->auditLog->log($patient->patient_id, 'Patient registered');

        return response()->json([
            'message' => 'Registration successful',
            'patient' => $patient,
            'token' => $token,
        ], 201);
    }

    public function patientLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $patient = Patient::where('email', $request->email)->first();

        if (! $patient || ! Hash::check($request->password, $patient->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $patient->createToken('patient-token')->plainTextToken;

        // LOGIN HISTORY: record patient login
        DB::table('login_history')->insert([
            'user_id' => $patient->patient_id,
            'user_type' => 'patient',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            // login_time will use CURRENT_TIMESTAMP by default
        ]);

        // AUDIT LOG: keep this, but user_id should be NULL (patient, not admin)
        $this->auditLog->log(
            null,
            "Patient logged in: #{$patient->patient_id}"
        );

        return response()->json([
            'message' => 'Login successful',
            'patient' => $patient,
            'token' => $token,
        ]);
    }

    public function adminLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $admin = Admin::where('email', $request->email)->first();

        if (! $admin || ! Hash::check($request->password, $admin->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $admin->createToken('admin-token')->plainTextToken;

        // LOGIN HISTORY: record admin login
        DB::table('login_history')->insert([
            'user_id' => $admin->admin_id,
            'user_type' => 'admin',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // AUDIT LOG: this one uses admin_id (FK to admin table)
        $this->auditLog->log(
            $admin->admin_id,
            "Admin logged in: #{$admin->admin_id}"
        );

        return response()->json([
            'message' => 'Login successful',
            'admin' => $admin,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $this->auditLog->log($user->getKey(), 'User logged out');

        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout successful',
        ]);
    }
}
