@extends('layouts.portal')

@section('title', 'Track Application: ' . $application->jobPosting->title)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 animate-fade-in">
    <!-- Back to list link -->
    <div class="mb-6">
        <a href="{{ route('candidate.applications.index') }}" class="inline-flex items-center text-sm font-semibold text-brand-700 hover:text-brand-800">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Job Applications
        </a>
    </div>

    <!-- Main Header Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sm:p-8 mb-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
            <div class="space-y-1">
                <div class="flex items-center space-x-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-{{ $application->status_color }}-50 text-{{ $application->status_color }}-800 border border-{{ $application->status_color }}-200">
                        {{ $application->display_status }}
                    </span>
                    <span class="text-xs text-gray-400 font-mono">#{{ $application->application_number }}</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900 tracking-tight">{{ $application->jobPosting->title }}</h1>
                <p class="text-sm text-gray-500">
                    {{ $application->jobPosting->department->name }} &bull; {{ $application->jobPosting->location->name }}
                </p>
            </div>
            <div class="mt-4 sm:mt-0 text-left sm:text-right">
                <p class="text-xs text-gray-400">Submitted on</p>
                <p class="text-sm font-bold text-gray-700">{{ $application->created_at->format('M d, Y') }}</p>
            </div>
        </div>
    </div>

    <!-- Stepper & Detailed Progress Panel -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
        <div class="px-6 py-5 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-900">Application Progression</h2>
        </div>
        <div class="p-6 sm:p-8">
            <!-- Vertical Stepper Layout -->
            <div class="flow-root">
                <ul role="list" class="-mb-8">
                    @foreach($stepperSteps as $index => $step)
                        <li>
                            <div class="relative pb-8">
                                <!-- Linking vertical bar -->
                                @if($index !== count($stepperSteps) - 1)
                                    <span class="absolute top-4 left-5 -ml-px h-full w-0.5 {{ $step['reached'] && $stepperSteps[$index + 1]['reached'] ? 'bg-brand-600' : 'bg-gray-200' }}" aria-hidden="true"></span>
                                @endif

                                <div class="relative flex space-x-3 sm:space-x-4">
                                    <!-- Circle status icon -->
                                    <div>
                                        <span class="h-10 w-10 rounded-full flex items-center justify-center ring-8 ring-white 
                                            {{ $step['current'] ? 'bg-brand-600 text-white animate-pulse' : ($step['reached'] ? 'bg-brand-50 text-brand-700 border-2 border-brand-600' : 'bg-gray-100 text-gray-400 border-2 border-gray-200') }}">
                                            <!-- Stepper Icon Mapping -->
                                            @if($step['key'] === 'submitted')
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            @elseif($step['key'] === 'screening')
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                            @elseif($step['key'] === 'shortlisted')
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            @elseif($step['key'] === 'interview')
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                            @elseif($step['key'] === 'offer')
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                            @else
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                            @endif
                                        </span>
                                    </div>

                                    <!-- Content detail -->
                                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                        <div class="space-y-1">
                                            <p class="text-sm font-bold text-gray-900">{{ $step['label'] }}</p>
                                            <!-- Candidate-visible status comment (only show if reached and not empty) -->
                                            @if($step['reached'] && !empty($step['comment']))
                                                <p class="text-xs text-gray-500 bg-gray-50 border border-gray-100 rounded-md p-2 max-w-xl">
                                                    {!! nl2br(e($step['comment'])) !!}
                                                </p>
                                            @endif
                                        </div>
                                        <div class="text-right text-xs whitespace-nowrap text-gray-500 font-medium">
                                            @if($step['date'])
                                                <time datetime="{{ $step['date'] }}">{{ $step['date']->format('M d, Y') }}</time>
                                            @else
                                                <span class="text-gray-300">Pending</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <!-- Active Offers Section -->
    @if($latestOffer && in_array($latestOffer->status, ['sent', 'negotiating']))
        <div class="bg-amber-50 rounded-xl shadow-sm border border-amber-200 p-6 mb-8">
            <h3 class="text-lg font-bold text-amber-900 mb-2">Offer of Employment Details</h3>
            <p class="text-sm text-amber-700 mb-4">
                We have prepared an offer letter for you. Please download and review the offer letter below.
            </p>
            @if($latestOffer->pdf_path)
                <div class="mb-4">
                    <a href="{{ asset('storage/' . $latestOffer->pdf_path) }}" target="_blank" class="inline-flex items-center px-4 py-2 border border-brand-300 text-xs font-semibold rounded-lg text-brand-700 bg-white hover:bg-brand-50 shadow-sm">
                        <svg class="w-4 h-4 mr-1.5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Download PDF Offer Letter
                    </a>
                </div>
            @endif
            <div class="flex space-x-3">
                <button onclick="document.getElementById('decline-offer-modal').classList.remove('hidden')" class="px-4 py-2 border border-gray-300 text-sm font-semibold rounded-lg text-gray-700 bg-white hover:bg-gray-50">
                    Decline Offer
                </button>
                <form method="POST" action="{{ route('candidate.applications.offer.accept', $application->id) }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 border border-transparent text-sm font-semibold rounded-lg text-white bg-brand-600 hover:bg-brand-700 shadow-md">
                        Accept Offer
                    </button>
                </form>
            </div>
        </div>
    @endif

    <!-- Interviews specific to this Application -->
    @if($application->interviews->isNotEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900">Interviews</h2>
            </div>
            <div class="divide-y divide-gray-200">
                @foreach($application->interviews as $interview)
                    <div class="p-6 flex flex-col sm:flex-row justify-between items-start sm:items-center">
                        <div class="space-y-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold 
                                bg-{{ $interview->status_color }}-50 text-{{ $interview->status_color }}-800 border border-{{ $interview->status_color }}-200 uppercase tracking-wide">
                                {{ $interview->display_status }}
                            </span>
                            <h3 class="text-base font-bold text-gray-900">{{ $interview->display_type }}</h3>
                            <div class="text-sm text-gray-500 space-y-1 mt-2 font-medium">
                                <p class="flex items-center">
                                    <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    {{ $interview->scheduled_date->format('F d, Y') }} at {{ $interview->formatted_time }} ({{ $interview->duration_minutes }} minutes)
                                </p>
                                <p class="flex items-center">
                                    <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                    Mode: {{ $interview->display_mode }}
                                </p>
                                @if($interview->mode === 'video_call' && $interview->meeting_link)
                                    <p class="flex items-center">
                                        <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                        Link: <a href="{{ $interview->meeting_link }}" target="_blank" class="text-brand-600 hover:text-brand-700 hover:underline break-all">{{ $interview->meeting_link }}</a>
                                    </p>
                                @elseif($interview->mode === 'in_person' && $interview->interview_address)
                                    <p class="flex items-center">
                                        <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        Address: {{ $interview->interview_address }}
                                    </p>
                                @endif
                            </div>
                        </div>
                        @if($interview->is_upcoming && $interview->mode === 'video_call' && $interview->meeting_link)
                            <div class="mt-4 sm:mt-0">
                                <a href="{{ $interview->meeting_link }}" target="_blank" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-semibold rounded-lg text-white bg-brand-600 hover:bg-brand-700 shadow-md transition">
                                    Join Interview Room
                                </a>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection
