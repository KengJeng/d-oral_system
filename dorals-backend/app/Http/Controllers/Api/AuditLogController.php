<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::query();
        
        // Filter by action type
        if ($request->filter && $request->filter !== 'all') {
            $query->where('action', 'like', "%{$request->filter}%");
        }
        
        // Order by most recent
        $query->orderBy('log_date', 'desc')
              ->orderBy('log_time', 'desc');
        
        $logs = $query->paginate(20);
        
        return response()->json($logs);
    }
    
    public function stats()
    {
        $today = Carbon::today();
        
        $todayCount = AuditLog::whereDate('log_date', $today)->count();
        
        $userActions = AuditLog::whereDate('log_date', $today)
            ->where('action', 'not like', '%system%')
            ->count();
        
        $systemEvents = AuditLog::whereDate('log_date', $today)
            ->where('action', 'like', '%system%')
            ->count();
        
        $activeUsers = AuditLog::whereDate('log_date', $today)
            ->distinct('user_id')
            ->count('user_id');
        
        return response()->json([
            'today_count' => $todayCount,
            'user_actions' => $userActions,
            'system_events' => $systemEvents,
            'active_users' => $activeUsers,
        ]);
    }
}