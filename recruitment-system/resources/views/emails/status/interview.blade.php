@extends('emails.layout')

@section('title', 'Application Update: Interview Round')

@section('content')
    <h1>Application Update: Interview Stage</h1>
    <p>Dear {{ $application->candidate->full_name }},</p>
    <p>Your application for the <strong>{{ $application->jobPosting->title }}</strong> position has progressed to the <strong>Interview</strong> stage.</p>

    @if($interview)
        <p>An interview has been scheduled for you. Below are the details:</p>
        
        <div class="details-box">
            <table>
                <tr>
                    <td class="label">Round Name:</td>
                    <td class="value">{{ $interview->display_type }}</td>
                </tr>
                <tr>
                    <td class="label">Date:</td>
                    <td class="value">{{ $interview->scheduled_date->format('F d, Y') }}</td>
                </tr>
                <tr>
                    <td class="label">Time:</td>
                    <td class="value">{{ $interview->formatted_time }}</td>
                </tr>
                <tr>
                    <td class="label">Duration:</td>
                    <td class="value">{{ $interview->duration_minutes }} minutes</td>
                </tr>
                <tr>
                    <td class="label">Interview Mode:</td>
                    <td class="value">{{ $interview->display_mode }}</td>
                </tr>
                @if($interview->mode === 'video_call')
                    <tr>
                        <td class="label">Platform:</td>
                        <td class="value">{{ ucfirst($interview->video_platform) }}</td>
                    </tr>
                    @if($interview->meeting_link)
                        <tr>
                            <td class="label">Meeting Link:</td>
                            <td class="value"><a href="{{ $interview->meeting_link }}" target="_blank">{{ $interview->meeting_link }}</a></td>
                        </tr>
                    @endif
                @elseif($interview->mode === 'in_person' && $interview->interview_address)
                    <tr>
                        <td class="label">Location:</td>
                        <td class="value">{{ $interview->interview_address }}</td>
                    </tr>
                @endif
            </table>
        </div>
        
        @if($interview->instructions)
            <p><strong>Instructions for the Candidate:</strong></p>
            <blockquote style="margin: 15px 0; padding: 10px 15px; background: #f8fafc; border-left: 4px solid #0f766e; color: #475569; font-size: 14px;">
                {!! nl2br(e($interview->instructions)) !!}
            </blockquote>
        @endif
    @else
        <p>We are currently coordinating our hiring team to schedule your interview. We will reach out to you shortly with confirmed slot options.</p>
    @endif

    <div class="button-wrapper">
        <a href="{{ route('candidate.dashboard') }}" class="button" target="_blank">Track Portal & Update Profile</a>
    </div>

    <p>If you have any questions or need to reschedule, please contact your recruiter or reply directly to this email.</p>
    <p>Best regards,<br>The Recruitment Team</p>
@endsection
