<?php

namespace Tests\Unit\Http\Controller;

use Tests\TestCase;
use Illuminate\Http\Request;
use App\Services\XblLookupService;
use App\Services\SteamLookupService;
use App\Services\MinecraftLookupService;
use App\Http\Controllers\LookupController;

class LookupControllerTest extends TestCase
{
    public function test_minecraft_lookup_with_id()
    {
        $minecraftLookupService = $this->createMock(MinecraftLookupService::class);
        $steamLookupService = $this->createMock(SteamLookupService::class);
        $xblLookupService = $this->createMock(XblLookupService::class);

        $controller = new LookupController(
            $minecraftLookupService,
            $steamLookupService,
            $xblLookupService
        );

        $data = [
            'username' => 'Notch',
            'id' => 'some_id',
            'avatar' => 'avatar_url'
        ];

        $minecraftLookupService->expects($this->once())
            ->method('lookup')
            ->with(null, $data['id'])
            ->willReturn($data);

        $request = Request::create('/lookup', 'GET', [
            'type' => 'minecraft',
            'id' => $data['id'],
        ]);

        $response = $controller->lookup($request);

        $this->assertEquals($data, $response);
    }

    public function test_minecraft_lookup_with_username()
    {
        $minecraftLookupService = $this->createMock(MinecraftLookupService::class);
        $steamLookupService = $this->createMock(SteamLookupService::class);
        $xblLookupService = $this->createMock(XblLookupService::class);

        $controller = new LookupController(
            $minecraftLookupService,
            $steamLookupService,
            $xblLookupService
        );

        $data = [
            'username' => 'Notch',
            'id' => 'some_id',
            'avatar' => 'avatar_url'
        ];

        $minecraftLookupService->expects($this->once())
            ->method('lookup')
            ->with($data['username'], null)
            ->willReturn($data);

        $request = Request::create('/lookup', 'GET', [
            'type' => 'minecraft',
            'username' => $data['username'],
        ]);

        $response = $controller->lookup($request);

        $this->assertEquals($data, $response);
    }

    public function test_steam_lookup()
    {
        $minecraftLookupService = $this->createMock(MinecraftLookupService::class);
        $steamLookupService = $this->createMock(SteamLookupService::class);
        $xblLookupService = $this->createMock(XblLookupService::class);

        $controller = new LookupController(
            $minecraftLookupService,
            $steamLookupService,
            $xblLookupService
        );

        $data = [
            'id' => 'some_id',
            'username' => 'User Name',
            'avatar' => 'avatar_url',
        ];

        $steamLookupService->expects($this->once())
            ->method('lookup')
            ->with($data['id'])
            ->willReturn($data);

        $request = Request::create('/lookup', 'GET', [
            'type' => 'steam',
            'id' => $data['id'],
        ]);

        $response = $controller->lookup($request);

        $this->assertEquals($data, $response);
    }

    public function test_steam_lookup_with_error()
    {
        $minecraftLookupService = $this->createMock(MinecraftLookupService::class);
        $steamLookupService = $this->createMock(SteamLookupService::class);
        $xblLookupService = $this->createMock(XblLookupService::class);

        $controller = new LookupController(
            $minecraftLookupService,
            $steamLookupService,
            $xblLookupService
        );

        // Not providing the 'id' will trigger validation error
        $request = Request::create('/lookup', 'GET', [
            'type' => 'steam',
        ]);

        $response = $controller->lookup($request);

        // Expected validation error
        $response = json_decode($response->content(), true)['type'][0];
        $this->assertEquals('Steam only supports IDs', $response);
    }
}
