@extends('layouts.portal')

@section('title', 'My Interviews')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 animate-fade-in">
    <div class="mb-8">
        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-gray-900">Interviews</h1>
        <p class="text-sm text-gray-500 mt-1">Review scheduled interview rounds and past history details.</p>
    </div>

    <!-- Upcoming Interviews Grid -->
    <div class="space-y-6 mb-10">
        <h2 class="text-lg font-bold text-gray-900">Upcoming Interviews</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($upcomingInterviews as $interview)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col justify-between">
                    <div class="space-y-3">
                        <div class="flex justify-between items-start">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-{{ $interview->status_color }}-50 text-{{ $interview->status_color }}-800 border border-{{ $interview->status_color }}-200 uppercase tracking-wider">
                                {{ $interview->display_status }}
                            </span>
                            <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">{{ $interview->display_type }}</span>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">{{ $interview->jobPosting->title }} Interview</h3>
                        <div class="text-sm text-gray-600 space-y-2 font-medium">
                            <p class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                {{ $interview->scheduled_date->format('F d, Y') }} at {{ $interview->formatted_time }}
                            </p>
                            <p class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Duration: {{ $interview->duration_minutes }} minutes
                            </p>
                            <p class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 100-6 3 3 0 000 6z"/></svg>
                                Mode: {{ $interview->display_mode }}
                            </p>
                            @if($interview->mode === 'video_call' && $interview->meeting_link)
                                <p class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                    Platform: {{ ucfirst($interview->video_platform) }}
                                </p>
                            @elseif($interview->mode === 'in_person' && $interview->interview_address)
                                <p class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    Location: {{ $interview->interview_address }}
                                </p>
                            @endif
                        </div>
                        @if($interview->instructions)
                            <div class="mt-4 p-3 bg-gray-50 border border-gray-100 rounded-lg text-xs text-gray-500">
                                <span class="font-bold text-gray-700 block mb-1">Special Instructions:</span>
                                {!! nl2br(e($interview->instructions)) !!}
                            </div>
                        @endif
                    </div>
                    @if($interview->mode === 'video_call' && $interview->meeting_link)
                        <div class="mt-6">
                            <a href="{{ $interview->meeting_link }}" target="_blank" class="w-full text-center inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-semibold rounded-lg text-white bg-brand-600 hover:bg-brand-700 shadow-md transition">
                                Join Video Room
                            </a>
                        </div>
                    @endif
                </div>
            @empty
                <div class="bg-white rounded-xl border border-gray-200 p-6 text-center text-gray-500 md:col-span-2 py-10">
                    No upcoming interviews scheduled at the moment.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Past Interviews List -->
    <div class="space-y-6">
        <h2 class="text-lg font-bold text-gray-900">Past & Rescheduled Interviews</h2>
        <div class="bg-white shadow-sm border border-gray-200 rounded-xl overflow-hidden">
            <div class="divide-y divide-gray-200">
                @forelse($pastInterviews as $interview)
                    <div class="p-6 flex flex-col sm:flex-row justify-between items-start sm:items-center">
                        <div class="space-y-2">
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600 border border-gray-200 uppercase tracking-wider">
                                    {{ $interview->display_status }}
                                </span>
                                <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">{{ $interview->display_type }}</span>
                            </div>
                            <h3 class="text-base font-bold text-gray-900">{{ $interview->jobPosting->title }} Interview</h3>
                            <p class="text-sm text-gray-500">
                                Conducted on {{ $interview->scheduled_date->format('M d, Y') }} &bull; Mode: {{ $interview->display_mode }}
                            </p>
                        </div>
                        <div class="mt-4 sm:mt-0 text-xs text-gray-400 font-semibold">
                            Date: {{ $interview->scheduled_date->format('M d, Y') }}
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-gray-500 py-10">
                        No historical interview data available.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection
