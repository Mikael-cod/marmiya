<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InmateExpenseRegistration extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'inmate_intake_registration_id',
        'certificate_date',
        'certificate_number',
        'full_name',
        'gender',
        'age',
        'religion',
        'nationality',
        'country_of_birth',
        'admission_date',
        'sentencing_court',
        'sentence_duration',
        'crime_type',
        'court_file_number',
        'institution_id_number',
        'education_skill_before',
        'previous_profession',
        'education_period_provided',
        'work_training_provided',
        'work_experience_during',
        'release_reason',
        'release_date',
        'health_condition',
        'official_name',
        'signature',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'certificate_date' => 'date',
            'admission_date' => 'date',
            'release_date' => 'date',
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
     * @param  Builder<InmateExpenseRegistration>  $query
     * @return Builder<InmateExpenseRegistration>
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        $like = '%'.$term.'%';

        return $query->where(function (Builder $builder) use ($like): void {
            $builder
                ->where('full_name', 'like', $like)
                ->orWhere('certificate_number', 'like', $like)
                ->orWhere('court_file_number', 'like', $like)
                ->orWhere('crime_type', 'like', $like)
                ->orWhere('institution_id_number', 'like', $like)
                ->orWhere('release_reason', 'like', $like)
                ->orWhereHas('inmate', fn (Builder $inmateQuery) => $inmateQuery->search($term));
        });
    }
}
