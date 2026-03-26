<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Subscription\CreatePaymentRequest;
use App\Services\PaymentService;
use App\Services\RobokassaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService,
        protected RobokassaService $robokassaService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $userId = auth()->id();
        $perPage = $request->get('per_page', 15);

        $payments = $this->paymentService->getPayments($userId, $perPage);

        return response()->json($payments);
    }

    public function create(CreatePaymentRequest $request): JsonResponse
    {
        try {
            $payment = $this->paymentService->createPayment(
                auth()->id(),
                $request->plan_id
            );

            return response()->json([
                'message' => 'Payment created successfully',
                'payment' => $payment,
                'payment_url' => $payment->payment_url,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create payment',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $payment = $this->paymentService->getPayment(auth()->id(), $id);
            return response()->json($payment);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Robokassa Result URL - called by Robokassa to verify payment
     */
    public function robokassaResult(Request $request): JsonResponse
    {
        $data = $request->all();

        Log::info('Robokassa Result webhook received', ['data' => $data]);

        try {
            $payment = $this->robokassaService->processWebhook($data);

            // Return OK to confirm receipt
            return response()->json([
                'code' => 'OK',
            ]);
        } catch (\Exception $e) {
            Log::error('Robokassa Result webhook failed', ['error' => $e->getMessage()]);

            return response()->json([
                'code' => 'ERROR',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Robokassa Success URL - called after successful payment
     */
    public function robokassaSuccess(Request $request): JsonResponse
    {
        $data = $request->all();

        Log::info('Robokassa Success webhook received', ['data' => $data]);

        if (!$this->robokassaService->verifySuccessSignature($data)) {
            return response()->json([
                'message' => 'Invalid signature',
            ], 400);
        }

        return response()->json([
            'message' => 'Payment successful',
        ]);
    }

    /**
     * Robokassa Fail URL - called after failed payment
     */
    public function robokassaFail(Request $request): JsonResponse
    {
        Log::info('Robokassa Fail webhook received', ['data' => $request->all()]);

        return response()->json([
            'message' => 'Payment failed',
        ]);
    }
}
