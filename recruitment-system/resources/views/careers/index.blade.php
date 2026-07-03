@use('App\Models\JobPosting')
@extends('layouts.careers')

@section('title', 'Careers')
@section('meta_description', 'Join our world-class team. Explore exciting career opportunities across engineering, design, marketing, and more.')

@section('content')
<!-- Hero Section -->
<section class="gradient-hero relative overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
            <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                <path d="M 10 0 L 0 0 0 10" fill="none" stroke="white" stroke-width="0.5"/>
            </pattern>
            <rect width="100" height="100" fill="url(#grid)"/>
        </svg>
    </div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 md:py-32 relative">
        <div class="max-w-3xl animate-slide-up">
            <div class="inline-flex items-center bg-white/10 backdrop-blur-sm border border-white/20 rounded-full px-4 py-1.5 mb-6">
                <span class="w-2 h-2 bg-emerald-400 rounded-full mr-2 animate-pulse"></span>
                <span class="text-sm text-white/90">We're hiring! {{ $featuredJobs->count() }} open positions</span>
            </div>
            <h1 class="text-4xl md:text-6xl font-extrabold text-white leading-tight mb-6">
                Build Your Career With <span class="text-teal-300">Purpose</span>
            </h1>
            <p class="text-xl text-white/80 mb-10 max-w-2xl leading-relaxed">
                Join a team where innovation meets impact. We believe in nurturing talent, embracing diversity, and creating an environment where everyone can thrive.
            </p>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('careers.jobs.index') }}" class="bg-white text-brand-700 px-8 py-4 rounded-xl font-semibold hover:bg-gray-100 transition shadow-xl">
                    View Open Positions
                </a>
                <a href="{{ route('careers.about') }}" class="bg-white/10 backdrop-blur-sm border border-white/30 text-white px-8 py-4 rounded-xl font-semibold hover:bg-white/20 transition">
                    Learn About Us
                </a>
            </div>
        </div>
    </div>
    <!-- Wave -->
    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 120L60 105C120 90 240 60 360 45C480 30 600 30 720 37.5C840 45 960 60 1080 67.5C1200 75 1320 75 1380 75L1440 75V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0Z" fill="#F9FAFB"/>
        </svg>
    </div>
</section>

<!-- Stats Section -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
            <div class="text-center">
                <div class="text-4xl font-bold text-brand-700 mb-2">500+</div>
                <div class="text-gray-600">Team Members</div>
            </div>
            <div class="text-center">
                <div class="text-4xl font-bold text-brand-700 mb-2">30+</div>
                <div class="text-gray-600">Countries</div>
            </div>
            <div class="text-center">
                <div class="text-4xl font-bold text-brand-700 mb-2">$50M+</div>
                <div class="text-gray-600">Funding Raised</div>
            </div>
            <div class="text-center">
                <div class="text-4xl font-bold text-brand-700 mb-2">4.8</div>
                <div class="text-gray-600">Employee Rating</div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Jobs Section -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Featured Opportunities</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">Discover roles that match your skills and passion. We're looking for exceptional talent across multiple disciplines.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($featuredJobs as $job)
            <div class="group glass-card border border-gray-100 rounded-2xl p-6 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-brand-50 rounded-xl flex items-center justify-center text-brand-600 font-bold text-lg">
                            {{ substr($job->department->name, 0, 1) }}
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 group-hover:text-brand-700 transition">
                                <a href="{{ route('careers.jobs.show', $job) }}">{{ $job->title }}</a>
                            </h3>
                            <p class="text-sm text-gray-500">{{ $job->department->name }}</p>
                        </div>
                    </div>
                    @if($job->is_urgent)
                    <span class="bg-red-50 text-red-600 text-xs font-medium px-2.5 py-1 rounded-full">Urgent</span>
                    @endif
                </div>

                <div class="flex flex-wrap gap-2 mb-4">
                    <span class="bg-gray-100 text-gray-600 text-xs px-2.5 py-1 rounded-full flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        {{ $job->location->city }}
                    </span>
                    <span class="bg-gray-100 text-gray-600 text-xs px-2.5 py-1 rounded-full">{{ JobPosting::EMPLOYMENT_TYPES[$job->employment_type] }}</span>
                    <span class="bg-gray-100 text-gray-600 text-xs px-2.5 py-1 rounded-full">{{ JobPosting::WORK_ARRANGEMENTS[$job->work_arrangement] }}</span>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                    <span class="text-sm text-gray-500">{{ $job->experience_range }}</span>
                    @if($job->salary_range)
                    <span class="text-sm font-medium text-brand-700">{{ $job->salary_range }}</span>
                    @else
                    <span class="text-sm text-gray-400">Competitive</span>
                    @endif
                </div>

                <a href="{{ route('careers.apply', $job) }}" class="mt-4 block w-full text-center bg-brand-50 hover:bg-brand-100 text-brand-700 font-medium py-2.5 rounded-xl transition">
                    Apply Now
                </a>
            </div>
            @empty
            <div class="col-span-full text-center py-12">
                <p class="text-gray-500">No featured jobs at the moment. Check back soon!</p>
            </div>
            @endforelse
        </div>

        <div class="text-center mt-10">
            <a href="{{ route('careers.jobs.index') }}" class="inline-flex items-center text-brand-700 font-semibold hover:text-brand-800 transition">
                View All Positions
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
    </div>
</section>

<!-- Browse by Department -->
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Browse by Department</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">Explore opportunities across our diverse teams and find where you fit best.</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($departments as $dept)
            <a href="{{ route('careers.jobs.index', ['department' => $dept->id]) }}" class="bg-white border border-gray-200 rounded-xl p-6 hover:shadow-lg hover:border-brand-200 transition-all group text-center">
                <div class="w-14 h-14 mx-auto bg-brand-50 group-hover:bg-brand-100 rounded-xl flex items-center justify-center text-brand-600 mb-3 transition">
                    <span class="text-2xl font-bold">{{ substr($dept->name, 0, 1) }}</span>
                </div>
                <h3 class="font-semibold text-gray-900 mb-1">{{ $dept->name }}</h3>
                <p class="text-sm text-brand-600 font-medium">{{ $dept->job_postings_count }} {{ Str::plural('opening', $dept->job_postings_count) }}</p>
            </a>
            @endforeach
        </div>
    </div>
</section>

<!-- Why Join Us -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Why Join Us?</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">We offer a workplace that puts people first, with benefits and perks designed to help you thrive.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center p-6">
                <div class="w-16 h-16 mx-auto bg-emerald-50 rounded-2xl flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Competitive Compensation</h3>
                <p class="text-gray-600">Above-market salaries, performance bonuses, and equity options for all full-time employees.</p>
            </div>

            <div class="text-center p-6">
                <div class="w-16 h-16 mx-auto bg-blue-50 rounded-2xl flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Growth & Learning</h3>
                <p class="text-gray-600">Annual learning budget, mentorship programs, and clear career progression paths.</p>
            </div>

            <div class="text-center p-6">
                <div class="w-16 h-16 mx-auto bg-purple-50 rounded-2xl flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Health & Wellness</h3>
                <p class="text-gray-600">Comprehensive health insurance, mental health support, gym membership, and unlimited PTO.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 gradient-hero relative overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-10 left-10 w-64 h-64 bg-white rounded-full blur-3xl"></div>
        <div class="absolute bottom-10 right-10 w-64 h-64 bg-white rounded-full blur-3xl"></div>
    </div>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative">
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">Ready to Make an Impact?</h2>
        <p class="text-xl text-white/80 mb-10">Take the first step towards your dream career. Browse our open positions and apply today.</p>
        <a href="{{ route('careers.jobs.index') }}" class="inline-block bg-white text-brand-700 px-10 py-4 rounded-xl font-semibold hover:bg-gray-100 transition shadow-xl">
            Explore All Jobs
        </a>
    </div>
</section>
@endsection
