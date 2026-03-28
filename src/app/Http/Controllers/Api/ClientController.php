<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StoreClientRequest;
use App\Http\Requests\Client\UpdateClientRequest;
use App\Services\ClientService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    public function __construct(
        protected ClientService $clientService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $userId = auth()->id();
        $query = $request->get('q', '');
        $perPage = $request->get('per_page', 50);

        // Get unique clients from appointments
        $clientsQuery = \DB::table('appointments')
            ->where('user_id', $userId)
            ->whereNotNull('client_name')
            ->select([
                'client_name as name',
                'client_phone as phone',
                'client_email as email',
                \DB::raw('COUNT(*) as total_appointments'),
                \DB::raw('MAX(created_at) as last_appointment'),
            ])
            ->groupBy('client_name', 'client_phone', 'client_email')
            ->orderByDesc('last_appointment');

        // Search filter
        if ($query) {
            $clientsQuery->where(function($q) use ($query) {
                $q->where('client_name', 'like', "%{$query}%")
                  ->orWhere('client_phone', 'like', "%{$query}%")
                  ->orWhere('client_email', 'like', "%{$query}%");
            });
        }

        $clients = $clientsQuery->paginate($perPage);

        // Format for frontend
        $clients->getCollection()->transform(function($client) {
            return [
                'id' => null,
                'name' => $client->name,
                'phone' => $client->phone,
                'email' => $client->email,
                'total_appointments' => $client->total_appointments,
                'last_appointment' => $client->last_appointment,
            ];
        });

        return response()->json($clients);
    }

    public function show(int $id): JsonResponse
    {
        try {
            $client = $this->clientService->getClient(auth()->id(), $id);
            return response()->json($client);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    public function store(StoreClientRequest $request): JsonResponse
    {
        try {
            $client = $this->clientService->createClient(auth()->id(), $request->validated());
            return response()->json([
                'message' => 'Client created successfully',
                'client' => $client,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create client',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    public function update(UpdateClientRequest $request, int $id): JsonResponse
    {
        try {
            $client = $this->clientService->updateClient(auth()->id(), $id, $request->validated());
            return response()->json([
                'message' => 'Client updated successfully',
                'client' => $client,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update client',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->clientService->deleteClient(auth()->id(), $id);
            return response()->json([
                'message' => 'Client deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete client',
                'error' => $e->getMessage(),
            ], 404);
        }
    }
}
