<?php

namespace App\Services;

use Illuminate\Support\Carbon;

/**
 * @deprecated Use ParoleScheduleService directly.
 */
class ParoleReleaseDateCalculator
{
    public function __construct(
        private readonly ParoleScheduleService $schedule,
    ) {}

    public function calculate(
        Carbon|string $sentenceStart,
        Carbon|string $sentenceEnd,
        ?string $sentenceDuration = null,
    ): ?Carbon {
        return $this->schedule->calculateParoleReleaseDate(
            $sentenceStart,
            $sentenceEnd,
            $sentenceDuration,
        );
    }
}
