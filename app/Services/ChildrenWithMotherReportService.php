<?php

namespace App\Services;

use App\Models\InmateIntakeRegistration;
use App\Support\EthiopianCalendar;
use Illuminate\Support\Carbon;

class ChildrenWithMotherReportService
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
        $childSentenceType = config('children_with_mother_report.child_sentence_type', 'ህፃናት');
        $rows = [];
        $no = 1;

        InmateIntakeRegistration::query()
            ->with(['fileRecord', 'motherInmate', 'expenseRegistration'])
            ->where('sentence_duration', $childSentenceType)
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
            ->each(function (InmateIntakeRegistration $child) use (&$rows, &$no, $period): void {
                $file = $child->fileRecord;

                if ($file === null) {
                    return;
                }

                $mother = $this->resolveMother($child);

                $rows[] = [
                    'no' => $no++,
                    'child_name' => $child->full_name,
                    'gender' => $this->formatGender($file->gender),
                    'age' => $this->formatChildAge($file->age, $file->birth_date, $period['end']),
                    'mother_name' => $mother?->full_name ?? $file->mother_name ?? '',
                    'mother_crime_type' => $mother?->crime_type ?? '',
                    'mother_sentence' => $this->formatMotherSentence($mother),
                    'admission' => $this->formatDateParts($child->admission_date),
                    'stay' => $this->formatStayDuration($child, $period['end']),
                    'remark' => trim((string) ($child->release_reason ?? '')),
                ];
            });

        return [
            'title' => EthiopianCalendar::childrenWithMotherReportTitle($ethYear, $ethMonth),
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

    private function resolveMother(InmateIntakeRegistration $child): ?InmateIntakeRegistration
    {
        if ($child->motherInmate !== null) {
            return $child->motherInmate;
        }

        $motherName = trim((string) ($child->fileRecord?->mother_name ?? ''));

        if ($motherName === '') {
            return null;
        }

        return InmateIntakeRegistration::query()
            ->where('full_name', $motherName)
            ->whereHas('fileRecord', fn ($query) => $query->where('gender', 'female'))
            ->first();
    }

    private function formatGender(?string $gender): string
    {
        return match ($gender) {
            'male' => __('app.reports.children_with_mother_columns.male_short'),
            'female' => __('app.reports.children_with_mother_columns.female_short'),
            default => '',
        };
    }

    private function formatChildAge(?int $age, Carbon|string|null $birthDate, Carbon $referenceDate): string
    {
        if (filled($birthDate)) {
            $birth = Carbon::parse($birthDate, EthiopianCalendar::timezone())->startOfDay();
            $reference = $referenceDate->copy()->startOfDay();
            $days = max(0, $birth->diffInDays($reference));

            if ($days < 30) {
                return $days.' '.__('app.reports.children_with_mother_columns.day_unit');
            }

            if ($days < 360) {
                $months = max(1, intdiv($days, 30));

                return $months.' '.__('app.reports.children_with_mother_columns.month_unit');
            }

            $years = max(1, intdiv($days, 360));

            return $years.' '.__('app.reports.children_with_mother_columns.year_unit');
        }

        if ($age !== null && $age > 0) {
            return $age.' '.__('app.reports.children_with_mother_columns.year_unit');
        }

        return '';
    }

    private function formatMotherSentence(?InmateIntakeRegistration $mother): string
    {
        if ($mother === null) {
            return '';
        }

        if ($mother->sentence_start_date !== null && $mother->sentence_end_date !== null) {
            $parts = EthiopianCalendar::durationParts(
                Carbon::parse($mother->sentence_start_date)->startOfDay(),
                Carbon::parse($mother->sentence_end_date)->startOfDay(),
            );

            $label = EthiopianCalendar::formatDurationLabel($parts);

            if ($label !== '') {
                return $label;
            }
        }

        return $mother->sentence_duration ?? '';
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
    private function formatStayDuration(InmateIntakeRegistration $child, Carbon $referenceDate): array
    {
        if ($child->admission_date === null) {
            return ['day' => '-', 'month' => '-', 'year' => '-'];
        }

        $endDate = $child->expenseRegistration?->release_date
            ?? $child->full_release_date
            ?? $referenceDate;

        return EthiopianCalendar::durationParts(
            Carbon::parse($child->admission_date)->startOfDay(),
            Carbon::parse($endDate)->startOfDay(),
        );
    }
}
