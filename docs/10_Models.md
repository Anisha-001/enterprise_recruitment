# Models & Relationships Reference

This document maps all Eloquent Models in the application, highlighting their fields, relations, query scopes, behaviors, and events.

---

## 1. Candidate Model (`App\Models\Candidate`)
Represents an applicant's central profile and credentials, doubling as the Authenticatable identity for the Candidate Portal.

- **Traits**: `HasFactory`, `SoftDeletes`, `LogsActivity`, `Notifiable`.
- **Primary Relations**:
  - `applications()`: `HasMany` Applications submitted.
  - `education()`: `HasMany` CandidateEducation rows.
  - `experiences()`: `HasMany` CandidateExperience rows.
  - `skills()`: `HasMany` CandidateSkill rows.
  - `interviews()`: `HasMany` Interview meetings scheduled.
  - `documents()`: Polymorphic `MorphMany` Documents.
  - `talentPools()`: `BelongsToMany` TalentPools.
- **Scopes**:
  - `scopeActive($query)`: Exclude blacklisted candidates.
  - `scopeSearch($query, $term)`: Fuzzy match name, email, phone, company, and designation.
- **Events & Hooks**:
  - `creating`: Generates a formatted applicant ID: `CAND-YYYY-XXXXXX`.

---

## 2. Application Model (`App\Models\Application`)
Represents the intersection of a candidate and a specific Job Posting, serving as the core entity tracked in the recruitment pipelines.

- **Traits**: `HasFactory`, `SoftDeletes`, `LogsActivity`.
- **Primary Relations**:
  - `candidate()`: `BelongsTo` Candidate.
  - `jobPosting()`: `BelongsTo` JobPosting.
  - `recruiter()`: `BelongsTo` User (Assigned Recruiter).
  - `interviews()`: `HasMany` Interview meetings.
  - `offers()`: `HasMany` Offer Letters.
  - `documents()`: Morphic `MorphMany` Documents.
  - `statusHistory()`: `HasMany` ApplicationStatusHistory logs.
  - `answers()`: `HasMany` CandidateAnswer.
- **Scopes**:
  - `scopePublished($query)`: Filter applications.
- **Events & Hooks**:
  - `creating`: Formulates a unique application reference number (`APP-YYYY-XXXXXX`).
  - `updating`: Automatically updates `status_changed_at` whenever the pipeline `status` attribute is modified.

---

## 3. JobPosting Model (`App\Models\JobPosting`)
Defines a job vacancy opening requisitioned by hiring teams.

- **Traits**: `HasFactory`, `SoftDeletes`, `LogsActivity`.
- **Relations**:
  - `department()`: `BelongsTo` Department.
  - `designation()`: `BelongsTo` Designation.
  - `location()`: `BelongsTo` Location.
  - `recruiter()`: `BelongsTo` User.
  - `hiringManager()`: `BelongsTo` User.
  - `screeningQuestions()`: `HasMany` ScreeningQuestion.
  - `applications()`: `HasMany` Application.
- **Scopes**:
  - `scopePublished($query)`: Checks status is published and date is valid.
  - `scopeFeatured($query)`: Returns featured openings.

---

## 4. Interview Model (`App\Models\Interview`)
Represents scheduled assessments (HR Screening, Tech round, Manager round).

- **Relations**:
  - `application()`: `BelongsTo` Application.
  - `candidate()`: `BelongsTo` Candidate.
  - `jobPosting()`: `BelongsTo` JobPosting.
  - `interviewers()`: `BelongsToMany` Users.
  - `feedbacks()`: `HasMany` InterviewFeedback.

---

## 5. Offer Model (`App\Models\Offer`)
Documents compensation packages and digital agreements generated for final candidate selection.

- **Relations**:
  - `application()`: `BelongsTo` Application.
  - `candidate()`: `BelongsTo` Candidate.
  - `jobPosting()`: `BelongsTo` JobPosting.
  - `department()`: `BelongsTo` Department.
  - `designation()`: `BelongsTo` Designation.
  - `reportingManager()`: `BelongsTo` User.
  - `location()`: `BelongsTo` Location.
- **Events & Hooks**:
  - `creating`: Formulates reference format: `OFF-YYYY-XXXXXX`.
