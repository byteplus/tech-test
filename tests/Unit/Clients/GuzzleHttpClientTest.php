<?php

namespace Tests\Unit\Clients;

use Tests\TestCase;
use App\Clients\GuzzleHttpClient;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Response;

class GuzzleHttpClientTest extends TestCase
{
    public function test_get_success()
    {
        $mockedGuzzleClient = $this->getMockBuilder(GuzzleClient::class)
            ->disableOriginalConstructor()
            ->getMock();

        $data = [
            "id" => "069a79f444e94726a5befca90e38aaf5",
            "name" => "Notch"
        ];
        $mockedResponse = new Response(200, [], json_encode($data));

        $mockedGuzzleClient->expects($this->once())
            ->method('get')
            ->willReturn($mockedResponse);

        $httpClient = new GuzzleHttpClient($mockedGuzzleClient);

        $result = $httpClient->get('https://api.website.com');

        $this->assertEquals($data['id'], $result->id);
        $this->assertEquals($data['name'], $result->name);
    }

    public function testGetFailure()
    {
        $mockedGuzzleClient = $this->getMockBuilder(GuzzleClient::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockedResponse = new Response(404);

        $mockedGuzzleClient->expects($this->once())
            ->method('get')
            ->willReturn($mockedResponse);

        $httpClient = new GuzzleHttpClient($mockedGuzzleClient);

        $this->expectException(\Exception::class);

        $httpClient->get('https://api.website.com');
    }
}
