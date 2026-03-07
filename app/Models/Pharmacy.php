<?php

namespace App\Models;

use App\Enums\ApprovalStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Pharmacy extends Model implements HasMedia
{
    use HasFactory;
    use SoftDeletes;
    use InteractsWithMedia;

    protected $casts = [
        'approval_status' => ApprovalStatus::class,
        'working_hours' => 'array',
        'delivery_available' => 'boolean',
        'is_active' => 'boolean',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'delivery_fee' => 'decimal:2',
        'approved_at' => 'datetime',
        'is_24_hours' => 'boolean',
        'accepts_insurance' => 'boolean',
    ];

    /**
     * Get the user that owns the pharmacy
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the medicines for the pharmacy
     */
    public function medicines(): BelongsToMany
    {
        return $this->belongsToMany(Medicine::class, 'pharmacy_inventories')
            ->withPivot(['stock_quantity', 'price', 'is_available', 'expiry_date'])
            ->withTimestamps();
    }

    /**
     * Get the prescriptions for the pharmacy
     */
    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }

    /**
     * Check if pharmacy is approved
     */
    public function isApproved(): bool
    {
        return $this->approval_status === ApprovalStatus::Approved;
    }

    /**
     * Check if pharmacy is pending approval
     */
    public function isPending(): bool
    {
        return $this->approval_status === ApprovalStatus::Pending;
    }

    /**
     * Approve the pharmacy
     */
    public function approve(): void
    {
        $this->update([
            'approval_status' => ApprovalStatus::Approved,
            'approved_at' => now(),
        ]);
    }

    /**
     * Reject the pharmacy
     */
    public function reject(string $reason): void
    {
        $this->update([
            'approval_status' => ApprovalStatus::Rejected,
            'rejection_reason' => $reason,
        ]);
    }
}
