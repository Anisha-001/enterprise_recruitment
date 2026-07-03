@use('App\Models\JobPosting')
@extends('layouts.careers')

@section('title', $job->title)
@section('meta_description', $job->meta_description ?? Str::limit(strip_tags($job->description), 160))

@section('content')
<!-- Job Header -->
<section class="gradient-hero py-12 md:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 mb-3">
                    <span class="bg-white/20 backdrop-blur-sm text-white text-sm px-3 py-1 rounded-full">{{ $job->department->name }}</span>
                    <span class="bg-white/20 backdrop-blur-sm text-white text-sm px-3 py-1 rounded-full">{{ JobPosting::EMPLOYMENT_TYPES[$job->employment_type] }}</span>
                    @if($job->work_arrangement !== 'on_site')
                    <span class="bg-white/20 backdrop-blur-sm text-white text-sm px-3 py-1 rounded-full">{{ JobPosting::WORK_ARRANGEMENTS[$job->work_arrangement] }}</span>
                    @endif
                </div>
                <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">{{ $job->title }}</h1>
                <div class="flex items-center gap-4 text-white/80 text-sm">
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        {{ $job->location->city }}, {{ $job->location->country }}
                    </span>
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $job->experience_range }}
                    </span>
                    @if($job->salary_range)
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $job->salary_range }}
                    </span>
                    @endif
                </div>
            </div>
            <a href="{{ route('careers.apply', $job) }}" class="bg-white text-brand-700 px-8 py-3.5 rounded-xl font-semibold hover:bg-gray-100 transition shadow-lg text-center">
                Apply for this Position
            </a>
        </div>
    </div>
</section>

<!-- Job Details -->
<section class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">
                @if($job->description)
                <div class="bg-white rounded-xl border border-gray-200 p-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">About the Role</h2>
                    <div class="prose max-w-none text-gray-600">{!! $job->description !!}</div>
                </div>
                @endif

                @if($job->responsibilities)
                <div class="bg-white rounded-xl border border-gray-200 p-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Key Responsibilities</h2>
                    <div class="prose max-w-none text-gray-600">{!! $job->responsibilities !!}</div>
                </div>
                @endif

                @if($job->requirements)
                <div class="bg-white rounded-xl border border-gray-200 p-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Requirements</h2>
                    <div class="prose max-w-none text-gray-600">{!! $job->requirements !!}</div>
                </div>
                @endif

                @if($job->benefits)
                <div class="bg-white rounded-xl border border-gray-200 p-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Benefits & Perks</h2>
                    <div class="prose max-w-none text-gray-600">{!! $job->benefits !!}</div>
                </div>
                @endif

                <!-- Apply CTA -->
                <div class="bg-gradient-to-r from-brand-600 to-brand-700 rounded-xl p-8 text-center text-white">
                    <h3 class="text-2xl font-bold mb-3">Interested in this role?</h3>
                    <p class="text-white/80 mb-6">Don't miss this opportunity. Apply now and take the next step in your career.</p>
                    <a href="{{ route('careers.apply', $job) }}" class="inline-block bg-white text-brand-700 px-8 py-3 rounded-xl font-semibold hover:bg-gray-100 transition">
                        Apply Now
                    </a>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Job Overview -->
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Job Overview</h3>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-brand-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <div>
                                <p class="text-sm text-gray-500">Department</p>
                                <p class="font-medium text-gray-900">{{ $job->department->name }}</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-brand-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <div>
                                <p class="text-sm text-gray-500">Location</p>
                                <p class="font-medium text-gray-900">{{ $job->location->full_address }}</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-brand-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div>
                                <p class="text-sm text-gray-500">Experience</p>
                                <p class="font-medium text-gray-900">{{ $job->experience_range }}</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-brand-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div>
                                <p class="text-sm text-gray-500">Salary</p>
                                <p class="font-medium text-gray-900">{{ $job->salary_range ?? 'Competitive' }}</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-brand-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <div>
                                <p class="text-sm text-gray-500">Date Posted</p>
                                <p class="font-medium text-gray-900">{{ $job->published_at ? $job->published_at->format('M d, Y') : 'Recently' }}</p>
                            </div>
                        </div>
                        @if($job->closing_date)
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-red-500 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div>
                                <p class="text-sm text-gray-500">Apply Before</p>
                                <p class="font-medium text-red-600">{{ $job->closing_date->format('M d, Y') }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Skills -->
                @if($job->skills->isNotEmpty())
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Required Skills</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($job->skills as $skill)
                        <span class="bg-brand-50 text-brand-700 text-sm px-3 py-1.5 rounded-lg font-medium">
                            {{ $skill->name }}
                            @if($skill->pivot->is_required)
                            <span class="text-brand-500">*</span>
                            @endif
                        </span>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Share -->
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Share this job</h3>
                    <div class="flex gap-3">
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->url()) }}" target="_blank" class="w-10 h-10 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center hover:bg-blue-100 transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                        </a>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode('Check out this job: ' . $job->title) }}" target="_blank" class="w-10 h-10 bg-sky-50 text-sky-500 rounded-lg flex items-center justify-center hover:bg-sky-100 transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                        </a>
                        <button onclick="navigator.clipboard.writeText('{{ request()->url() }}'); alert('Link copied!')" class="w-10 h-10 bg-gray-100 text-gray-600 rounded-lg flex items-center justify-center hover:bg-gray-200 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related Jobs -->
@if($relatedJobs->isNotEmpty())
<section class="py-16 bg-white border-t border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-8">Similar Positions</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($relatedJobs as $related)
            <a href="{{ route('careers.jobs.show', $related) }}" class="flex items-center p-4 bg-gray-50 rounded-xl hover:bg-brand-50 transition group">
                <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-brand-600 font-bold mr-4 group-hover:bg-brand-100 transition">
                    {{ substr($related->department->name, 0, 1) }}
                </div>
                <div class="flex-1">
                    <h3 class="font-semibold text-gray-900 group-hover:text-brand-700 transition">{{ $related->title }}</h3>
                    <p class="text-sm text-gray-500">{{ $related->department->name }} &middot; {{ $related->location->city }}</p>
                </div>
                <svg class="w-5 h-5 text-gray-400 group-hover:text-brand-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
