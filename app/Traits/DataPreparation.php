<?php

namespace App\Traits;

use App\Models\Technology;

trait DataPreparation
{
    protected function getIndustryArray(): array
    {
        $out = [];
        foreach ($this->domains as $domain) {
            $out[] = $domain->id;
        }
        return $out;
    }

    protected function getIndustryText(): string
    {
        $out = [];
        foreach ($this->domains as $domain) {
            $out[] = ucfirst($domain->name);
        }
        return implode(', ', $out);
    }

    protected function getLanguagesArray(): array
    {
        $out = [];
        foreach ($this->languages as $language) {
            $out[] = [
                'name' => $language->name,
                'language_id' => $language->id,
                'level' => $language->pivot->level,
            ];
        }
        return $out;
    }

    protected function getSkillsArray(): array
    {
        $out = [];
        foreach ($this->skills as $skill) {
            $out[] = [
                'technology_id' => $skill->technology_id,
                'experience' => $skill->experience
            ];
        }

        return $out;
    }

    protected function getDetailedSkills(): array
    {
        $out = [];
        foreach ($this->skills as $skill) {
            $out[$skill->technology->type][] = [
                'technology_id' => $skill->technology_id,
                'technology_name' => $skill->technology->name,
                'experience' => $skill->experience
            ];
        }

        return $out;
    }

    protected function getBenefitsArray(): array
    {
        $out = [];
        foreach ($this->benefits as $benefit) {
            $out[] = $benefit->id;
        }
        return $out;
    }

    protected function getBenefitsText(): string
    {
        $out = [];
        foreach ($this->benefits as $benefit) {
            $out[] = ucfirst($benefit->name);
        }
        return implode(', ', $out);
    }

    protected function getRecentProjectsArray($stackAsText = false): array
    {
        $out = [];
        foreach ($this->recentProjects as $project) {
            if ($stackAsText) {
                $stackTextArray = [];
                $technologies = Technology::whereIn('id', $project->stack)->get('name');
                foreach ($technologies as $tech) {
                    $stackTextArray[] = $tech['name'];
                }
                $stack = implode(', ', $stackTextArray);
            } else {
                $stack = $project->stack;
            }
            $out[] = [
                'id' => $project->id,
                'title' => $project->title,
                'start_date' => $project->start_date,
                'is_in_progress' => (boolean) $project->is_in_progress,
                'end_date' => $project->end_date,
                'industry' => $project->industry,
                'stack' => $stack,
                'description' => $project->description
            ];
        }
        return $out;
    }
}
