<?php

use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\AuditLogController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\PatientController;
use App\Http\Controllers\Api\QueueController;
use App\Http\Controllers\Api\ServiceController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/patient/register', [AuthController::class, 'patientRegister']);
Route::post('/patient/login', [AuthController::class, 'patientLogin']);
Route::post('/admin/login', [AuthController::class, 'adminLogin']);

// Services (public - for viewing)
Route::get('/services', [ServiceController::class, 'index']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Appointments
    Route::get('/appointments/my', [AppointmentController::class, 'myAppointments']);
    Route::get('/appointments/today-queue', [AppointmentController::class, 'todayQueue']);
    Route::apiResource('appointments', AppointmentController::class);

    // Patients
    Route::get('/patients', [PatientController::class, 'index']);
    Route::get('/patients/{id}', [PatientController::class, 'show']);
    Route::patch('/patients/{id}', [PatientController::class, 'update']);

    // Services Management
    Route::post('/services', [ServiceController::class, 'store']);
    Route::patch('/services/{id}', [ServiceController::class, 'update']);
    Route::delete('/services/{id}', [ServiceController::class, 'destroy']);

    // Dashboard
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
    Route::get('/analytics/trends', [DashboardController::class, 'trends']);

    // Analytics
    Route::get('/analytics/appointments', [AnalyticsController::class, 'appointments']);
    Route::get('/analytics/demographics', [AnalyticsController::class, 'demographics']);

    // Queue Management
    Route::get('/queue/next', [QueueController::class, 'getNext']);
    Route::post('/queue/call-next', [QueueController::class, 'callNext']);
    Route::get('/queue/my-position/{appointmentId}', [QueueController::class, 'myPosition']);

    // Audit Logs
    Route::get('/audit-logs', [AuditLogController::class, 'index']);
    Route::get('/audit-logs/stats', [AuditLogController::class, 'stats']);
});
