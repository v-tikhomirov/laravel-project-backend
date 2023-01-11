<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SelectOptionsResource;
use App\Http\Resources\TechnologyResource;
use App\Models\Technology;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class TechnologyController extends Controller
{
    public function all(Request $request): JsonResponse
    {
        $out = Technology::orderBy('weight', 'desc')
            ->addSelect(DB::raw('*, id as technology_id'))
            ->where('is_root',1)
            ->get();
        $out = $out->groupBy('type');
        $user = auth()->user();
        $user->load('skills.technology');
        foreach ($user->skills as $skill) {
            if ($skill->technology->is_root){
                $out2 = Technology::orderBy('weight', 'desc')
                    ->addSelect(DB::raw('*, id as technology_id'))
                    ->where('group', $skill->technology->group)
                    ->where('is_root',0)
                    ->get();
                $out2 = $out2->groupBy('type')->toArray();
                $out = $out->mergeRecursive($out2);
            }
        }

        return response()->json($out);
    }

    public function allLight(): JsonResponse
    {
        if (Cache::has('technologies:all:light')) {
            $resource = Cache::get('technologies:all:light');
        } else {
            $data = Technology::select(['id', 'name'])->orderBy('weight', 'desc')->get();
            $resource = SelectOptionsResource::collection($data)->resolve();
            Cache::put('technologies:all:light', $resource);
        }

        return response()->json(
            getResponseStructure($resource)
        );
    }

    public function getByGroup($group): JsonResponse
    {

        $data = Technology::orderBy('weight', 'desc')
            ->whereIn('group', explode(',',$group))
            ->where('is_root',0)
            ->get();
        $resource = collect(TechnologyResource::collection($data)->resolve());
        return response()->json(
            getResponseStructure($resource->groupBy('type'))
        );
    }

    public function getRoot(): JsonResponse
    {
        $data = Technology::orderBy('weight', 'desc')
            ->where('is_root', 1)->get();
        $resource = collect(TechnologyResource::collection($data)->resolve());

        return response()->json(
            getResponseStructure($resource->groupBy('type'))
        );
    }

    public function getLanguages(): JsonResponse
    {
        $data = Technology::orderBy('weight', 'desc')
            ->where('type', Technology::TYPE_LANGUAGE)->get();
        $resource = collect(TechnologyResource::collection($data)->resolve());

        return response()->json(
            getResponseStructure($resource->groupBy('type'))
        );
    }


}
