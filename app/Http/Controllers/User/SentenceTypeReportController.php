<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\User\Concerns\ResolvesEthiopianReportPeriod;
use App\Services\SentenceTypeReportService;
use App\Support\EthiopianCalendar;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SentenceTypeReportController extends Controller
{
    use ResolvesEthiopianReportPeriod;

    public function __construct(
        private readonly SentenceTypeReportService $reportService,
    ) {}

    public function index(Request $request): View
    {
        [$ethYear, $ethMonth] = $this->resolvePeriod($request);
        $report = $this->reportService->build($ethYear, $ethMonth);

        return view('user.pages.reports.sentence-type', [
            'title' => __('app.reports.sentence_type'),
            'description' => __('app.reports.sentence_type_description'),
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

        return view('user.pages.reports.sentence-type-export', [
            'report' => $this->reportService->build($ethYear, $ethMonth),
            'ethYear' => $ethYear,
            'ethMonth' => $ethMonth,
        ]);
    }
}
