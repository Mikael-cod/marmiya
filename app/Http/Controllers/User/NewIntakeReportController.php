<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\User\Concerns\ResolvesEthiopianReportPeriod;
use App\Services\NewIntakeReportService;
use App\Support\EthiopianCalendar;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NewIntakeReportController extends Controller
{
    use ResolvesEthiopianReportPeriod;

    public function __construct(
        private readonly NewIntakeReportService $reportService,
    ) {}

    public function index(Request $request): View
    {
        [$ethYear, $ethMonth] = $this->resolvePeriod($request);

        return view('user.pages.reports.new-intake', [
            'title' => __('app.reports.new_intake'),
            'description' => __('app.reports.new_intake_description'),
            'report' => $this->reportService->build($ethYear, $ethMonth),
            'ethYear' => $ethYear,
            'ethMonth' => $ethMonth,
            'ethYears' => range(EthiopianCalendar::currentYear() - 5, EthiopianCalendar::currentYear() + 1),
            'ethMonths' => EthiopianCalendar::monthNames(),
        ]);
    }

    public function export(Request $request): View
    {
        [$ethYear, $ethMonth] = $this->resolvePeriod($request);

        return view('user.pages.reports.new-intake-export', [
            'report' => $this->reportService->build($ethYear, $ethMonth),
            'ethYear' => $ethYear,
            'ethMonth' => $ethMonth,
        ]);
    }

}
