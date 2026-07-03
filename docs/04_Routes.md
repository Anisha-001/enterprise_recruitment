# Route Reference

This document maps all accessible routes within the application, separating the Public Careers site, Candidate Portal, and Administrative dashboard.

---

## 1. Careers (Public Website) Routes

These routes reside under the `/careers` prefix and require no authentication (public access).

| Method | URI | Controller | Action | Purpose | Auth Required |
|:---|:---|:---|:---|:---|:---|
| `GET` | `/` | *Redirect* | *Closure* | Redirects the default home URL to the `/careers` board | No |
| `GET` | `/careers` | *Closure* | *Inline* | Careers Landing Page showing featured jobs & departments | No |
| `GET` | `/careers/jobs` | `Careers\JobController` | `index` | Filterable job listing search results | No |
| `GET` | `/careers/jobs/{slug}` | `Careers\JobController` | `show` | Detailed job posting information and questions | No |
| `GET` | `/careers/apply/{slug}` | `Careers\ApplicationController` | `create` | Job application form (details, resume, education, skills) | No |
| `POST` | `/careers/apply/{slug}` | `Careers\ApplicationController` | `store` | Process and store a candidate's application | No |
| `GET` | `/careers/thank-you/{application}`| `Careers\ApplicationController` | `thankYou` | Post-submission confirmation page | No |
| `GET` | `/careers/about` | *Closure* | *Inline* | Static "About Us" company page | No |
| `GET` | `/careers/culture` | *Closure* | *Inline* | Static "Our Culture" values page | No |
| `GET` | `/careers/benefits` | *Closure* | *Inline* | Static "Company Benefits" details page | No |

---

## 2. Candidate Portal Routes

These routes reside under the `/portal` prefix. Guest middleware blocks logged-in candidates, and Auth middleware protects internal features.

### Public & Authentication Routes (Guest Candidate Guard)
These routes require the `guest:candidate` middleware.

| Method | URI | Controller | Action | Purpose | Auth Required |
|:---|:---|:---|:---|:---|:---|
| `GET` | `/portal/login` | `Portal\AuthController` | `showLogin` | Show candidate login form | Guest only |
| `POST` | `/portal/login` | `Portal\AuthController` | `login` | Attempt to authenticate candidate | Guest only |
| `GET` | `/portal/set-password` | `Portal\AuthController` | `showSetPassword` | First-time setup password form (via Signed URL) | Guest + Signed |
| `POST` | `/portal/set-password` | `Portal\AuthController` | `setPassword` | Store newly set password & activate account | Guest + Signed |
| `GET` | `/portal/forgot-password`| `Portal\AuthController` | `showForgotPassword` | Request reset link form | Guest only |
| `POST` | `/portal/forgot-password`| `Portal\AuthController` | `sendResetLinkEmail` | Send reset link transactional email | Guest only |
| `GET` | `/portal/reset-password/{token}`| `Portal\AuthController` | `showResetPassword` | Show password update form | Guest only |
| `POST` | `/portal/reset-password`| `Portal\AuthController` | `resetPassword` | Change candidate password in DB | Guest only |

### Protected Candidate Routes (Authenticated Candidate Guard)
These routes require the `auth:candidate` middleware.

| Method | URI | Controller | Action | Purpose | Auth Required |
|:---|:---|:---|:---|:---|:---|
| `POST` | `/portal/logout` | `Portal\AuthController` | `logout` | Invalidate candidate session and token | Yes (`candidate`) |
| `GET` | `/portal/dashboard` | `Portal\DashboardController` | `index` | Candidate home console showing status summary | Yes (`candidate`) |
| `GET` | `/portal/applications` | `Portal\ApplicationController`| `index` | View lists of user's active/past applications | Yes (`candidate`) |
| `GET` | `/portal/applications/{application}`| `Portal\ApplicationController`| `show` | View application pipeline and active offer details | Yes (`candidate`) |
| `POST` | `/portal/applications/{application}/offer/accept`| `Portal\ApplicationController`| `acceptOffer` | Digitally sign and accept job offer | Yes (`candidate`) |
| `POST` | `/portal/applications/{application}/offer/reject`| `Portal\ApplicationController`| `rejectOffer` | Record offer rejection notes | Yes (`candidate`) |
| `GET` | `/portal/interviews` | `Portal\InterviewController` | `index` | List upcoming and completed interview stages | Yes (`candidate`) |
| `GET` | `/portal/documents` | `Portal\DocumentController` | `index` | Manage supporting documents list | Yes (`candidate`) |
| `POST` | `/portal/documents` | `Portal\DocumentController` | `store` | Upload support attachments (ID, certificates) | Yes (`candidate`) |
| `DELETE`| `/portal/documents/{document}`| `Portal\DocumentController` | `destroy` | Remove support document attachment | Yes (`candidate`) |
| `GET` | `/portal/profile` | `Portal\ProfileController` | `edit` | View profile update form | Yes (`candidate`) |
| `PUT` | `/portal/profile` | `Portal\ProfileController` | `update` | Update candidate basic and address info | Yes (`candidate`) |
| `PUT` | `/portal/profile/password`| `Portal\ProfileController` | `updatePassword` | Change candidate password from profile | Yes (`candidate`) |

---

## 3. Administrative Panel Routes (Filament Engine)

Filament admin routes are managed under the `/admin` prefix and require the `web` guard (with Filament user validation logic inside `App\Models\User.php` via `canAccessPanel`).

- **Base Landing**: `/admin/login` & `/admin`
- **Candidate Resource**: `/admin/candidates` (list, create, edit)
- **Application Resource**: `/admin/applications` (kanban pipeline, reviews)
- **Interview Resource**: `/admin/interviews` (calendar scheduling, feedback scorecards)
- **Job Resource**: `/admin/jobs` (postings, screening questions)
- **Offer Resource**: `/admin/offers` (compensation breakdown, letter sending)
