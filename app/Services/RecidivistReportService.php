<?php

namespace App\Services;

use App\Models\InmateIntakeRegistration;
use App\Support\EthiopianCalendar;
use Illuminate\Support\Carbon;

class RecidivistReportService
{
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
        $rows = [];
        $no = 1;

        InmateIntakeRegistration::query()
            ->with(['fileRecord', 'expenseRegistration'])
            ->whereHas('fileRecord')
            ->whereBetween('admission_date', [$period['start'], $period['end']])
            ->orderBy('admission_date')
            ->orderBy('full_name')
            ->get()
            ->filter(fn (InmateIntakeRegistration $registration): bool => $this->isRecidivist($registration))
            ->each(function (InmateIntakeRegistration $registration) use (&$rows, &$no): void {
                $file = $registration->fileRecord;

                if ($file === null) {
                    return;
                }

                $rows[] = [
                    'no' => $no++,
                    'full_name' => $registration->full_name,
                    'gender' => $this->formatGender($file->gender),
                    'crime_type' => $registration->crime_type ?? '',
                    'sentencing_court' => $registration->verdict_court ?? $registration->detaining_court ?? '',
                    'sentence_amount' => $this->formatSentenceAmount($registration),
                    'admission' => $this->formatDateParts($registration->admission_date),
                    'remark' => trim((string) ($registration->release_reason ?? '')),
                ];
            });

        return [
            'title' => EthiopianCalendar::recidivistReportTitle($ethYear, $ethMonth),
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

    private function isRecidivist(InmateIntakeRegistration $registration): bool
    {
        if ($registration->admission_date === null) {
            return false;
        }

        $admissionDate = Carbon::parse($registration->admission_date)->startOfDay();

        return InmateIntakeRegistration::query()
            ->where('id', '!=', $registration->id)
            ->where('full_name', $registration->full_name)
            ->whereDate('admission_date', '<', $admissionDate)
            ->where(function ($query) use ($admissionDate): void {
                $query
                    ->whereHas('expenseRegistration', function ($expenseQuery) use ($admissionDate): void {
                        $expenseQuery->whereDate('release_date', '<', $admissionDate);
                    })
                    ->orWhere(function ($priorQuery) use ($admissionDate): void {
                        $priorQuery
                            ->whereDate('full_release_date', '<', $admissionDate)
                            ->whereDoesntHave('expenseRegistration');
                    });
            })
            ->exists();
    }

    private function formatGender(?string $gender): string
    {
        return match ($gender) {
            'male' => __('app.reports.recidivist_columns.male_short'),
            'female' => __('app.reports.recidivist_columns.female_short'),
            default => '',
        };
    }

    private function formatSentenceAmount(InmateIntakeRegistration $registration): string
    {
        if ($registration->sentence_start_date !== null && $registration->sentence_end_date !== null) {
            $parts = EthiopianCalendar::durationParts(
                Carbon::parse($registration->sentence_start_date)->startOfDay(),
                Carbon::parse($registration->sentence_end_date)->startOfDay(),
            );

            $label = EthiopianCalendar::formatDurationLabel($parts);

            if ($label !== '') {
                return $label;
            }
        }

        return trim((string) ($registration->sentence_duration ?? ''));
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
