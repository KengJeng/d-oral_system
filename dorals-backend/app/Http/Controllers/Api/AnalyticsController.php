<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function appointments()
    {
        // Summary statistics
        $totalAppointments = Appointment::count();
        $completed = Appointment::where('status', 'Completed')->count();
        $canceled = Appointment::whereIn('status', ['Canceled', 'No-show'])->count();
        
        $completionRate = $totalAppointments > 0 ? round(($completed / $totalAppointments) * 100, 1) : 0;
        $cancellationRate = $totalAppointments > 0 ? round(($canceled / $totalAppointments) * 100, 1) : 0;
        
        // Average appointments per day
        $firstAppointment = Appointment::min('scheduled_date');
        $daysOperating = $firstAppointment ? Carbon::parse($firstAppointment)->diffInDays(Carbon::now()) + 1 : 1;
        $avgPerDay = round($totalAppointments / $daysOperating, 1);
        
        // Appointment trends (last 30 days)
        $trends = $this->getAppointmentTrends(30);
        
        // Status breakdown
        $statusBreakdown = $this->getStatusBreakdown();
        
        // Peak days (day of week)
        $peakDays = $this->getPeakDays();
        
        // Monthly comparison (last 3 months)
        $monthly = $this->getMonthlyComparison();
        
        return response()->json([
            'total_appointments' => $totalAppointments,
            'completion_rate' => $completionRate,
            'cancellation_rate' => $cancellationRate,
            'avg_per_day' => $avgPerDay,
            'trends' => $trends,
            'status_breakdown' => $statusBreakdown,
            'peak_days' => $peakDays,
            'monthly' => $monthly
        ]);
    }
    
    public function demographics()
    {
        // Summary statistics
        $totalPatients = Patient::count();
        $newThisMonth = Patient::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
        $activePatients = Patient::has('appointments')->count();
        
        // Average visits per patient
        $totalAppointments = Appointment::count();
        $avgVisits = $activePatients > 0 ? round($totalAppointments / $activePatients, 1) : 0;
        
        // Gender distribution
        $gender = $this->getGenderDistribution();
        
        // Barangay distribution (top 10)
        $barangays = $this->getBarangayDistribution();
        
        // Patient growth (last 6 months)
        $growth = $this->getPatientGrowth();
        
        // Service utilization
        $services = $this->getServiceUtilization();
        
        return response()->json([
            'total_patients' => $totalPatients,
            'new_this_month' => $newThisMonth,
            'active_patients' => $activePatients,
            'avg_visits' => $avgVisits,
            'gender' => $gender,
            'barangays' => $barangays,
            'growth' => $growth,
            'services' => $services
        ]);
    }
    
    private function getAppointmentTrends($days)
    {
        $startDate = Carbon::now()->subDays($days);
        $appointments = Appointment::selectRaw('DATE(scheduled_date) as date, COUNT(*) as count')
            ->where('scheduled_date', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        $labels = [];
        $values = [];
        
        foreach ($appointments as $apt) {
            $labels[] = Carbon::parse($apt->date)->format('M d');
            $values[] = $apt->count;
        }
        
        return ['labels' => $labels, 'values' => $values];
    }
    
    private function getStatusBreakdown()
    {
        $statuses = Appointment::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();
        
        $labels = [];
        $values = [];
        
        foreach ($statuses as $status) {
            $labels[] = $status->status;
            $values[] = $status->count;
        }
        
        return ['labels' => $labels, 'values' => $values];
    }
    
    private function getPeakDays()
    {
        // Initialize days array (Monday to Sunday)
        $days = array_fill(0, 7, 0);
        
        $appointments = Appointment::selectRaw('DAYOFWEEK(scheduled_date) as day, COUNT(*) as count')
            ->groupBy('day')
            ->get();
        
        foreach ($appointments as $apt) {
            // MySQL DAYOFWEEK: 1 = Sunday, 2 = Monday, etc.
            // Convert to 0 = Monday, 6 = Sunday
            $dayIndex = ($apt->day + 5) % 7;
            $days[$dayIndex] = $apt->count;
        }
        
        return $days;
    }
    
    private function getMonthlyComparison()
    {
        $months = Appointment::selectRaw('DATE_FORMAT(scheduled_date, "%Y-%m") as month, COUNT(*) as count')
            ->where('scheduled_date', '>=', Carbon::now()->subMonths(3))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        $labels = [];
        $values = [];
        
        foreach ($months as $month) {
            $labels[] = Carbon::parse($month->month . '-01')->format('M Y');
            $values[] = $month->count;
        }
        
        return ['labels' => $labels, 'values' => $values];
    }
    
    private function getGenderDistribution()
    {
        $genders = Patient::select('sex', DB::raw('COUNT(*) as count'))
            ->groupBy('sex')
            ->get();
        
        $labels = [];
        $values = [];
        
        foreach ($genders as $gender) {
            $labels[] = $gender->sex;
            $values[] = $gender->count;
        }
        
        return ['labels' => $labels, 'values' => $values];
    }
    
    private function getBarangayDistribution()
    {
        $barangays = Patient::selectRaw('SUBSTRING_INDEX(address, ",", 1) as barangay, COUNT(*) as count')
            ->groupBy('barangay')
            ->orderByDesc('count')
            ->limit(10)
            ->get();
        
        $labels = [];
        $values = [];
        
        foreach ($barangays as $brgy) {
            $labels[] = str_replace('Barangay ', 'Brgy. ', trim($brgy->barangay));
            $values[] = $brgy->count;
        }
        
        return ['labels' => $labels, 'values' => $values];
    }
    
    private function getPatientGrowth()
    {
        $months = Patient::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        $labels = [];
        $values = [];
        
        foreach ($months as $month) {
            $labels[] = Carbon::parse($month->month . '-01')->format('M Y');
            $values[] = $month->count;
        }
        
        return ['labels' => $labels, 'values' => $values];
    }
    
    private function getServiceUtilization()
    {
        $services = DB::table('service')
            ->leftJoin('appointment_service', 'service.service_id', '=', 'appointment_service.service_id')
            ->select('service.name', DB::raw('COUNT(appointment_service.service_id) as count'))
            ->groupBy('service.service_id', 'service.name')
            ->orderByDesc('count')
            ->limit(10)
            ->get();
        
        $labels = [];
        $values = [];
        
        foreach ($services as $service) {
            $labels[] = $service->name;
            $values[] = $service->count;
        }
        
        return ['labels' => $labels, 'values' => $values];
    }
}