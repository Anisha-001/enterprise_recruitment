@extends('layouts.portal')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 animate-fade-in">
    <!-- Top Welcome Banner -->
    <div class="gradient-bg rounded-2xl p-6 sm:p-8 text-white shadow-lg mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Welcome, {{ $candidate->first_name }}!</h1>
            <p class="mt-2 text-brand-100 max-w-xl text-sm sm:text-base">
                Track your active applications, schedule reviews, upload onboarding documents, and keep in touch with your recruiter.
            </p>
        </div>
        <div class="mt-4 sm:mt-0 bg-white/10 backdrop-blur-md border border-white/20 rounded-xl px-4 py-2 text-right self-stretch sm:self-auto flex sm:flex-col justify-between sm:justify-start items-center sm:items-end">
            <span class="text-xs text-brand-100 font-semibold uppercase tracking-wider">Candidate ID</span>
            <span class="text-sm font-bold tracking-mono">{{ $candidate->candidate_number }}</span>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left 2-Column: Applications & Updates -->
        <div class="lg:col-span-2 space-y-8">
            
            <!-- Pending Offers Section -->
            @if($pendingOffers->isNotEmpty())
                @foreach($pendingOffers as $offer)
                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-6 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center">
                        <div class="flex items-center space-x-4">
                            <div class="p-3 bg-amber-100 rounded-lg text-amber-800">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-amber-900">Employment Offer Received!</h3>
                                <p class="text-sm text-amber-700 mt-0.5">
                                    You have received an offer for the <strong>{{ $offer->proposed_designation }}</strong> position.
                                </p>
                                @if($offer->pdf_path)
                                    <a href="{{ asset('storage/' . $offer->pdf_path) }}" target="_blank" class="inline-flex items-center text-xs font-semibold text-brand-700 hover:text-brand-800 mt-2">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        Download Offer Letter PDF
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="mt-4 md:mt-0 flex space-x-3 w-full md:w-auto">
                            <!-- Decline Button trigger form -->
                            <button onclick="document.getElementById('decline-offer-modal').classList.remove('hidden')" class="flex-1 md:flex-none text-center px-4 py-2 border border-gray-300 text-sm font-semibold rounded-lg text-gray-700 bg-white hover:bg-gray-50">
                                Decline
                            </button>
                            <!-- Accept Form -->
                            <form method="POST" action="{{ route('candidate.applications.offer.accept', $offer->application_id) }}" class="flex-1 md:flex-none">
                                @csrf
                                <button type="submit" class="w-full text-center px-4 py-2 border border-transparent text-sm font-semibold rounded-lg text-white bg-brand-600 hover:bg-brand-700 shadow-md">
                                    Accept Offer
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Decline Modal -->
                    <div id="decline-offer-modal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
                        <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-2xl relative">
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Decline Offer</h3>
                            <p class="text-sm text-gray-500 mb-4">Please provide a brief reason for declining our offer. We value your feedback.</p>
                            <form method="POST" action="{{ route('candidate.applications.offer.reject', $offer->application_id) }}">
                                @csrf
                                <textarea name="rejection_reason" rows="4" required placeholder="Reason for declining..." class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:ring-brand-500 focus:border-brand-500 focus:outline-none"></textarea>
                                <div class="mt-4 flex justify-end space-x-3">
                                    <button type="button" onclick="document.getElementById('decline-offer-modal').classList.add('hidden')" class="px-4 py-2 border border-gray-300 text-sm rounded-lg text-gray-700">Cancel</button>
                                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-sm font-semibold text-white rounded-lg">Submit Decline</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach
            @endif

            <!-- Active Applications List -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="text-lg font-bold text-gray-900">Your Applications</h2>
                    <a href="{{ route('candidate.applications.index') }}" class="text-sm font-semibold text-brand-700 hover:text-brand-800">View All</a>
                </div>
                <div class="divide-y divide-gray-200">
                    @forelse($applications as $app)
                        <div class="p-6 flex flex-col sm:flex-row justify-between items-start sm:items-center hover:bg-gray-50 transition">
                            <div class="space-y-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-{{ $app->status_color }}-50 text-{{ $app->status_color }}-800 border border-{{ $app->status_color }}-200">
                                    {{ $app->display_status }}
                                </span>
                                <h3 class="text-base font-bold text-gray-900">{{ $app->jobPosting->title }}</h3>
                                <p class="text-sm text-gray-500">
                                    {{ $app->jobPosting->department->name }} &bull; {{ $app->jobPosting->location->name }}
                                </p>
                            </div>
                            <div class="mt-4 sm:mt-0 flex items-center space-x-4">
                                <span class="text-xs text-gray-400">Updated {{ $app->updated_at->diffForHumans() }}</span>
                                <a href="{{ route('candidate.applications.show', $app->id) }}" class="inline-flex items-center px-3.5 py-2 border border-gray-300 text-xs font-semibold rounded-lg text-gray-700 bg-white hover:bg-gray-50 shadow-sm transition">
                                    Track Status
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-gray-500 py-12">
                            No applications submitted yet.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Next Interview Card -->
            @if($nextInterview)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Next Scheduled Interview</h2>
                    <div class="bg-brand-50 border border-brand-100 rounded-xl p-5 flex flex-col md:flex-row justify-between items-start md:items-center">
                        <div class="space-y-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-brand-100 text-brand-800 uppercase tracking-wider">
                                {{ $nextInterview->display_type }}
                            </span>
                            <h3 class="text-base font-bold text-gray-900">{{ $nextInterview->jobPosting->title }} Interview</h3>
                            <div class="text-sm text-gray-600 flex flex-wrap gap-y-1 gap-x-4 mt-2">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    {{ $nextInterview->scheduled_date->format('F d, Y') }} at {{ $nextInterview->formatted_time }}
                                </span>
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                    {{ $nextInterview->display_mode }}
                                </span>
                            </div>
                        </div>
                        @if($nextInterview->mode === 'video_call' && $nextInterview->meeting_link)
                            <a href="{{ $nextInterview->meeting_link }}" target="_blank" class="mt-4 md:mt-0 inline-flex items-center px-4 py-2 border border-transparent text-sm font-semibold rounded-lg text-white bg-brand-600 hover:bg-brand-700 shadow-md transition">
                                Join Call
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Side Columns: Sidebar widgets -->
        <div class="space-y-8">
            
            <!-- Recruiter Widget -->
            @if($recruiter)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Your Recruiter Point of Contact</h3>
                    <div class="w-20 h-20 rounded-full bg-brand-50 border border-brand-200 overflow-hidden flex items-center justify-center font-bold text-brand-700 mx-auto">
                        @if($recruiter->avatar)
                            <img src="{{ asset('storage/' . $recruiter->avatar) }}" alt="Recruiter" class="w-full h-full object-cover">
                        @else
                            {{ substr($recruiter->first_name, 0, 1) . substr($recruiter->last_name, 0, 1) }}
                        @endif
                    </div>
                    <h4 class="text-base font-bold text-gray-900 mt-3">{{ $recruiter->full_name }}</h4>
                    <p class="text-xs text-gray-500">Recruitment Specialist</p>
                    <div class="mt-4">
                        <a href="mailto:{{ $recruiter->email }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-xs font-semibold rounded-lg text-gray-700 bg-white hover:bg-gray-50 shadow-sm w-full justify-center">
                            <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            Email Recruiter
                        </a>
                    </div>
                </div>
            @endif

            <!-- Missing Documents / Checklist Widget -->
            @if(count($pendingDocs) > 0)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Onboarding Checklist</h3>
                    <p class="text-xs text-gray-500 mb-4">Please upload the following required files to complete your onboarding verification profile:</p>
                    <ul class="space-y-3">
                        @foreach($pendingDocs as $doc)
                            <li class="flex justify-between items-center text-sm border-b border-gray-100 pb-2">
                                <span class="text-gray-700 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                                    {{ $doc['label'] }}
                                </span>
                                <a href="{{ route('candidate.documents.index') }}" class="text-xs font-semibold text-brand-600 hover:text-brand-700 hover:underline">
                                    Upload
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Latest Notification Alert widget -->
            @if($latestNotification)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Latest Update</h3>
                    <div class="text-xs text-gray-600 bg-gray-50 rounded-lg p-3 border border-gray-100">
                        <span class="font-bold text-gray-800 block mb-1">
                            {{ $latestNotification->created_at->diffForHumans() }}
                        </span>
                        {{ $latestNotification->data['message'] ?? 'Status update logged.' }}
                    </div>
                </div>
            @endif

        </div>

    </div>
</div>
@endsection
