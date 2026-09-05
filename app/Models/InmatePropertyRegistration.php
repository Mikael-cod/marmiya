<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InmatePropertyRegistration extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'inmate_intake_registration_id',
        'entry_cash_amount',
        'form_85_number',
        'deposit_amount',
        'form_86_number',
        'withdrawal_amount',
        'other_property_receipt_number',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'entry_cash_amount' => 'decimal:2',
            'deposit_amount' => 'decimal:2',
            'withdrawal_amount' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function inmate(): BelongsTo
    {
        return $this->belongsTo(InmateIntakeRegistration::class, 'inmate_intake_registration_id');
    }

    /**
     * @param  Builder<InmatePropertyRegistration>  $query
     * @return Builder<InmatePropertyRegistration>
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        $like = '%'.$term.'%';

        return $query->where(function (Builder $builder) use ($like, $term): void {
            $builder
                ->where('form_85_number', 'like', $like)
                ->orWhere('form_86_number', 'like', $like)
                ->orWhere('other_property_receipt_number', 'like', $like)
                ->orWhereHas('inmate', fn (Builder $inmateQuery) => $inmateQuery->search($term));
        });
    }
}
