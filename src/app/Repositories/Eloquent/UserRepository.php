<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository extends BaseRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function create(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        return $this->model->create($data);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->findBy('email', $email);
    }

    public function updatePassword(int $id, string $password): bool
    {
        return $this->update($id, ['password' => Hash::make($password)]);
    }
}
