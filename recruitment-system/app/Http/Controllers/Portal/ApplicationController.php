<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Services\Portal\ApplicationTrackingService;
use App\Services\Offer\OfferService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ApplicationController extends Controller
{
    public function __construct(
        private readonly ApplicationTrackingService $trackingService,
        private readonly OfferService $offerService
    ) {}

    /**
     * List all applications for the logged-in candidate.
     */
    public function index(): View
    {
        $candidate = Auth::guard('candidate')->user();

        $applications = $candidate->applications()
            ->with(['jobPosting.department', 'jobPosting.location'])
            ->orderByDesc('created_at')
            ->get();

        return view('portal.applications.index', compact('applications'));
    }

    /**
     * Display a specific application with stepper.
     */
    public function show(Application $application): View
    {
        // Scope ownership security check
        if ((int) $application->candidate_id !== (int) Auth::guard('candidate')->id()) {
            abort(403, 'Unauthorized access to application.');
        }

        $application->load([
            'jobPosting.department',
            'jobPosting.location',
            'statusHistory.changedBy',
            'interviews' => fn($q) => $q->orderBy('scheduled_date')->orderBy('start_time'),
            'offers' => fn($q) => $q->latest()
        ]);

        $stepperSteps = $this->trackingService->getStepperSteps($application);
        $latestOffer = $application->offers->first();

        return view('portal.applications.show', compact('application', 'stepperSteps', 'latestOffer'));
    }

    /**
     * Candidate accepts the offer letter.
     */
    public function acceptOffer(Request $request, Application $application): RedirectResponse
    {
        if ((int) $application->candidate_id !== (int) Auth::guard('candidate')->id()) {
            abort(403);
        }

        $offer = $application->offers()->where('status', 'sent')->latest()->first();

        if (!$offer) {
            return back()->with('error', 'No pending offer letter found to accept.');
        }

        try {
            $this->offerService->acceptOffer($offer, $request->ip());
            return back()->with('success', 'You have successfully accepted the offer letter! Welcome aboard.');
        } catch (\Exception $e) {
            report($e);
            return back()->with('error', 'Failed to process offer acceptance. Please try again.');
        }
    }

    /**
     * Candidate rejects the offer letter.
     */
    public function rejectOffer(Request $request, Application $application): RedirectResponse
    {
        if ((int) $application->candidate_id !== (int) Auth::guard('candidate')->id()) {
            abort(403);
        }

        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        $offer = $application->offers()->where('status', 'sent')->latest()->first();

        if (!$offer) {
            return back()->with('error', 'No pending offer letter found.');
        }

        try {
            $this->offerService->rejectOffer($offer, $request->rejection_reason);
            return back()->with('success', 'Offer declined successfully.');
        } catch (\Exception $e) {
            report($e);
            return back()->with('error', 'Failed to process offer decline. Please try again.');
        }
    }
}
