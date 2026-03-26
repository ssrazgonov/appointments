<?php

namespace App\Repositories\Eloquent;

use App\Models\Payment;

class PaymentRepository extends BaseRepository
{
    public function __construct(Payment $model)
    {
        parent::__construct($model);
    }

    public function getByUser(int $userId, int $perPage = 15)
    {
        return $this->model->with('subscriptionPlan')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function findByTransactionId(string $transactionId): ?Payment
    {
        return $this->findBy('transaction_id', $transactionId);
    }

    public function markAsPaid(int $id, string $transactionId): bool
    {
        return $this->update($id, [
            'status' => 'paid',
            'transaction_id' => $transactionId,
            'paid_at' => now(),
        ]);
    }

    public function markAsFailed(int $id): bool
    {
        return $this->update($id, ['status' => 'failed']);
    }
}
