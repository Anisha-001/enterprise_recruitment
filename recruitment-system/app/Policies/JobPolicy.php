<?php

namespace App\Policies;

use App\Models\JobPosting;
use App\Models\User;

class JobPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'hr_admin', 'recruiter', 'hiring_manager']);
    }

    public function view(User $user, JobPosting $jobPosting): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'hr_admin', 'recruiter']);
    }

    public function update(User $user, JobPosting $jobPosting): bool
    {
        if ($user->hasRole('super_admin') || $user->hasRole('hr_admin')) return true;
        if ($user->hasRole('recruiter') && $jobPosting->recruiter_id === $user->id) return true;
        if ($user->hasRole('hiring_manager') && $jobPosting->hiring_manager_id === $user->id) return true;
        return false;
    }

    public function delete(User $user, JobPosting $jobPosting): bool
    {
        return $user->hasRole('super_admin') || $user->hasRole('hr_admin');
    }

    public function publish(User $user, JobPosting $jobPosting): bool
    {
        return $this->update($user, $jobPosting);
    }

    public function clone(User $user, JobPosting $jobPosting): bool
    {
        return $this->create($user);
    }

    public function close(User $user, JobPosting $jobPosting): bool
    {
        return $this->update($user, $jobPosting);
    }

    public function reopen(User $user, JobPosting $jobPosting): bool
    {
        return $this->update($user, $jobPosting);
    }
}
