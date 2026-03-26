<?php

namespace App\Services;

use App\Repositories\Eloquent\ClientRepository;

class ClientService
{
    public function __construct(
        protected ClientRepository $clientRepository
    ) {}

    public function getClients(int $userId, int $perPage = 15)
    {
        return $this->clientRepository->getByUser($userId, $perPage);
    }

    public function searchClients(int $userId, string $query, int $perPage = 15)
    {
        if (empty($query)) {
            return $this->getClients($userId, $perPage);
        }

        return $this->clientRepository->searchByUser($userId, $query, $perPage);
    }

    public function getClient(int $userId, int $clientId)
    {
        $client = $this->clientRepository->find($clientId);
        
        if (!$client || $client->user_id !== $userId) {
            throw new \Exception('Client not found', 404);
        }

        return $client->load('appointments');
    }

    public function createClient(int $userId, array $data)
    {
        $data['user_id'] = $userId;
        return $this->clientRepository->create($data);
    }

    public function updateClient(int $userId, int $clientId, array $data)
    {
        $client = $this->getClient($userId, $clientId);
        
        // Remove user_id from data to prevent manipulation
        unset($data['user_id']);
        
        $this->clientRepository->update($clientId, $data);
        
        return $this->clientRepository->find($clientId);
    }

    public function deleteClient(int $userId, int $clientId): bool
    {
        $client = $this->getClient($userId, $clientId);
        return $this->clientRepository->delete($clientId);
    }

    public function getClientStats(int $userId): array
    {
        $clients = $this->clientRepository->getByUser($userId, 10000);
        
        return [
            'total_clients' => $clients->total(),
            'new_this_month' => $this->clientRepository->getByUser($userId)->where('created_at', '>=', now()->startOfMonth())->count(),
        ];
    }
}
