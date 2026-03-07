<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PharmacyInventory extends Model
{
    protected $fillable = [
        'pharmacy_id',
        'medicine_id',
        'quantity',
        'reorder_level',
        'unit_price',
        'selling_price',
        'batch_number',
        'expiry_date',
        'supplier',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'expiry_date' => 'date',
        ];
    }

    public function pharmacy(): BelongsTo
    {
        return $this->belongsTo(Pharmacy::class);
    }

    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class);
    }
}
