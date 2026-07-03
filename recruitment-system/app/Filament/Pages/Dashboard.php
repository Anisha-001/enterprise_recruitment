<?php

namespace App\Filament\Pages;

use App\Models\Application;
use App\Models\Interview;
use App\Models\JobPosting;
use App\Models\Offer;
use App\Services\Application\ApplicationMetricsService;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\DB;

class Dashboard extends BaseDashboard
{
    use InteractsWithPageFilters;

    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationGroup = 'Recruitment';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.pages.dashboard';

    public function getStats(): array
    {
        $metricsService = app(ApplicationMetricsService::class);
        $stats = $metricsService->getStats();

        return [
            Stat::make('New Applications', $stats['new_applications'])
                ->description('This month: ' . $stats['month_applications'])
                ->descriptionIcon('heroicon-m-arrow-trend-up')
                ->color('success')
                ->chart([7, 4, 6, 8, 5, 9, 3])
                ->icon('heroicon-m-document-text'),

            Stat::make('Active Jobs', JobPosting::published()->count())
                ->description('Total: ' . JobPosting::count())
                ->color('primary')
                ->icon('heroicon-m-briefcase'),

            Stat::make('In Screening', $stats['screening_count'])
                ->description('Shortlisted: ' . $stats['shortlisted_count'])
                ->color('warning')
                ->icon('heroicon-m-magnifying-glass'),

            Stat::make('Interviews Today', Interview::today()->count())
                ->description('This week: ' . Interview::thisWeek()->count())
                ->color('info')
                ->icon('heroicon-m-users'),

            Stat::make('Offers Pending', Offer::pending()->count())
                ->description('Accepted: ' . Offer::where('status', 'accepted')->count())
                ->color('success')
                ->icon('heroicon-m-envelope'),

            Stat::make('Hired This Month', $stats['hired_count'])
                ->description('Rejected: ' . $stats['rejected_count'])
                ->color('success')
                ->icon('heroicon-m-user-plus'),
        ];
    }

    public function getPipelineData(): array
    {
        $statuses = ['new', 'screening', 'shortlisted', 'technical_interview', 'manager_interview', 'final_interview', 'offer_sent', 'hired'];
        $data = [];

        foreach ($statuses as $status) {
            $data[] = [
                'label' => Application::STATUSES[$status] ?? $status,
                'count' => Application::where('status', $status)->count(),
                'color' => match ($status) {
                    'new' => '#6b7280',
                    'screening' => '#3b82f6',
                    'shortlisted' => '#6366f1',
                    'technical_interview' => '#f59e0b',
                    'manager_interview' => '#f97316',
                    'final_interview' => '#10b981',
                    'offer_sent' => '#14b8a6',
                    'hired' => '#22c55e',
                    default => '#6b7280',
                },
            ];
        }

        return $data;
    }

    public function getSourceData(): array
    {
        return DB::table('candidates')
            ->select('source', DB::raw('COUNT(*) as count'))
            ->join('applications', 'candidates.id', '=', 'applications.candidate_id')
            ->where('applications.created_at', '>=', now()->subMonths(6))
            ->groupBy('source')
            ->orderByDesc('count')
            ->limit(8)
            ->get()
            ->map(fn($row) => [
                'label' => \App\Models\Candidate::SOURCES[$row->source] ?? $row->source,
                'value' => (int) $row->count,
            ])
            ->toArray();
    }

    public function getDepartmentHiringData(): array
    {
        return DB::table('applications')
            ->select('departments.name', DB::raw('COUNT(*) as count'))
            ->join('job_postings', 'applications.job_posting_id', '=', 'job_postings.id')
            ->join('departments', 'job_postings.department_id', '=', 'departments.id')
            ->where('applications.created_at', '>=', now()->subMonths(6))
            ->groupBy('departments.id', 'departments.name')
            ->orderByDesc('count')
            ->limit(10)
            ->get()
            ->map(fn($row) => [
                'label' => $row->name,
                'value' => (int) $row->count,
            ])
            ->toArray();
    }

    public function getRecentApplications(): array
    {
        return Application::with(['candidate', 'jobPosting'])
            ->latest()
            ->limit(10)
            ->get()
            ->map(fn($app) => [
                'id' => $app->id,
                'application_number' => $app->application_number,
                'candidate_name' => $app->candidate->full_name,
                'job_title' => $app->jobPosting->title,
                'status' => $app->status,
                'status_label' => $app->display_status,
                'status_color' => $app->status_color,
                'applied_at' => $app->created_at->diffForHumans(),
            ])
            ->toArray();
    }

    public function getUpcomingInterviews(): array
    {
        return Interview::upcoming()
            ->with(['candidate', 'jobPosting'])
            ->orderBy('scheduled_date')
            ->orderBy('start_time')
            ->limit(10)
            ->get()
            ->map(fn($interview) => [
                'id' => $interview->id,
                'candidate_name' => $interview->candidate->full_name,
                'job_title' => $interview->jobPosting->title,
                'type' => $interview->display_type,
                'date' => $interview->scheduled_date->format('M d, Y'),
                'time' => $interview->formatted_time,
                'mode' => $interview->display_mode,
            ])
            ->toArray();
    }
}
