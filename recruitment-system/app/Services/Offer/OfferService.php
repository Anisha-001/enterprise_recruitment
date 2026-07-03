<?php

namespace App\Services\Offer;

use App\Models\Offer;
use App\Models\Application;
use App\Events\OfferSent;
use App\Events\OfferAccepted;
use App\Events\OfferRejected;
use App\Services\Notification\NotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class OfferService
{
    public function __construct(
        private readonly NotificationService $notificationService,
    ) {}

    public function createDraft(Application $application, array $data, int $createdBy): Offer
    {
        return DB::transaction(function () use ($application, $data, $createdBy) {
            $totalCtc = $this->calculateTotalCtc($data);

            $offer = Offer::create([
                'application_id' => $application->id,
                'candidate_id' => $application->candidate_id,
                'job_posting_id' => $application->job_posting_id,
                'status' => 'draft',
                'proposed_designation' => $data['proposed_designation'],
                'department_id' => $data['department_id'],
                'designation_id' => $data['designation_id'] ?? $application->jobPosting->designation_id,
                'reporting_manager_id' => $data['reporting_manager_id'] ?? null,
                'location_id' => $data['location_id'],
                'basic_salary' => $data['basic_salary'],
                'housing_allowance' => $data['housing_allowance'] ?? 0,
                'transport_allowance' => $data['transport_allowance'] ?? 0,
                'medical_allowance' => $data['medical_allowance'] ?? 0,
                'other_allowances' => $data['other_allowances'] ?? 0,
                'bonus_percentage' => $data['bonus_percentage'] ?? null,
                'total_ctc' => $totalCtc,
                'salary_currency' => $data['salary_currency'] ?? 'USD',
                'salary_period' => $data['salary_period'] ?? 'yearly',
                'joining_date' => $data['joining_date'],
                'offer_expiry_date' => $data['offer_expiry_date'] ?? now()->addDays(config('recruitment.offer.validity_days', 7)),
                'proposed_joining_date' => $data['proposed_joining_date'] ?? $data['joining_date'],
                'special_conditions' => $data['special_conditions'] ?? null,
                'notes' => $data['notes'] ?? null,
                'created_by' => $createdBy,
            ]);

            Log::info('Offer draft created', ['offer_id' => $offer->id]);

            return $offer;
        });
    }

    public function sendOffer(Offer $offer, int $sentBy): Offer
    {
        return DB::transaction(function () use ($offer, $sentBy) {
            if ($offer->status !== 'draft' && $offer->status !== 'negotiating') {
                throw new \InvalidArgumentException('Only draft or negotiating offers can be sent.');
            }

            $pdfPath = $this->generateOfferPdf($offer);

            $offer->update([
                'status' => 'sent',
                'pdf_path' => $pdfPath,
                'sent_at' => now(),
                'sent_by' => $sentBy,
            ]);

            $offer->application->update([
                'status' => 'offer_sent',
                'offered_salary' => $offer->total_ctc,
                'offered_joining_date' => $offer->joining_date,
            ]);

            OfferSent::dispatch($offer);
            $this->notificationService->sendOfferLetter($offer);

            Log::info('Offer sent', ['offer_id' => $offer->id]);

            return $offer->fresh();
        });
    }

    public function acceptOffer(Offer $offer, ?string $ipAddress = null): Offer
    {
        return DB::transaction(function () use ($offer, $ipAddress) {
            if ($offer->status !== 'sent') {
                throw new \InvalidArgumentException('Only sent offers can be accepted.');
            }

            $offer->update([
                'status' => 'accepted',
                'responded_at' => now(),
                'signed_ip' => $ipAddress,
            ]);

            $offer->application->update([
                'status' => 'offer_accepted',
                'actual_joining_date' => $offer->joining_date,
            ]);

            OfferAccepted::dispatch($offer);
            $this->notificationService->sendOfferAcceptedNotification($offer);

            Log::info('Offer accepted', ['offer_id' => $offer->id]);

            return $offer->fresh();
        });
    }

    public function rejectOffer(Offer $offer, ?string $reason = null): Offer
    {
        return DB::transaction(function () use ($offer, $reason) {
            if ($offer->status !== 'sent' && $offer->status !== 'negotiating') {
                throw new \InvalidArgumentException('Only sent or negotiating offers can be rejected.');
            }

            $offer->update([
                'status' => 'rejected',
                'rejection_reason' => $reason,
                'responded_at' => now(),
            ]);

            $offer->application->update([
                'status' => 'offer_rejected',
                'rejection_notes' => $reason,
            ]);

            OfferRejected::dispatch($offer, $reason);
            $this->notificationService->sendOfferRejectedNotification($offer, $reason);

            Log::info('Offer rejected', ['offer_id' => $offer->id, 'reason' => $reason]);

            return $offer->fresh();
        });
    }

    public function negotiateOffer(Offer $offer, array $changes, int $userId): Offer
    {
        return DB::transaction(function () use ($offer, $changes, $userId) {
            $newOffer = $offer->createNewVersion($changes);

            $offer->update(['status' => 'withdrawn']);
            $newOffer->update(['status' => 'negotiating']);

            Log::info('Offer negotiation started', [
                'old_offer_id' => $offer->id,
                'new_offer_id' => $newOffer->id,
            ]);

            return $newOffer;
        });
    }

    public function withdrawOffer(Offer $offer, int $withdrawnBy): Offer
    {
        $offer->update([
            'status' => 'withdrawn',
            'updated_by' => $withdrawnBy,
        ]);

        Log::info('Offer withdrawn', ['offer_id' => $offer->id]);

        return $offer->fresh();
    }

    public function generateOfferPdf(Offer $offer): string
    {
        $offer->load(['candidate', 'jobPosting', 'department', 'designation', 'location', 'reportingManager']);

        $pdf = Pdf::loadView('pdf.offer-letter', ['offer' => $offer]);
        $pdf->setPaper('A4');

        $filename = "offer-letter-{$offer->offer_number}.pdf";
        $path = "offers/{$offer->application_id}/{$filename}";

        Storage::disk('private')->put($path, $pdf->output());

        return $path;
    }

    private function calculateTotalCtc(array $data): float
    {
        $basic = (float) ($data['basic_salary'] ?? 0);
        $housing = (float) ($data['housing_allowance'] ?? 0);
        $transport = (float) ($data['transport_allowance'] ?? 0);
        $medical = (float) ($data['medical_allowance'] ?? 0);
        $other = (float) ($data['other_allowances'] ?? 0);

        return $basic + $housing + $transport + $medical + $other;
    }
}
