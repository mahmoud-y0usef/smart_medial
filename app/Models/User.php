<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser, HasTenants, MustVerifyEmail
{
    use HasApiTokens;

    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use Notifiable;

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'role' => UserRole::class,
    ];

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    /**
     * Check if user is doctor
     */
    public function isDoctor(): bool
    {
        return $this->role === UserRole::Doctor;
    }

    /**
     * Check if user is pharmacy
     */
    public function isPharmacy(): bool
    {
        return $this->role === UserRole::Pharmacy;
    }

    /**
     * Check if user is patient
     */
    public function isPatient(): bool
    {
        return $this->role === UserRole::Patient;
    }

    /**
     * Check if user is receptionist
     */
    public function isReceptionist(): bool
    {
        return $this->role === UserRole::Receptionist;
    }

    /**
     * Get the clinic associated with this user (as owner/doctor)
     */
    public function clinic(): HasOne
    {
        return $this->hasOne(Clinic::class);
    }

    /**
     * Get the clinic this user works for (as receptionist)
     */
    public function clinicEmployer(): BelongsTo
    {
        return $this->belongsTo(Clinic::class, 'clinic_id');
    }

    /**
     * Get the pharmacy associated with this user
     */
    public function pharmacy(): HasOne
    {
        return $this->hasOne(Pharmacy::class);
    }

    /**
     * Get the pharmacist profile associated with this user
     */
    public function pharmacist(): HasOne
    {
        return $this->hasOne(Pharmacist::class);
    }

    /**
     * Get the patient profile associated with this user
     */
    public function patient(): HasOne
    {
        return $this->hasOne(Patient::class);
    }

    /**
     * Get the doctor profile associated with this user
     */
    public function doctor(): HasOne
    {
        return $this->hasOne(Doctor::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // Admins can access admin panel
        if ($panel->getId() === 'admin') {
            return $this->isAdmin();
        }

        // Doctors and Receptionists can access clinic panel
        if ($panel->getId() === 'clinic') {
            return $this->isDoctor() || $this->isReceptionist();
        }

        // Pharmacies can access pharmacy panel
        if ($panel->getId() === 'pharmacy') {
            return $this->isPharmacy();
        }

        // Default app panel for all users (if exists)
        return true;
    }

    public function canAccessTenant(Model $tenant): bool
    {
        return true;
    }

    /** @return Collection<int,Team> */
    public function getTenants(Panel $panel): Collection
    {
        return Team::all();
    }

    /** @return BelongsToMany<Team, $this> */
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class);
    }
}
