<?php

namespace App\Services\Job;

use App\Models\JobPosting;
use App\Models\Skill;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class JobService
{
    public function __construct() {}

    public function createJob(array $data, int $userId): JobPosting
    {
        return DB::transaction(function () use ($data, $userId) {
            $job = JobPosting::create([
                'title' => $data['title'],
                'summary' => $data['summary'] ?? null,
                'description' => $data['description'],
                'responsibilities' => $data['responsibilities'] ?? null,
                'requirements' => $data['requirements'] ?? null,
                'benefits' => $data['benefits'] ?? null,
                'department_id' => $data['department_id'],
                'designation_id' => $data['designation_id'],
                'location_id' => $data['location_id'],
                'hiring_manager_id' => $data['hiring_manager_id'] ?? null,
                'recruiter_id' => $data['recruiter_id'] ?? null,
                'employment_type' => $data['employment_type'],
                'experience_level' => $data['experience_level'] ?? 'mid',
                'work_arrangement' => $data['work_arrangement'] ?? 'on_site',
                'min_experience_years' => $data['min_experience_years'] ?? 0,
                'max_experience_years' => $data['max_experience_years'] ?? null,
                'min_salary' => $data['min_salary'] ?? null,
                'max_salary' => $data['max_salary'] ?? null,
                'salary_currency' => $data['salary_currency'] ?? 'USD',
                'salary_period' => $data['salary_period'] ?? 'yearly',
                'show_salary' => $data['show_salary'] ?? false,
                'vacancies' => $data['vacancies'] ?? 1,
                'published_at' => $data['published_at'] ?? null,
                'closing_date' => $data['closing_date'] ?? null,
                'meta_title' => $data['meta_title'] ?? $data['title'],
                'meta_description' => $data['meta_description'] ?? null,
                'meta_keywords' => $data['meta_keywords'] ?? null,
                'is_featured' => $data['is_featured'] ?? false,
                'is_urgent' => $data['is_urgent'] ?? false,
                'status' => $data['status'] ?? 'draft',
                'requisition_number' => $data['requisition_number'] ?? null,
                'created_by' => $userId,
            ]);

            $this->syncSkills($job, $data['skills'] ?? []);

            Log::info('Job created', ['job_id' => $job->id, 'title' => $job->title]);

            return $job->load(['department', 'designation', 'location', 'skills']);
        });
    }

    public function updateJob(JobPosting $job, array $data, int $userId): JobPosting
    {
        return DB::transaction(function () use ($job, $data, $userId) {
            $job->update([
                'title' => $data['title'] ?? $job->title,
                'summary' => $data['summary'] ?? $job->summary,
                'description' => $data['description'] ?? $job->description,
                'responsibilities' => $data['responsibilities'] ?? $job->responsibilities,
                'requirements' => $data['requirements'] ?? $job->requirements,
                'benefits' => $data['benefits'] ?? $job->benefits,
                'department_id' => $data['department_id'] ?? $job->department_id,
                'designation_id' => $data['designation_id'] ?? $job->designation_id,
                'location_id' => $data['location_id'] ?? $job->location_id,
                'hiring_manager_id' => $data['hiring_manager_id'] ?? $job->hiring_manager_id,
                'recruiter_id' => $data['recruiter_id'] ?? $job->recruiter_id,
                'employment_type' => $data['employment_type'] ?? $job->employment_type,
                'experience_level' => $data['experience_level'] ?? $job->experience_level,
                'work_arrangement' => $data['work_arrangement'] ?? $job->work_arrangement,
                'min_experience_years' => $data['min_experience_years'] ?? $job->min_experience_years,
                'max_experience_years' => $data['max_experience_years'] ?? $job->max_experience_years,
                'min_salary' => $data['min_salary'] ?? $job->min_salary,
                'max_salary' => $data['max_salary'] ?? $job->max_salary,
                'show_salary' => $data['show_salary'] ?? $job->show_salary,
                'vacancies' => $data['vacancies'] ?? $job->vacancies,
                'published_at' => $data['published_at'] ?? $job->published_at,
                'closing_date' => $data['closing_date'] ?? $job->closing_date,
                'meta_title' => $data['meta_title'] ?? $job->meta_title,
                'meta_description' => $data['meta_description'] ?? $job->meta_description,
                'is_featured' => $data['is_featured'] ?? $job->is_featured,
                'is_urgent' => $data['is_urgent'] ?? $job->is_urgent,
                'status' => $data['status'] ?? $job->status,
                'updated_by' => $userId,
            ]);

            if (isset($data['skills'])) {
                $this->syncSkills($job, $data['skills']);
            }

            return $job->fresh()->load(['department', 'designation', 'location', 'skills']);
        });
    }

    public function publishJob(JobPosting $job, int $userId): JobPosting
    {
        $job->update([
            'status' => 'published',
            'published_at' => now(),
            'updated_by' => $userId,
        ]);

        Log::info('Job published', ['job_id' => $job->id]);

        return $job->fresh();
    }

    public function closeJob(JobPosting $job, int $userId): JobPosting
    {
        $job->update([
            'status' => 'closed',
            'updated_by' => $userId,
        ]);

        Log::info('Job closed', ['job_id' => $job->id]);

        return $job->fresh();
    }

    public function archiveJob(JobPosting $job, int $userId): JobPosting
    {
        $job->update([
            'status' => 'archived',
            'updated_by' => $userId,
        ]);

        Log::info('Job archived', ['job_id' => $job->id]);

        return $job->fresh();
    }

    public function reopenJob(JobPosting $job, int $userId, ?string $newClosingDate = null): JobPosting
    {
        $job->update([
            'status' => 'published',
            'closing_date' => $newClosingDate ?? $job->closing_date,
            'updated_by' => $userId,
        ]);

        Log::info('Job reopened', ['job_id' => $job->id]);

        return $job->fresh();
    }

    public function cloneJob(JobPosting $job, int $userId): JobPosting
    {
        return DB::transaction(function () use ($job, $userId) {
            $newJob = $job->replicate([
                'slug',
                'application_count',
                'status',
                'published_at',
            ]);

            $newJob->title = $job->title . ' (Copy)';
            $newJob->status = 'draft';
            $newJob->created_by = $userId;
            $newJob->save();

            foreach ($job->skills as $skill) {
                $newJob->skills()->attach($skill->id, [
                    'proficiency' => $skill->pivot->proficiency,
                    'is_required' => $skill->pivot->is_required,
                    'years_experience' => $skill->pivot->years_experience,
                    'sort_order' => $skill->pivot->sort_order,
                ]);
            }

            foreach ($job->screeningQuestions as $question) {
                $newJob->screeningQuestions()->create([
                    'question' => $question->question,
                    'type' => $question->type,
                    'options' => $question->options,
                    'is_required' => $question->is_required,
                    'sort_order' => $question->sort_order,
                ]);
            }

            Log::info('Job cloned', ['original_id' => $job->id, 'new_id' => $newJob->id]);

            return $newJob->load(['department', 'designation', 'location', 'skills']);
        });
    }

    private function syncSkills(JobPosting $job, array $skills): void
    {
        $syncData = [];

        foreach ($skills as $index => $skillData) {
            if (is_array($skillData)) {
                $skillName = $skillData['name'] ?? $skillData;
                $proficiency = $skillData['proficiency'] ?? 'intermediate';
                $isRequired = $skillData['is_required'] ?? true;
                $yearsExp = $skillData['years_experience'] ?? null;
            } else {
                $skillName = $skillData;
                $proficiency = 'intermediate';
                $isRequired = true;
                $yearsExp = null;
            }

            $skill = Skill::firstOrCreate(
                ['name' => $skillName],
                ['slug' => Str::slug($skillName), 'category' => 'technical', 'is_active' => true]
            );

            $syncData[$skill->id] = [
                'proficiency' => $proficiency,
                'is_required' => $isRequired,
                'years_experience' => $yearsExp,
                'sort_order' => $index,
            ];
        }

        $job->skills()->sync($syncData);
    }
}
