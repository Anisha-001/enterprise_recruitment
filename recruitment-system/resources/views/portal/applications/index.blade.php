@extends('layouts.portal')

@section('title', 'My Applications')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 animate-fade-in">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-gray-900">Job Applications</h1>
            <p class="text-sm text-gray-500 mt-1">Review the list of your submitted job applications and their current status.</p>
        </div>
        <a href="{{ route('careers.jobs.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-semibold rounded-lg text-white bg-brand-600 hover:bg-brand-700 shadow-md transition">
            Explore Open Jobs
        </a>
    </div>

    <div class="bg-white shadow-sm border border-gray-200 rounded-xl overflow-hidden">
        <div class="divide-y divide-gray-200">
            @forelse($applications as $app)
                <div class="p-6 flex flex-col md:flex-row justify-between items-start md:items-center hover:bg-gray-50 transition">
                    <div class="space-y-2">
                        <div class="flex items-center space-x-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-{{ $app->status_color }}-50 text-{{ $app->status_color }}-800 border border-{{ $app->status_color }}-200">
                                {{ $app->display_status }}
                            </span>
                            <span class="text-xs text-gray-400 font-mono">#{{ $app->application_number }}</span>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">{{ $app->jobPosting->title }}</h3>
                        <p class="text-sm text-gray-500">
                            {{ $app->jobPosting->department->name }} &bull; {{ $app->jobPosting->location->name }}
                        </p>
                        <p class="text-xs text-gray-400">Submitted on {{ $app->created_at->format('M d, Y') }}</p>
                    </div>
                    <div class="mt-4 md:mt-0 flex items-center space-x-4 w-full md:w-auto justify-between md:justify-start">
                        <span class="text-xs text-gray-500">Last activity {{ $app->updated_at->diffForHumans() }}</span>
                        <a href="{{ route('candidate.applications.show', $app->id) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-semibold rounded-lg text-gray-700 bg-white hover:bg-gray-50 shadow-sm transition">
                            Track Status
                        </a>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center text-gray-500 py-16">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <h3 class="text-base font-bold text-gray-900 mb-1">No Applications Yet</h3>
                    <p class="text-sm text-gray-500 mb-4">You haven't applied to any job postings yet.</p>
                    <a href="{{ route('careers.jobs.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-semibold rounded-lg text-white bg-brand-600 hover:bg-brand-700 shadow-md transition">
                        Explore Open Jobs
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
