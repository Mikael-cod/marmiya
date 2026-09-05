<?php

namespace App\Services;

use App\Models\InmateExpenseRegistration;
use App\Support\EthiopianCalendar;
use Illuminate\Support\Carbon;

class ParoleReleasedReportService
{
    public function __construct(
        private readonly ParoleScheduleService $paroleSchedule,
    ) {}

    /**
     * @return array{
     *     title: string,
     *     period: array{year: int, month: int, start: Carbon, end: Carbon, days: int},
     *     rows: list<array<string, mixed>>,
     *     total: int
     * }
     */
    public function build(int $ethYear, int $ethMonth): array
    {
        $period = EthiopianCalendar::monthGregorianRange($ethYear, $ethMonth);
        $paroleReason = config('parole_released_report.release_reason', config('release_reasons.parole', 'አመክሮ'));
        $rows = [];
        $no = 1;

        InmateExpenseRegistration::query()
            ->with(['inmate.fileRecord'])
            ->whereBetween('release_date', [$period['start'], $period['end']])
            ->where(function ($query) use ($paroleReason): void {
                $query
                    ->where('release_reason', $paroleReason)
                    ->orWhere('release_reason', 'like', '%አመክሮ%');
            })
            ->orderBy('release_date')
            ->orderBy('full_name')
            ->get()
            ->each(function (InmateExpenseRegistration $expense) use (&$rows, &$no): void {
                $inmate = $expense->inmate;

                if ($inmate === null) {
                    return;
                }

                $admissionDate = $inmate->admission_date ?? $expense->admission_date;
                $releaseDate = $expense->release_date;
                $gender = $inmate->fileRecord?->gender ?? $this->resolveStoredGender($expense->gender);

                $rows[] = [
                    'no' => $no++,
                    'institution_file_number' => $inmate->institution_file_number ?? $expense->institution_id_number ?? '',
                    'court_file_number' => $inmate->court_file_number ?? $expense->court_file_number ?? '',
                    'full_name' => $expense->full_name ?? $inmate->full_name,
                    'gender' => $this->formatGender($gender),
                    'admission' => $this->formatDateParts($admissionDate),
                    'release' => $this->formatDateParts($releaseDate),
                    'crime_type' => $inmate->crime_type ?? $expense->crime_type ?? '',
                    'sentence_duration' => $this->formatSentenceDuration($inmate, $expense),
                    'stay' => $this->formatDurationParts($admissionDate, $releaseDate),
                    'reduction' => $this->formatReductionParts($inmate),
                    'institution' => config('parole_released_report.institution_short', 'አ/ምንጭ'),
                    'release_court' => $inmate->verdict_court ?? $inmate->appeal_court ?? $expense->sentencing_court ?? '',
                ];
            });

        return [
            'title' => EthiopianCalendar::paroleReleasedReportTitle($ethYear, $ethMonth),
            'period' => [
                'year' => $ethYear,
                'month' => $ethMonth,
                'start' => $period['start'],
                'end' => $period['end'],
                'days' => $period['daysInMonth'],
            ],
            'rows' => $rows,
            'total' => count($rows),
        ];
    }

    /**
     * @return array{day: string, month: string, year: string}
     */
    private function formatDateParts(Carbon|string|null $value): array
    {
        $parts = EthiopianCalendar::dateParts($value);

        if ($parts === null) {
            return ['day' => '', 'month' => '', 'year' => ''];
        }

        return [
            'day' => (string) $parts['day'],
            'month' => (string) $parts['month'],
            'year' => (string) $parts['year'],
        ];
    }

    /**
     * @return array{day: string, month: string, year: string}
     */
    private function formatDurationParts(Carbon|string|null $start, Carbon|string|null $end): array
    {
        if ($start === null || $end === null) {
            return ['day' => '-', 'month' => '-', 'year' => '-'];
        }

        return EthiopianCalendar::durationParts(
            Carbon::parse($start)->startOfDay(),
            Carbon::parse($end)->startOfDay(),
        );
    }

    /**
     * @return array{day: string, month: string, year: string}
     */
    private function formatReductionParts(\App\Models\InmateIntakeRegistration $inmate): array
    {
        if ($inmate->sentence_start_date === null || $inmate->sentence_end_date === null) {
            return ['day' => '-', 'month' => '-', 'year' => '-'];
        }

        $row = $this->paroleSchedule->matchRow(
            $inmate->sentence_start_date,
            $inmate->sentence_end_date,
            $inmate->sentence_duration,
        );

        if ($row === null || $this->paroleSchedule->hasNoParoleDeduction($row)) {
            return ['day' => '-', 'month' => '-', 'year' => '-'];
        }

        return $this->formatScheduleDuration($row['deducted'] ?? []);
    }

    /**
     * @param  array{year?: string, month?: string, day?: string}  $duration
     * @return array{day: string, month: string, year: string}
     */
    private function formatScheduleDuration(array $duration): array
    {
        return [
            'day' => $this->formatDurationComponent($duration['day'] ?? '-'),
            'month' => $this->formatDurationComponent($duration['month'] ?? '-'),
            'year' => $this->formatDurationComponent($duration['year'] ?? '-'),
        ];
    }

    private function formatDurationComponent(string $value): string
    {
        if ($value === '-' || $value === '' || $value === '0') {
            return '-';
        }

        return $value;
    }

    private function formatSentenceDuration(\App\Models\InmateIntakeRegistration $inmate, InmateExpenseRegistration $expense): string
    {
        if ($inmate->sentence_start_date !== null && $inmate->sentence_end_date !== null) {
            $parts = EthiopianCalendar::durationParts(
                Carbon::parse($inmate->sentence_start_date)->startOfDay(),
                Carbon::parse($inmate->sentence_end_date)->startOfDay(),
            );

            $label = EthiopianCalendar::formatDurationLabel($parts);

            if ($label !== '') {
                return $label;
            }
        }

        $served = trim((string) ($expense->work_experience_during ?? ''));

        if ($served !== '') {
            return $served;
        }

        return '';
    }

    private function formatGender(?string $gender): string
    {
        return match ($gender) {
            'male' => __('app.reports.parole_released_columns.male_short'),
            'female' => __('app.reports.parole_released_columns.female_short'),
            default => '',
        };
    }

    private function resolveStoredGender(?string $gender): ?string
    {
        if ($gender === null || trim($gender) === '') {
            return null;
        }

        $normalized = mb_strtolower(trim($gender));

        if (str_contains($normalized, 'ወንድ') || str_contains($normalized, 'male')) {
            return 'male';
        }

        if (str_contains($normalized, 'ሴ') || str_contains($normalized, 'female')) {
            return 'female';
        }

        return null;
    }
}
