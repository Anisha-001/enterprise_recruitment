<?php

namespace App\Policies;

use App\Models\Candidate;
use App\Models\Application;
use App\Models\Interview;
use App\Models\Document;
use App\Models\Offer;

class CandidatePolicy
{
    /**
     * Determine if the candidate can view the application.
     */
    public function viewApplication(Candidate $candidate, Application $application): bool
    {
        return $application->candidate_id === $candidate->id;
    }

    /**
     * Determine if the candidate can view the interview.
     */
    public function viewInterview(Candidate $candidate, Interview $interview): bool
    {
        return $interview->candidate_id === $candidate->id;
    }

    /**
     * Determine if the candidate can view/manage the document.
     */
    public function viewDocument(Candidate $candidate, Document $document): bool
    {
        if ($document->documentable_type === Candidate::class) {
            return (int) $document->documentable_id === (int) $candidate->id;
        }

        if ($document->documentable_type === Application::class) {
            $application = $document->documentable;
            return $application && (int) $application->candidate_id === (int) $candidate->id;
        }

        return false;
    }

    /**
     * Determine if the candidate can delete the document.
     */
    public function deleteDocument(Candidate $candidate, Document $document): bool
    {
        // A candidate can only delete a document they uploaded themselves
        return $this->viewDocument($candidate, $document) && (int) $document->uploaded_by === null;
    }

    /**
     * Determine if the candidate can view the offer.
     */
    public function viewOffer(Candidate $candidate, Offer $offer): bool
    {
        return $offer->candidate_id === $candidate->id;
    }
}
