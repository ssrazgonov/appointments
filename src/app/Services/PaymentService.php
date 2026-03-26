<?php

namespace App\Services;

use App\Repositories\Eloquent\PaymentRepository;
use App\Repositories\Eloquent\SubscriptionPlanRepository;
use App\Services\RobokassaService;
use App\Models\Payment;

class PaymentService
{
    public function __construct(
        protected PaymentRepository $paymentRepository,
        protected SubscriptionPlanRepository $planRepository,
        protected RobokassaService $robokassaService
    ) {}

    public function getPayments(int $userId, int $perPage = 15)
    {
        return $this->paymentRepository->getByUser($userId, $perPage);
    }

    public function getPayment(int $userId, int $paymentId): Payment
    {
        $payment = $this->paymentRepository->find($paymentId);

        if (!$payment || $payment->user_id !== $userId) {
            throw new \Exception('Payment not found', 404);
        }

        return $payment;
    }

    public function createPayment(int $userId, int $planId): Payment
    {
        return $this->robokassaService->createPayment($userId, $planId);
    }

    public function processSuccessfulPayment(Payment $payment): void
    {
        // Activate subscription
        // This would typically be done in a job
    }
}
