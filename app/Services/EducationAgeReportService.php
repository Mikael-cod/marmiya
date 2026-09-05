<?php

namespace App\Services;

use App\Models\InmateIntakeRegistration;
use App\Support\EthiopianCalendar;
use Illuminate\Support\Carbon;

class EducationAgeReportService
{
    /**
     * @return array{
     *     title: string,
     *     form_code: string,
     *     period: array{year: int, month: int, start: Carbon, end: Carbon, days: int},
     *     rows: list<array<string, mixed>>,
     *     grand_total: array<string, mixed>
     * }
     */
    public function build(int $ethYear, int $ethMonth): array
    {
        $period = EthiopianCalendar::monthGregorianRange($ethYear, $ethMonth);
        $counts = $this->emptyCounts();

        InmateIntakeRegistration::query()
            ->with('fileRecord')
            ->whereBetween('admission_date', [$period['start'], $period['end']])
            ->get()
            ->each(function (InmateIntakeRegistration $registration) use (&$counts): void {
                $file = $registration->fileRecord;

                if ($file === null) {
                    return;
                }

                $gender = $file->gender;

                if (! in_array($gender, ['male', 'female'], true)) {
                    return;
                }

                $educationKey = $this->resolveEducationKey($file->education_level);

                if ($educationKey !== null) {
                    $counts['education'][$educationKey][$gender]++;
                }

                $ageGroup = $this->resolveAgeGroup($file->age, $file->birth_date);

                if ($ageGroup !== null) {
                    $counts['age'][$ageGroup][$gender]++;
                }
            });

        $counts = $this->finalizeCounts($counts);

        $row = [
            'no' => 1,
            'institution' => __('app.institute'),
            'education' => $counts['education'],
            'age' => $counts['age'],
        ];

        return [
            'title' => EthiopianCalendar::educationAgeReportTitle($ethYear, $ethMonth),
            'form_code' => config('education_age_report.form_code', 'ቅ-5'),
            'period' => [
                'year' => $ethYear,
                'month' => $ethMonth,
                'start' => $period['start'],
                'end' => $period['end'],
                'days' => $period['daysInMonth'],
            ],
            'rows' => [$row],
            'grand_total' => $counts,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function emptyCounts(): array
    {
        $genderBlock = static fn (): array => ['male' => 0, 'female' => 0];

        $education = [];
        foreach (config('education_age_report.education_levels', []) as $level) {
            $education[$level] = $genderBlock();
        }
        $education['subtotal'] = $genderBlock();

        $age = [];
        foreach (array_keys(config('education_age_report.age_groups', [])) as $group) {
            $age[$group] = $genderBlock();
        }
        $age['subtotal'] = $genderBlock();

        return [
            'education' => $education,
            'age' => $age,
        ];
    }

    /**
     * @param  array<string, mixed>  $counts
     * @return array<string, mixed>
     */
    private function finalizeCounts(array $counts): array
    {
        foreach (['male', 'female'] as $gender) {
            $counts['education']['subtotal'][$gender] = 0;

            foreach (config('education_age_report.education_levels', []) as $level) {
                $counts['education']['subtotal'][$gender] += $counts['education'][$level][$gender];
            }

            $counts['age']['subtotal'][$gender] = 0;

            foreach (array_keys(config('education_age_report.age_groups', [])) as $group) {
                $counts['age']['subtotal'][$gender] += $counts['age'][$group][$gender];
            }
        }

        return $counts;
    }

    private function resolveEducationKey(?string $educationLevel): ?string
    {
        if ($educationLevel === null || trim($educationLevel) === '') {
            return null;
        }

        $levels = config('education_age_report.education_levels', []);

        if (in_array($educationLevel, $levels, true)) {
            return $educationLevel;
        }

        return null;
    }

    private function resolveAgeGroup(?int $age, Carbon|string|null $birthDate): ?string
    {
        $resolvedAge = $age;

        if ($resolvedAge === null && filled($birthDate)) {
            $resolvedAge = Carbon::parse($birthDate, EthiopianCalendar::timezone())->age;
        }

        if ($resolvedAge === null) {
            return null;
        }

        if ($resolvedAge <= 18) {
            return 'under_18';
        }

        if ($resolvedAge <= 35) {
            return 'from_19_35';
        }

        if ($resolvedAge <= 60) {
            return 'from_36_60';
        }

        return 'above_60';
    }
}
