<?php

namespace App\Services;

use App\Models\InmateIntakeRegistration;
use App\Support\EthiopianCalendar;
use Illuminate\Support\Carbon;

class NewIntakeReportService
{
    /**
     * @return array{
     *     title: string,
     *     form_code: string,
     *     institution: string,
     *     period: array{year: int, month: int, start: Carbon, end: Carbon, days: int},
     *     counts: array<string, mixed>,
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
                $status = $this->resolveStatus($registration->sentence_status);

                if ($status === null) {
                    return;
                }

                $isEthiopian = $this->isEthiopian($registration->fileRecord?->nationality);
                $gender = $registration->fileRecord?->gender;

                $this->increment($counts, $status, $isEthiopian, $gender);
            });

        return [
            'title' => EthiopianCalendar::newIntakeReportTitle($ethYear, $ethMonth),
            'form_code' => config('new_intake_report.form_code', 'ቅፅ - 3'),
            'institution' => config('new_intake_report.institution', 'በአ/ምንጭ ማረሚያ ተቋም'),
            'period' => [
                'year' => $ethYear,
                'month' => $ethMonth,
                'start' => $period['start'],
                'end' => $period['end'],
                'days' => $period['daysInMonth'],
            ],
            'counts' => $this->finalizeCounts($counts),
        ];
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
        } else {
            return;
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
            'both' => 'both',
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
