@extends('emails.layout')

@section('title', 'Offer Accepted Confirmation')

@section('content')
    <h1>Thank You for Accepting Our Offer!</h1>
    <p>Dear {{ $application->candidate->full_name }},</p>
    <p>We are absolutely thrilled to receive your formal acceptance of the offer for the <strong>{{ $application->jobPosting->title }}</strong> position at {{ config('recruitment.seo.company_name') }}.</p>
    <p>Welcome to our team! We are excited to have you join us and look forward to the amazing things we will accomplish together.</p>
    
    <div class="details-box">
        <table>
            <tr>
                <td class="label">Position:</td>
                <td class="value">{{ $application->latestOffer()->proposed_designation ?? $application->jobPosting->title }}</td>
            </tr>
            <tr>
                <td class="label">Confirmed Joining Date:</td>
                <td class="value">{{ $application->latestOffer()->joining_date ? $application->latestOffer()->joining_date->format('F d, Y') : ($application->offered_joining_date ? $application->offered_joining_date->format('F d, Y') : 'TBD') }}</td>
            </tr>
        </table>
    </div>

    <p>Our HR Onboarding team will be in touch with you shortly to guide you through the next steps, including background verification and submitting onboarding documentation.</p>
    <p>You can upload your identity proof, qualification certificates, and other required documents directly in the Candidate Portal.</p>

    <div class="button-wrapper">
        <a href="{{ route('candidate.dashboard') }}" class="button" target="_blank">Upload Onboarding Documents</a>
    </div>

    <p>Congratulations once again! We cannot wait to see you on your first day.</p>
    <p>Best regards,<br>The Onboarding Team<br>{{ config('recruitment.seo.company_name') }}</p>
@endsection
