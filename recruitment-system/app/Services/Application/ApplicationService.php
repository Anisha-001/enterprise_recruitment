<?php

namespace App\Services\Application;

use App\Models\Application;
use App\Models\Candidate;
use App\Models\JobPosting;
use App\Models\ApplicationStatusHistory;
use App\Models\ApplicationActivity;
use App\Models\InternalNote;
use App\Models\User;
use App\Events\ApplicationStatusChanged;
use App\Events\ApplicationSubmitted;
use App\Services\Notification\NotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ApplicationService
{
    public function __construct(
        private readonly NotificationService $notificationService,
        private readonly ApplicationValidationService $validationService,
        private readonly ApplicationMetricsService $metricsService,
    ) {}

    public function submitApplication(array $data, JobPosting $jobPosting, array $files = []): Application
    {
        return DB::transaction(function () use ($data, $jobPosting, $files) {
            $this->validationService->validateSubmission($data, $jobPosting);

            $candidate = $this->findOrCreateCandidate($data);

            if ($this->checkDuplicateApplication($candidate, $jobPosting)) {
                throw new \App\Exceptions\DuplicateApplicationException(
                    'You have already applied for this position.'
                );
            }

            $this->attachResume($candidate, $files['resume'] ?? null);
            $this->attachCoverLetter($candidate, $files['cover_letter'] ?? null);
            $this->attachPhotograph($candidate, $files['photograph'] ?? null);

            $application = $this->createApplication($candidate, $jobPosting, $data);

            $this->storeEducation($candidate, $data['education'] ?? []);
            $this->storeExperience($candidate, $data['experience'] ?? []);
            $this->storeSkills($candidate, $data['skills'] ?? []);
            $this->storeAnswers($application, $data['screening_answers'] ?? []);
            $this->storeDocuments($application, $files['documents'] ?? []);

            $this->logActivity($application, 'system', 'Application Submitted', 'Application received successfully');
            $this->recordStatusHistory($application, null, 'new', 'Application submitted');

            ApplicationSubmitted::dispatch($application);

            Log::info('Application submitted', [
                'application_id' => $application->id,
                'candidate_id' => $candidate->id,
                'job_id' => $jobPosting->id,
            ]);

            return $application;
        });
    }

    public function transitionStatus(Application $application, string $newStatus, ?string $notes = null, ?int $changedBy = null): Application
    {
        return DB::transaction(function () use ($application, $newStatus, $notes, $changedBy) {
            if (!$application->canTransitionTo($newStatus)) {
                throw new \InvalidArgumentException(
                    "Cannot transition from {$application->status} to {$newStatus}"
                );
            }

            $oldStatus = $application->status;

            $application->update([
                'status' => $newStatus,
                'status_changed_at' => now(),
                'status_changed_by' => $changedBy,
                'is_new' => $newStatus === 'new',
            ]);

            if (in_array($newStatus, ['rejected', 'offer_rejected'])) {
                $application->update([
                    'rejected_at' => now(),
                    'rejected_by' => $changedBy,
                    'rejection_notes' => $notes,
                ]);
            }

            $this->recordStatusHistory($application, $oldStatus, $newStatus, $notes);
            $this->logActivity($application, 'status_change', 'Status Updated', "Status changed from {$oldStatus} to {$newStatus}", [
                'from' => $oldStatus,
                'to' => $newStatus,
                'notes' => $notes,
            ]);

            ApplicationStatusChanged::dispatch($application, $oldStatus, $newStatus);

            $this->notificationService->sendStatusChangeNotification($application, $oldStatus, $newStatus);

            Log::info('Application status changed', [
                'application_id' => $application->id,
                'from' => $oldStatus,
                'to' => $newStatus,
            ]);

            return $application->fresh();
        });
    }

    public function assignRecruiter(Application $application, int $recruiterId, ?int $assignedBy = null): Application
    {
        $oldRecruiter = $application->recruiter_id;

        $application->update([
            'recruiter_id' => $recruiterId,
        ]);

        $this->logActivity($application, 'system', 'Recruiter Assigned', "Recruiter assigned to application", [
            'old_recruiter' => $oldRecruiter,
            'new_recruiter' => $recruiterId,
            'assigned_by' => $assignedBy,
        ]);

        return $application->fresh();
    }

    public function addRating(Application $application, int $rating, ?string $notes = null): Application
    {
        if ($rating < 1 || $rating > 5) {
            throw new \InvalidArgumentException('Rating must be between 1 and 5');
        }

        $application->update([
            'rating' => $rating,
        ]);

        $this->logActivity($application, 'system', 'Rating Updated', "Application rated {$rating}/5", [
            'rating' => $rating,
            'notes' => $notes,
        ]);

        return $application->fresh();
    }

    public function markAsReviewed(Application $application, int $reviewedBy): Application
    {
        $application->update([
            'is_new' => false,
            'reviewed_at' => now(),
            'reviewed_by' => $reviewedBy,
        ]);

        $this->logActivity($application, 'system', 'Application Reviewed', 'Application marked as reviewed');

        return $application->fresh();
    }

    public function addNote(Application $application, string $content, string $type = 'general', bool $isPrivate = false, int $userId): InternalNote
    {
        $note = $application->internalNotes()->create([
            'content' => $content,
            'type' => $type,
            'is_private' => $isPrivate,
            'user_id' => $userId,
        ]);

        $this->logActivity($application, 'note', 'Note Added', substr($content, 0, 100), [
            'note_id' => $note->id,
            'type' => $type,
        ]);

        return $note;
    }

    public function getApplicationStats(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        return $this->metricsService->getStats($startDate, $endDate);
    }

    public function getPipelineMetrics(int $jobPostingId): array
    {
        return $this->metricsService->getPipelineMetrics($jobPostingId);
    }

    private function findOrCreateCandidate(array $data): Candidate
    {
        $existingCandidate = Candidate::where('email', $data['email'])
            ->orWhere(function ($q) use ($data) {
                $q->where('first_name', $data['first_name'])
                    ->where('last_name', $data['last_name'])
                    ->where('phone', $data['phone']);
            })
            ->first();

        if ($existingCandidate) {
            $existingCandidate->update([
                'current_company' => $data['current_company'] ?? $existingCandidate->current_company,
                'current_designation' => $data['current_designation'] ?? $existingCandidate->current_designation,
                'expected_salary' => $data['expected_salary'] ?? $existingCandidate->expected_salary,
                'total_experience_years' => $data['total_experience_years'] ?? $existingCandidate->total_experience_years,
            ]);
            return $existingCandidate;
        }

        return Candidate::create([
            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'] ?? null,
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'alternate_phone' => $data['alternate_phone'] ?? null,
            'gender' => $data['gender'] ?? null,
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'nationality' => $data['nationality'] ?? null,
            'marital_status' => $data['marital_status'] ?? null,
            'current_address' => $data['current_address'] ?? null,
            'permanent_address' => $data['permanent_address'] ?? null,
            'city' => $data['city'] ?? null,
            'state' => $data['state'] ?? null,
            'country' => $data['country'] ?? null,
            'postal_code' => $data['postal_code'] ?? null,
            'current_company' => $data['current_company'] ?? null,
            'current_designation' => $data['current_designation'] ?? null,
            'current_salary' => $data['current_salary'] ?? null,
            'expected_salary' => $data['expected_salary'] ?? null,
            'salary_currency' => $data['salary_currency'] ?? 'USD',
            'notice_period' => $data['notice_period'] ?? null,
            'total_experience_years' => $data['total_experience_years'] ?? 0,
            'highest_qualification' => $data['highest_qualification'] ?? null,
            'university' => $data['university'] ?? null,
            'passing_year' => $data['passing_year'] ?? null,
            'linkedin_url' => $data['linkedin_url'] ?? null,
            'github_url' => $data['github_url'] ?? null,
            'portfolio_url' => $data['portfolio_url'] ?? null,
            'website_url' => $data['website_url'] ?? null,
            'source' => $data['source'] ?? 'careers_page',
            'referral_code' => $data['referral_code'] ?? null,
            'utm_source' => $data['utm_source'] ?? null,
            'utm_medium' => $data['utm_medium'] ?? null,
            'utm_campaign' => $data['utm_campaign'] ?? null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'browser' => $this->getBrowserInfo(),
            'operating_system' => $this->getOsInfo(),
            'device' => $this->getDeviceInfo(),
        ]);
    }

    private function createApplication(Candidate $candidate, JobPosting $jobPosting, array $data): Application
    {
        return $candidate->applications()->create([
            'job_posting_id' => $jobPosting->id,
            'expected_salary' => $data['expected_salary'] ?? null,
            'expected_joining_date' => $data['expected_joining_date'] ?? null,
            'recruiter_id' => $jobPosting->recruiter_id,
        ]);
    }

    private function checkDuplicateApplication(Candidate $candidate, JobPosting $jobPosting): bool
    {
        return $candidate->applications()
            ->where('job_posting_id', $jobPosting->id)
            ->where('created_at', '>=', now()->subDays(config('recruitment.application.duplicate_check_days', 180)))
            ->exists();
    }

    private function storeEducation(Candidate $candidate, array $educationList): void
    {
        foreach ($educationList as $index => $edu) {
            if (empty($edu['degree_name']) && empty($edu['institution'])) {
                continue;
            }
            $candidate->education()->create([
                'degree_type' => !empty($edu['degree_type']) ? $edu['degree_type'] : 'other',
                'degree_name' => $edu['degree_name'],
                'field_of_study' => !empty($edu['field_of_study']) ? $edu['field_of_study'] : 'N/A',
                'institution' => $edu['institution'],
                'university' => $edu['university'] ?? null,
                'start_year' => !empty($edu['start_year']) ? $edu['start_year'] : date('Y'),
                'end_year' => $edu['end_year'] ?? null,
                'is_current' => $edu['is_current'] ?? false,
                'grade_cgpa' => $edu['grade_cgpa'] ?? null,
                'grade_scale' => $edu['grade_scale'] ?? null,
                'percentage' => $edu['percentage'] ?? null,
                'sort_order' => $index,
            ]);
        }
    }

    private function storeExperience(Candidate $candidate, array $experiences): void
    {
        foreach ($experiences as $index => $exp) {
            if (empty($exp['company_name']) && empty($exp['designation'])) {
                continue;
            }
            $candidate->experiences()->create([
                'company_name' => $exp['company_name'],
                'designation' => $exp['designation'],
                'department' => $exp['department'] ?? null,
                'location' => $exp['location'] ?? null,
                'start_date' => !empty($exp['start_date']) ? $exp['start_date'] : now()->toDateString(),
                'end_date' => $exp['end_date'] ?? null,
                'is_current' => $exp['is_current'] ?? false,
                'responsibilities' => $exp['responsibilities'] ?? null,
                'achievements' => $exp['achievements'] ?? null,
                'sort_order' => $index,
            ]);
        }
    }

    private function storeSkills(Candidate $candidate, array $skills): void
    {
        foreach ($skills as $skill) {
            if (empty($skill['name'])) continue;

            $dbSkill = \App\Models\Skill::where('name', $skill['name'])->first();

            $candidate->skills()->create([
                'skill_id' => $dbSkill?->id,
                'skill_name' => $skill['name'],
                'proficiency' => $skill['proficiency'] ?? 'intermediate',
                'years_experience' => $skill['years_experience'] ?? null,
                'is_primary' => $skill['is_primary'] ?? false,
            ]);
        }
    }

    private function storeAnswers(Application $application, array $answers): void
    {
        foreach ($answers as $questionId => $answer) {
            if (empty($answer)) continue;
            $application->answers()->create([
                'screening_question_id' => $questionId,
                'answer' => is_array($answer) ? json_encode($answer) : $answer,
            ]);
        }
    }

    private function attachResume(Candidate $candidate, $file): void
    {
        if (!$file) return;

        $path = $file->store('candidates/' . $candidate->id . '/resumes', 'private');
        $candidate->update([
            'resume_path' => $path,
            'resume_original_name' => $file->getClientOriginalName(),
        ]);
    }

    private function attachCoverLetter(Candidate $candidate, $file): void
    {
        if (!$file) return;

        $path = $file->store('candidates/' . $candidate->id . '/cover_letters', 'private');
        $candidate->update(['cover_letter_path' => $path]);
    }

    private function attachPhotograph(Candidate $candidate, $file): void
    {
        if (!$file) return;

        $path = $file->store('candidates/' . $candidate->id . '/photos', 'public');
        $candidate->update(['photograph' => $path]);
    }

    private function storeDocuments(Application $application, array $documents): void
    {
        foreach ($documents as $doc) {
            if (!$doc) continue;

            $path = $doc->store('applications/' . $application->id . '/documents', 'private');
            $application->documents()->create([
                'name' => $doc->getClientOriginalName(),
                'file_path' => $path,
                'original_name' => $doc->getClientOriginalName(),
                'mime_type' => $doc->getMimeType(),
                'file_size' => $doc->getSize(),
                'disk' => 'private',
                'collection' => 'application_documents',
            ]);
        }
    }

    private function recordStatusHistory(Application $application, ?string $from, string $to, ?string $notes): void
    {
        ApplicationStatusHistory::create([
            'application_id' => $application->id,
            'from_status' => $from,
            'to_status' => $to,
            'notes' => $notes,
            'changed_by' => auth()->id(),
            'ip_address' => request()->ip(),
        ]);
    }

    private function logActivity(Application $application, string $type, string $title, string $description, array $properties = []): void
    {
        ApplicationActivity::create([
            'application_id' => $application->id,
            'causer_type' => auth()->check() ? User::class : null,
            'causer_id' => auth()->id(),
            'type' => $type,
            'icon' => ApplicationActivity::ICONS[$type] ?? 'information-circle',
            'color' => ApplicationActivity::COLORS[$type] ?? 'gray',
            'title' => $title,
            'description' => $description,
            'properties' => $properties,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    private function getBrowserInfo(): string
    {
        $agent = request()->userAgent() ?? '';
        if (str_contains($agent, 'Firefox')) return 'Firefox';
        if (str_contains($agent, 'Chrome') && !str_contains($agent, 'Edg')) return 'Chrome';
        if (str_contains($agent, 'Safari') && !str_contains($agent, 'Chrome')) return 'Safari';
        if (str_contains($agent, 'Edg')) return 'Edge';
        if (str_contains($agent, 'Opera') || str_contains($agent, 'OPR')) return 'Opera';
        return 'Unknown';
    }

    private function getOsInfo(): string
    {
        $agent = request()->userAgent() ?? '';
        if (str_contains($agent, 'Windows')) return 'Windows';
        if (str_contains($agent, 'Mac OS')) return 'macOS';
        if (str_contains($agent, 'Linux')) return 'Linux';
        if (str_contains($agent, 'Android')) return 'Android';
        if (str_contains($agent, 'iPhone') || str_contains($agent, 'iPad')) return 'iOS';
        return 'Unknown';
    }

    private function getDeviceInfo(): string
    {
        $agent = request()->userAgent() ?? '';
        if (str_contains($agent, 'Mobile')) return 'Mobile';
        if (str_contains($agent, 'Tablet') || str_contains($agent, 'iPad')) return 'Tablet';
        return 'Desktop';
    }
}
