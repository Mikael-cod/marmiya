<?php

namespace App\Services;

use App\Models\InmateIntakeRegistration;
use App\Support\EthiopianCalendar;
use Illuminate\Support\Carbon;

class SentenceTypeReportService
{
    /**
     * @return array{
     *     title: string,
     *     form_code: string,
     *     period: array{year: int, month: int, start: Carbon, end: Carbon, days: int},
     *     rows: list<array{no: int, sentence_type: string, male: int, female: int, total: int}>,
     *     grand_total: array{male: int, female: int, total: int}
     * }
     */
    public function build(int $ethYear, int $ethMonth): array
    {
        $period = EthiopianCalendar::monthGregorianRange($ethYear, $ethMonth);
        $counts = [];

        foreach (config('sentence_types', []) as $sentenceType) {
            $counts[$sentenceType] = ['male' => 0, 'female' => 0, 'total' => 0];
        }

        InmateIntakeRegistration::query()
            ->with('fileRecord')
            ->whereBetween('admission_date', [$period['start'], $period['end']])
            ->get()
            ->each(function (InmateIntakeRegistration $registration) use (&$counts): void {
                $sentenceType = $registration->sentence_duration;

                if ($sentenceType === null || trim($sentenceType) === '') {
                    return;
                }

                if (! isset($counts[$sentenceType])) {
                    $counts[$sentenceType] = ['male' => 0, 'female' => 0, 'total' => 0];
                }

                $gender = $registration->fileRecord?->gender;

                if ($gender === 'male') {
                    $counts[$sentenceType]['male']++;
                } elseif ($gender === 'female') {
                    $counts[$sentenceType]['female']++;
                } else {
                    return;
                }

                $counts[$sentenceType]['total']++;
            });

        $rows = [];
        $no = 1;

        foreach (config('sentence_types', []) as $sentenceType) {
            $rowCounts = $counts[$sentenceType] ?? ['male' => 0, 'female' => 0, 'total' => 0];

            $rows[] = [
                'no' => $no++,
                'sentence_type' => $sentenceType,
                'male' => $rowCounts['male'],
                'female' => $rowCounts['female'],
                'total' => $rowCounts['total'],
            ];
        }

        foreach ($counts as $sentenceType => $rowCounts) {
            if (in_array($sentenceType, config('sentence_types', []), true)) {
                continue;
            }

            $rows[] = [
                'no' => $no++,
                'sentence_type' => $sentenceType,
                'male' => $rowCounts['male'],
                'female' => $rowCounts['female'],
                'total' => $rowCounts['total'],
            ];
        }

        return [
            'title' => EthiopianCalendar::sentenceTypeReportTitle($ethYear, $ethMonth),
            'form_code' => config('sentence_type_report.form_code', 'ቅፅ - 1'),
            'period' => [
                'year' => $ethYear,
                'month' => $ethMonth,
                'start' => $period['start'],
                'end' => $period['end'],
                'days' => $period['daysInMonth'],
            ],
            'rows' => $rows,
            'grand_total' => $this->buildGrandTotal($rows),
        ];
    }

    /**
     * @param  list<array{male: int, female: int, total: int}>  $rows
     * @return array{male: int, female: int, total: int}
     */
    private function buildGrandTotal(array $rows): array
    {
        $totals = ['male' => 0, 'female' => 0, 'total' => 0];

        foreach ($rows as $row) {
            $totals['male'] += $row['male'];
            $totals['female'] += $row['female'];
            $totals['total'] += $row['total'];
        }

        return $totals;
    }
}
