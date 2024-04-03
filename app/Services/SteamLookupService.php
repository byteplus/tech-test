<?php

namespace App\Services;

use App\Clients\ClientInterface;
use Illuminate\Support\Facades\Log;

class SteamLookupService
{
    protected $httpClient;

    public function __construct(ClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function lookup(string $id): array
    {
        $url = "https://ident.tebex.io/usernameservices/4/username/{$id}";

        try {
            $responseData = $this->httpClient->get($url);

            if (! empty($responseData)) {
                return [
                    'id' => $responseData->id,
                    'username' => $responseData->username,
                    'avatar' => $responseData->meta->avatar,
                ];
            } else {
                return [
                    'error' => 'Failed to retrieve user data'
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
