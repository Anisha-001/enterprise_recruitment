# Enterprise Recruitment & Applicant Tracking System

A production-ready, enterprise-grade Recruitment & Applicant Tracking System (ATS) built with Laravel 12, Filament v3, and modern web technologies. Designed for organizations managing high-volume recruitment with multi-stage pipelines, interview scheduling, offer management, and comprehensive reporting.

## System Architecture

```
recruitment-system/
├── app/
│   ├── Console/Commands/         # Artisan commands
│   ├── Enums/                    # Enumeration classes
│   ├── Events/                   # Application events
│   ├── Exceptions/               # Custom exceptions
│   ├── Filament/
│   │   ├── Pages/                # Dashboard, custom pages
│   │   └── Resources/            # Admin CRUD resources
│   ├── Http/
│   │   ├── Controllers/          # HTTP controllers
│   │   │   ├── Careers/          # Public-facing controllers
│   │   │   └── Admin/            # Admin controllers
│   │   ├── Middleware/           # HTTP middleware
│   │   └── Requests/             # Form request validators
│   ├── Jobs/                     # Queue jobs
│   ├── Listeners/                # Event listeners
│   ├── Mail/                     # Mailable classes
│   ├── Models/                   # Eloquent models
│   ├── Notifications/            # Notification classes
│   ├── Observers/                # Model observers
│   ├── Policies/                 # Authorization policies
│   ├── Providers/                # Service providers
│   ├── Repositories/             # Data repositories
│   ├── Services/                 # Business logic services
│   │   ├── Application/          # Application workflows
│   │   ├── Interview/            # Interview management
│   │   ├── Job/                  # Job posting management
│   │   ├── Notification/         # Notification delivery
│   │   ├── Offer/                # Offer letter management
│   │   └── Report/               # Reporting & analytics
│   ├── Traits/                   # Reusable traits
│   └── View/Components/          # Blade components
├── bootstrap/
├── config/
│   └── recruitment.php           # Module configuration
├── database/
│   ├── factories/                # Model factories
│   ├── migrations/               # Database migrations
│   └── seeders/                  # Database seeders
├── resources/
│   ├── views/                    # Blade templates
│   │   ├── careers/              # Public website
│   │   ├── emails/               # Email templates
│   │   ├── layouts/              # Layout templates
│   │   ├── livewire/             # Livewire components
│   │   └── pdf/                  # PDF templates
│   ├── css/
│   └── js/
├── routes/                       # Application routes
├── storage/
└── tests/                        # Feature & unit tests
```

## Technology Stack

| Component | Technology |
|-----------|------------|
| Framework | Laravel 12.x |
| PHP | 8.3+ |
| Admin Panel | Filament v3 |
| Frontend | Livewire, Alpine.js, Tailwind CSS |
| Database | MySQL 8.0+ |
| Cache | Redis |
| Queues | Redis |
| Search | Laravel Scout (configurable) |
| PDF | DOMPDF |
| Excel | Laravel Excel (Maatwebsite) |
| Auth | Laravel Auth + Spatie Permission |
| Activity Log | Spatie Activity Log |

## Features

### Public Careers Website
- Modern, responsive career portal
- SEO-optimized job listings with clean URLs
- Advanced job search and filtering
- Multi-step application wizard with progress tracking
- Resume and document upload support
- Social sharing integration
- Mobile-first design

### Job Management (Admin)
- Create, edit, clone, archive jobs
- Rich text job descriptions
- Skill requirements management
- SEO metadata (title, description, keywords)
- Featured and urgent job flags
- Publish/unpublish workflow
- Auto-expiry with closing dates

### Candidate Application Portal
- 9-step application wizard
- Personal information, contact details
- Education history (multiple entries)
- Work experience (multiple entries)
- Skills with proficiency levels
- Online profile links
- Document uploads (resume, cover letter, certificates)
- Screening questions per job posting
- Terms acceptance and privacy consent

### Application Pipeline
- Complete recruitment lifecycle:
  - New → Screening → Shortlisted → Technical Interview → Manager Interview → Final Interview → Offer → Hired
- Status transition validation
- Role-based access control
- Bulk actions for recruiters
- Advanced filtering and search

### Interview Management
- Schedule interviews (multiple rounds)
- Support for: HR Screening, Technical, Manager, Cultural, Final, Panel
- Interview modes: In-person, Video Call, Phone
- Video platform integration: Zoom, Google Meet, Teams
- Interviewer assignment with primary/secondary
- Calendar view
- Interview feedback with scoring
- Recommendation tracking (Strong Hire → Strong Reject)

### Offer Letter Management
- Generate professional PDF offer letters
- Compensation breakdown (basic, allowances, bonus)
- Custom terms and conditions
- Digital signature support
- Version history for negotiations
- Expiry tracking
- Acceptance/rejection workflow

### Employee Conversion
- Convert hired candidates to employees
- Automatic data migration
- Preserve all historical records

### Dashboard & Analytics
- KPI widgets (applications, hires, offers)
- Hiring funnel visualization
- Source analytics
- Department-wise hiring metrics
- Time-to-hire reporting
- Monthly trends

### Security
- Role-based access control (RBAC)
- Application-level authorization policies
- Audit logging with Spatie Activity Log
- Soft deletes on all major entities
- Rate limiting on application submissions
- Duplicate application detection
- CSRF protection

## Database Schema

### Core Tables
- `users` - System users (HR staff, recruiters, managers)
- `departments` - Organization departments
- `designations` - Job designations/levels
- `locations` - Office locations
- `skills` - Master skills database

### Recruitment Tables
- `job_postings` - Job requisitions and postings
- `job_skills` - Job-skill requirements (pivot)
- `candidates` - Candidate profiles
- `candidate_education` - Education history
- `candidate_experiences` - Work experience
- `candidate_skills` - Candidate skills with proficiency
- `applications` - Job applications
- `application_activities` - Activity timeline
- `application_status_history` - Status change log
- `screening_questions` - Per-job screening questions
- `candidate_answers` - Screening question responses

### Interview Tables
- `interviews` - Interview schedules
- `interview_interviewers` - Interviewer assignments
- `interview_feedbacks` - Feedback and scoring

### Offer Tables
- `offers` - Offer letters with versioning

### Supporting Tables
- `documents` - File uploads (morphable)
- `internal_notes` - Private recruiter notes
- `email_logs` - Email delivery tracking
- `talent_pools` - Candidate talent pools
- `recruitment_sources` - Source tracking

## Installation

### Prerequisites
- PHP 8.3+
- MySQL 8.0+ or MariaDB 10.6+
- Redis 6.0+
- Composer 2.0+
- Node.js 18+ & NPM

### Step-by-Step Setup

```bash
# 1. Clone the repository
git clone <repository-url> recruitment-system
cd recruitment-system

# 2. Install PHP dependencies
composer install --no-dev --optimize-autoloader

# 3. Install frontend dependencies
npm install && npm run build

# 4. Environment configuration
cp .env.example .env
php artisan key:generate

# 5. Configure database in .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=recruitment_system
# DB_USERNAME=your_username
# DB_PASSWORD=your_password

# 6. Configure Redis in .env
# REDIS_CLIENT=predis
# REDIS_HOST=127.0.0.1

# 7. Run migrations
php artisan migrate

# 8. Seed database
php artisan db:seed

# 9. Create admin user
php artisan make:admin

# 10. Create storage link
php artisan storage:link

# 11. Start queue worker
php artisan queue:work

# 12. Start scheduler
php artisan schedule:work
```

### Filament Admin Access
```
URL: /admin
Default admin credentials created during seeding
```

## User Roles & Permissions

| Role | Capabilities |
|------|-------------|
| Super Admin | Full system access |
| HR Admin | Manage all recruitment operations, settings, reports |
| Recruiter | Manage jobs, applications, interviews, offers |
| Hiring Manager | View candidates, conduct interviews, approve offers |
| Interviewer | View assigned interviews, provide feedback |

## Configuration

### Recruitment Settings (`config/recruitment.php`)
```php
// Application numbering
'application.prefix' => 'APP',

// File upload limits
'max_file_size' => 10240, // KB
'allowed_extensions' => ['pdf', 'doc', 'docx'],

// Duplicate detection (days)
'tuplicate_check_days' => 180,

// Offer validity (days)
'offer.validity_days' => 7,

// Interview defaults
'interview.default_duration' => 60, // minutes
'interview.reminder_hours' => [24, 2],
```

## API Endpoints

### Public Career Portal
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/careers` | Careers homepage |
| GET | `/careers/jobs` | Job listings with filters |
| GET | `/careers/jobs/{slug}` | Job detail page |
| GET | `/careers/apply/{slug}` | Application form |
| POST | `/careers/apply/{slug}` | Submit application |
| GET | `/careers/thank-you/{id}` | Confirmation page |

### Admin Panel
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/admin` | Admin dashboard |
| GET | `/admin/jobs` | Job management |
| GET | `/admin/applications` | Application management |
| GET | `/admin/candidates` | Candidate management |
| GET | `/admin/interviews` | Interview scheduling |
| GET | `/admin/offers` | Offer management |

## Events & Notifications

| Event | Description | Recipients |
|-------|-------------|------------|
| ApplicationSubmitted | New application received | Recruiter, Hiring Manager |
| ApplicationStatusChanged | Pipeline stage change | Candidate, Recruiter |
| InterviewScheduled | Interview scheduled | Candidate, Interviewers |
| InterviewCancelled | Interview cancelled | Candidate, Interviewers |
| InterviewReminder | Upcoming interview | Candidate |
| OfferSent | Offer letter sent | Candidate |
| OfferAccepted | Offer accepted | HR Admin, Recruiter |
| OfferRejected | Offer rejected | HR Admin, Recruiter |

## Testing

```bash
# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage --min=80

# Feature tests
php artisan test --filter=Feature

# Unit tests
php artisan test --filter=Unit
```

## Performance Optimization

- Database indexes on frequently queried columns
- Redis caching for job listings and dashboard data
- Queue-based email delivery
- Lazy loading relationships in Filament tables
- Chunked processing for bulk operations
- Full-text search on job postings

## Security Considerations

- All file uploads validated and stored securely
- Rate limiting on application submissions (5/hour per IP)
- Role-based access control on all admin actions
- CSRF tokens on all forms
- Input sanitization and validation
- Audit logging for all status changes
- Soft deletes prevent data loss
- Encrypted sensitive fields

## License

Proprietary - All Rights Reserved.
