# Services Reference Guide

This document details the Service Layer, explaining the purpose, function lists, business logics, and dependency maps of every service in the system.

---

## 1. `ApplicationService` (`App\Services\Application\ApplicationService`)
* **Purpose**: Coordinates all applicant activities, duplicate reviews, pipeline flows, and history logs.
* **Key Functions**:
  - `submitApplication(array $data, JobPosting $job, array $files)`: Starts candidate profile generation, checks for duplicate records, attaches CV assets, builds dynamic entries, and dispatches creation events.
  - `transitionStatus(Application $application, string $newStatus, ...)`: Modifies application statuses, updates timestamps, logs histories, dispatches transitions, and triggers alerts.
  - `assignRecruiter(Application $app, int $recruiterId)`: Links recruiters and logs system tasks.
* **Dependencies**: `NotificationService`, `ApplicationValidationService`, `ApplicationMetricsService`.

---

## 2. `ApplicationMetricsService` (`App\Services\Application\ApplicationMetricsService`)
* **Purpose**: Computes performance and metrics for dashboards and reports.
* **Key Functions**:
  - `getStats()`: Computes total counts (new, screening, shortlist, hired, rejected, etc.).
  - `getConversionMetrics()`: Computes interview and offer conversions, and averages time-to-hire in days.
  - `getSourceMetrics()`: Groups candidates by sourcing channel.
* **Dependencies**: Uses the `DB` query builder directly for efficiency.

---

## 3. `ApplicationValidationService` (`App\Services\Application\ApplicationValidationService`)
* **Purpose**: Custom check layers ensuring candidate input consistency.
* **Key Functions**:
  - `validateSubmission(array $data, JobPosting $jobPosting)`: Validates phone/email formatting, file types, and screening answers.

---

## 4. `InterviewService` (`App\Services\Interview\InterviewService`)
* **Purpose**: Manages interview setups and schedules.
* **Key Functions**:
  - Schedules interview rounds, assigns interviewer arrays, handles video platforms (Zoom, Meet, Teams), and distributes reminders/cancellations.

---

## 5. `OfferService` (`App\Services\Offer\OfferService`)
* **Purpose**: Manages compensation configurations, drafting, generation, and signing of contracts.
* **Key Functions**:
  - `createDraft(Application $app, array $data, int $createdBy)`: Saves compensation, calculates total CTC, and drafts offer letters.
  - `sendOffer(Offer $offer, int $sentBy)`: Generates offer letter PDF via DOMPDF, saves in storage, transitions pipeline states, and sends details.
  - `acceptOffer(Offer $offer, ?string $ip)`: Digitally signs and accepts.
  - `rejectOffer(Offer $offer, ?string $reason)`: Records rejection status and reasons.
* **Dependencies**: `NotificationService`, Barryvdh DOMPDF.

---

## 6. `ApplicationTrackingService` (`App\Services\Portal\ApplicationTrackingService`)
* **Purpose**: Generates interactive visual pipeline indicators (stepper steps) rendered in the candidate portal.
