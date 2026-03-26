<?php

namespace App\Services;

use App\Repositories\Eloquent\AppointmentRepository;
use App\Repositories\Eloquent\ClientRepository;
use App\Repositories\Eloquent\PaymentRepository;
use Carbon\Carbon;

class ReportService
{
    public function __construct(
        protected AppointmentRepository $appointmentRepository,
        protected ClientRepository $clientRepository,
        protected PaymentRepository $paymentRepository
    ) {}

    public function getDashboardStats(int $userId): array
    {
        $today = now();
        $monthStart = $today->copy()->startOfMonth();
        $monthEnd = $today->copy()->endOfMonth();

        // Today's appointments
        $todayAppointments = $this->appointmentRepository->getToday($userId);
        
        // Upcoming appointments
        $upcomingAppointments = $this->appointmentRepository->getUpcoming($userId, 5);

        // Month stats
        $monthAppointments = $this->appointmentRepository->getByDateRange($userId, $monthStart, $monthEnd);
        $monthRevenue = $monthAppointments->where('status', 'completed')->sum('price');
        $monthCompleted = $monthAppointments->where('status', 'completed')->count();

        // Client stats
        $totalClients = $this->clientRepository->getByUser($userId, 1)->total();
        $newClientsThisMonth = $this->clientRepository->getByUser($userId, 10000)
            ->where('created_at', '>=', $monthStart)
            ->count();

        return [
            'today' => [
                'appointments_count' => $todayAppointments->count(),
                'appointments' => $todayAppointments->map(fn($a) => [
                    'id' => $a->id,
                    'title' => $a->title,
                    'start_time' => $a->start_time->toIso8601String(),
                    'client_name' => $a->client->full_name ?? 'Unknown',
                    'status' => $a->status,
                ]),
            ],
            'upcoming' => $upcomingAppointments->map(fn($a) => [
                'id' => $a->id,
                'title' => $a->title,
                'start_time' => $a->start_time->toIso8601String(),
                'client_name' => $a->client->full_name ?? 'Unknown',
                'status' => $a->status,
            ]),
            'month' => [
                'revenue' => $monthRevenue,
                'completed_appointments' => $monthCompleted,
                'total_appointments' => $monthAppointments->count(),
            ],
            'clients' => [
                'total' => $totalClients,
                'new_this_month' => $newClientsThisMonth,
            ],
        ];
    }

    public function getMonthlyReport(int $userId, int $year, int $month): array
    {
        $date = Carbon::create($year, $month, 1);
        $start = $date->copy()->startOfMonth();
        $end = $date->copy()->endOfMonth();

        $appointments = $this->appointmentRepository->getByDateRange($userId, $start, $end);

        // Revenue by day
        $dailyRevenue = [];
        for ($day = 1; $day <= $date->daysInMonth; $day++) {
            $dayDate = Carbon::create($year, $month, $day);
            $dailyRevenue[$day] = $appointments
                ->where('status', 'completed')
                ->whereDate('start_time', $dayDate)
                ->sum('price');
        }

        // Top clients by revenue
        $clientRevenue = $appointments
            ->where('status', 'completed')
            ->groupBy('client_id')
            ->map(fn($items) => $items->sum('price'))
            ->sortDesc()
            ->take(5);

        return [
            'period' => [
                'year' => $year,
                'month' => $month,
                'start_date' => $start->toDateString(),
                'end_date' => $end->toDateString(),
            ],
            'summary' => [
                'total_appointments' => $appointments->count(),
                'completed' => $appointments->where('status', 'completed')->count(),
                'cancelled' => $appointments->where('status', 'cancelled')->count(),
                'scheduled' => $appointments->where('status', 'scheduled')->count(),
                'total_revenue' => $appointments->where('status', 'completed')->sum('price'),
                'average_check' => $appointments->where('status', 'completed')->count() > 0 
                    ? $appointments->where('status', 'completed')->sum('price') / $appointments->where('status', 'completed')->count() 
                    : 0,
            ],
            'daily_revenue' => $dailyRevenue,
            'top_clients' => $clientRevenue,
        ];
    }

    public function getYearlyReport(int $userId, int $year): array
    {
        $monthlyData = [];
        
        for ($month = 1; $month <= 12; $month++) {
            $report = $this->getMonthlyReport($userId, $year, $month);
            $monthlyData[$month] = [
                'revenue' => $report['summary']['total_revenue'],
                'appointments' => $report['summary']['total_appointments'],
                'completed' => $report['summary']['completed'],
            ];
        }

        $totalRevenue = collect($monthlyData)->sum('revenue');
        $totalAppointments = collect($monthlyData)->sum('appointments');
        $totalCompleted = collect($monthlyData)->sum('completed');

        return [
            'year' => $year,
            'summary' => [
                'total_revenue' => $totalRevenue,
                'total_appointments' => $totalAppointments,
                'completed_appointments' => $totalCompleted,
                'average_monthly_revenue' => $totalRevenue / 12,
            ],
            'monthly_data' => $monthlyData,
        ];
    }
}
