<?php

namespace App\Http\Controllers\Careers;

use App\Http\Controllers\Controller;
use App\Models\JobPosting;
use App\Services\Application\ApplicationService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ApplicationController extends Controller
{
    public function __construct(
        private readonly ApplicationService $applicationService,
    ) {}

    public function create(string $slug): View
    {
        $job = JobPosting::published()
            ->where('slug', $slug)
            ->with(['department', 'location', 'screeningQuestions' => fn($q) => $q->active()])
            ->firstOrFail();

        return view('careers.applications.create', compact('job'));
    }

    public function store(Request $request, string $slug): RedirectResponse
    {
        $job = JobPosting::published()->where('slug', $slug)->firstOrFail();

        try {
            $application = $this->applicationService->submitApplication(
                $request->all(),
                $job,
                [
                    'resume' => $request->file('resume'),
                    'cover_letter' => $request->file('cover_letter'),
                    'photograph' => $request->file('photograph'),
                    'documents' => $request->file('documents', []),
                ]
            );

            return redirect()
                ->route('careers.thank-you', ['application' => $application->application_number])
                ->with('success', 'Your application has been submitted successfully!');

        } catch (\App\Exceptions\DuplicateApplicationException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            report($e);
            return back()->withInput()->with('error', 'An error occurred while submitting your application. Please try again.');
        }
    }

    public function thankYou(string $applicationNumber): View
    {
        $application = \App\Models\Application::where('application_number', $applicationNumber)
            ->with(['candidate', 'jobPosting'])
            ->firstOrFail();

        return view('careers.applications.thank-you', compact('application'));
    }
}
