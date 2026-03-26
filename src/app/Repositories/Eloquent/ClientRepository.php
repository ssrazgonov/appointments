<?php

namespace App\Repositories\Eloquent;

use App\Models\Client;

class ClientRepository extends BaseRepository
{
    public function __construct(Client $model)
    {
        parent::__construct($model);
    }

    public function getByUser(int $userId, int $perPage = 15)
    {
        return $this->model->where('user_id', $userId)
            ->orderBy('last_name')
            ->paginate($perPage);
    }

    public function searchByUser(int $userId, string $query, int $perPage = 15)
    {
        return $this->model->where('user_id', $userId)
            ->where(function ($q) use ($query) {
                $q->where('first_name', 'like', "%{$query}%")
                  ->orWhere('last_name', 'like', "%{$query}%")
                  ->orWhere('phone', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            })
            ->orderBy('last_name')
            ->paginate($perPage);
    }
}
