<?php

namespace App\Services;

use App\Repositories\Eloquent\PaymentRepository;
use App\Repositories\Eloquent\SubscriptionPlanRepository;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RobokassaService
{
    private string $merchantId;
    private string $secretKey1;
    private string $secretKey2;
    private bool $testMode;

    public function __construct(
        protected PaymentRepository $paymentRepository,
        protected SubscriptionPlanRepository $planRepository
    ) {
        $this->merchantId = config('services.robokassa.merchant_id');
        $this->secretKey1 = config('services.robokassa.secret_key_1');
        $this->secretKey2 = config('services.robokassa.secret_key_2');
        $this->testMode = config('services.robokassa.test_mode', true);
    }

    public function createPayment(int $userId, int $planId): Payment
    {
        $plan = $this->planRepository->find($planId);
        
        if (!$plan) {
            throw new \Exception('Subscription plan not found');
        }

        $payment = $this->paymentRepository->create([
            'user_id' => $userId,
            'subscription_plan_id' => $planId,
            'payment_system' => 'robokassa',
            'status' => 'pending',
            'amount' => $plan->price,
            'currency' => $plan->currency,
            'metadata' => [
                'plan_name' => $plan->name,
                'plan_slug' => $plan->slug,
            ],
        ]);

        $paymentUrl = $this->generatePaymentUrl($payment);
        $payment->payment_url = $paymentUrl;
        $payment->save();

        return $payment;
    }

    public function generatePaymentUrl(Payment $payment): string
    {
        $invId = $payment->id;
        $amount = $payment->amount;
        $description = "Subscription payment #{$payment->id}";
        
        // Generate signature
        $signature = $this->generateSignature($invId, $amount, $description);

        $baseUrl = $this->testMode 
            ? 'https://auth.robokassa.ru/Merchant/Invoice.aspx'
            : 'https://auth.robokassa.ru/Merchant/Invoice.aspx';

        return "{$baseUrl}?MerchantLogin={$this->merchantId}&OutSum={$amount}&InvId={$invId}&Description={$description}&SignatureValue={$signature}&IsTest=" . ($this->testMode ? '1' : '0');
    }

    public function verifyResultSignature(array $data): bool
    {
        $receivedSignature = $data['SignatureValue'] ?? '';
        $amount = $data['OutSum'];
        $invId = $data['InvId'];
        
        $expectedSignature = $this->generateSignature($invId, $amount);

        return strtoupper($receivedSignature) === strtoupper($expectedSignature);
    }

    public function verifySuccessSignature(array $data): bool
    {
        $receivedSignature = $data['SignatureValue'] ?? '';
        $amount = $data['OutSum'];
        $invId = $data['InvId'];
        
        // For SuccessResult, use secret key 2
        $signatureString = "{$amount}:{$invId}:{$this->secretKey2}";
        $expectedSignature = md5($signatureString);

        return strtoupper($receivedSignature) === strtoupper($expectedSignature);
    }

    public function processWebhook(array $data): Payment
    {
        $invId = $data['InvId'];
        $amount = (float) $data['OutSum'];
        $transactionId = $data['InvId'] . '_' . time();

        $payment = $this->paymentRepository->find((int) $invId);

        if (!$payment) {
            throw new \Exception('Payment not found');
        }

        if (!$this->verifyResultSignature($data)) {
            Log::warning('Robokassa webhook signature verification failed', ['data' => $data]);
            throw new \Exception('Invalid signature');
        }

        // Mark payment as paid
        $this->paymentRepository->markAsPaid($payment->id, $transactionId);

        return $payment;
    }

    private function generateSignature(string $invId, float $amount, string $description = ''): string
    {
        $signatureString = "{$this->merchantId}:{$amount}:{$invId}:{$this->secretKey1}";
        return md5($signatureString);
    }

    public function getPaymentStatus(int $paymentId): string
    {
        $payment = $this->paymentRepository->find($paymentId);
        
        if (!$payment) {
            return 'not_found';
        }

        return $payment->status;
    }
}
