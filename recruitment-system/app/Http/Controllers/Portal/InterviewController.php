<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class InterviewController extends Controller
{
    /**
     * Display a listing of upcoming and past interviews.
     */
    public function index(): View
    {
        $candidate = Auth::guard('candidate')->user();

        // Load upcoming interviews (scheduled for today or in the future)
        $upcomingInterviews = $candidate->interviews()
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->where('scheduled_date', '>=', now()->startOfDay())
            ->with(['jobPosting', 'location'])
            ->orderBy('scheduled_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();

        // Load past interviews (completed, cancelled, or in the past)
        $pastInterviews = $candidate->interviews()
            ->where(function($q) {
                $q->where('scheduled_date', '<', now()->startOfDay())
                  ->orWhereIn('status', ['completed', 'cancelled', 'no_show']);
            })
            ->with(['jobPosting', 'location'])
            ->orderByDesc('scheduled_date')
            ->orderByDesc('start_time')
            ->get();

        return view('portal.interviews.index', compact('upcomingInterviews', 'pastInterviews'));
    }
}
