<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\ClientAccount;
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
        // Parse date without timezone conversion - just extract Y-m-d
        $date = Carbon::createFromFormat('Y-m-d', $request->date);
        $date->startOfDay(); // Set to start of day
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
            ->first();

        // If no working hour record exists, use default (Mon-Fri 9-18)
        if (!$workingHour) {
            // Default: Mon-Fri are working days 9:00-18:00
            if ($dayOfWeek >= 1 && $dayOfWeek <= 5) {
                $workingHour = (object) [
                    'is_working_day' => true,
                    'start_time' => '09:00',
                    'end_time' => '18:00',
                    'break_start' => null,
                    'break_end' => null,
                ];
            } else {
                // Weekend
                return response()->json([
                    'slots' => [],
                    'message' => 'Выходной день',
                ]);
            }
        }

        // Check if it's a working day
        if (!$workingHour->is_working_day) {
            return response()->json([
                'slots' => [],
                'message' => 'Выходной день',
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
                $startTime = $request->start_time;
                $endTime = $request->end_time;
                
                $query->where(function ($q) use ($startTime, $endTime) {
                    // New appointment starts during existing appointment
                    $q->where('start_time', '<=', $startTime)
                      ->where('end_time', '>', $startTime);
                })->orWhere(function ($q) use ($startTime, $endTime) {
                    // New appointment ends during existing appointment
                    $q->where('start_time', '<', $endTime)
                      ->where('end_time', '>=', $endTime);
                })->orWhere(function ($q) use ($startTime, $endTime) {
                    // New appointment completely overlaps existing appointment
                    $q->where('start_time', '>=', $startTime)
                      ->where('end_time', '<=', $endTime);
                });
            })
            ->exists();

        \Log::info('Booking conflict check', [
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'has_conflict' => $hasConflict,
        ]);

        if ($hasConflict) {
            return response()->json([
                'message' => 'Это время уже занято. Пожалуйста, выберите другое.',
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
            'notes' => $request->notes ?? '',
            'client_account_id' => null, // Will be set if user is authenticated
        ]);

        // If client token provided, link to client account
        $clientToken = $request->input('client_token');
        $clientAccountId = null;

        if ($clientToken) {
            try {
                // Use JWTAuth facade properly
                $client = \Tymon\JWTAuth\Facades\JWTAuth::toUser($clientToken);
                if ($client instanceof \App\Models\ClientAccount) {
                    $clientAccountId = $client->id;
                    $appointment->update(['client_account_id' => $clientAccountId]);
                }
            } catch (\Exception $e) {
                \Log::error('Failed to link client account', ['error' => $e->getMessage()]);
            }
        } else {
            // Create or find client account by phone
            $client = ClientAccount::firstOrCreate(
                ['phone' => $request->client_phone],
                [
                    'name' => $request->client_name,
                    'email' => $request->client_email,
                    'is_verified' => false,
                ]
            );
            
            $appointment->update(['client_account_id' => $client->id]);
            
            // Generate token for new client using Facade
            $clientToken = \Tymon\JWTAuth\Facades\JWTAuth::fromUser($client);
        }

        return response()->json([
            'message' => 'Booking created successfully',
            'appointment' => $appointment,
            'client_token' => $clientToken,
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
