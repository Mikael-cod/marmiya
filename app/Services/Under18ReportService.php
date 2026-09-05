<?php

namespace App\Services;

use App\Models\InmateIntakeRegistration;
use App\Support\EthiopianCalendar;
use Illuminate\Support\Carbon;

class Under18ReportService
{
    /**
     * @return array{
     *     title: string,
     *     period: array{year: int, month: int, start: Carbon, end: Carbon, days: int},
     *     rows: list<array{
     *         no: int,
     *         full_name: string,
     *         gender: string,
     *         age: int|string,
     *         crime_type: string,
     *         sentence_duration: string,
     *         admission: array{day: string, month: string, year: string},
     *         stay: array{day: string, month: string, year: string},
     *         family_status: string
     *     }>,
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
            ->each(function (InmateIntakeRegistration $registration) use (&$rows, &$no, $period): void {
                $file = $registration->fileRecord;

                if ($file === null) {
                    return;
                }

                $age = $this->resolveAge($file->age, $file->birth_date, $period['end']);

                if ($age === null || $age > (int) config('under_18_report.max_age', 17)) {
                    return;
                }

                $rows[] = [
                    'no' => $no++,
                    'full_name' => $registration->full_name,
                    'gender' => $this->formatGender($file->gender),
                    'age' => $age,
                    'crime_type' => $registration->crime_type ?? '',
                    'sentence_duration' => $registration->sentence_duration ?? '',
                    'admission' => $this->formatDateParts($registration->admission_date),
                    'stay' => $this->formatStayDateParts($registration),
                    'family_status' => $this->formatFamilyStatus($file->marital_status),
                ];
            });

        return [
            'title' => EthiopianCalendar::under18ReportTitle($ethYear, $ethMonth),
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

    private function resolveAge(?int $age, Carbon|string|null $birthDate, Carbon $referenceDate): ?int
    {
        if (filled($birthDate)) {
            return Carbon::parse($birthDate, EthiopianCalendar::timezone())
                ->startOfDay()
                ->diffInYears($referenceDate->copy()->startOfDay());
        }

        return $age;
    }

    private function formatGender(?string $gender): string
    {
        return match ($gender) {
            'male' => __('app.reports.under_18_columns.male_short'),
            'female' => __('app.reports.under_18_columns.female_short'),
            default => '',
        };
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
    private function formatStayDateParts(InmateIntakeRegistration $registration): array
    {
        $stayDate = $registration->expenseRegistration?->release_date
            ?? $registration->full_release_date
            ?? $registration->sentence_end_date
            ?? $registration->parole_release_date;

        return $this->formatDateParts($stayDate);
    }

    private function formatFamilyStatus(?string $maritalStatus): string
    {
        return match ($maritalStatus) {
            'unmarried' => __('app.prisoners.marital_unmarried'),
            'married' => __('app.prisoners.marital_married'),
            'divorced' => __('app.prisoners.marital_divorced'),
            default => '',
        };
    }
}
