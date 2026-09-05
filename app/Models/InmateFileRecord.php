<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InmateFileRecord extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'inmate_intake_registration_id',
        'birth_date',
        'age',
        'mother_name',
        'gender',
        'birth_region',
        'birth_zone',
        'birth_woreda',
        'birth_kebele',
        'residence_region',
        'residence_zone',
        'residence_woreda',
        'residence_kebele',
        'education_level',
        'occupation',
        'ethnicity',
        'nationality',
        'religion',
        'marital_status',
        'height',
        'hair_type',
        'appearance',
        'forehead',
        'nose',
        'eye_color',
        'teeth',
        'lips',
        'ears',
        'distinguishing_mark',
        'emergency_contact_name',
        'emergency_region',
        'emergency_zone',
        'emergency_woreda',
        'emergency_kebele',
        'emergency_phone_landline',
        'emergency_phone_mobile',
        'filled_by_professional_name',
        'signature',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'age' => 'integer',
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
     * @return HasMany<InmateFilePage, $this>
     */
    public function pages(): HasMany
    {
        return $this->hasMany(InmateFilePage::class)->orderBy('page_number');
    }

    /**
     * @param  Builder<InmateFileRecord>  $query
     * @return Builder<InmateFileRecord>
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        $like = '%'.$term.'%';

        return $query->where(function (Builder $builder) use ($like, $term): void {
            $builder
                ->where('mother_name', 'like', $like)
                ->orWhere('education_level', 'like', $like)
                ->orWhere('occupation', 'like', $like)
                ->orWhere('ethnicity', 'like', $like)
                ->orWhere('emergency_contact_name', 'like', $like)
                ->orWhereHas('inmate', fn (Builder $inmateQuery) => $inmateQuery->search($term));
        });
    }
}
