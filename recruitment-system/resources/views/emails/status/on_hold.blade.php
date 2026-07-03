@extends('emails.layout')

@section('title', 'Application Update: On Hold')

@section('content')
    <h1>Application Update: On Hold</h1>
    <p>Dear {{ $application->candidate->full_name }},</p>
    <p>We are writing to provide an update regarding your application for the <strong>{{ $application->jobPosting->title }}</strong> position.</p>
    <p>Due to recent changes in project timelines and staffing priorities, we have temporarily placed this recruitment process <strong>on hold</strong>.</p>
    <p>Please be assured that we are highly interested in your profile and qualifications. Your application remains active, and we will resume the review process as soon as hiring resumes for this role.</p>
    <p>We appreciate your patience and understanding. You can track any updates via the Candidate Portal.</p>

    <div class="button-wrapper">
        <a href="{{ route('candidate.dashboard') }}" class="button" target="_blank">View Portal Dashboard</a>
    </div>

    <p>We will keep you updated on any progress. Thank you again for your interest in joining our team.</p>
    <p>Best regards,<br>The Recruitment Team<br>{{ config('recruitment.seo.company_name') }}</p>
@endsection
