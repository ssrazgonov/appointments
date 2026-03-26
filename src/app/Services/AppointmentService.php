<?php

namespace App\Services;

use App\Repositories\Eloquent\AppointmentRepository;
use App\Repositories\Eloquent\ClientRepository;
use App\Models\Appointment;
use Carbon\Carbon;

class AppointmentService
{
    public function __construct(
        protected AppointmentRepository $appointmentRepository,
        protected ClientRepository $clientRepository
    ) {}

    public function getAppointments(int $userId, int $perPage = 15)
    {
        return $this->appointmentRepository->getByUser($userId, $perPage);
    }

    public function getTodayAppointments(int $userId)
    {
        return $this->appointmentRepository->getToday($userId);
    }

    public function getUpcomingAppointments(int $userId, int $limit = 10)
    {
        return $this->appointmentRepository->getUpcoming($userId, $limit);
    }

    public function getAppointmentsByDateRange(int $userId, Carbon $start, Carbon $end)
    {
        return $this->appointmentRepository->getByDateRange($userId, $start, $end);
    }

    public function getAppointment(int $userId, int $appointmentId)
    {
        $appointment = $this->appointmentRepository->find($appointmentId);
        
        if (!$appointment || $appointment->user_id !== $userId) {
            throw new \Exception('Appointment not found', 404);
        }

        return $appointment->load('client');
    }

    public function createAppointment(int $userId, array $data): Appointment
    {
        // Verify client belongs to user
        $client = $this->clientRepository->find($data['client_id']);
        if (!$client || $client->user_id !== $userId) {
            throw new \Exception('Client not found', 404);
        }

        // Check for time conflicts
        $startTime = Carbon::parse($data['start_time']);
        $endTime = Carbon::parse($data['end_time']);
        
        if ($this->appointmentRepository->hasConflict($userId, $startTime, $endTime)) {
            throw new \Exception('Time slot is already booked', 422);
        }

        $data['user_id'] = $userId;
        return $this->appointmentRepository->create($data);
    }

    public function updateAppointment(int $userId, int $appointmentId, array $data): Appointment
    {
        $appointment = $this->getAppointment($userId, $appointmentId);

        // Check for time conflicts if time is being changed
        if (isset($data['start_time']) || isset($data['end_time'])) {
            $startTime = Carbon::parse($data['start_time'] ?? $appointment->start_time);
            $endTime = Carbon::parse($data['end_time'] ?? $appointment->end_time);
            
            if ($this->appointmentRepository->hasConflict($userId, $startTime, $endTime, $appointmentId)) {
                throw new \Exception('Time slot is already booked', 422);
            }
        }

        // Remove user_id from data to prevent manipulation
        unset($data['user_id']);
        
        $this->appointmentRepository->update($appointmentId, $data);
        
        return $this->appointmentRepository->find($appointmentId);
    }

    public function deleteAppointment(int $userId, int $appointmentId): bool
    {
        $appointment = $this->getAppointment($userId, $appointmentId);
        return $this->appointmentRepository->delete($appointmentId);
    }

    public function cancelAppointment(int $userId, int $appointmentId): Appointment
    {
        $appointment = $this->getAppointment($userId, $appointmentId);
        $this->appointmentRepository->update($appointmentId, ['status' => 'cancelled']);
        return $this->appointmentRepository->find($appointmentId);
    }

    public function completeAppointment(int $userId, int $appointmentId, array $data = []): Appointment
    {
        $appointment = $this->getAppointment($userId, $appointmentId);
        
        $updateData = array_merge($data, ['status' => 'completed']);
        $this->appointmentRepository->update($appointmentId, $updateData);
        
        return $this->appointmentRepository->find($appointmentId);
    }

    public function getAppointmentStats(int $userId, ?Carbon $month = null): array
    {
        $month = $month ?? now();
        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();

        $appointments = $this->getAppointmentsByDateRange($userId, $start, $end);

        $total = $appointments->count();
        $completed = $appointments->where('status', 'completed')->count();
        $cancelled = $appointments->where('status', 'cancelled')->count();
        $scheduled = $appointments->where('status', 'scheduled')->count();
        $revenue = $appointments->where('status', 'completed')->sum('price');

        return [
            'total' => $total,
            'completed' => $completed,
            'cancelled' => $cancelled,
            'scheduled' => $scheduled,
            'revenue' => $revenue,
            'completion_rate' => $total > 0 ? round(($completed / $total) * 100, 2) : 0,
        ];
    }
}
