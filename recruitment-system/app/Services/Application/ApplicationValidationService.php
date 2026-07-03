<?php

namespace App\Services\Application;

use App\Models\JobPosting;
use Illuminate\Validation\ValidationException;
use Validator;

class ApplicationValidationService
{
    public function validateSubmission(array &$data, JobPosting $jobPosting): void
    {
        \Illuminate\Support\Facades\Log::info('Validation data before filtering:', $data);

        if (!$jobPosting->isOpen()) {
            throw ValidationException::withMessages([
                'job' => ['This job posting is no longer accepting applications.'],
            ]);
        }

        // Clean up empty education entries
        if (isset($data['education']) && is_array($data['education'])) {
            $data['education'] = array_values(array_filter($data['education'], function ($edu) {
                return !empty($edu['degree_type']) ||
                       !empty($edu['degree_name']) ||
                       !empty($edu['field_of_study']) ||
                       !empty($edu['institution']) ||
                       !empty($edu['start_year']) ||
                       !empty($edu['end_year']);
            }));
            if (empty($data['education'])) {
                unset($data['education']);
            }
        }

        // Clean up empty experience entries
        if (isset($data['experience']) && is_array($data['experience'])) {
            $data['experience'] = array_values(array_filter($data['experience'], function ($exp) {
                return !empty($exp['company_name']) ||
                       !empty($exp['designation']) ||
                       !empty($exp['start_date']) ||
                       !empty($exp['end_date']);
            }));
            if (empty($data['experience'])) {
                unset($data['experience']);
            }
        }

        // Clean up empty skills entries
        if (isset($data['skills']) && is_array($data['skills'])) {
            $data['skills'] = array_values(array_filter($data['skills'], function ($skill) {
                return !empty($skill['name']);
            }));
            if (empty($data['skills'])) {
                unset($data['skills']);
            }
        }

        \Illuminate\Support\Facades\Log::info('Validation data after filtering:', $data);

        $validator = Validator::make($data, $this->getValidationRules($jobPosting));

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    private function getValidationRules(JobPosting $jobPosting): array
    {
        $rules = [
            // Step 1: Personal Information
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],

            // Step 2: Contact Information (optional fields)
            'middle_name' => ['nullable', 'string', 'max:100'],
            'alternate_phone' => ['nullable', 'string', 'max:20'],
            'gender' => ['nullable', 'in:male,female,non_binary,prefer_not_to_say'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'marital_status' => ['nullable', 'in:single,married,divorced,widowed,separated'],
            'current_address' => ['nullable', 'string', 'max:1000'],
            'permanent_address' => ['nullable', 'string', 'max:1000'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],

            // Step 3: Education
            'education' => ['nullable', 'array'],
            'education.*.degree_type' => ['nullable', 'string', 'in:' . implode(',', array_keys(\App\Models\CandidateEducation::DEGREE_TYPES))],
            'education.*.degree_name' => ['required_with:education', 'string', 'max:200'],
            'education.*.field_of_study' => ['nullable', 'string', 'max:200'],
            'education.*.institution' => ['required_with:education', 'string', 'max:200'],
            'education.*.start_year' => ['nullable', 'integer', 'min:1950', 'max:' . date('Y')],

            // Step 4: Experience
            'experience' => ['nullable', 'array'],
            'experience.*.company_name' => ['required_with:experience', 'string', 'max:200'],
            'experience.*.designation' => ['required_with:experience', 'string', 'max:150'],
            'experience.*.start_date' => ['nullable', 'date'],

            // Step 5: Skills
            'skills' => ['nullable', 'array'],
            'skills.*.name' => ['required_with:skills', 'string', 'max:100'],
            'skills.*.proficiency' => ['nullable', 'in:beginner,intermediate,advanced,expert'],

            // Step 6: Online Profiles
            'linkedin_url' => ['nullable', 'url', 'max:500'],
            'github_url' => ['nullable', 'url', 'max:500'],
            'portfolio_url' => ['nullable', 'url', 'max:500'],
            'website_url' => ['nullable', 'url', 'max:500'],

            // Step 7: Documents
            'expected_salary' => ['nullable', 'numeric', 'min:0'],
            'salary_currency' => ['nullable', 'string', 'size:3'],
            'current_company' => ['nullable', 'string', 'max:150'],
            'current_designation' => ['nullable', 'string', 'max:150'],
            'total_experience_years' => ['nullable', 'numeric', 'min:0', 'max:50'],
            'notice_period' => ['nullable', 'in:immediate,15_days,30_days,60_days,90_days,more_than_90'],
            'expected_joining_date' => ['nullable', 'date', 'after:today'],

            // Step 8: Screening Questions
            'screening_answers' => ['nullable', 'array'],

            // Step 9: Source & Referral
            'source' => ['nullable', 'string', 'in:careers_page,linkedin,indeed,referral,agency,job_fair,campus,social_media,other'],
            'referral_code' => ['nullable', 'string', 'max:50'],

            // Declaration
            'terms_accepted' => ['required', 'accepted'],
            'privacy_accepted' => ['required', 'accepted'],
        ];

        // Add validation for screening questions
        foreach ($jobPosting->screeningQuestions()->where('is_active', true)->get() as $question) {
            $key = "screening_answers.{$question->id}";
            $rules[$key] = $question->is_required ? ['required'] : ['nullable'];

            $rules[$key][] = match ($question->type) {
                'text' => 'string|max:500',
                'textarea' => 'string|max:5000',
                'yes_no' => 'in:yes,no',
                'number' => 'numeric',
                'date' => 'date',
                'single_choice' => 'string|in:' . implode(',', $question->options ?? []),
                'multiple_choice' => 'array',
                default => 'string',
            };
        }

        return $rules;
    }
}
