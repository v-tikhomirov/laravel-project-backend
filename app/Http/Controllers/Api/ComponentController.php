<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ComponentTechnology;
use App\Models\Technology;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ComponentController extends Controller
{
    public function vacancies()
    {

    }

    public function technology(Request $request): JsonResponse
    {
        $search = $request->get('search');
        $cacheKey = 'technologies_component';
        if ($search) {
            $cacheKey.='_'.$search;
        }
        if (Cache::has($cacheKey)) {
            return response()->json(
                getResponseStructure(Cache::get($cacheKey))
            );
        }
        $query = Technology::query();
        if ($search) {
            $query->where('name','like', '%'.$search. '%');
        }
        $technologies = $query->get();
        $technologies = ComponentTechnology::collection($technologies)->resolve();
        Cache::put($cacheKey, $technologies);

        return response()->json(
            getResponseStructure($technologies)
        );
    }

    public function loadCards(Request $request): JsonResponse
    {
        $type = $request->get('type', 'cv');
        if ($type === 'cv') {
            $json = Storage::disk('data')->get('cards.json');
        } else {
            $json = Storage::disk('data')->get('companies.json');
        }

        $decoded = json_decode($json, true);

        $skills = $request->get('skills');
        $skillsNames = [];

        if ($skills) {
            foreach ($skills as $skill) {
                $skillsNames[] = strtolower($skill['name']);
            }
        } else {
            $skillsNames = array_keys($decoded);
        }

        $out = [];

        foreach ($decoded as $key => $item) {
            if (in_array(strtolower($key), $skillsNames)) {
                foreach ($item as &$card) {
                    if ($card['image']) {
                        $card['image'] = url('/media/images/' . $card['image']);
                    }
                }
                $out = array_merge($out, $item);
            }
        }

        shuffle($out);
        if (count($out) > 10) {
            $out = array_chunk($out, 10);
            return response()->json(
                getResponseStructure($out[0])
            );
        }

        return response()->json(
            getResponseStructure($out)
        );

    }
}
