# Business & Recruitment Workflow

This document traces the complete lifecycle of a candidate application in the system, explaining the Frontend, Backend, and Database behavior at every phase.

---

## Stage-by-Stage Workflow

### Stage 1: Candidate Applies
- **Frontend**: Candidate visits `/careers/jobs/{slug}`, clicks **Apply**, fills out details, lists education & work history, responds to screening questions, uploads resume/photograph, and submits.
- **Backend**: `Careers\ApplicationController@store` calls `ApplicationService@submitApplication` inside a database transaction:
  - Invokes `ApplicationValidationService` to check formats.
  - Matches candidate email or phone to check duplicate status.
  - Stores files in private or public storage discs.
  - Inserts profile structures, answers, and creates a unique candidate number (`CAND-YYYY-00000X`).
- **Database**:
  - `candidates`: inserts or updates candidates profile record.
  - `applications`: inserts application row with `status = 'new'`.
  - `candidate_education` / `candidate_experiences` / `candidate_skills` / `candidate_answers` / `documents`: inserts lists of related parameters.

---

### Stage 2: Account Activation
- **Frontend**: Candidate receives email containing a signed password-setup link. Clicking this navigates them to `/portal/set-password?signature=...`. Candidate inputs password.
- **Backend**: `Portal\AuthController@setPassword` validates URL signature:
  - Hashes the password using Laravel's Default hash engine (bcrypt).
  - Updates candidate flags.
  - Authenticates candidate guard session.
- **Database**:
  - `candidates`: updates `password`, sets `password_set_at` and `email_verified_at` to `NOW()`.

---

### Stage 3: Application Screening
- **Frontend**: Recruiter logs in to `/admin` dashboard, views Applications resources, and moves the candidate's card from **New Application** to **HR Screening**.
- **Backend**: Filament triggers `ApplicationService@transitionStatus`:
  - Validates pipeline state transitions (defined in `config/recruitment.php`).
  - Dispatches `ApplicationStatusChanged` event.
  - Invokes `NotificationService` to send a status change email.
- **Database**:
  - `applications`: updates `status` to `'screening'`, `status_changed_at = NOW()`.
  - `application_status_history`: inserts history trace.
  - `application_activities`: logs activity feed entry.

---

### Stage 4: Interview Scheduling
- **Frontend**: Recruiter opens **Interviews** page, clicks **Create Interview**, selects Candidate and Application, enters date/time, chooses mode (e.g. Video Call via Google Meet), and assigns panel interviewers.
- **Backend**: `InterviewService` creates the scheduled stage:
  - Generates meeting link parameters.
  - Dispatches `InterviewScheduled` event.
  - Fires notifications with invitation ICS/ICS files to candidates and panel members.
- **Database**:
  - `interviews`: inserts interview details with `status = 'scheduled'`.
  - `interview_interviewers`: populates pivot mapping for panel members.

---

### Stage 5: Interview Feedback
- **Frontend**: Panel member completes the interview, logs into `/admin`, visits the interview details, and completes the **Interview Feedback** scorecard (rating 1-5, pros/cons, selection choice).
- **Backend**: Filament resource saves scorecard inputs and checks if all panels have submitted reviews. If yes, flags the interview status as `completed`.
- **Database**:
  - `interview_feedbacks`: inserts feedback details and scores.
  - `interviews`: updates `status` to `'completed'`.

---

### Stage 6: Offer Letters
- **Frontend**: Recruiter updates application pipeline to **Offer Pending**, navigates to **Offers**, inputs compensation details (Basic, Housing, Transport, Medical, CTC), selects manager, set expiry date, and clicks **Publish & Send**.
- **Backend**: `OfferService` generates an offer package:
  - Computes allowances and Total CTC.
  - Generates PDF using `barryvdh/laravel-dompdf`.
  - Dispatches `OfferSent` event and mails PDF link to candidate.
- **Database**:
  - `offers`: inserts offer details with `status = 'sent'`.
  - `applications`: transitions `status` to `'offer_sent'`.

---

### Stage 7: Offer Acceptance
- **Frontend**: Candidate logs into `/portal`, clicks **Applications**, views the active offer details, inspects salary numbers, types their name in the signature box, and clicks **Accept Offer**.
- **Backend**: `Portal\ApplicationController@acceptOffer` processes acceptance:
  - Verifies digital signature input.
  - Captures candidate IP address.
  - Transitions application to `'offer_accepted'`.
  - Sends verification email to candidate and hiring team.
- **Database**:
  - `offers`: updates `status` to `'accepted'`, sets `digital_signature`, `signed_ip`, `signed_at = NOW()`.
  - `applications`: updates `status` to `'offer_accepted'`.

---

### Stage 8: Hired
- **Frontend**: Recruiter checks signed paperwork, clicks **Mark as Hired**.
- **Backend**: `ApplicationService` transitions candidate profile:
  - Generates a new Employee account in the `users` table.
  - Mails congratulations letter.
- **Database**:
  - `applications`: updates `status` to `'hired'`, records `actual_joining_date`.
  - `users`: inserts new employee row.
  - `candidates`: updates `converted_to_employee_id` and `converted_at`.
