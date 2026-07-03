<?php

namespace App\Services\Portal;

use App\Models\Application;

class ApplicationTrackingService
{
    /**
     * Build the stepper steps with dates and candidate-visible comments.
     */
    public function getStepperSteps(Application $application): array
    {
        // Load status history grouped by target status
        $history = $application->statusHistory->groupBy('to_status');

        // Sequential stages
        $stages = [
            'submitted' => [
                'label' => 'Applied',
                'statuses' => ['new', 'screening', 'shortlisted', 'technical_interview', 'manager_interview', 'final_interview', 'offer_pending', 'offer_sent', 'offer_accepted', 'offer_rejected', 'hired', 'rejected', 'on_hold'],
                'icon' => 'heroicon-o-document-text',
            ],
            'screening' => [
                'label' => 'Screening',
                'statuses' => ['screening', 'shortlisted', 'technical_interview', 'manager_interview', 'final_interview', 'offer_pending', 'offer_sent', 'offer_accepted', 'offer_rejected', 'hired', 'rejected', 'on_hold'],
                'icon' => 'heroicon-o-magnifying-glass',
            ],
            'shortlisted' => [
                'label' => 'Shortlisted',
                'statuses' => ['shortlisted', 'technical_interview', 'manager_interview', 'final_interview', 'offer_pending', 'offer_sent', 'offer_accepted', 'offer_rejected', 'hired', 'rejected', 'on_hold'],
                'icon' => 'heroicon-o-check-circle',
            ],
            'interview' => [
                'label' => 'Interviews',
                'statuses' => ['technical_interview', 'manager_interview', 'final_interview', 'offer_pending', 'offer_sent', 'offer_accepted', 'offer_rejected', 'hired', 'rejected'],
                'icon' => 'heroicon-o-users',
            ],
            'offer' => [
                'label' => 'Offer',
                'statuses' => ['offer_pending', 'offer_sent', 'offer_accepted', 'offer_rejected', 'hired'],
                'icon' => 'heroicon-o-envelope',
            ],
            'terminal' => [
                'label' => 'Hired',
                'statuses' => ['hired'],
                'icon' => 'heroicon-o-briefcase',
            ]
        ];

        // Adjust terminal step based on final application status
        if (in_array($application->status, ['rejected', 'offer_rejected'])) {
            $stages['terminal']['label'] = 'Rejected';
            $stages['terminal']['statuses'] = ['rejected', 'offer_rejected'];
            $stages['terminal']['icon'] = 'heroicon-o-x-circle';
        } elseif ($application->status === 'withdrawn') {
            $stages['terminal']['label'] = 'Withdrawn';
            $stages['terminal']['statuses'] = ['withdrawn'];
            $stages['terminal']['icon'] = 'heroicon-o-x-mark';
        }

        $steps = [];
        $currentStatus = $application->status;
        $activeFound = false;

        foreach ($stages as $key => $stageInfo) {
            $reached = false;
            $date = null;
            $comment = null;

            // Check if this stage has been reached
            foreach ($stageInfo['statuses'] as $st) {
                if (isset($history[$st])) {
                    $reached = true;
                    // Fetch the earliest timestamp it entered this stage status
                    $earliest = $history[$st]->sortBy('created_at')->first();
                    $date = $earliest->created_at;
                    $comment = $earliest->notes;
                    break;
                }
            }

            // Fallback for the first stage
            if ($key === 'submitted' && !$date) {
                $reached = true;
                $date = $application->created_at;
            }

            // Determine if this is the active current stage
            $isCurrent = false;
            if (!$activeFound) {
                if ($key === 'interview' && in_array($currentStatus, ['technical_interview', 'manager_interview', 'final_interview'])) {
                    $isCurrent = true;
                    $activeFound = true;
                } elseif ($key === 'offer' && in_array($currentStatus, ['offer_pending', 'offer_sent', 'offer_accepted', 'offer_rejected'])) {
                    $isCurrent = true;
                    $activeFound = true;
                } elseif ($currentStatus === 'new' && $key === 'submitted') {
                    $isCurrent = true;
                    $activeFound = true;
                } elseif ($currentStatus === $key) {
                    $isCurrent = true;
                    $activeFound = true;
                }
            }

            // Map each step for rendering
            $steps[] = [
                'key' => $key,
                'label' => $stageInfo['label'],
                'reached' => $reached,
                'current' => $isCurrent,
                'date' => $date,
                'comment' => $comment,
                'icon' => $stageInfo['icon'],
            ];
        }

        // If no active stage was matched and the application is terminal, the terminal stage is current
        if (!$activeFound && in_array($currentStatus, ['hired', 'rejected', 'offer_rejected', 'withdrawn'])) {
            $steps[count($steps) - 1]['current'] = true;
        }

        return $steps;
    }
}
