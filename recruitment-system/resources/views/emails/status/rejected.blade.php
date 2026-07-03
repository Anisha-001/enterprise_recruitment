@extends('emails.layout')

@section('title', 'Application Update')

@section('content')
    <h1>Application Update: {{ $application->jobPosting->title }}</h1>
    <p>Dear {{ $application->candidate->full_name }},</p>
    <p>Thank you for your interest in the <strong>{{ $application->jobPosting->title }}</strong> position at {{ config('recruitment.seo.company_name') }} and for taking the time to participate in our recruitment process.</p>
    <p>Our hiring team has carefully reviewed your application and qualifications, and after thorough consideration, we regret to inform you that we will not be moving forward with your candidacy at this time.</p>
    
    @if(!empty($rejectionReason))
        <p><strong>Feedback from our selection committee:</strong></p>
        <blockquote style="margin: 15px 0; padding: 12px 18px; background: #fff5f5; border-left: 4px solid #f87171; color: #7f1d1d; font-size: 14px; border-radius: 4px;">
            {!! nl2br(e($rejectionReason)) !!}
        </blockquote>
    @endif

    <p>Every hiring cycle brings a large volume of exceptional candidates, making our decisions extremely competitive. Please know that this decision is a result of aligning current project profiles, and we are very grateful for the opportunity to have met you.</p>
    <p>With your permission, we will keep your resume on file in our talent pool for future openings that match your skills. We wish you the absolute best in your career pursuits.</p>
    <p>Best regards,<br>The Recruitment Team<br>{{ config('recruitment.seo.company_name') }}</p>
@endsection
