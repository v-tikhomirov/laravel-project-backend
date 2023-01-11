<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Cv;
use App\Models\Matching;
use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class MatchService
{
    protected Cv|Vacancy $_currentEntity;
    protected string $_currentEntityClass = '';
    protected string $_operand;
    protected bool $_isHybridOffice;

    protected array $_entitySkills;
    protected array $_entityLanguages;

    protected $langWeight = [
        'a1' => 1,
        'a2' => 2,
        'b1' => 3,
        'b2' => 4,
        'c1' => 5,
        'c2' => 6
    ];

    public static function checkPermissionToView($match, $user): bool
    {
        switch ($user->type) {
            case 'company':
                if (
                    $match->company_id === $user->companies->first()->id &&
                    $match->status > Matching::STATUS_MATCHED &&
                    $match->status < Matching::STATUS_COMPLETE
                ) {
                    return true;
                }
                break;
            case 'user':
                if ($match->user_id === $user->id) {
                    return true;
                }
                break;
        }

        return false;
    }

    /**
     *
     * skills - 50%
     * domain - 10%
     * language - 10%
     * salary - 10%
     * office_type/relocation - 10%
     *
     *
     *
     */

    public function run($entity): void
    {
        $this->_operand = $entity::class === Vacancy::class ? '>=' : '<=';

        $entity->refresh();
        $entity->load('skills.technology');
        $this->_currentEntityClass = $entity::class;
        $this->_currentEntity = $entity;
        $this->match($entity);
    }

    // @TODO refactor whole match function
    protected function match($entity): void
    {
        $this->_entitySkills = $this->prepareSkillsForSearch($entity);
        $this->_isHybridOffice = $entity->getAttribute('office_type') === 'hybrid';
        $this->_entityLanguages = $this->prepareLanguagesForCompare($entity);

//        $collection = $this->getDataFromDatabase($entity, $this->getNewModel());
//        $checkedIds = $this->analyzeDataAndMatch($collection);
//
//        $collection = $this->getDataFromDatabase($entity, $this->getNewModel(), 10, $checkedIds);
//        $checkedIds = array_merge($checkedIds,$this->analyzeDataAndMatch($collection));

        $collection = $this->getDataFromDatabase($entity, $this->getNewModel(), 20);
        $this->analyzeDataAndMatch($collection);
    }

    protected function getDataFromDatabase($entity, $model, $modifier = null, $excludeIds = [])
    {
        $model = $model->newQuery();
        $skills = $this->_entitySkills;
        $operand = $this->_operand;
        $officeHybrid = $this->_isHybridOffice;

        if ($this->_currentEntityClass == Vacancy::class) {
            $model->whereIn('status',[1,2]);
        } else {
            $model->where('status', 1);
        }

        if (!empty($excludeIds)) {
            $model->whereNotIn('id',$excludeIds);
        }

        $model->whereHas('skills', function (Builder $query) use($skills, $operand, $modifier) {
            $query->where(function($q1) use($skills, $operand, $modifier) {
                foreach ($skills as $id => $experience) {
                    $q1->orWhere(function($queryWhere) use($id,$experience, $operand, $modifier) {
                        $queryWhere->where('technology_id',$id);
                        if ($experience) {
                            if ($modifier) {
                                $experience = $this->applyModifier($operand, $experience, $modifier);
                            }
                            $queryWhere->where('experience', $operand , $experience);
                        }
                    });
                }
            });
        });

        $model->where(function($query) use($entity, $modifier) {
            if ($entity::class === Vacancy::class) {
                $maxSalary = $entity->max_salary;
                if ($modifier) {
                    $maxSalary = $this->applyModifier('<=', $maxSalary, $modifier);
                }
                $query->Where('desired_salary', '<=', $maxSalary)
                    ->orWhere('minimal_salary', '<=', $maxSalary);
            } else {
                $minSalary = $entity->minimal_salary;
                if ($modifier) {
                    $minSalary = $this->applyModifier('>=', $minSalary, $modifier);
                }
                $query->Where('desired_salary', '>=', $minSalary)
                    ->orWhere('max_salary', '>=', $minSalary);
            }
        });

        $model->when(!$officeHybrid, function ($query) use($entity) {
            $query->where('office_type', $entity->getRawOriginal('office_type'));
        });
        if ($entity->getAttribute('office_type') !== 'remote') {
            $model->where('is_ready_to_relocate', $entity->getRawOriginal('is_ready_to_relocate'));
        }
        $model->whereHas('languages', function (Builder $query) use($entity){
            foreach($entity->languages as $language) {
                $query->orWhere(function($q1) use($language) {
                    $q1->where('language_id', $language->id);
                });
            }
        });

        return $model->with(['skills.technology'])->get();
    }

    protected function analyzeDataAndMatch($collection): array
    {
        $skills = $this->_entitySkills;
        $operand = $this->_operand;
        $entity = $this->_currentEntity;
        $languages = $this->_entityLanguages;
        $checkedIds = [];
        foreach ($collection as $item) {
            Log::debug('Start match for ' .$item::class. ' with id '. $item->id);
            $checkedIds[] = $item->id;
            // check skills
            $skillsCount = count($item->skills);
            $matchSkillsCount = 0;
            foreach ($item->skills as $skill) {
                switch ($skill->technology->type){
                    case 'language':
                    case 'framework':
                        if (isset($skills[$skill->technology_id])) {
                            if (
                                ($operand == '>=' && $skill->experience >= $skills[$skill->technology_id]) ||
                                ($operand == '<=' && $skill->experience <= $skills[$skill->technology_id])
                            ) {
                                $matchSkillsCount++;
                            }
                        }
                        break;
                    case 'library':
                        if (isset($skills[$skill->technology_id])) {
                            if (
                                ($operand == '>=' && $skill->experience >= $skills[$skill->technology_id]) ||
                                ($operand == '<=' && $skill->experience <= $skills[$skill->technology_id])
                            ) {
                                $matchSkillsCount++;
                            } else {
                                $matchSkillsCount+= 0.5;
                            }
                        }
                        break;
                    case 'other':
                    case 'tools':
                    case 'database':
                        if (isset($skills[$skill->technology_id])) {
                            if (
                                ($operand == '>=' && $skill->experience >= $skills[$skill->technology_id]) ||
                                ($operand == '<=' && $skill->experience <= $skills[$skill->technology_id])
                            ) {
                                $matchSkillsCount++;
                            } else {
                                $matchSkillsCount+= 0.7;
                            }
                        }
                        break;
                }
            }

            $percentOfSkills = $matchSkillsCount/$skillsCount * 100;

            if ($percentOfSkills < 50) {
                continue;
            }

            // check salary
            $percentOfSalary = 0;
            if ($entity->desired_salary == $item->desired_salary) {
                $percentOfSalary = 100;
            } else {
                switch ($this->_currentEntityClass) {
                    case Vacancy::class:
                        if ($entity->desired_salary < $item->desired_salary) {
                            $percentOfSalary = $entity->desired_salary / $item->desired_salary * 100;
                        } else {
                            $percentOfSalary = 100;
                        }
                        break;
                    case Cv::class:
                        if ($entity->desired_salary > $item->desired_salary) {
                            $percentOfSalary = $item->desired_salary / $entity->desired_salary * 100;
                        } else {
                            $percentOfSalary = 100;
                        }
                        break;
                }
            }

            if ($percentOfSalary < 60) {
                continue;
            }

            // check languages
            $itemLanguages = $this->prepareLanguagesForCompare($item);
            if ($this->_currentEntityClass == Cv::class) {
                $percentOfLanguages = $this->compareLanguages($languages, $itemLanguages);
            } else {
                $percentOfLanguages = $this->compareLanguages($itemLanguages, $languages);
            }

            // @todo check industry


            $totalMatchPercent = ( $percentOfSkills + $percentOfLanguages + $percentOfSalary ) / 3;

            if ($totalMatchPercent < 60) {
                continue;
            }

            $i=1;

            if ($this->_currentEntityClass == Cv::class) {
                $userId = $entity->user_id;
                $companyId = $item->company_id;
                $cvId = $entity->id;
                $vacancyId = $item->id;
            } else {
                $userId = $item->user_id;
                $companyId = $entity->company_id;
                $cvId = $item->id;
                $vacancyId = $entity->id;
            }
            $data = [
                'user_id' => $userId,
                'company_id' => $companyId,
                'cv_id' => $cvId,
                'vacancy_id' => $vacancyId,
                'percent' => $totalMatchPercent,
                'status' => Matching::STATUS_MATCHED
            ];

            Matching::create($data);
        }

        return $checkedIds;
    }

    protected function getNewModel(): Vacancy|Cv
    {
        if ($this->_currentEntityClass == Vacancy::class) {
            return new Cv();
        }

        return new Vacancy();
    }

    protected function applyModifier($operand, $value, $modifier): float|int
    {
        if ($operand === '>=') {
            return $value + ($value * ($modifier / 100)); // add modifier
        }

        return $value - ($value * ($modifier / 100));
    }

    /**
     * @param array $target Array of languages from entity that we're comparing
     * @param array $dist Array of languages from entity that we need to compare with.
     * @return float
     */
    protected function compareLanguages(array $target, array $dist): float
    {
        $exact = 0;
        foreach ($dist as $key => $distItem) {
            if (isset($target[$key])) {
                if ($this->langWeight[$distItem] <= $this->langWeight[$target[$key]]) {
                    $exact++;
                } else {
                    $exact += 0.5;
                }
            }
        }

        if ($exact > 0 & count($target)) {
            return $exact / count($target) * 100;
        }

        return 0;
    }

    protected function prepareSkillsForSearch($entity): array
    {
        $skills = $entity->skills;
        $forSearch = [];
        foreach ($skills as $skill) {
            $forSearch[$skill->technology_id] = $skill->experience;
        }

        return $forSearch;
    }

    protected function prepareLanguagesForCompare($entity): array
    {
        $languages = $entity->languages;
        $prepared = [];
        foreach ($languages as $language) {
            $prepared[$language->id] = $language->pivot->level;
        }

        return $prepared;
    }
}
