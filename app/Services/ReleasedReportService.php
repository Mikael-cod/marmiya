<?php

namespace App\Services;

use App\Models\InmateExpenseRegistration;
use App\Support\EthiopianCalendar;
use Illuminate\Support\Carbon;

class ReleasedReportService
{
    /**
     * @return array{
     *     title: string,
     *     form_code: string,
     *     facility: string,
     *     period: array{year: int, month: int, start: Carbon, end: Carbon, days: int},
     *     nationality_rows: array<string, array{label: string, categories: array<string, array{male: int, female: int, total: int}>, row_total: array{male: int, female: int, total: int}>>,
     *     footer: array{ethiopian: array<string, mixed>, foreign: array<string, mixed>, grand: array<string, mixed>}
     * }
     */
    public function build(int $ethYear, int $ethMonth): array
    {
        $period = EthiopianCalendar::monthGregorianRange($ethYear, $ethMonth);
        $nationalityRows = [
            'ethiopian' => $this->emptyNationalityRow(__('app.reports.released_columns.ethiopian_short')),
            'foreign' => $this->emptyNationalityRow(__('app.reports.released_columns.foreign_short')),
        ];

        InmateExpenseRegistration::query()
            ->whereBetween('release_date', [$period['start'], $period['end']])
            ->get()
            ->each(function (InmateExpenseRegistration $expense) use (&$nationalityRows): void {
                $category = $this->resolveCategory($expense->release_reason);

                if ($category === null) {
                    return;
                }

                $nationalityKey = $this->isEthiopian($expense->nationality) ? 'ethiopian' : 'foreign';
                $gender = $this->resolveGender($expense->gender);

                if ($gender === null) {
                    return;
                }

                $this->increment($nationalityRows[$nationalityKey], $category, $gender);
            });

        foreach (array_keys($nationalityRows) as $nationalityKey) {
            $nationalityRows[$nationalityKey] = $this->finalizeNationalityRow($nationalityRows[$nationalityKey]);
        }

        return [
            'title' => EthiopianCalendar::releasedReportTitle($ethYear, $ethMonth),
            'form_code' => config('released_report.form_code', 'ቅፅ-4'),
            'facility' => config('released_report.facility', 'አርባምንጭ ማረሚያ'),
            'period' => [
                'year' => $ethYear,
                'month' => $ethMonth,
                'start' => $period['start'],
                'end' => $period['end'],
                'days' => $period['daysInMonth'],
            ],
            'nationality_rows' => $nationalityRows,
            'footer' => $this->buildFooter($nationalityRows),
        ];
    }

    /**
     * @return array{label: string, categories: array<string, array{male: int, female: int, total: int}>, row_total: array{male: int, female: int, total: int}}
     */
    private function emptyNationalityRow(string $label): array
    {
        $block = static fn (): array => ['male' => 0, 'female' => 0, 'total' => 0];
        $categories = [];

        foreach ($this->allCategories() as $category) {
            $categories[$category] = $block();
        }

        return [
            'label' => $label,
            'categories' => $categories,
            'row_total' => $block(),
        ];
    }

    /**
     * @param  array{label: string, categories: array<string, array{male: int, female: int, total: int}>, row_total: array{male: int, female: int, total: int}}  $row
     */
    private function increment(array &$row, string $category, string $gender): void
    {
        $row['categories'][$category][$gender]++;
        $row['categories'][$category]['total']++;
    }

    /**
     * @param  array{label: string, categories: array<string, array{male: int, female: int, total: int}>, row_total: array{male: int, female: int, total: int}}  $row
     * @return array{label: string, categories: array<string, array{male: int, female: int, total: int}>, row_total: array{male: int, female: int, total: int}}
     */
    private function finalizeNationalityRow(array $row): array
    {
        $rowTotal = ['male' => 0, 'female' => 0, 'total' => 0];

        foreach (config('released_report.count_categories', []) as $category) {
            foreach (['male', 'female', 'total'] as $gender) {
                $rowTotal[$gender] += $row['categories'][$category][$gender];
            }
        }

        $row['row_total'] = $rowTotal;

        return $row;
    }

    /**
     * @param  array<string, array{label: string, categories: array<string, array{male: int, female: int, total: int}>, row_total: array{male: int, female: int, total: int}>>  $nationalityRows
     * @return array{ethiopian: array<string, mixed>, foreign: array<string, mixed>, grand: array<string, mixed>}
     */
    private function buildFooter(array $nationalityRows): array
    {
        $grandCategories = [];
        $grandRowTotal = ['male' => 0, 'female' => 0, 'total' => 0];

        foreach ($this->allCategories() as $category) {
            $grandCategories[$category] = ['male' => 0, 'female' => 0, 'total' => 0];
        }

        foreach (['ethiopian', 'foreign'] as $nationalityKey) {
            foreach ($this->allCategories() as $category) {
                foreach (['male', 'female', 'total'] as $gender) {
                    $grandCategories[$category][$gender] += $nationalityRows[$nationalityKey]['categories'][$category][$gender];
                }
            }

            foreach (['male', 'female', 'total'] as $gender) {
                $grandRowTotal[$gender] += $nationalityRows[$nationalityKey]['row_total'][$gender];
            }
        }

        return [
            'ethiopian' => $nationalityRows['ethiopian'],
            'foreign' => $nationalityRows['foreign'],
            'grand' => [
                'categories' => $grandCategories,
                'row_total' => $grandRowTotal,
            ],
        ];
    }

    /**
     * @return list<string>
     */
    private function allCategories(): array
    {
        return [
            ...config('released_report.count_categories', []),
            config('released_report.transfer_category', 'transferred'),
        ];
    }

    private function resolveCategory(?string $releaseReason): ?string
    {
        if ($releaseReason === null || trim($releaseReason) === '') {
            return null;
        }

        $normalized = trim($releaseReason);

        foreach (config('release_reasons', []) as $key => $label) {
            if ($normalized === $label) {
                return $key;
            }
        }

        $normalizedLower = mb_strtolower($normalized);

        foreach (config('release_reasons', []) as $key => $label) {
            if (str_contains($normalizedLower, mb_strtolower($label))) {
                return $key;
            }
        }

        return 'other';
    }

    private function resolveGender(?string $gender): ?string
    {
        if ($gender === 'male' || $gender === 'female') {
            return $gender;
        }

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

    private function isEthiopian(?string $nationality): bool
    {
        if ($nationality === null || trim($nationality) === '') {
            return true;
        }

        $normalized = mb_strtolower(trim($nationality));

        return str_contains($normalized, 'ኢት')
            || str_contains($normalized, 'ethiop')
            || str_contains($normalized, 'የኢት');
    }
}
