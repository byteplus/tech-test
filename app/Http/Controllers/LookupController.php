<?php

namespace App\Http\Controllers;

use App\Services\LookupCacheService;
use App\Services\MinecraftLookupService;
use App\Services\SteamLookupService;
use App\Services\XblLookupService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LookupController extends Controller
{
    protected $minecraftLookupService;
    protected $steamLookupService;
    protected $xblLookupService;

    public function __construct(
        MinecraftLookupService $minecraftLookupService,
        SteamLookupService $steamLookupService,
        XblLookupService $xblLookupService
    ) {
        $this->minecraftLookupService = $minecraftLookupService;
        $this->steamLookupService = $steamLookupService;
        $this->xblLookupService = $xblLookupService;
    }

    public function lookup(Request $request)
    {
        $validator = $this->validateRequest($request);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $type = $request->get('type');
        $id = $request->get('id');
        $username = $request->get('username');

        $cacheKey = LookupCacheService::generateCacheKey($type, $id, $username);

        if (LookupCacheService::has($cacheKey)) {
            return LookupCacheService::get($cacheKey);
        }

        $data = $this->fetchDataFromService($type, $id, $username);

        LookupCacheService::put($cacheKey, $data, 60);

        return $data;
    }

    private function validateRequest(Request $request)
    {
        return Validator::make($request->all(), [
            'type' => [
                'required',
                'in:minecraft,steam,xbl',
                function (string $attribute, mixed $value, Closure $fail) use ($request) {
                    if ($value === 'steam' && empty($request->id)) {
                        return $fail("Steam only supports IDs");
                    }
                    if (empty($request->id) && empty($request->username)) {
                        return $fail("Username or ID should not be empty");
                    }
                }
            ],
            'id' => 'sometimes|string|min:1|max:36',
            'username' => 'sometimes|string|min:1|max:200',
        ]);
    }

    private function fetchDataFromService(string $type, ?string $id, ?string $username): array
    {
        switch ($type) {
            case 'minecraft':
                return $this->minecraftLookupService->lookup(username: $username, id: $id);
            case 'steam':
                return $this->steamLookupService->lookup(id: $id);
            case 'xbl':
                return $this->xblLookupService->lookup(username: $username, id: $id);
            default:
                return ['error' => 'Invalid lookup type'];
        }
    }
}

