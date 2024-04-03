<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Clients\ClientInterface;

class MinecraftLookupService
{
    protected $httpClient;

    public function __construct(ClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function lookup(string $username = null, string $id = null): array
    {
        if ($username) {
            // Username lookup
            $url = "https://api.mojang.com/users/profiles/minecraft/{$username}";
        } else {
            // ID lookup
            $url = "https://sessionserver.mojang.com/session/minecraft/profile/{$id}";
        }

        try {
            $responseData = $this->httpClient->get($url);

            if (! empty($responseData)) {
                return [
                    'id' => $responseData->id,
                    'username' => $responseData->name,
                    'avatar' => "https://crafatar.com/avatars/" . $responseData->id,
                ];
            } else {
                return [
                    'error' => 'Failed to retrieve user data',
                ];
            }
        } catch (\Exception $e) {
            Log::error('An error occurred: ' . $e->getMessage());

            return [
                'error' => 'An error occurred',
            ];
        }
    }
}
