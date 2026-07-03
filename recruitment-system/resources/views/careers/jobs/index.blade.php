@use('App\Models\JobPosting')
@extends('layouts.careers')

@section('title', 'Open Positions')
@section('meta_description', 'Explore all open positions. Find your dream job in engineering, design, marketing, sales, and more.')

@section('content')
<!-- Page Header -->
<section class="gradient-hero py-16 md:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl">
            <h1 class="text-3xl md:text-5xl font-bold text-white mb-4">Open Positions</h1>
            <p class="text-lg text-white/80">Find the perfect role that matches your skills, experience, and passion.</p>
        </div>
    </div>
</section>

<!-- Search & Filters -->
<section class="py-8 bg-white border-b border-gray-200 sticky top-16 z-40 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <form method="GET" action="{{ route('careers.jobs.index') }}" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Job title, skills, or keywords..."
                        class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                    <svg class="absolute left-3 top-3 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                <select name="department" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 bg-white">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ request('department') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                <select name="location" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 bg-white">
                    <option value="">All Locations</option>
                    @foreach($locations as $loc)
                    <option value="{{ $loc->id }}" {{ request('location') == $loc->id ? 'selected' : '' }}>{{ $loc->city }}, {{ $loc->country }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select name="type" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 bg-white">
                    <option value="">All Types</option>
                    @foreach(JobPosting::EMPLOYMENT_TYPES as $key => $label)
                    <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-brand-600 text-white px-6 py-2.5 rounded-lg font-medium hover:bg-brand-700 transition">
                    Filter
                </button>
                <a href="{{ route('careers.jobs.index') }}" class="bg-gray-100 text-gray-700 px-6 py-2.5 rounded-lg font-medium hover:bg-gray-200 transition">
                    Reset
                </a>
            </div>
        </form>
    </div>
</section>

<!-- Job Listings -->
<section class="py-12 bg-gray-50 min-h-[500px]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <p class="text-gray-600"><span class="font-semibold text-gray-900">{{ $jobs->total() }}</span> position{{ $jobs->total() !== 1 ? 's' : ''}} found</p>
        </div>

        <div class="space-y-4">
            @forelse($jobs as $job)
            <div class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-all hover:border-brand-200 group">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <h3 class="text-lg font-semibold text-gray-900 group-hover:text-brand-700 transition">
                                <a href="{{ route('careers.jobs.show', $job) }}">{{ $job->title }}</a>
                            </h3>
                            @if($job->is_featured)
                            <span class="bg-amber-50 text-amber-700 text-xs font-medium px-2 py-1 rounded-full">Featured</span>
                            @endif
                            @if($job->is_urgent)
                            <span class="bg-red-50 text-red-600 text-xs font-medium px-2 py-1 rounded-full">Urgent</span>
                            @endif
                        </div>
                        <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500 mb-3">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                {{ $job->department->name }}
                            </span>
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                {{ $job->location->city }}
                            </span>
                            <span>{{ JobPosting::EMPLOYMENT_TYPES[$job->employment_type] }}</span>
                            <span>{{ JobPosting::WORK_ARRANGEMENTS[$job->work_arrangement] }}</span>
                        </div>
                        <p class="text-gray-600 text-sm line-clamp-2">{{ $job->summary ?? Str::limit(strip_tags($job->description), 150) }}</p>
                    </div>
                    <div class="flex flex-col items-end gap-2">
                        @if($job->salary_range)
                        <span class="text-brand-700 font-semibold">{{ $job->salary_range }}</span>
                        @endif
                        <span class="text-sm text-gray-500">{{ $job->experience_range }}</span>
                        <a href="{{ route('careers.apply', $job) }}" class="bg-brand-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-brand-700 transition text-sm">
                            Apply Now
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-16">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No positions found</h3>
                <p class="text-gray-500">Try adjusting your search criteria or check back later for new opportunities.</p>
            </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $jobs->links() }}
        </div>
    </div>
</section>
@endsection
