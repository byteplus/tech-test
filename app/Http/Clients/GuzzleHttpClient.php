<?php

namespace App\Http\Clients;

use Exception;
use GuzzleHttp\Client as GuzzleClient;

class GuzzleHttpClient implements ClientInterface
{
    protected $client;

    public function __construct(GuzzleClient $client)
    {
        $this->client = $client;
    }

    public function get(string $url, array $options = []): object
    {
        $response = $this->client->get($url, $options);

        if ($response->getStatusCode() !== 200) {
            // Some logic for log the error...
            throw new Exception('An error has occurred');
        }

        return $this->parseResponse($response);
    }

    protected function parseResponse($response): object
    {
        $body = $response->getBody()->getContents();
        $data = json_decode($body);

        return $data;
    }
}

