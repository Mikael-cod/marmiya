<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\User\Concerns\ResolvesEthiopianReportPeriod;
use App\Services\CrimeTypeReportService;
use App\Support\EthiopianCalendar;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CrimeTypeReportController extends Controller
{
    use ResolvesEthiopianReportPeriod;

    public function __construct(
        private readonly CrimeTypeReportService $reportService,
    ) {}

    public function index(Request $request): View
    {
        [$ethYear, $ethMonth] = $this->resolvePeriod($request);
        $report = $this->reportService->build($ethYear, $ethMonth);

        return view('user.pages.reports.crime-type', [
            'title' => __('app.reports.crime_type'),
            'description' => __('app.reports.crime_type_description'),
            'report' => $report,
            'ethYear' => $ethYear,
            'ethMonth' => $ethMonth,
            'ethYears' => range(EthiopianCalendar::currentYear() - 5, EthiopianCalendar::currentYear() + 1),
            'ethMonths' => EthiopianCalendar::monthNames(),
        ]);
    }

    public function export(Request $request): View
    {
        [$ethYear, $ethMonth] = $this->resolvePeriod($request);

        return view('user.pages.reports.crime-type-export', [
            'report' => $this->reportService->build($ethYear, $ethMonth),
            'ethYear' => $ethYear,
            'ethMonth' => $ethMonth,
        ]);
    }
}
