<?php

namespace App\Repositories;

use App\Models\Cv;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class CvRepository implements RepositoryInterface
{
    private Cv $model;

    public function __construct(Cv $cv)
    {
        $this->model = $cv;
    }

    public function find($id)
    {
        $this->model = $this->model->find($id);
    }


    public function save(): Cv
    {
        $this->model->save();
        return $this->model;
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function getId()
    {
        return $this->model->id;
    }

    public function getSkills()
    {
        return $this->model->skills;
    }

    public function getProjects()
    {
        return $this->model->recentProjects;
    }

    public function createSkills($skills)
    {
        $this->model->skills()->createMany($skills);
    }

    public function createProjects($projects)
    {
        $this->model->recentProjects()->createMany($projects);
    }

    public function getByUserId($userId): Cv | null
    {
        return $this->model->where('user_id', $userId)->first();
    }

    public function allByUserId($userId): Collection
    {
        return $this->model->where('user_id', $userId)->get();
    }

    public function fillData(array $data)
    {
        $this->model->slug = $data['slug'];
        $this->model->user_id = $data['user_id'];
        $this->model->position = $data['position'] ?? null;
        $this->model->type = $data['type'] ?? null;
        $this->model->office_type = $data['office_type'] ?? null;
        $this->model->status = $data['status'] ?? null;
        $this->model->is_ready_to_relocate = $data['is_ready_to_relocate'] ?? null;
        $this->model->currency = $data['currency'] ?? null;
        $this->model->desired_salary = $data['desired_salary'] ?? null;
        $this->model->minimal_salary = $data['minimal_salary'] ?? null;
        $this->model->about = $data['about'] ?? null;
        $this->model->is_draft = $data['is_draft'] ?? null;

        if (!$this->model->id){
            $this->save();
        }

        if ($data['industry']) {
            $domains = collect($data['industry'])->map(function ($i) {
                return ['domain_id' => $i];
            });

            $this->model->domains()->sync($domains);
        }

        if ($data['languages']) {
            $this->model->languages()->sync($data['languages']);
        }
    }
}
