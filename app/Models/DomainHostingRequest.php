<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'customer_id',
    'assigned_to',
    'domain_name',
    'service_type',
    'plan',
    'status',
    'requested_start_date',
    'renewal_date',
    'quoted_amount',
    'requirements',
    'internal_notes',
])]
class DomainHostingRequest extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'requested_start_date' => 'date',
            'renewal_date' => 'date',
            'quoted_amount' => 'decimal:2',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
