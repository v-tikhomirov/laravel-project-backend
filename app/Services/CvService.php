<?php

namespace App\Services;

use App\Models\Cv;
use App\Repositories\CvRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use PHPUnit\Exception;

class CvService
{
    private CvRepository $cvRepository;

    public function __construct(CvRepository $cvRepository)
    {
        $this->cvRepository = $cvRepository;
    }

    public function saveDraft(array $data)
    {
        $user = auth()->user();
        $user_id = $user->id;
        $slug = md5(Carbon::now() . ':' . $user_id);
        $primary = $data['primary'];
        $primary['slug'] = $slug;
        $primary['user_id'] = $user_id;
        $primary['is_draft'] = 1;

        if (isset($data['id'])) {
            $primary['id'] = $data['id'];
        }

        DB::beginTransaction();

        try {
            $this->fillData($primary);
            if (!empty($data['skills'])) {
                $this->syncSkills($data['skills']);
            }
            if (!empty($data['recent_projects'])){
                $this->syncProjects($data['recent_projects']);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

    }

    public function getCurrent(): Cv | null
    {
        $user = auth()->user();
        return $this->cvRepository->getByUserId($user->id);
    }

    public function getId()
    {
        return $this->cvRepository->getId();
    }

    public function getAll(): Collection
    {
        $user = auth()->user();
        return $this->cvRepository->allByUserId($user->id);
    }

    public function fillData(array $data): void
    {
        if (isset($data['id'])) {
            $this->cvRepository->find($data['id']);
        }
        $this->cvRepository->fillData($data);
        $this->cvRepository->save();
    }

    public function syncSkills(array $skills): void
    {
        foreach ($this->cvRepository->getSkills() as $skill) {
            $toDelete = true;
            foreach ($skills as $key => $skillData) {
                if ($skill->technology_id == $skillData['technology_id']) {
                    $toDelete = false;
                    if ($skill->experience != $skillData['experience']) {
                        $skill->update(['experience' => $skillData['experience']]);
                    }
                    unset($skills[$key]);
                    break;
                }
            }
            if ($toDelete) {
                $skill->delete();
            }
        }
        if (count($skills) > 0) {
            $this->cvRepository->createSkills($skills);
        }
    }

    public function syncProjects(array $projects): void
    {
        foreach ($this->cvRepository->getProjects() as $recentProject) {
            $toDelete = true;
            foreach ($projects as $key => $project) {
                if (isset($project['id']) && $recentProject->id == $project['id']) {
                    $toDelete = false;
                    $recentProject->update($project);
                    unset($projects[$key]);
                    break;
                }
            }
            if ($toDelete) {
                $recentProject->delete();
            }

        }
        if (count($projects) > 0) {
            $this->cvRepository->createProjects($projects);
        }
    }
}
