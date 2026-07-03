@extends('layouts.careers')

@section('title', 'Application Submitted')

@section('content')
<section class="min-h-[70vh] flex items-center justify-center py-20">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="w-24 h-24 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-6 animate-fade-in">
            <svg class="w-12 h-12 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>

        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4 animate-slide-up">
            Application Submitted Successfully!
        </h1>

        <p class="text-lg text-gray-600 mb-8 animate-slide-up" style="animation-delay: 0.1s">
            Thank you for your interest in joining our team. We've received your application for the <strong>{{ $application->jobPosting->title }}</strong> position.
        </p>

        <div class="bg-gray-50 rounded-xl border border-gray-200 p-6 mb-8 animate-slide-up" style="animation-delay: 0.2s">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-left">
                <div>
                    <p class="text-sm text-gray-500">Application Number</p>
                    <p class="font-mono font-bold text-brand-700 text-lg">{{ $application->application_number }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Applied On</p>
                    <p class="font-medium text-gray-900">{{ $application->created_at->format('F d, Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Position</p>
                    <p class="font-medium text-gray-900">{{ $application->jobPosting->title }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Department</p>
                    <p class="font-medium text-gray-900">{{ $application->jobPosting->department->name }}</p>
                </div>
            </div>
        </div>

        <div class="space-y-4 text-gray-600 mb-10 animate-slide-up" style="animation-delay: 0.3s">
            <p>Here's what happens next:</p>
            <div class="flex flex-col gap-3 max-w-md mx-auto text-left">
                <div class="flex items-start">
                    <span class="w-8 h-8 bg-brand-100 text-brand-700 rounded-full flex items-center justify-center text-sm font-bold mr-3 flex-shrink-0">1</span>
                    <p class="text-sm">Our HR team will review your application within 3-5 business days.</p>
                </div>
                <div class="flex items-start">
                    <span class="w-8 h-8 bg-brand-100 text-brand-700 rounded-full flex items-center justify-center text-sm font-bold mr-3 flex-shrink-0">2</span>
                    <p class="text-sm">If shortlisted, we'll contact you via email to schedule an interview.</p>
                </div>
                <div class="flex items-start">
                    <span class="w-8 h-8 bg-brand-100 text-brand-700 rounded-full flex items-center justify-center text-sm font-bold mr-3 flex-shrink-0">3</span>
                    <p class="text-sm">You'll be able to track your application status using your application number.</p>
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-4 justify-center animate-slide-up" style="animation-delay: 0.4s">
            <a href="{{ route('careers.jobs.index') }}" class="bg-brand-600 text-white px-8 py-3 rounded-xl font-medium hover:bg-brand-700 transition">
                Browse More Jobs
            </a>
            <a href="{{ route('careers.home') }}" class="bg-white border border-gray-300 text-gray-700 px-8 py-3 rounded-xl font-medium hover:bg-gray-50 transition">
                Back to Home
            </a>
        </div>
    </div>
</section>
@endsection
