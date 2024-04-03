<?php

namespace App\Clients;

interface ClientInterface
{
    public function get(string $url, array $options = []): object;

    // Other methods, PUT, POST, etc.
}

