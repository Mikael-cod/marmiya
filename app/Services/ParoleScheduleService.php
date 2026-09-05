<?php

namespace App\Services;

use Illuminate\Support\Carbon;

class ParoleScheduleService
{
    private const DAYS_PER_MONTH = 30;

    private const DAYS_PER_YEAR = 360;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function rows(): array
    {
        return config('parole_schedule.rows', []);
    }

    public function calculateParoleReleaseDate(
        Carbon|string $sentenceStart,
        Carbon|string $sentenceEnd,
        ?string $sentenceDuration = null,
    ): ?Carbon {
        $start = Carbon::parse($sentenceStart)->startOfDay();
        $end = Carbon::parse($sentenceEnd)->startOfDay();

        if ($end->lt($start)) {
            return null;
        }

        $row = $this->matchRow($start, $end, $sentenceDuration);

        if ($row === null) {
            return $end->copy();
        }

        if ($this->hasNoParoleDeduction($row)) {
            return $end->copy();
        }

        return $this->applyDuration($start, $row['served']);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function matchRow(
        Carbon $start,
        Carbon $end,
        ?string $sentenceDuration = null,
    ): ?array {
        if ($sentenceDuration !== null && $sentenceDuration !== '') {
            $label = $this->normalizeSentenceLabel($sentenceDuration);

            if ($label !== null) {
                $row = $this->findRowByLabel($label);

                if ($row !== null) {
                    return $row;
                }
            }
        }

        return $this->matchRowByDateRange($start, $end);
    }

    /**
     * @param  array<string, mixed>  $row
     */
    public function hasNoParoleDeduction(array $row): bool
    {
        if (in_array($row['sentence'], ['1 ወር', '2 ወር'], true)) {
            return true;
        }

        return $this->durationIsEmpty($row['deducted'] ?? [])
            && $this->durationIsEmpty($row['served'] ?? []);
    }

    public function normalizeSentenceLabel(string $value): ?string
    {
        $value = trim(preg_replace('/\s+/u', ' ', $value) ?? '');

        if ($value === '') {
            return null;
        }

        $row = $this->findRowByLabel($value);

        if ($row !== null) {
            return $row['sentence'];
        }

        if (preg_match('/^(\d+)\s*ወር(?:\s|$)/u', $value, $matches) === 1) {
            $row = $this->findRowByLabel($matches[1].' ወር');

            return $row['sentence'] ?? null;
        }

        if (preg_match('/^(\d+)\s*ዓመት(?:\s|$)/u', $value, $matches) === 1) {
            $row = $this->findRowByLabel($matches[1].' ዓመት');

            return $row['sentence'] ?? null;
        }

        return null;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findRowByLabel(string $label): ?array
    {
        foreach ($this->rows() as $row) {
            if ($row['sentence'] === $label) {
                return $row;
            }
        }

        return null;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function matchRowByDateRange(Carbon $start, Carbon $end): ?array
    {
        $totalDays = $start->diffInDays($end) + 1;
        $exactMatch = null;
        $closestMatch = null;
        $closestDiff = PHP_INT_MAX;

        foreach ($this->rows() as $row) {
            $rowDays = $this->sentenceLabelToDays($row['sentence']);

            if ($rowDays <= 0) {
                continue;
            }

            if ($rowDays === $totalDays) {
                $exactMatch = $row;

                break;
            }

            $diff = abs($rowDays - $totalDays);

            if ($diff < $closestDiff) {
                $closestDiff = $diff;
                $closestMatch = $row;
            }
        }

        return $exactMatch ?? $closestMatch;
    }

    /**
     * @param  array{year?: string, month?: string, day?: string}  $duration
     */
    public function applyDuration(Carbon $date, array $duration): Carbon
    {
        $result = $date->copy();

        $years = $this->parseComponent($duration['year'] ?? '-');
        $months = $this->parseComponent($duration['month'] ?? '-');
        $days = $this->parseComponent($duration['day'] ?? '-');

        if ($years > 0) {
            $result->addYears($years);
        }

        if ($months > 0) {
            $result->addMonths($months);
        }

        if ($days > 0) {
            $result->addDays($days);
        }

        return $result;
    }

    /**
     * @param  array{year?: string, month?: string, day?: string}  $duration
     */
    private function durationIsEmpty(array $duration): bool
    {
        return $this->parseComponent($duration['year'] ?? '-') === 0
            && $this->parseComponent($duration['month'] ?? '-') === 0
            && $this->parseComponent($duration['day'] ?? '-') === 0;
    }

    private function sentenceLabelToDays(string $label): int
    {
        if (preg_match('/^(\d+)\s*ወር/u', $label, $matches) === 1) {
            return (int) $matches[1] * self::DAYS_PER_MONTH;
        }

        if (preg_match('/^(\d+)\s*ዓመት/u', $label, $matches) === 1) {
            return (int) $matches[1] * self::DAYS_PER_YEAR;
        }

        return 0;
    }

    private function parseComponent(string $value): int
    {
        if ($value === '-' || $value === '') {
            return 0;
        }

        return max(0, (int) $value);
    }
}
