<?php

namespace App\Repositories\Eloquent;

use App\Models\UserSubscription;
use Carbon\Carbon;

class UserSubscriptionRepository extends BaseRepository
{
    public function __construct(UserSubscription $model)
    {
        parent::__construct($model);
    }

    public function getActiveByUser(int $userId): ?UserSubscription
    {
        return $this->model->where('user_id', $userId)
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->latest()
            ->first();
    }

    public function createSubscription(int $userId, int $planId, array $data): UserSubscription
    {
        $data['user_id'] = $userId;
        $data['subscription_plan_id'] = $planId;
        return $this->create($data);
    }

    public function getExpiringSoon(int $days = 3)
    {
        return $this->model->where('status', 'active')
            ->whereBetween('ends_at', [now(), now()->addDays($days)])
            ->get();
    }
}
