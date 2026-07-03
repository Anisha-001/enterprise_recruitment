<?php

return [
    'application' => [
        'prefix' => env('RECRUITMENT_APPLICATION_PREFIX', 'APP'),
        'number_format' => '%s-%s-%06d',
        'max_file_size' => env('RECRUITMENT_MAX_FILE_SIZE', 10240),
        'allowed_extensions' => explode(',', env('RECRUITMENT_ALLOWED_EXTENSIONS', 'pdf,doc,docx')),
        'resume_storage' => env('RECRUITMENT_RESUME_STORAGE', 'resumes'),
        'document_storage' => env('RECRUITMENT_DOCUMENT_STORAGE', 'documents'),
        'auto_archive_days' => 90,
        'duplicate_check_days' => 180,
        'max_applications_per_hour' => env('RATE_LIMIT_APPLICATIONS_PER_HOUR', 5),
    ],

    'interview' => [
        'types' => [
            'hr_screening' => 'HR Screening',
            'technical' => 'Technical Round',
            'manager' => 'Manager Round',
            'cultural' => 'Cultural Fit',
            'final' => 'Final Round',
            'panel' => 'Panel Interview',
        ],
        'modes' => [
            'in_person' => 'In-Person',
            'video_call' => 'Video Call',
            'phone' => 'Phone Call',
        ],
        'video_platforms' => [
            'zoom' => 'Zoom',
            'google_meet' => 'Google Meet',
            'microsoft_teams' => 'Microsoft Teams',
        ],
        'default_duration' => 60,
        'reminder_hours' => [24, 2],
    ],

    'offer' => [
        'validity_days' => 7,
        'currency' => 'USD',
        'pdf_template' => 'offer-letter',
    ],

    'pipeline' => [
        'stages' => [
            'new' => 'New Application',
            'screening' => 'HR Screening',
            'shortlisted' => 'Shortlisted',
            'technical_interview' => 'Technical Interview',
            'manager_interview' => 'Manager Interview',
            'final_interview' => 'Final Interview',
            'offer_pending' => 'Offer Pending',
            'offer_sent' => 'Offer Sent',
            'offer_accepted' => 'Offer Accepted',
            'offer_rejected' => 'Offer Rejected',
            'hired' => 'Hired',
            'rejected' => 'Rejected',
            'withdrawn' => 'Withdrawn',
            'on_hold' => 'On Hold',
        ],
        'transitions' => [
            'new' => ['screening', 'rejected'],
            'screening' => ['shortlisted', 'rejected', 'on_hold'],
            'shortlisted' => ['technical_interview', 'rejected', 'on_hold'],
            'technical_interview' => ['manager_interview', 'rejected', 'on_hold'],
            'manager_interview' => ['final_interview', 'rejected', 'on_hold'],
            'final_interview' => ['offer_pending', 'rejected', 'on_hold'],
            'offer_pending' => ['offer_sent', 'rejected'],
            'offer_sent' => ['offer_accepted', 'offer_rejected', 'rejected'],
            'offer_accepted' => ['hired', 'offer_rejected'],
            'offer_rejected' => ['rejected'],
            'on_hold' => ['screening', 'rejected'],
        ],
    ],

    'features' => [
        'ai_screening' => env('FEATURE_AI_SCREENING', false),
        'video_interview' => env('FEATURE_VIDEO_INTERVIEW', false),
        'calendar_sync' => env('FEATURE_CALENDAR_SYNC', false),
        'recaptcha' => env('RECAPTCHA_SITE_KEY') !== null,
    ],

    'seo' => [
        'company_name' => 'Enterprise Recruitment',
        'meta_title_suffix' => ' | Careers at Enterprise',
        'default_description' => 'Join our world-class team. Explore exciting career opportunities.',
    ],

    'notifications' => [
        'notify_candidate_on_status_change' => env('NOTIFY_CANDIDATE_ON_STATUS_CHANGE', true),
        'show_rejection_reason_to_candidate' => env('SHOW_REJECTION_REASON_TO_CANDIDATE', false),
    ],
];
