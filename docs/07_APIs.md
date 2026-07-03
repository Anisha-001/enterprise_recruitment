# API Architecture & Integration Endpoints

This document outlines the API design philosophy and lists the web routes acting as form processors or transaction endpoints.

---

## 1. Architectural Style

The application is built as a **monolithic TALL stack** (Tailwind, Alpine, Livewire, Laravel) architecture. It does not expose public REST/GraphQL APIs for external integrations. 

Instead, communications occur through:
- **Standard HTTP POST/PUT/DELETE forms** with CSRF protection, returning redirects or views.
- **Livewire Asynchronous Requests**: Filament handles database actions (like filtering, searching, bulk editing, or transitioning cards) via AJAX request pipelines routed through Livewire components.
- **Document upload/download streams**: Candidate document uploads and resumes are uploaded securely via POST actions.

---

## 2. Forms & Data Processing Schema

### Job Application Submission
- **Route**: `POST` `/careers/apply/{slug}`
- **Request Type**: `multipart/form-data`
- **Controller**: `Careers\ApplicationController@store`
- **Validation**:
  - `first_name` / `last_name`: required, string, max 100
  - `email`: required, email, max 255
  - `phone`: required, string, max 20
  - `resume`: required, file (pdf, doc, docx), max 10MB
  - `cover_letter` / `photograph`: optional file uploads
  - `screening_answers`: array of screening question responses
  - `education` / `experience` / `skills`: arrays of nested inputs.

### Candidate Set Password
- **Route**: `POST` `/portal/set-password`
- **Request Type**: `application/x-www-form-urlencoded`
- **Controller**: `Portal\AuthController@setPassword`
- **Validation**:
  - `email`: required, email, exists in candidates
  - `password`: required, confirmed, min 8 characters

### Document Upload
- **Route**: `POST` `/portal/documents`
- **Request Type**: `multipart/form-data`
- **Controller**: `Portal\DocumentController@store`
- **Validation**:
  - `name`: required, string, max 255
  - `document`: required, file, max 10MB
- **Response**: Redirect back to `/portal/documents` with success alert.

### Digital Signature of Offer Letter
- **Route**: `POST` `/portal/applications/{application}/offer/accept`
- **Request Type**: `application/x-www-form-urlencoded`
- **Controller**: `Portal\ApplicationController@acceptOffer`
- **Validation**:
  - `digital_signature`: required, string, max 255 (Candidate's typed name/signature)
- **Database Interaction**:
  - Updates the active `Offer` status to `accepted`.
  - Saves the `digital_signature`, `signed_at` timestamp, and candidate's `signed_ip` (captured from client header).
  - Transitions the corresponding `Application` status to `offer_accepted` via `ApplicationService`.
