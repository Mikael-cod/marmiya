<?php

namespace App\Services;

use App\Models\InmateIntakeRegistration;
use App\Support\EthiopianCalendar;
use Illuminate\Support\Carbon;

class DeathSentencedReportService
{
    /**
     * @return array{
     *     title: string,
     *     form_code: string,
     *     period: array{year: int, month: int, start: Carbon, end: Carbon, days: int},
     *     rows: list<array<string, mixed>>,
     *     total: int,
     *     death_separation_total: int
     * }
     */
    public function build(int $ethYear, int $ethMonth): array
    {
        $period = EthiopianCalendar::monthGregorianRange($ethYear, $ethMonth);
        $deathSentenceType = config('death_sentenced_report.death_sentence_type', 'ሞት ፍርድ');
        $rows = [];
        $no = 1;
        $deathSeparationTotal = 0;

        InmateIntakeRegistration::query()
            ->with(['fileRecord', 'expenseRegistration'])
            ->where('sentence_duration', $deathSentenceType)
            ->whereHas('fileRecord')
            ->whereDate('admission_date', '<=', $period['end'])
            ->where(function ($query) use ($period): void {
                $query
                    ->whereDoesntHave('expenseRegistration')
                    ->orWhereHas('expenseRegistration', function ($expenseQuery) use ($period): void {
                        $expenseQuery->whereDate('release_date', '>=', $period['start']);
                    });
            })
            ->orderBy('full_name')
            ->get()
            ->each(function (InmateIntakeRegistration $registration) use (&$rows, &$no, &$deathSeparationTotal, $period): void {
                $file = $registration->fileRecord;

                if ($file === null) {
                    return;
                }

                $deathSeparation = $this->resolveDeathSeparation($registration);

                if ($deathSeparation === '1') {
                    $deathSeparationTotal++;
                }

                $rows[] = [
                    'no' => $no++,
                    'full_name' => $registration->full_name,
                    'gender' => $this->formatGender($file->gender),
                    'age' => $this->resolveAge($file->age, $file->birth_date, $period['end']),
                    'nationality' => $this->formatNationality($file->nationality),
                    'crime_type' => $registration->crime_type ?? '',
                    'file_number' => $registration->court_file_number ?? $registration->institution_file_number ?? '',
                    'verdict_court' => $registration->verdict_court ?? '',
                    'sentence_amount' => __('app.reports.death_sentenced_columns.death_short'),
                    'admission' => $this->formatDateParts($registration->admission_date),
                    'station_stay' => '-',
                    'total_prison_time' => $this->formatTotalPrisonTime($registration, $period['end']),
                    'death_separation' => $deathSeparation,
                ];
            });

        return [
            'title' => EthiopianCalendar::deathSentencedReportTitle($ethYear, $ethMonth),
            'form_code' => config('death_sentenced_report.form_code', 'ቅፅ - 6'),
            'period' => [
                'year' => $ethYear,
                'month' => $ethMonth,
                'start' => $period['start'],
                'end' => $period['end'],
                'days' => $period['daysInMonth'],
            ],
            'rows' => $rows,
            'total' => count($rows),
            'death_separation_total' => $deathSeparationTotal,
        ];
    }

    private function resolveDeathSeparation(InmateIntakeRegistration $registration): string
    {
        $releaseReason = trim((string) ($registration->expenseRegistration?->release_reason ?? $registration->release_reason ?? ''));

        if ($releaseReason === '') {
            return '-';
        }

        $deathReason = config('death_sentenced_report.death_release_reason', 'በሞት የተለዩ');

        if ($releaseReason === $deathReason || str_contains($releaseReason, 'በሞት')) {
            return '1';
        }

        return '-';
    }

    private function formatGender(?string $gender): string
    {
        return match ($gender) {
            'male' => __('app.reports.death_sentenced_columns.male_short'),
            'female' => __('app.reports.death_sentenced_columns.female_short'),
            default => '',
        };
    }

    private function resolveAge(?int $age, Carbon|string|null $birthDate, Carbon $referenceDate): int|string
    {
        if (filled($birthDate)) {
            return Carbon::parse($birthDate, EthiopianCalendar::timezone())
                ->startOfDay()
                ->diffInYears($referenceDate->copy()->startOfDay());
        }

        return $age ?? '';
    }

    private function formatNationality(?string $nationality): string
    {
        if ($nationality === null || trim($nationality) === '') {
            return __('app.reports.death_sentenced_columns.ethiopian_short');
        }

        $normalized = mb_strtolower(trim($nationality));

        if (
            str_contains($normalized, 'ኢት')
            || str_contains($normalized, 'ethiop')
            || str_contains($normalized, 'የኢት')
        ) {
            return __('app.reports.death_sentenced_columns.ethiopian_short');
        }

        return $nationality;
    }

    private function formatTotalPrisonTime(InmateIntakeRegistration $registration, Carbon $referenceDate): string
    {
        if ($registration->admission_date === null) {
            return '';
        }

        $endDate = $registration->expenseRegistration?->release_date
            ?? $registration->full_release_date
            ?? $referenceDate;

        $parts = EthiopianCalendar::durationParts(
            Carbon::parse($registration->admission_date)->startOfDay(),
            Carbon::parse($endDate)->startOfDay(),
        );

        return EthiopianCalendar::formatDurationLabel($parts);
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
}
