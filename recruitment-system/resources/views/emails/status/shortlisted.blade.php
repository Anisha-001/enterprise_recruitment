@extends('emails.layout')

@section('title', 'Application Update: Shortlisted!')

@section('content')
    <h1>Congratulations! You've been Shortlisted</h1>
    <p>Dear {{ $application->candidate->full_name }},</p>
    <p>We have exciting news! Your application for the <strong>{{ $application->jobPosting->title }}</strong> position has been reviewed by our hiring team, and you have been <strong>shortlisted</strong> for the next round.</p>
    <p>We are impressed by your profile and believe your background aligns well with what we are looking for. The next step is scheduling your interview round.</p>
    <p>One of our recruiters will reach out to you shortly to coordinate interview timings, or you will receive an interview invitation directly via the Candidate Portal.</p>

    <div class="button-wrapper">
        <a href="{{ route('candidate.dashboard') }}" class="button" target="_blank">Access Candidate Portal</a>
    </div>

    <p>Thank you again for your time, and we look forward to speaking with you soon!</p>
    <p>Best regards,<br>The Recruitment Team</p>
@endsection
