<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\User;

class ApplicationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'hr_admin', 'recruiter', 'hiring_manager', 'interviewer']);
    }

    public function view($user, Application $application): bool
    {
        if ($user instanceof \App\Models\Candidate) {
            return (int) $application->candidate_id === (int) $user->id;
        }

        if (!$user instanceof \App\Models\User) {
            return false;
        }

        if ($user->hasRole('super_admin') || $user->hasRole('hr_admin')) return true;
        if ($user->hasRole('recruiter') && $application->recruiter_id === $user->id) return true;
        if ($user->hasRole('hiring_manager') && $application->jobPosting->hiring_manager_id === $user->id) return true;
        if ($user->hasRole('interviewer')) {
            return $application->interviews()
                ->whereHas('interviewers', fn($q) => $q->where('users.id', $user->id))
                ->exists();
        }
        return false;
    }

    public function create(User $user): bool
    {
        return true; // Anyone can create an application through the public portal
    }

    public function update(User $user, Application $application): bool
    {
        if ($user->hasRole('super_admin') || $user->hasRole('hr_admin')) return true;
        if ($user->hasRole('recruiter') && $application->recruiter_id === $user->id) return true;
        if ($user->hasRole('hiring_manager') && $application->jobPosting->hiring_manager_id === $user->id) return true;
        return false;
    }

    public function delete(User $user, Application $application): bool
    {
        return $user->hasRole('super_admin') || $user->hasRole('hr_admin');
    }

    public function manageInterview(User $user, Application $application): bool
    {
        return $this->update($user, $application);
    }

    public function createOffer(User $user, Application $application): bool
    {
        if ($user->hasRole('super_admin') || $user->hasRole('hr_admin')) return true;
        if ($user->hasRole('hiring_manager') && $application->jobPosting->hiring_manager_id === $user->id) return true;
        return false;
    }

    public function addNote(User $user, Application $application): bool
    {
        return $this->view($user, $application);
    }

    public function export(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'hr_admin', 'recruiter']);
    }
}
