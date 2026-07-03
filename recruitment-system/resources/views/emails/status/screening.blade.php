@extends('emails.layout')

@section('title', 'Application Update: Screening Stage')

@section('content')
    <h1>Application Update: Screening Stage</h1>
    <p>Dear {{ $application->candidate->full_name }},</p>
    <p>We are pleased to inform you that your application for the <strong>{{ $application->jobPosting->title }}</strong> position has successfully passed the initial review and has been moved to the <strong>Screening</strong> stage.</p>
    <p>Our recruitment team is currently reviewing your resume, experience, and skills in detail to assess alignment with the role's requirements.</p>
    <p>If any additional information or screening tasks are required, a member of our team will contact you. You can track your application status anytime via the Candidate Portal.</p>

    <div class="button-wrapper">
        <a href="{{ route('candidate.dashboard') }}" class="button" target="_blank">Track Your Application</a>
    </div>

    <p>Thank you for your patience and interest in {{ config('recruitment.seo.company_name') }}.</p>
    <p>Best regards,<br>The Recruitment Team</p>
@endsection
