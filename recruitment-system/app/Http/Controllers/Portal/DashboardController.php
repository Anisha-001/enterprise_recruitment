<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display candidate dashboard.
     */
    public function index(): View
    {
        $candidate = Auth::guard('candidate')->user();

        // Load applications
        $applications = $candidate->applications()
            ->with(['jobPosting.department', 'jobPosting.location', 'recruiter'])
            ->orderByDesc('updated_at')
            ->get();

        $latestApplication = $applications->first();

        // Next upcoming interview
        $nextInterview = $candidate->interviews()
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->where('scheduled_date', '>=', now()->startOfDay())
            ->with(['jobPosting', 'location'])
            ->orderBy('scheduled_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->first();

        // Pending offers requiring response
        $pendingOffers = [];
        if ($latestApplication) {
            $pendingOffers = $latestApplication->offers()
                ->whereIn('status', ['sent', 'negotiating'])
                ->get();
        }

        // Recruiter contact details
        $recruiter = $latestApplication?->recruiter;

        // Latest system notification
        $latestNotification = $candidate->unreadNotifications()->first() 
            ?? $candidate->notifications()->first();

        // Define expected documents and check what's missing
        $uploadedCollections = $candidate->documents()->pluck('collection')->toArray();
        $expectedDocs = [
            'identity' => 'Identity Proof (Passport/ID)',
            'certificates' => 'Academic Certificates',
        ];
        
        $pendingDocs = [];
        foreach ($expectedDocs as $col => $label) {
            if (!in_array($col, $uploadedCollections)) {
                $pendingDocs[] = [
                    'collection' => $col,
                    'label' => $label
                ];
            }
        }

        return view('portal.dashboard', compact(
            'candidate',
            'applications',
            'latestApplication',
            'nextInterview',
            'pendingOffers',
            'recruiter',
            'latestNotification',
            'pendingDocs'
        ));
    }
}
