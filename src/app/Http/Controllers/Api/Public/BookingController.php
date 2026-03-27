<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\MasterProfile;
use App\Models\Service;
use App\Models\User;
use App\Models\WorkingHour;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function getMasterBySlug(string $slug): JsonResponse
    {
        $masterProfile = MasterProfile::where('slug', $slug)
            ->where('is_active', true)
            ->with('user')
            ->firstOrFail();

        return response()->json([
            'master' => [
                'id' => $masterProfile->id,
                'user_id' => $masterProfile->user_id,
                'slug' => $masterProfile->slug,
                'display_name' => $masterProfile->display_name,
                'bio' => $masterProfile->bio,
                'avatar_url' => $masterProfile->avatar_url,
                'phone' => $masterProfile->phone,
                'email' => $masterProfile->email,
                'address' => $masterProfile->address,
                'appointment_duration' => $masterProfile->appointment_duration,
                'social_links' => $masterProfile->social_links,
            ],
        ]);
    }

    public function getServices(int $userId): JsonResponse
    {
        $services = Service::where('user_id', $userId)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'services' => $services,
        ]);
    }

    public function getAvailableSlots(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'date' => 'required|date',
            'service_id' => 'nullable|integer|exists:services,id',
        ]);

        $userId = $request->user_id;
        $date = Carbon::parse($request->date);
        $serviceId = $request->service_id;

        // Get service duration or default
        $duration = 60;
        if ($serviceId) {
            $service = Service::find($serviceId);
            if ($service) {
                $duration = $service->duration;
            }
        }

        // Get working hours for the day
        $dayOfWeek = $date->dayOfWeek;
        $workingHour = WorkingHour::where('user_id', $userId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_working_day', true)
            ->first();

        if (!$workingHour) {
            return response()->json([
                'slots' => [],
            ]);
        }

        // Generate time slots
        $slots = $this->generateTimeSlots(
            $userId,
            $date,
            $workingHour->start_time,
            $workingHour->end_time,
            $workingHour->break_start,
            $workingHour->break_end,
            $duration
        );

        return response()->json([
            'slots' => $slots,
        ]);
    }

    public function createBooking(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'service_id' => 'nullable|integer|exists:services,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'client_name' => 'required|string|max:255',
            'client_phone' => 'required|string|max:20',
            'client_email' => 'nullable|email|max:255',
            'notes' => 'nullable|string',
        ]);

        // Check for time conflicts
        $hasConflict = Appointment::where('user_id', $request->user_id)
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                      ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                      ->orWhere(function ($q) use ($request) {
                          $q->where('start_time', '<=', $request->start_time)
                            ->where('end_time', '>=', $request->end_time);
                      });
            })
            ->exists();

        if ($hasConflict) {
            return response()->json([
                'message' => 'This time slot is not available',
            ], 422);
        }

        // Get service info
        $service = null;
        $price = 0;
        $title = 'Запись';
        if ($request->service_id) {
            $service = Service::find($request->service_id);
            if ($service) {
                $price = $service->price;
                $title = $service->name;
            }
        }

        // Create appointment
        $appointment = Appointment::create([
            'user_id' => $request->user_id,
            'service_id' => $request->service_id,
            'title' => $title,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'status' => 'scheduled',
            'price' => $price,
            'is_paid' => false,
            'client_name' => $request->client_name,
            'client_phone' => $request->client_phone,
            'client_email' => $request->client_email,
            'notes' => $request->notes,
        ]);

        return response()->json([
            'message' => 'Booking created successfully',
            'appointment' => $appointment,
        ], 201);
    }

    private function generateTimeSlots(
        int $userId,
        Carbon $date,
        string $startTime,
        string $endTime,
        ?string $breakStart,
        ?string $breakEnd,
        int $duration
    ): array {
        $slots = [];
        $slotDuration = $duration;

        $start = Carbon::parse($date->format('Y-m-d') . ' ' . $startTime);
        $end = Carbon::parse($date->format('Y-m-d') . ' ' . $endTime);

        $breakStartTime = null;
        $breakEndTime = null;
        if ($breakStart && $breakEnd) {
            $breakStartTime = Carbon::parse($date->format('Y-m-d') . ' ' . $breakStart);
            $breakEndTime = Carbon::parse($date->format('Y-m-d') . ' ' . $breakEnd);
        }

        $currentTime = clone $start;

        while ($currentTime->copy()->addMinutes($slotDuration) <= $end) {
            $slotEnd = $currentTime->copy()->addMinutes($slotDuration);

            // Check if slot overlaps with break
            if ($breakStartTime && $breakEndTime) {
                if ($currentTime < $breakEndTime && $slotEnd > $breakStartTime) {
                    $currentTime = clone $breakEndTime;
                    continue;
                }
            }

            // Check if slot is already booked
            $isBooked = Appointment::where('user_id', $userId)
                ->where('status', '!=', 'cancelled')
                ->where(function ($query) use ($currentTime, $slotEnd) {
                    $query->whereBetween('start_time', [$currentTime, $slotEnd])
                          ->orWhereBetween('end_time', [$currentTime, $slotEnd])
                          ->orWhere(function ($q) use ($currentTime, $slotEnd) {
                              $q->where('start_time', '<=', $currentTime)
                                ->where('end_time', '>=', $slotEnd);
                          });
                })
                ->exists();

            if (!$isBooked) {
                $slots[] = [
                    'start' => $currentTime->format('H:i'),
                    'end' => $slotEnd->format('H:i'),
                    'available' => true,
                ];
            }

            $currentTime = $slotEnd;
        }

        return $slots;
    }
}
