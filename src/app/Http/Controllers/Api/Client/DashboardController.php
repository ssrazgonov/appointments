<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        $client = auth()->user();

        // Get all appointments
        $appointments = Appointment::where('client_account_id', $client->id)
            ->with(['user.masterProfile', 'service'])
            ->orderByDesc('start_time')
            ->get();

        // Group by status
        $upcoming = $appointments->whereIn('status', ['scheduled', 'confirmed']);
        $completed = $appointments->where('status', 'completed');
        $cancelled = $appointments->where('status', 'cancelled');

        // Get unique masters
        $masters = $appointments->groupBy('user_id')->map(function ($group) {
            $appointment = $group->first();
            return [
                'id' => $appointment->user_id,
                'name' => $appointment->user->masterProfile->display_name ?? $appointment->user->name,
                'slug' => $appointment->user->masterProfile->slug ?? null,
                'avatar_url' => $appointment->user->masterProfile->avatar_url ?? $appointment->user->avatar_url,
                'last_visit' => $group->max('start_time'),
                'total_visits' => $group->count(),
            ];
        })->values();

        return response()->json([
            'appointments' => [
                'all' => $appointments,
                'upcoming' => $upcoming,
                'completed' => $completed,
                'cancelled' => $cancelled,
            ],
            'masters' => $masters,
            'stats' => [
                'total_appointments' => $appointments->count(),
                'upcoming_count' => $upcoming->count(),
                'completed_count' => $completed->count(),
            ],
        ]);
    }

    public function masters(): JsonResponse
    {
        $client = auth()->user();

        $masters = Appointment::where('client_account_id', $client->id)
            ->with(['user.masterProfile'])
            ->get()
            ->groupBy('user_id')
            ->map(function ($group) {
                $appointment = $group->first();
                $user = $appointment->user;
                $profile = $user->masterProfile;

                return [
                    'id' => $user->id,
                    'slug' => $profile->slug ?? null,
                    'display_name' => $profile->display_name ?? $user->name,
                    'avatar_url' => $profile->avatar_url ?? $user->avatar_url,
                    'bio' => $profile->bio,
                    'last_visit' => $group->max('start_time'),
                    'total_visits' => $group->count(),
                    'services' => $group->pluck('service')->filter()->unique('id')->values(),
                ];
            })
            ->values();

        return response()->json([
            'masters' => $masters,
        ]);
    }

    public function appointmentHistory(Request $request): JsonResponse
    {
        $client = auth()->user();
        $status = $request->get('status', 'all');

        $query = Appointment::where('client_account_id', $client->id)
            ->with(['user.masterProfile', 'service']);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $appointments = $query->orderByDesc('start_time')->paginate(20);

        return response()->json($appointments);
    }
}
