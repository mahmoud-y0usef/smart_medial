<?php

namespace App\Traits;

use App\Enums\UserRole;

trait HasRoles
{
    /**
     * Check if user has admin role
     */
    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    /**
     * Check if user has doctor role
     */
    public function isDoctor(): bool
    {
        return $this->role === UserRole::Doctor;
    }

    /**
     * Check if user has pharmacy role
     */
    public function isPharmacy(): bool
    {
        return $this->role === UserRole::Pharmacy;
    }

    /**
     * Check if user has patient role
     */
    public function isPatient(): bool
    {
        return $this->role === UserRole::Patient;
    }

    /**
     * Check if user has any of the given roles
     *
     * @param  list<UserRole>|UserRole  $roles
     */
    public function hasRole(array|UserRole $roles): bool
    {
        if (! is_array($roles)) {
            $roles = [$roles];
        }

        return in_array($this->role, $roles, true);
    }

    /**
     * Check if user has approved clinic
     */
    public function hasApprovedClinic(): bool
    {
        return $this->isDoctor() && $this->clinic?->isApproved();
    }

    /**
     * Check if user has approved pharmacy
     */
    public function hasApprovedPharmacy(): bool
    {
        return $this->isPharmacy() && $this->pharmacy?->isApproved();
    }
}
