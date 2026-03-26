<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Appointment\StoreAppointmentRequest;
use App\Http\Requests\Appointment\UpdateAppointmentRequest;
use App\Services\AppointmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function __construct(
        protected AppointmentService $appointmentService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $userId = auth()->id();
        $perPage = $request->get('per_page', 15);

        $appointments = $this->appointmentService->getAppointments($userId, $perPage);

        return response()->json($appointments);
    }

    public function today(): JsonResponse
    {
        $appointments = $this->appointmentService->getTodayAppointments(auth()->id());

        return response()->json($appointments);
    }

    public function upcoming(): JsonResponse
    {
        $appointments = $this->appointmentService->getUpcomingAppointments(auth()->id());

        return response()->json($appointments);
    }

    public function show(int $id): JsonResponse
    {
        try {
            $appointment = $this->appointmentService->getAppointment(auth()->id(), $id);
            return response()->json($appointment);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    public function store(StoreAppointmentRequest $request): JsonResponse
    {
        try {
            $appointment = $this->appointmentService->createAppointment(auth()->id(), $request->validated());
            return response()->json([
                'message' => 'Appointment created successfully',
                'appointment' => $appointment,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create appointment',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    public function update(UpdateAppointmentRequest $request, int $id): JsonResponse
    {
        try {
            $appointment = $this->appointmentService->updateAppointment(auth()->id(), $id, $request->validated());
            return response()->json([
                'message' => 'Appointment updated successfully',
                'appointment' => $appointment,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update appointment',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->appointmentService->deleteAppointment(auth()->id(), $id);
            return response()->json([
                'message' => 'Appointment deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete appointment',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    public function cancel(int $id): JsonResponse
    {
        try {
            $appointment = $this->appointmentService->cancelAppointment(auth()->id(), $id);
            return response()->json([
                'message' => 'Appointment cancelled successfully',
                'appointment' => $appointment,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to cancel appointment',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    public function complete(UpdateAppointmentRequest $request, int $id): JsonResponse
    {
        try {
            $appointment = $this->appointmentService->completeAppointment(
                auth()->id(),
                $id,
                $request->validated()
            );
            return response()->json([
                'message' => 'Appointment completed successfully',
                'appointment' => $appointment,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to complete appointment',
                'error' => $e->getMessage(),
            ], 422);
        }
    }
}
