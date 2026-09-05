<?php

namespace App\Http\Controllers\User\Concerns;

use App\Support\EthiopianCalendar;
use Illuminate\Http\Request;

trait ResolvesEthiopianReportPeriod
{
    /**
     * @return array{0: int, 1: int}
     */
    protected function resolvePeriod(Request $request): array
    {
        $now = EthiopianCalendar::now();
        $defaultYear = (int) ($request->user()?->preference('report_eth_year') ?? $now->getYear());
        $defaultMonth = (int) ($request->user()?->preference('report_eth_month') ?? $now->getMonth());

        $year = (int) $request->query('eth_year', $defaultYear);
        $month = (int) $request->query('eth_month', $defaultMonth);

        if ($month < 1 || $month > 13) {
            $month = $defaultMonth >= 1 && $defaultMonth <= 13 ? $defaultMonth : $now->getMonth();
        }

        if ($year < 2000 || $year > 2100) {
            $year = $defaultYear >= 2000 && $defaultYear <= 2100 ? $defaultYear : $now->getYear();
        }

        return [$year, $month];
    }
}
