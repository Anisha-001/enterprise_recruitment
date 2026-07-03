<?php

namespace App\Http\Controllers\Careers;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\JobPosting;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JobController extends Controller
{
    public function index(Request $request): View
    {
        $jobs = JobPosting::published()
            ->with(['department', 'location', 'skills'])
            ->when($request->filled('search'), fn($q) => $q->search($request->search))
            ->when($request->filled('department'), fn($q) => $q->byDepartment($request->department))
            ->when($request->filled('location'), fn($q) => $q->byLocation($request->location))
            ->when($request->filled('type'), fn($q) => $q->byEmploymentType($request->type))
            ->when($request->filled('experience'), fn($q) => $q->where('experience_level', $request->experience))
            ->when($request->filled('arrangement'), fn($q) => $q->where('work_arrangement', $request->arrangement))
            ->orderBy('is_featured', 'desc')
            ->orderBy('published_at', 'desc')
            ->paginate(12)
            ->withQueryString();

        $departments = Department::active()->orderBy('name')->get();
        $locations = Location::active()->orderBy('city')->get();

        return view('careers.jobs.index', compact('jobs', 'departments', 'locations'));
    }

    public function show(string $slug): View
    {
        $job = JobPosting::published()
            ->where('slug', $slug)
            ->with(['department', 'location', 'skills', 'screeningQuestions' => fn($q) => $q->active()])
            ->firstOrFail();

        $relatedJobs = JobPosting::published()
            ->where('id', '!=', $job->id)
            ->where(function ($q) use ($job) {
                $q->where('department_id', $job->department_id)
                    ->orWhere('location_id', $job->location_id);
            })
            ->limit(4)
            ->get();

        return view('careers.jobs.show', compact('job', 'relatedJobs'));
    }
}
