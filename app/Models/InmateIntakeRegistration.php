<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
class InmateIntakeRegistration extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'court_file_number',
        'institution_file_number',
        'cell_number',
        'full_name',
        'photo_path',
        'crime_type',
        'detaining_court',
        'admission_date',
        'admission_time',
        'verdict_court',
        'sentence_status',
        'sentence_duration',
        'verdict_date',
        'appeal_court',
        'sentence_start_date',
        'sentence_end_date',
        'parole_release_date',
        'release_reason',
        'full_release_date',
        'mother_inmate_intake_registration_id',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'admission_date' => 'date',
            'verdict_date' => 'date',
            'sentence_start_date' => 'date',
            'sentence_end_date' => 'date',
            'parole_release_date' => 'date',
            'full_release_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function propertyRegistrations(): HasMany
    {
        return $this->hasMany(InmatePropertyRegistration::class);
    }

    public function fileRecord(): HasOne
    {
        return $this->hasOne(InmateFileRecord::class);
    }

    public function expenseRegistration(): HasOne
    {
        return $this->hasOne(InmateExpenseRegistration::class);
    }

    public function motherInmate(): BelongsTo
    {
        return $this->belongsTo(self::class, 'mother_inmate_intake_registration_id');
    }

    /**
     * @return HasMany<InmateIntakeRegistration, $this>
     */
    public function childrenWithMother(): HasMany
    {
        return $this->hasMany(self::class, 'mother_inmate_intake_registration_id');
    }

    public function photoUrl(): ?string
    {
        if (blank($this->photo_path)) {
            return null;
        }

        return '/storage/'.ltrim($this->photo_path, '/');
    }

    /**
     * @param  Builder<InmateIntakeRegistration>  $query
     * @return Builder<InmateIntakeRegistration>
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        $like = '%'.$term.'%';

        return $query->where(function (Builder $builder) use ($like, $term): void {
            $builder
                ->where('full_name', 'like', $like)
                ->orWhere('court_file_number', 'like', $like)
                ->orWhere('institution_file_number', 'like', $like)
                ->orWhere('cell_number', 'like', $like)
                ->orWhere('crime_type', 'like', $like)
                ->orWhere('detaining_court', 'like', $like)
                ->orWhere('verdict_court', 'like', $like)
                ->orWhere('appeal_court', 'like', $like)
                ->orWhere('sentence_duration', 'like', $like)
                ->orWhere('release_reason', 'like', $like)
                ->orWhere('sentence_status', 'like', $like);
        });
    }
}
