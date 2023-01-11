<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SkillsResource;
use App\Models\SkillSet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SkillSetController extends Controller
{
    public function all(): JsonResponse
    {
        $skills = auth()->user()->load('skills.technology')->skills;
        return response()->json([
            'success' => true,
            'skills' => SkillsResource::collection($skills)->resolve()
        ]);
    }

    public function save(Request $request): JsonResponse
    {
        $data = $request->get('skills');
        $skills = SkillSet::prepareSkills($data);
        $user = auth()->user();

        foreach ($user->skills as $skill) {
            if (isset($skills[$skill->technology_id])) {
                $skill->update($skills[$skill->technology_id]);
                unset($skills[$skill->technology_id]);
            }
        }

        if (count($skills) > 0) {
            $user->skills()->createMany($skills);
        }

        return response()->json([
            'success' => true
        ]);
    }
}
