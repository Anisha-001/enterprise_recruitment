@extends('emails.layout')

@section('title', 'Welcome to the Team!')

@section('content')
    <h1>Welcome Aboard! You are Hired</h1>
    <p>Dear {{ $application->candidate->full_name }},</p>
    <p>It is our absolute pleasure to officially welcome you to the <strong>{{ config('recruitment.seo.company_name') }}</strong> team! Your onboarding process is complete, and your status has been updated to <strong>Hired</strong>.</p>
    
    <div class="details-box">
        <table>
            <tr>
                <td class="label">Job Title:</td>
                <td class="value">{{ $application->latestOffer()->proposed_designation ?? $application->jobPosting->title }}</td>
            </tr>
            <tr>
                <td class="label">Start Date:</td>
                <td class="value">{{ $application->actual_joining_date ? $application->actual_joining_date->format('F d, Y') : ($application->offered_joining_date ? $application->offered_joining_date->format('F d, Y') : 'TBD') }}</td>
            </tr>
            @if($application->recruiter)
                <tr>
                    <td class="label">Point of Contact:</td>
                    <td class="value">{{ $application->recruiter->full_name }} ({{ $application->recruiter->email }})</td>
                </tr>
            @endif
        </table>
    </div>

    <p>Your team and reporting manager are excited to welcome you and have been preparing for your arrival. Please keep an eye on your inbox for details about IT setup and day-one orientation schedules.</p>

    <div class="button-wrapper">
        <a href="{{ route('candidate.dashboard') }}" class="button" target="_blank">Access Your Portal Dashboard</a>
    </div>

    <p>We are delighted to have you as part of our company and look forward to a successful journey together!</p>
    <p>Warmest regards,<br>The HR & Operations Team<br>{{ config('recruitment.seo.company_name') }}</p>
@endsection
