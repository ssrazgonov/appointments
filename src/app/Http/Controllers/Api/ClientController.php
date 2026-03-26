<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StoreClientRequest;
use App\Http\Requests\Client\UpdateClientRequest;
use App\Services\ClientService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function __construct(
        protected ClientService $clientService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $userId = auth()->id();
        $query = $request->get('q', '');
        $perPage = $request->get('per_page', 15);

        if ($query) {
            $clients = $this->clientService->searchClients($userId, $query, $perPage);
        } else {
            $clients = $this->clientService->getClients($userId, $perPage);
        }

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
