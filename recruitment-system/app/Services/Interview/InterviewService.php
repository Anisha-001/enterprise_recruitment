<?php

namespace App\Services\Interview;

use App\Models\Interview;
use App\Models\Application;
use App\Events\InterviewScheduled;
use App\Events\InterviewCancelled;
use App\Services\Notification\NotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class InterviewService
{
    public function __construct(
        private readonly NotificationService $notificationService,
    ) {}

    public function scheduleInterview(Application $application, array $data, int $scheduledBy): Interview
    {
        return DB::transaction(function () use ($application, $data, $scheduledBy) {
            $roundNumber = $this->getNextRoundNumber($application);

            $interview = Interview::create([
                'application_id' => $application->id,
                'candidate_id' => $application->candidate_id,
                'job_posting_id' => $application->job_posting_id,
                'round_type' => $data['round_type'],
                'round_number' => $roundNumber,
                'mode' => $data['mode'],
                'video_platform' => $data['video_platform'] ?? null,
                'meeting_link' => $data['meeting_link'] ?? null,
                'meeting_id' => $data['meeting_id'] ?? null,
                'meeting_password' => $data['meeting_password'] ?? null,
                'scheduled_date' => $data['scheduled_date'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'duration_minutes' => $data['duration_minutes'] ?? config('recruitment.interview.default_duration', 60),
                'location_id' => $data['location_id'] ?? null,
                'interview_address' => $data['interview_address'] ?? null,
                'room_number' => $data['room_number'] ?? null,
                'instructions' => $data['instructions'] ?? null,
                'description' => $data['description'] ?? null,
                'scheduled_by' => $scheduledBy,
            ]);

            $interviewerIds = $data['interviewer_ids'] ?? [];
            foreach ($interviewerIds as $index => $interviewerId) {
                $interview->interviewers()->attach($interviewerId, [
                    'is_primary' => $index === 0,
                    'is_required' => true,
                    'response_status' => 'pending',
                ]);
            }

            InterviewScheduled::dispatch($interview);
            $this->notificationService->sendInterviewInvitation($interview);

            Log::info('Interview scheduled', [
                'interview_id' => $interview->id,
                'application_id' => $application->id,
                'round_type' => $interview->round_type,
            ]);

            return $interview->load(['interviewers', 'application', 'candidate']);
        });
    }

    public function rescheduleInterview(Interview $interview, array $data, int $updatedBy): Interview
    {
        return DB::transaction(function () use ($interview, $data, $updatedBy) {
            $interview->update([
                'scheduled_date' => $data['scheduled_date'] ?? $interview->scheduled_date,
                'start_time' => $data['start_time'] ?? $interview->start_time,
                'end_time' => $data['end_time'] ?? $interview->end_time,
                'duration_minutes' => $data['duration_minutes'] ?? $interview->duration_minutes,
                'mode' => $data['mode'] ?? $interview->mode,
                'meeting_link' => $data['meeting_link'] ?? $interview->meeting_link,
                'location_id' => $data['location_id'] ?? $interview->location_id,
                'interview_address' => $data['interview_address'] ?? $interview->interview_address,
                'instructions' => $data['instructions'] ?? $interview->instructions,
                'status' => 'rescheduled',
                'updated_by' => $updatedBy,
            ]);

            if (!empty($data['interviewer_ids'])) {
                $interview->interviewers()->detach();
                foreach ($data['interviewer_ids'] as $index => $interviewerId) {
                    $interview->interviewers()->attach($interviewerId, [
                        'is_primary' => $index === 0,
                        'is_required' => true,
                        'response_status' => 'pending',
                    ]);
                }
            }

            $this->notificationService->sendInterviewRescheduled($interview);

            Log::info('Interview rescheduled', ['interview_id' => $interview->id]);

            return $interview->fresh()->load(['interviewers', 'application', 'candidate']);
        });
    }

    public function cancelInterview(Interview $interview, string $reason, int $cancelledBy): Interview
    {
        $interview->update([
            'status' => 'cancelled',
            'updated_by' => $cancelledBy,
        ]);

        InterviewCancelled::dispatch($interview, $reason);
        $this->notificationService->sendInterviewCancellation($interview, $reason);

        Log::info('Interview cancelled', ['interview_id' => $interview->id, 'reason' => $reason]);

        return $interview->fresh();
    }

    public function completeInterview(Interview $interview, int $completedBy): Interview
    {
        $interview->update([
            'status' => 'completed',
            'updated_by' => $completedBy,
        ]);

        Log::info('Interview completed', ['interview_id' => $interview->id]);

        return $interview->fresh();
    }

    public function markNoShow(Interview $interview, int $markedBy): Interview
    {
        $interview->update([
            'status' => 'no_show',
            'updated_by' => $markedBy,
        ]);

        Log::info('Interview marked as no-show', ['interview_id' => $interview->id]);

        return $interview->fresh();
    }

    public function startInterview(Interview $interview, int $startedBy): Interview
    {
        $interview->update([
            'status' => 'in_progress',
            'updated_by' => $startedBy,
        ]);

        return $interview->fresh();
    }

    public function getInterviewCalendar(int $userId, Carbon $startDate, Carbon $endDate): array
    {
        $interviews = Interview::whereBetween('scheduled_date', [$startDate, $endDate])
            ->where(function ($q) use ($userId) {
                $q->where('scheduled_by', $userId)
                    ->orWhereHas('interviewers', fn($i) => $i->where('users.id', $userId));
            })
            ->whereIn('status', ['scheduled', 'confirmed', 'in_progress'])
            ->with(['candidate', 'jobPosting', 'interviewers'])
            ->orderBy('scheduled_date')
            ->orderBy('start_time')
            ->get();

        return [
            'interviews' => $interviews,
            'total' => $interviews->count(),
            'events' => $interviews->map(fn($i) => $i->calendar_event)->toArray(),
        ];
    }

    private function getNextRoundNumber(Application $application): int
    {
        return $application->interviews()->max('round_number') + 1;
    }
}
