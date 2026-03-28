<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MasterProfile\StoreMasterProfileRequest;
use App\Http\Requests\MasterProfile\UpdateMasterProfileRequest;
use App\Models\MasterProfile;
use App\Models\Service;
use App\Models\WorkingHour;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MasterProfileController extends Controller
{
    public function show(): JsonResponse
    {
        $userId = auth()->id();
        $masterProfile = MasterProfile::where('user_id', $userId)->first();

        return response()->json([
            'profile' => $masterProfile,
        ]);
    }

    public function store(StoreMasterProfileRequest $request): JsonResponse
    {
        $userId = auth()->id();

        // Generate unique slug
        $slug = Str::slug($request->display_name);
        $originalSlug = $slug;
        $counter = 1;

        while (MasterProfile::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $profile = MasterProfile::create([
            'user_id' => $userId,
            'slug' => $slug,
            'display_name' => $request->display_name,
            'bio' => $request->bio,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'appointment_duration' => $request->appointment_duration ?? 60,
            'booking_advance_days' => $request->booking_advance_days ?? 30,
            'social_links' => $request->social_links,
        ]);

        // Create default working hours
        $this->createDefaultWorkingHours($userId);

        return response()->json([
            'message' => 'Master profile created successfully',
            'profile' => $profile,
        ], 201);
    }

    public function update(UpdateMasterProfileRequest $request): JsonResponse
    {
        $userId = auth()->id();
        $profile = MasterProfile::where('user_id', $userId)->firstOrFail();

        $profile->update($request->validated());

        return response()->json([
            'message' => 'Profile updated successfully',
            'profile' => $profile,
        ]);
    }

    public function getWorkingHours(): JsonResponse
    {
        $userId = auth()->id();
        $workingHours = WorkingHour::where('user_id', $userId)
            ->orderBy('day_of_week')
            ->get();

        return response()->json([
            'working_hours' => $workingHours,
        ]);
    }

    public function updateWorkingHours(Request $request): JsonResponse
    {
        $request->validate([
            'working_hours' => 'required|array',
            'working_hours.*.day_of_week' => 'required|integer|between:0,6',
            'working_hours.*.is_working_day' => 'boolean',
            'working_hours.*.start_time' => 'nullable|date_format:H:i:s',
            'working_hours.*.end_time' => 'nullable|date_format:H:i:s',
            'working_hours.*.break_start' => 'nullable|date_format:H:i:s',
            'working_hours.*.break_end' => 'nullable|date_format:H:i:s',
        ]);

        $userId = auth()->id();

        foreach ($request->working_hours as $whData) {
            WorkingHour::updateOrCreate(
                [
                    'user_id' => $userId,
                    'day_of_week' => $whData['day_of_week'],
                ],
                $whData
            );
        }

        return response()->json([
            'message' => 'Working hours updated successfully',
        ]);
    }

    public function getServices(): JsonResponse
    {
        $userId = auth()->id();
        $services = Service::where('user_id', $userId)
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'services' => $services,
        ]);
    }

    public function storeService(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'required|integer|min:15',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $userId = auth()->id();

        $service = Service::create([
            'user_id' => $userId,
            'name' => $request->name,
            'description' => $request->description,
            'duration' => $request->duration,
            'price' => $request->price,
            'is_active' => $request->is_active ?? true,
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return response()->json([
            'message' => 'Service created successfully',
            'service' => $service,
        ], 201);
    }

    public function updateService(Request $request, int $serviceId): JsonResponse
    {
        $userId = auth()->id();

        $service = Service::where('user_id', $userId)->findOrFail($serviceId);

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'sometimes|required|integer|min:15',
            'price' => 'sometimes|required|numeric|min:0',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $service->update($request->validated());

        return response()->json([
            'message' => 'Service updated successfully',
            'service' => $service,
        ]);
    }

    public function deleteService(int $serviceId): JsonResponse
    {
        $userId = auth()->id();

        $service = Service::where('user_id', $userId)->findOrFail($serviceId);
        $service->delete();

        return response()->json([
            'message' => 'Service deleted successfully',
        ]);
    }

    private function createDefaultWorkingHours(int $userId): void
    {
        $defaultHours = [
            ['day_of_week' => 1, 'is_working_day' => true, 'start_time' => '09:00', 'end_time' => '18:00'],
            ['day_of_week' => 2, 'is_working_day' => true, 'start_time' => '09:00', 'end_time' => '18:00'],
            ['day_of_week' => 3, 'is_working_day' => true, 'start_time' => '09:00', 'end_time' => '18:00'],
            ['day_of_week' => 4, 'is_working_day' => true, 'start_time' => '09:00', 'end_time' => '18:00'],
            ['day_of_week' => 5, 'is_working_day' => true, 'start_time' => '09:00', 'end_time' => '18:00'],
            ['day_of_week' => 6, 'is_working_day' => false, 'start_time' => null, 'end_time' => null],
            ['day_of_week' => 0, 'is_working_day' => false, 'start_time' => null, 'end_time' => null],
        ];

        foreach ($defaultHours as $hours) {
            WorkingHour::create([
                'user_id' => $userId,
                ...$hours,
            ]);
        }
    }
}
