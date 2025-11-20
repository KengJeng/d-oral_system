<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    protected $auditLog;

    public function __construct(AuditLogService $auditLog)
    {
        $this->auditLog = $auditLog;
    }

    /**
     * Display a listing of services
     */
    public function index()
    {
        $services = Service::orderBy('name')->get();
        return response()->json($services);
    }

    /**
     * Store a newly created service
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:services,name',
            'duration' => 'required|integer|min:1|max:480', // max 8 hours
        ]);

        $service = Service::create([
            'name' => $request->name,
            'duration' => $request->duration,
        ]);

        $this->auditLog->log(
            $request->user()->getKey(),
            "Service created: {$service->name}"
        );

        return response()->json([
            'message' => 'Service created successfully',
            'service' => $service,
        ], 201);
    }

    /**
     * Display the specified service
     */
    public function show($id)
    {
        $service = Service::findOrFail($id);
        return response()->json($service);
    }

    /**
     * Update the specified service
     */
    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|string|max:255|unique:services,name,' . $id . ',service_id',
            'duration' => 'sometimes|integer|min:1|max:480',
        ]);

        $service->update($request->only(['name', 'duration']));

        $this->auditLog->log(
            $request->user()->getKey(),
            "Service updated: {$service->name}"
        );

        return response()->json([
            'message' => 'Service updated successfully',
            'service' => $service,
        ]);
    }

    /**
     * Remove the specified service
     */
    public function destroy(Request $request, $id)
    {
        $service = Service::findOrFail($id);
        $serviceName = $service->name;

        // Check if service is used in any appointments
        $appointmentCount = $service->appointments()->count();
        
        if ($appointmentCount > 0) {
            return response()->json([
                'message' => "Cannot delete service. It is used in {$appointmentCount} appointment(s).",
            ], 422);
        }

        $service->delete();

        $this->auditLog->log(
            $request->user()->getKey(),
            "Service deleted: {$serviceName}"
        );

        return response()->json([
            'message' => 'Service deleted successfully',
        ]);
    }

    /**
     * Get service utilization statistics
     */
    public function utilization($id)
    {
        $service = Service::withCount(['appointments' => function ($query) {
            $query->whereIn('status', ['Confirmed', 'Completed']);
        }])->findOrFail($id);

        $completedCount = $service->appointments()
            ->where('status', 'Completed')
            ->count();

        return response()->json([
            'service' => $service,
            'total_bookings' => $service->appointments_count,
            'completed' => $completedCount,
            'utilization_rate' => $service->appointments_count > 0 
                ? round(($completedCount / $service->appointments_count) * 100, 2) 
                : 0,
        ]);
    }

    /**
     * Get most popular services
     */
    public function popular(Request $request)
    {
        $limit = $request->input('limit', 5);

        $services = Service::withCount(['appointments' => function ($query) {
            $query->whereIn('status', ['Confirmed', 'Completed']);
        }])
        ->orderBy('appointments_count', 'desc')
        ->limit($limit)
        ->get();

        return response()->json($services);
    }
}