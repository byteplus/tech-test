<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Log;
use App\Http\Clients\ClientInterface;

class XblLookupService
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
            $params = "{$username}?type=username";
        } else {
            // ID lookup
            $params = $id;
        }

        $url = "https://ident.tebex.io/usernameservices/3/username/" . $params;

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
