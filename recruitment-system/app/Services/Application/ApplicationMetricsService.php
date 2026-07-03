<?php

namespace App\Services\Application;

use App\Models\Application;
use App\Models\JobPosting;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ApplicationMetricsService
{
    public function getStats(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $start = $startDate ?? now()->startOfMonth();
        $end = $endDate ?? now()->endOfMonth();

        $applications = Application::whereBetween('created_at', [$start, $end]);

        return [
            'total_applications' => (clone $applications)->count(),
            'today_applications' => Application::whereDate('created_at', today())->count(),
            'week_applications' => Application::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'month_applications' => Application::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count(),
            'new_applications' => (clone $applications)->where('is_new', true)->count(),
            'screening_count' => (clone $applications)->where('status', 'screening')->count(),
            'shortlisted_count' => (clone $applications)->where('status', 'shortlisted')->count(),
            'interview_count' => (clone $applications)->whereIn('status', ['technical_interview', 'manager_interview', 'final_interview'])->count(),
            'offer_pending_count' => (clone $applications)->where('status', 'offer_pending')->count(),
            'offer_sent_count' => (clone $applications)->where('status', 'offer_sent')->count(),
            'hired_count' => (clone $applications)->where('status', 'hired')->count(),
            'rejected_count' => (clone $applications)->where('status', 'rejected')->count(),
            'withdrawn_count' => (clone $applications)->where('status', 'withdrawn')->count(),
            'on_hold_count' => (clone $applications)->where('status', 'on_hold')->count(),
        ];
    }

    public function getPipelineMetrics(int $jobPostingId): array
    {
        $statuses = array_keys(Application::STATUSES);
        $metrics = [];

        foreach ($statuses as $status) {
            $count = Application::where('job_posting_id', $jobPostingId)
                ->where('status', $status)
                ->count();
            $metrics[] = [
                'status' => $status,
                'label' => Application::STATUSES[$status],
                'count' => $count,
            ];
        }

        return $metrics;
    }

    public function getConversionMetrics(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $start = $startDate ?? now()->subMonths(6);
        $end = $endDate ?? now();

        $totalApplications = Application::whereBetween('created_at', [$start, $end])->count();
        $totalHired = Application::where('status', 'hired')
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $screenedToShortlisted = $this->getStageConversion('screening', 'shortlisted', $start, $end);
        $shortlistedToInterview = $this->getStageConversion('shortlisted', 'technical_interview', $start, $end);
        $interviewToOffer = $this->getStageConversion('technical_interview', 'offer_sent', $start, $end);
        $offerToHire = $this->getStageConversion('offer_sent', 'hired', $start, $end);

        return [
            'total_applications' => $totalApplications,
            'total_hired' => $totalHired,
            'overall_conversion_rate' => $totalApplications > 0 ? round(($totalHired / $totalApplications) * 100, 2) : 0,
            'screening_to_shortlisted' => $screenedToShortlisted,
            'shortlisted_to_interview' => $shortlistedToInterview,
            'interview_to_offer' => $interviewToOffer,
            'offer_to_hire' => $offerToHire,
            'time_to_hire_days' => $this->getAverageTimeToHire($start, $end),
        ];
    }

    public function getSourceMetrics(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $start = $startDate ?? now()->subMonths(6);
        $end = $endDate ?? now();

        return DB::table('candidates')
            ->select('source', DB::raw('COUNT(*) as count'))
            ->join('applications', 'candidates.id', '=', 'applications.candidate_id')
            ->whereBetween('applications.created_at', [$start, $end])
            ->groupBy('source')
            ->orderByDesc('count')
            ->get()
            ->map(fn($row) => [
                'source' => $row->source,
                'label' => \App\Models\Candidate::SOURCES[$row->source] ?? $row->source,
                'count' => (int) $row->count,
            ])
            ->toArray();
    }

    public function getMonthlyTrend(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $start = $startDate ?? now()->subMonths(12);
        $end = $endDate ?? now();

        $results = DB::table('applications')
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status = "hired" THEN 1 ELSE 0 END) as hired'),
                DB::raw('SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected')
            )
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        return $results->map(fn($row) => [
            'period' => "{$row->year}-" . str_pad($row->month, 2, '0', STR_PAD_LEFT),
            'total' => (int) $row->total,
            'hired' => (int) $row->hired,
            'rejected' => (int) $row->rejected,
        ])->toArray();
    }

    private function getStageConversion(string $fromStage, string $toStage, Carbon $start, Carbon $end): array
    {
        $enteredStage = Application::where('status', $fromStage)
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $convertedToNext = ApplicationStatusHistory::where('from_status', $fromStage)
            ->where('to_status', $toStage)
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $rate = $enteredStage > 0 ? round(($convertedToNext / $enteredStage) * 100, 2) : 0;

        return [
            'entered' => $enteredStage,
            'converted' => $convertedToNext,
            'rate_percentage' => $rate,
        ];
    }

    private function getAverageTimeToHire(Carbon $start, Carbon $end): ?float
    {
        $hiredApplications = Application::where('status', 'hired')
            ->whereBetween('created_at', [$start, $end])
            ->whereNotNull('actual_joining_date')
            ->get();

        if ($hiredApplications->isEmpty()) {
            return null;
        }

        $totalDays = $hiredApplications->sum(fn($app) => $app->created_at->diffInDays($app->actual_joining_date));

        return round($totalDays / $hiredApplications->count(), 1);
    }
}
