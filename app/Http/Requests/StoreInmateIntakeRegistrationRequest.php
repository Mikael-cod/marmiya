<?php

namespace App\Http\Requests;

use App\Services\ParoleScheduleService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class StoreInmateIntakeRegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isUser() ?? false;
    }

    protected function prepareForValidation(): void
    {
        if (! $this->filled(['sentence_start_date', 'sentence_end_date'])) {
            return;
        }

        $paroleReleaseDate = app(ParoleScheduleService::class)->calculateParoleReleaseDate(
            Carbon::parse($this->input('sentence_start_date')),
            Carbon::parse($this->input('sentence_end_date')),
            $this->input('sentence_duration'),
        );

        if ($paroleReleaseDate !== null) {
            $this->merge([
                'parole_release_date' => $paroleReleaseDate->format('Y-m-d'),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'court_file_number' => ['required', 'string', 'max:100'],
            'institution_file_number' => ['required', 'string', 'max:100'],
            'cell_number' => ['required', 'string', 'max:50'],
            'full_name' => ['required', 'string', 'max:255'],
            'photo' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'crime_type' => ['required', 'string', Rule::in(config('crime_types'))],
            'detaining_court' => ['required', 'string', 'max:255'],
            'admission_date' => ['required', 'date'],
            'admission_time' => ['required', 'date_format:H:i'],
            'verdict_court' => ['required', 'string', 'max:255'],
            'sentence_status' => ['required', Rule::in(['remand', 'convicted'])],
            'sentence_duration' => ['required', 'string', Rule::in(config('sentence_types'))],
            'verdict_date' => ['required', 'date'],
            'appeal_court' => ['required', 'string', 'max:255'],
            'sentence_start_date' => ['required', 'date'],
            'sentence_end_date' => ['required', 'date', 'after_or_equal:sentence_start_date'],
            'parole_release_date' => ['required', 'date'],
            'release_reason' => ['nullable', 'string', 'max:1000'],
            'full_release_date' => ['nullable', 'date'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'court_file_number' => __('app.income.fields.court_file_number'),
            'institution_file_number' => __('app.income.fields.institution_file_number'),
            'cell_number' => __('app.income.fields.cell_number'),
            'full_name' => __('app.income.fields.full_name'),
            'photo' => __('app.income.fields.photo'),
            'crime_type' => __('app.income.fields.crime_type'),
            'detaining_court' => __('app.income.fields.detaining_court'),
            'admission_date' => __('app.income.fields.admission_date'),
            'admission_time' => __('app.income.fields.admission_time'),
            'verdict_court' => __('app.income.fields.verdict_court'),
            'sentence_status' => __('app.income.fields.sentence_status'),
            'sentence_duration' => __('app.income.fields.sentence_duration'),
            'verdict_date' => __('app.income.fields.verdict_date'),
            'appeal_court' => __('app.income.fields.appeal_court'),
            'sentence_start_date' => __('app.income.fields.sentence_start_date'),
            'sentence_end_date' => __('app.income.fields.sentence_end_date'),
            'parole_release_date' => __('app.income.fields.parole_release_date'),
            'release_reason' => __('app.income.fields.release_reason'),
            'full_release_date' => __('app.income.fields.full_release_date'),
        ];
    }
}
