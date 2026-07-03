# Code Flow & Data Tracing

This document traces the exact path of data execution—from the frontend button click, through routing, controllers, services, and models, to the database operations, and back up to the response view—for the system's most important features.

---

## 1. Feature: Candidate Submits Job Application

```
[Candidate Form] ── Click Submit ──> [POST: /careers/apply/{slug}]
                                               │
                                       (ApplicationController)
                                               │
                                      (ApplicationService)
                                               │
                                 ┌─────────────┴─────────────┐
                         [Duplicate Check]             [Attach Media]
                                 │                           │
                            (Candidate)                   (Storage)
                                 ├───────────────────────────┤
                                 ▼                           ▼
                        [DB Transactions] ───────────> [SQL Inserts]
                                 │
                     (Redirect with ID parameter)
                                 │
                                 ▼
                     [Blade: careers.thank-you]
```

### Steps Trace
1. **Button Click**: Candidate clicks **Submit Application** on `/careers/apply/{slug}`.
2. **Route**: `POST` `/careers/apply/{slug}` mapped to `careers.apply.store`.
3. **Controller**: `Careers\ApplicationController@store` validation verifies payloads, extracts files, and calls the logic handler.
4. **Service**: `ApplicationService@submitApplication` executes a DB transaction:
   - Validates formats.
   - Scopes existing email to avoid duplicates.
   - Copies PDF files into private directories.
5. **Model**: Calls `Candidate::create()` (sets number sequence) and `Candidate->applications()->create()`.
6. **Database**: Writes inserts to `candidates`, `applications`, `candidate_skills`, `candidate_experiences`, and `candidate_education`. Dispatches `ApplicationSubmitted` event.
7. **Response**: Redirects candidate to `careers.thank-you` route, carrying application reference parameters.
8. **View**: Renders `careers.applications.thank-you` template with validation counts.

---

## 2. Feature: Candidate Accepts Offer Letter

```
[Portal View] ── Click Accept ──> [POST: /portal/applications/{id}/offer/accept]
                                               │
                                       (ApplicationController)
                                               │
                                         (OfferService)
                                               │
                                      (SQL DB Transaction)
                                               ├─────────────────────────────┐
                                               ▼                             ▼
                                        (Update Offer)              (Update Application)
                                               │                             │
                                        [status=accepted]           [status=offer_accepted]
                                               ├─────────────────────────────┘
                                               ▼
                                     (Dispatch OfferAccepted)
                                               │
                                      (Redirect with Flash)
                                               │
                                               ▼
                                 [Blade: portal.applications.show]
```

### Steps Trace
1. **Button Click**: Candidate types their name in the signature input and clicks **Accept Offer** on `/portal/applications/{application}`.
2. **Route**: `POST` `/portal/applications/{application}/offer/accept` mapped to `candidate.applications.offer.accept`.
3. **Controller**: `Portal\ApplicationController@acceptOffer` performs verification that the logged-in candidate owns this application.
4. **Service**: Invokes `OfferService@acceptOffer($offer, $ipAddress)`:
   - Verifies the offer status is sent.
   - Bundles timestamp, signature string, and IP parameters.
   - Transitions corresponding application stage to `offer_accepted`.
5. **Model**: Triggers update hooks on `Offer` and `Application` models.
6. **Database**: Executes SQL updates on `offers` (`status = 'accepted'`, `signed_at = NOW()`) and `applications` (`status = 'offer_accepted'`).
7. **Response**: Redirects user back to application details page with success toast alert.
8. **View**: Blade renders `portal.applications.show`, showing stepper stage progress as green (accepted) and hides signature actions.
