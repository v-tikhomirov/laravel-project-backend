<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCvRequest;
use App\Http\Requests\CvUpdateRequest;
use App\Http\Requests\DraftCvRequest;
use App\Http\Resources\CvFullResource;
use App\Jobs\TryToMatchJob;
use App\Models\Cv;
use App\Models\SkillSet;
use App\Models\User\Profile;
use App\Services\CvService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use PHPUnit\Exception;

class CvController extends Controller
{
    private CvService $cvService;

    public function __construct(CvService $cvService)
    {
        $this->cvService = $cvService;
    }
    public function index(): JsonResponse
    {
        $cvs = $this->cvService->getAll();
        return response()->json(['success' => true, 'cvs' => $cvs]);
    }

    public function get(): JsonResponse
    {
        $cv = $this->cvService->getCurrent();
        $resource = null;
        if ($cv) {
            $resource = CvFullResource::make($cv)->resolve();
        }

        return response()->json(
            getResponseStructure($resource)
        );
    }

    public function getBySlug($slug): JsonResponse
    {
        $cv = Cv::with('skills')->where('slug', $slug)->first();

        return response()->json(
            getResponseStructure(CvFullResource::make($cv)->resolve())
        );
    }

    public function draft(DraftCvRequest $request): JsonResponse
    {
        try {
            $this->cvService->saveDraft($request->validated());
        } catch (Exception $e) {
            return response()->json(
                getResponseStructure([], false, $e->getMessage())
            );
        }

        return response()->json(
            getResponseStructure([])
        );
    }

    public function create(CreateCvRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = auth()->user();
        $user_id = $user->id;
        $primary = $data['primary'];
        $primary = [
            'slug' => md5(Carbon::now() . ':' . $user_id),
            'user_id' => $user_id,
            'position'=> $primary['position'],
            'type'=> $primary['type'],
            'office_type'=> $primary['office_type'],
            'status'=> $primary['status'],
            'is_ready_to_relocate'=> $primary['is_ready_to_relocate'],
            'currency'=> $primary['currency'],
            'desired_salary'=> $primary['desired_salary'],
            'minimal_salary'=> $primary['minimal_salary'],
            'about'=> $primary['about'],
            'is_draft' => 0
        ];

        $cv = Cv::create($primary);
        $cv->skills()->createMany($data['skills']);
        $domains = collect($data['primary']['industry'])->map(function ($i) {
            return ['domain_id' => $i];
        });
        $cv->domains()->sync($domains);
        $cv->languages()->sync($data['primary']['languages']);
        $cv->recentProjects()->createMany($data['recent_projects']);

        if ($data['id']) {
            $draftCv = Cv::find($data['id']);
            $draftCv->delete();
        }

        TryToMatchJob::dispatchAfterResponse($cv);


        return response()->json(
            getResponseStructure(['id' => $cv->id])
        );
    }

    public function update(CvUpdateRequest $request): JsonResponse
    {
        $data = $request->validated();

        $cv = Cv::find($data['id']);
        if ($cv) {
            $cv->load(['skills','domains', 'languages', 'recentProjects']);
            switch ($data['step']) {
                case 'primary':
                    $primary = [
                        'position'=> $data['primary']['position'],
                        'type'=> $data['primary']['type'],
                        'office_type'=> $data['primary']['office_type'],
                        'status'=> $data['primary']['status'],
                        'is_ready_to_relocate'=> $data['primary']['is_ready_to_relocate'],
                        'currency'=> $data['primary']['currency'],
                        'desired_salary'=> $data['primary']['desired_salary'],
                        'minimal_salary'=> $data['primary']['minimal_salary'],
                        'about'=> $data['primary']['about'],
                    ];
                    $cv->update($primary);
                    $domains = collect($data['primary']['industry'])->map(function ($i) {
                        return ['domain_id' => $i];
                    });
                    $cv->domains()->sync($domains);
                    $cv->languages()->sync($data['primary']['languages']);
                    break;
                case 'skills':
                    foreach ($cv->skills as $skill) {
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
                        $cv->skills()->createMany($data['skills']);
                    }
                    break;

                case 'recent_projects':
                    foreach ($cv->recentProjects as $recentProject) {
                        $toDelete = true;
                        foreach ($data['recent_projects'] as $key => $project) {
                            if (isset($project['id']) && $recentProject->id == $project['id']) {
                                $toDelete = false;
                                $recentProject->update($project);
                                unset($data['recent_projects'][$key]);
                                break;
                            }
                        }
                        if ($toDelete) {
                            $recentProject->delete();
                        }

                    }
                    if (count($data['recent_projects']) > 0) {
                        $cv->recentProjects()->createMany($data['recent_projects']);
                    }
                    break;
            }
        }

        $cv = Cv::find($data['id']);
        $cv->load(['skills','domains', 'languages', 'recentProjects']);

        TryToMatchJob::dispatchAfterResponse($cv);

        return response()->json(
            getResponseStructure(CvFullResource::make($cv)->resolve())
        );
    }
}
