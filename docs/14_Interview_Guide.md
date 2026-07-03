# Architectural Interview & Developer Guide

This document functions as a technical guide for developers onboarding or interviewing for the Enterprise Recruitment codebase. It details why specific design patterns, folders, and modules were chosen.

---

## 1. Architectural Rationales

### Why the Directory Structure exists?
- **`/app/Services`**: Keeps business processes centralized. Rather than scattering database inserts across different controllers or Filament forms, logic (like duplicate verification) is encapsulated in service files.
- **`/app/Events` & `/app/Listeners`**: Decouples transactions. Triggers like scheduling an interview only write dates in the database; email alerts are dispatched separately via events.
- **`/app/Policies`**: Isolates access validation. Filament resources read these classes to automatically gate view/edit actions based on Spatie roles.

### Why MVC + Services Pattern?
- **MVC (Model-View-Controller)**: Provides a division of labor. Models represent structured databases, Views determine templates, and Controllers handle endpoints.
- **Why introduce Services?**: Standard MVC often leads to bloated controllers. By moving core transactions (such as application submittals or offer approvals) into dedicated Services, controllers remain thin and readable, and the same logic can be reused in Artisan commands, Web portals, or API routes.

### Why Filament PHP?
- Generates fully functional backoffice admin portals using the TALL stack (Tailwind, Alpine, Livewire, Laravel).
- Enables rapid building of CRUD widgets, drag-and-drop kanban boards, calendar widgets, filters, and audit logs.
- Reduces administration dashboard development times from months to days.

### Why Middleware?
- Permits request-level checks before execution. For example, ensuring only verified candidates can review offer details (`auth:candidate`) and preventing logged-in applicants from reaching login pages (`guest:candidate`).

---

## 2. Expected Interview Questions & Exemplary Answers

### Q1: How does Candidate Auth operate separate from Employee logins?
> **Answer**: We configure multiple authentication guards inside `config/auth.php`. The standard web guard authenticates employee users from the `users` table via Eloquent. The specialized `candidate` guard authenticates applicants from the `candidates` table using session drivers. This isolates login states so that logging into the candidate portal does not grant admin dashboard access.

### Q2: What security measures prevent applicants from tampering with portal invite links?
> **Answer**: Account activation relies on Laravel's Signed URLs. The system appends an SHA-256 HMAC signature to activation links (`candidate.set-password`) along with expiration parameters. If a candidate attempts to modify query values (like the target email) or access the link after expiration, signature verification fails and aborts the request.

### Q3: How is candidate duplicate detection handled?
> **Answer**: When an application is submitted, `ApplicationService` checks if a candidate record already exists with the same email or combination of name and phone. If found, it links the new application to the existing candidate instead of creating a duplicate profile, updates their current company details, and flags duplicate application attempts.

### Q4: How does the offer signing capture candidate consent?
> **Answer**: When a candidate accepts an offer through the portal, we capture the typed signature string, the candidate's remote client IP address, and save a UTC timestamp. The offer record transitions to `'accepted'`, and its parent application transitions to `'offer_accepted'`. This provides audit logs for compliance.
