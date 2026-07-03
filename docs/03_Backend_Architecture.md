# Backend Architecture

This document outlines the backend design patterns, services separation, events pipeline, and administration resources of the recruitment application.

---

## 1. MVC & Layered Architecture

The application follows the classic MVC pattern enhanced by a dedicated **Service Layer** to isolate business transactions from controllers and admin frameworks.

```
                  ┌───────────────────────┐
                  │   HTTP Router / Web   │
                  └───────────┬───────────┘
                              │
                  ┌───────────▼───────────┐
                  │      Controllers      │
                  └───────────┬───────────┘
                              │ Calls
                  ┌───────────▼───────────┐
                  │    Service Layer      │◄───── Filament Resources
                  └───────────┬───────────┘      (Admin Console)
                              │ Writes
             ┌────────────────┼────────────────┐
             │                │                │
     ┌───────▼───────┐┌───────▼───────┐┌───────▼───────┐
     │  Models (DB)  ││    Events     ││ Notifications │
     └───────────────┘└───────────────┘└───────────────┘
```

---

## 2. Component Explanations

### Controllers (`app/Http/Controllers/`)
Controllers act purely as HTTP coordinators. They capture inputs, validate parameters, invoke corresponding Services, and returning redirects (with session alerts) or rendering Blade templates.
- `app/Http/Controllers/Careers/`: Careers page jobs board and applicant creations.
- `app/Http/Controllers/Portal/`: Candidate portal dashboards, profile edits, documents and interview lists, and custom auth routes.

### Services Layer (`app/Services/`)
Services house the transaction scripts. Controllers and Filament Resources invoke them to guarantee consistency across CLI, Web, and Admin actions.
- **`ApplicationService`**: Validates applications, manages duplicate detection, persists resumes and education records, triggers events, and changes pipelines.
- **`InterviewService`**: Handles scheduled dates, calendar reminders, platform setups, and notifications.
- **`OfferService`**: Prepares and records details of compensation terms and executes digital signings.

### Models (`app/Models/`)
Define database entities, relationships, validation constraints, default attributes, and scopes (e.g. `JobPosting::published()`). Models use Spatie traits for audit logging (`LogsActivity`) and tag indexing.

### Middleware
Manages request filtering and authentication gates.
- Default Laravel middleware stack handles session states, CSRF, and cookies.
- Specialized **Auth Guards** (`web` for employees/admins and `candidate` for applicants) are configured in `config/auth.php` and applied via `auth:candidate` and `guest:candidate`.

### Policies (`app/Policies/`)
Determine object-level authorizations, utilized heavily by Filament to restrict resource creation or status updates based on role permissions (e.g., `ApplicationPolicy`, `JobPolicy`).

### Events & Listeners (`app/Events/` and `app/Listeners/`)
Decouples primary actions (e.g. submitting an application) from side-effects (e.g. sending emails or generating portal access codes).
- **`ApplicationSubmitted`** triggers **`SendCandidatePortalInvite`** (which emails candidates a signed URL to set their passwords).
- **`ApplicationStatusChanged`** triggers **`SendApplicationStatusEmail`** to dispatch context-sensitive updates (screening, shortlisted, rejected, offer sent).

---

## 3. Filament Resources (`app/Filament/Resources/`)

Filament resources act as the administrative controller and view layers for the internal team:
1. **`CandidateResource`**: View resumes, check duplicates, update blacklists.
2. **`ApplicationResource`**: Drag-and-drop pipeline progression, recruiter allocation, ratings, notes, and activity history tracking.
3. **`InterviewResource`**: Schedule interview sessions, record video meeting links, and submit scorecard reviews.
4. **`JobResource`**: Author job descriptions, screening questions, and publish status.
5. **`OfferResource`**: Construct compensation packages and generate offer letters.
