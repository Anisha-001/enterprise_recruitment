# Controller Reference Guide

This document details all controllers in the application, summarizing their operational purpose, inputs, outputs, services utilized, models accessed, and Blade views returned.

---

## 1. Careers Website Controllers (`app/Http/Controllers/Careers/`)

### `JobController`
- **Purpose**: Displays the public careers job board and job details.
- **Methods**:
  - `index(Request $request)`: Filter and paginate active jobs.
    - *Inputs*: Query parameters: `search`, `department`, `location`, `type`, `experience`, `arrangement`.
    - *Outputs*: Renders listings.
    - *Models*: `JobPosting`, `Department`, `Location`.
    - *Views*: `careers.jobs.index`
  - `show(string $slug)`: Displays a specific job posting.
    - *Inputs*: Route parameter: `slug`.
    - *Outputs*: Renders job details.
    - *Models*: `JobPosting`.
    - *Views*: `careers.jobs.show`

### `ApplicationController`
- **Purpose**: Processes candidate applications submitted via the Careers forms.
- **Methods**:
  - `create(string $slug)`: Renders application submission form.
    - *Inputs*: Route parameter: `slug`.
    - *Outputs*: HTML form.
    - *Models*: `JobPosting`.
    - *Views*: `careers.applications.create`
  - `store(Request $request, string $slug)`: Handles application storage.
    - *Inputs*: Personal details, files (Resume, photograph, cover letter, identity documents), education list, experience list, skills list, screening answers.
    - *Outputs*: Redirect to thank-you page.
    - *Services*: `ApplicationService`.
    - *Models*: `JobPosting`, `Application`.
    - *Views*: Redirects to `careers.thank-you`
  - `thankYou(string $applicationNumber)`: Renders the submission success page.
    - *Inputs*: Route parameter: `applicationNumber`.
    - *Outputs*: Renders success page.
    - *Models*: `Application`.
    - *Views*: `careers.applications.thank-you`

---

## 2. Candidate Portal Controllers (`app/Http/Controllers/Portal/`)

### `AuthController`
- **Purpose**: Candidate registration activation, login, logout, and password resets under the `candidate` guard.
- **Methods**:
  - `showLogin()`, `login(Request $request)`, `logout(Request $request)`
  - `showSetPassword()`, `setPassword()`: Activate candidate profile.
  - `showForgotPassword()`, `sendResetLinkEmail()`, `showResetPassword()`, `resetPassword()`
  - *Models*: `Candidate`.
  - *Views*: `portal.auth.login`, `portal.auth.set-password`, `portal.auth.forgot-password`, `portal.auth.reset-password`.

### `DashboardController`
- **Purpose**: Displays the candidate portal dashboard containing summaries of applications, interviews, pending documents, and active offer letters.
- **Methods**:
  - `index()`: Aggregates dashboard counts.
    - *Models*: `Candidate`, `Application`, `Interview`, `Offer`, `Document`.
    - *Views*: `portal.dashboard`

### `ApplicationController`
- **Purpose**: Candidates can check application pipeline status and respond to offer letters.
- **Methods**:
  - `index()`: Lists candidate's applications.
    - *Views*: `portal.applications.index`
  - `show(Application $application)`: Details application with pipeline timeline.
    - *Views*: `portal.applications.show`
  - `acceptOffer(Request $request, Application $application)`: Accept offer letter.
    - *Services*: `OfferService`.
    - *Views*: Redirect back.
  - `rejectOffer(Request $request, Application $application)`: Reject offer letter.
    - *Services*: `OfferService`.
    - *Views*: Redirect back.

### `InterviewController`
- **Purpose**: Candidates inspect scheduled upcoming rounds and past records.
- **Methods**:
  - `index()`: Fetches past and future interviews.
    - *Views*: `portal.interviews.index`

### `DocumentController`
- **Purpose**: Allows candidates to upload supporting documents (certificates, passports, additional CV versions).
- **Methods**:
  - `index()`, `store(Request $request)`, `destroy(Document $document)`
  - *Models*: `Document`.
  - *Views*: `portal.documents.index`

### `ProfileController`
- **Purpose**: Allows candidates to update contact details and secure passwords.
- **Methods**:
  - `edit()`, `update(Request $request)`, `updatePassword(Request $request)`
  - *Views*: `portal.profile.edit`
