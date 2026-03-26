<?php

namespace App\Repositories\Eloquent;

use App\Models\Appointment;
use Carbon\Carbon;

class AppointmentRepository extends BaseRepository
{
    public function __construct(Appointment $model)
    {
        parent::__construct($model);
    }

    public function getByUser(int $userId, int $perPage = 15)
    {
        return $this->model->with('client')
            ->where('user_id', $userId)
            ->orderBy('start_time', 'desc')
            ->paginate($perPage);
    }

    public function getUpcoming(int $userId, int $limit = 10)
    {
        return $this->model->with('client')
            ->where('user_id', $userId)
            ->where('start_time', '>=', now())
            ->orderBy('start_time')
            ->limit($limit)
            ->get();
    }

    public function getByDateRange(int $userId, Carbon $start, Carbon $end)
    {
        return $this->model->with('client')
            ->where('user_id', $userId)
            ->whereBetween('start_time', [$start, $end])
            ->orderBy('start_time')
            ->get();
    }

    public function getToday(int $userId)
    {
        return $this->model->with('client')
            ->where('user_id', $userId)
            ->whereDate('start_time', today())
            ->orderBy('start_time')
            ->get();
    }

    public function hasConflict(int $userId, Carbon $start, Carbon $end, ?int $excludeId = null)
    {
        $query = $this->model->where('user_id', $userId)
            ->where('status', '!=', 'cancelled')
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_time', [$start, $end])
                  ->orWhereBetween('end_time', [$start, $end])
                  ->orWhere(function ($q2) use ($start, $end) {
                      $q2->where('start_time', '<=', $start)
                         ->where('end_time', '>=', $end);
                  });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}
