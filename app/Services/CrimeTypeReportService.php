<?php

namespace App\Services;

use App\Models\InmateIntakeRegistration;
use App\Support\EthiopianCalendar;
use Illuminate\Support\Carbon;

class CrimeTypeReportService
{
    /**
     * @return array{
     *     title: string,
     *     period: array{year: int, month: int, start: Carbon, end: Carbon, days: int},
     *     rows: list<array{no: int, crime_type: string, counts: array<string, mixed>}>,
     *     totals: array<string, mixed>
     * }
     */
    public function build(int $ethYear, int $ethMonth): array
    {
        $period = EthiopianCalendar::monthGregorianRange($ethYear, $ethMonth);
        $crimeTypes = config('crime_types', []);
        $rowsByCrime = [];

        foreach ($crimeTypes as $crimeType) {
            $rowsByCrime[$crimeType] = $this->emptyCounts();
        }

        InmateIntakeRegistration::query()
            ->with('fileRecord')
            ->whereBetween('admission_date', [$period['start'], $period['end']])
            ->get()
            ->each(function (InmateIntakeRegistration $registration) use (&$rowsByCrime): void {
                $crimeType = $registration->crime_type;

                if (! isset($rowsByCrime[$crimeType])) {
                    $rowsByCrime[$crimeType] = $this->emptyCounts();
                }

                $status = $this->resolveStatus($registration->sentence_status);

                if ($status === null) {
                    return;
                }
                $isEthiopian = $this->isEthiopian($registration->fileRecord?->nationality);
                $gender = $registration->fileRecord?->gender;

                $this->increment($rowsByCrime[$crimeType], $status, $isEthiopian, $gender);
            });

        $rows = [];
        $no = 1;

        foreach ($crimeTypes as $crimeType) {
            $counts = $this->finalizeCounts($rowsByCrime[$crimeType] ?? $this->emptyCounts());

            $rows[] = [
                'no' => $no++,
                'crime_type' => $crimeType,
                'counts' => $counts,
            ];
        }

        foreach ($rowsByCrime as $crimeType => $counts) {
            if (in_array($crimeType, $crimeTypes, true)) {
                continue;
            }

            $rows[] = [
                'no' => $no++,
                'crime_type' => $crimeType,
                'counts' => $this->finalizeCounts($counts),
            ];
        }

        return [
            'title' => EthiopianCalendar::monthReportTitle($ethYear, $ethMonth),
            'period' => [
                'year' => $ethYear,
                'month' => $ethMonth,
                'start' => $period['start'],
                'end' => $period['end'],
                'days' => $period['daysInMonth'],
            ],
            'rows' => $rows,
            'totals' => $this->buildTotals($rows),
        ];
    }

    /**
     * @param  list<array{counts: array<string, mixed>}>  $rows
     * @return array<string, mixed>
     */
    private function buildTotals(array $rows): array
    {
        $totals = $this->emptyCounts();

        foreach ($rows as $row) {
            foreach (['convicted', 'remand', 'both'] as $status) {
                foreach (['ethiopian', 'foreign'] as $nationality) {
                    foreach (['male', 'female', 'total'] as $gender) {
                        $totals[$status][$nationality][$gender] += $row['counts'][$status][$nationality][$gender];
                    }
                }
            }
        }

        return $this->finalizeCounts($totals);
    }

    /**
     * @return array<string, mixed>
     */
    private function emptyCounts(): array
    {
        $block = static fn (): array => ['male' => 0, 'female' => 0, 'total' => 0];

        return [
            'convicted' => ['ethiopian' => $block(), 'foreign' => $block()],
            'remand' => ['ethiopian' => $block(), 'foreign' => $block()],
            'both' => ['ethiopian' => $block(), 'foreign' => $block()],
            'subtotal' => ['ethiopian' => $block(), 'foreign' => $block()],
            'grand' => $block(),
        ];
    }

    /**
     * @param  array<string, mixed>  $counts
     */
    private function increment(array &$counts, string $status, bool $isEthiopian, ?string $gender): void
    {
        $nationality = $isEthiopian ? 'ethiopian' : 'foreign';

        if ($gender === 'male') {
            $counts[$status][$nationality]['male']++;
        } elseif ($gender === 'female') {
            $counts[$status][$nationality]['female']++;
        }

        $counts[$status][$nationality]['total']++;
    }

    /**
     * @param  array<string, mixed>  $counts
     * @return array<string, mixed>
     */
    private function finalizeCounts(array $counts): array
    {
        foreach (['ethiopian', 'foreign'] as $nationality) {
            foreach (['male', 'female', 'total'] as $gender) {
                $counts['subtotal'][$nationality][$gender] =
                    $counts['convicted'][$nationality][$gender]
                    + $counts['remand'][$nationality][$gender]
                    + $counts['both'][$nationality][$gender];
            }
        }

        foreach (['male', 'female', 'total'] as $gender) {
            $counts['grand'][$gender] =
                $counts['subtotal']['ethiopian'][$gender]
                + $counts['subtotal']['foreign'][$gender];
        }

        return $counts;
    }

    private function resolveStatus(?string $sentenceStatus): ?string
    {
        return match ($sentenceStatus) {
            'convicted' => 'convicted',
            'remand' => 'remand',
            default => null,
        };
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
