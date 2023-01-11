<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VacancyCreateRequest;
use App\Http\Requests\VacancyUpdateRequest;
use App\Http\Resources\VacancyFullResource;
use App\Http\Resources\VacancyResource;
use App\Jobs\TryToMatchJob;
use App\Models\SkillSet;
use App\Models\Vacancy;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VacancyController extends Controller
{
    public function index(): JsonResponse
    {
        $user = auth()->user();
        $user->load(['companies']);
        $company = $user->companies->first();
        if ($company) {
            $vacancies = Vacancy::with('skills.technology')->where('company_id', $company->id)->get();
            $resource = collect(VacancyResource::collection($vacancies)->toArray(\request()));
            $grouped = $resource->groupBy('status');

            return response()->json(
                getResponseStructure($grouped)
            );
        }

        return response()->json(['success' => false, 'message' => 'Something went wrong']);
    }

    public function create(VacancyCreateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user_id = auth()->user()->id;
        $general = $data['general'];
        $vacancyData = [
            'company_id' => $data['company_id'],
            'slug' => md5(Carbon::now() . ':' . $user_id),
            'created_by' => $user_id,
            'status' => $data['status'] == 'draft' ? Vacancy::STATUS_DRAFT : Vacancy::STATUS_ACTIVE,
            'position' => $general['position'],
            'office_type' => $general['office_type'],
            'is_ready_to_relocate' => $general['is_ready_to_relocate'],
            'relocation_benefits' => $general['relocation_benefits'] ?? null,
            'city_id' => $general['location']['city'],
            'country_id' => $general['location']['country'],
            'currency' => $general['currency'],
            'max_salary' => $general['max_salary'],
            'desired_salary' => $general['desired_salary'],
            'description' => $general['description'],
            'about' => $data['about'],
        ];

        $vacancy = Vacancy::create($vacancyData);
        $domains = collect($general['industry'])->map(function ($i) {
            return ['domain_id' => $i];
        });
        $vacancy->domains()->sync($domains);
        $vacancy->languages()->sync($general['languages']);
        $vacancy->benefits()->sync($general['benefits']);
        $vacancy->skills()->createMany($data['skills']);

        TryToMatchJob::dispatchAfterResponse($vacancy);

        return response()->json(
            getResponseStructure([
                'id' => $vacancy->id
            ])
        );
    }

    public function createSkills(Request $request): JsonResponse
    {
        $vacancy = Vacancy::find($request->get('id'));
        $skills = $request->get('skills');
        $vacancy->skills()->createMany($skills);
        $vacancy->status = Vacancy::STATUS_ACTIVE;
        $vacancy->save();

        return response()->json([
            getResponseStructure([])
        ]);
    }

    public function archive(Request $request): JsonResponse
    {
        $vacancy = Vacancy::find($request->get('id'));
        if ($vacancy){
            $vacancy->status = Vacancy::STATUS_ARCHIVE;
            $vacancy->save();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false]);
    }

    public function load($slug): JsonResponse
    {
        $vacancy = Vacancy::where('slug', $slug)->first();
        if ($vacancy) {
            $vacancy->load(['domains', 'languages', 'skills']);

            return response()->json(
                getResponseStructure(VacancyFullResource::make($vacancy)->resolve())
            );
        }

        return response()->json(
            getResponseStructure([], false, 'not found')
        );
    }

    public function update(VacancyUpdateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $vacancy = Vacancy::find($data['id']);
        if ($vacancy) {
            switch ($data['step']) {
                case 'general':
                    $general = $data['general'];
                    $vacancyData = [
                        'position' => $general['position'],
                        'office_type' => $general['office_type'],
                        'is_ready_to_relocate' => $general['is_ready_to_relocate'],
                        'relocation_benefits' => $general['relocation_benefits'] ?? null,
                        'city_id' => $general['location']['city'],
                        'country_id' => $general['location']['country'],
                        'currency' => $general['currency'],
                        'max_salary' => $general['max_salary'],
                        'desired_salary' => $general['desired_salary'],
                        'description' => $general['description']
                    ];
                    $domains = collect($general['industry'])->map(function ($i) {
                        return ['domain_id' => $i];
                    });
                    $vacancy->update($vacancyData);
                    $vacancy->domains()->sync($domains);
                    $vacancy->languages()->sync($general['languages']);
                    $vacancy->benefits()->sync($general['benefits']);
                    break;
                case 'skills':
                    foreach ($vacancy->skills as $skill) {
                        $toDelete = true;
                        foreach ($data['skills'] as $key => $skillData) {
                            if ($skill->technology_id == $skillData['technology_id']) {
                                $toDelete = false;
                                if ($skill->experience != $skillData['experience']) {
                                    $skill->update(['experience' => $skillData['experience']]);
                                }
                                unset($data['skills'][$key]);
                                break;
                            }
                        }
                        if ($toDelete) {
                            $skill->delete();
                        }
                    }
                    if (count($data['skills']) > 0) {
                        $vacancy->skills()->createMany($data['skills']);
                    }
                    break;
                case 'about':
                    $vacancy->update(['about' => $data['about']]);
                    break;
            }

            if ($vacancy->status === 'draft' && $data['status'] === 'active') {
                $vacancy->status = Vacancy::STATUS_ACTIVE;
                $vacancy->save();
            }


            return response()->json(
                getResponseStructure([
                    'id' => $vacancy->id
                ])
            );
        }

        return response()->json(
            getResponseStructure([], false, 'not found')
        );
    }
}
