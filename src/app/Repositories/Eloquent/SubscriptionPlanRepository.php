<?php

namespace App\Repositories\Eloquent;

use App\Models\SubscriptionPlan;

class SubscriptionPlanRepository extends BaseRepository
{
    public function __construct(SubscriptionPlan $model)
    {
        parent::__construct($model);
    }

    public function getActivePlans()
    {
        return $this->model->where('is_active', true)
            ->orderBy('price')
            ->get();
    }

    public function findBySlug(string $slug): ?SubscriptionPlan
    {
        return $this->findBy('slug', $slug);
    }
}
