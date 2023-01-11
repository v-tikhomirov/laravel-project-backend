<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Benefit;
use App\Models\Domain;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Nnjeim\World\World;
use Nnjeim\World\WorldHelper;

class CommonController extends Controller
{
    public const CACHE_TAG = 'common:cache:';

    public function countries(): JsonResponse
    {
        if (Cache::has(self::CACHE_TAG . 'countries')) {
            $out = Cache::get(self::CACHE_TAG . 'countries');
        } else {
            $list = World::countries();
            $out = $this->prepareArrayForSelect($list->data);
            Cache::put(self::CACHE_TAG . 'countries', $out);
        }

        return response()->json(
            getResponseStructure($out)
        );
    }

    public function cities($id): JsonResponse
    {
        $world = new WorldHelper();
        $action = $world->cities([
            'filters' => [
                'country_id' => $id,
            ],
        ]);

        if ($action->success) {
            $out = $this->prepareArrayForSelect($action->data);
            return response()->json(
                getResponseStructure($out)
            );
        } else {
            return response()->json(
                getResponseStructure([], false)
            );
        }
    }

    public function languages(): JsonResponse
    {
        if (Cache::has(self::CACHE_TAG . 'languages')) {
            $out = Cache::get(self::CACHE_TAG . 'languages');
        } else {
            $list = World::languages();
            $out = $this->prepareArrayForSelect($list->data);
            Cache::put(self::CACHE_TAG . 'languages', $out);
        }

        return response()->json(
            getResponseStructure($out)
        );
    }

    public function domains(): JsonResponse
    {
        if (Cache::has(self::CACHE_TAG . 'domains')) {
            $out = Cache::get(self::CACHE_TAG . 'domains');
        } else {
            $out = $this->prepareArrayForSelect(Domain::all());
            Cache::put(self::CACHE_TAG . 'domains', $out);
        }

        return response()->json(
            getResponseStructure($out)
        );
    }

    public function benefits(): JsonResponse
    {
        if (Cache::has(self::CACHE_TAG . 'benefits')) {
            $out = Cache::get(self::CACHE_TAG . 'benefits');
        } else {
            $out = $this->prepareArrayForSelect(Benefit::all());
            Cache::put(self::CACHE_TAG . 'benefits', $out);
        }

        return response()->json(
            getResponseStructure($out)
        );
    }

    protected function prepareArrayForSelect($array, $label = 'name', $value = 'id'): array
    {
        $out = [];
        foreach($array as $item) {
            $out[] = [
                'label' => $item[$label],
                'value' => $item[$value]
            ];
        }

        return $out;
    }
}
