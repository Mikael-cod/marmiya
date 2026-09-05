@php
    $displayCount = static fn (int $value): string => $value > 0 ? (string) $value : '';

    $countCategories = config('released_report.count_categories', []);
    $transferCategory = config('released_report.transfer_category', 'transferred');
    $categoryLabels = config('release_reasons', []);
@endphp

<table class="released-report-table">
    <thead>
        <tr>
            <th rowspan="2" class="rel-col-no">{{ __('app.reports.released_columns.no') }}</th>
            <th rowspan="2" class="rel-col-facility">{{ __('app.reports.released_columns.facility') }}</th>
            <th rowspan="2" class="rel-col-nationality">{{ __('app.reports.released_columns.nationality_status') }}</th>
            @foreach ($countCategories as $category)
                <th colspan="3" class="rel-group-head">{{ $categoryLabels[$category] ?? $category }}</th>
            @endforeach
            <th colspan="3" class="rel-group-head rel-group-total">{{ __('app.reports.released_columns.row_total') }}</th>
            <th colspan="3" class="rel-group-head">{{ $categoryLabels[$transferCategory] ?? $transferCategory }}</th>
        </tr>
        <tr>
            @foreach (range(1, count($countCategories) + 2) as $groupIndex)
                @foreach (['male', 'female', 'total'] as $genderIndex => $gender)
                    <th @class([
                        'rel-head-gender',
                        'rel-section-start' => $genderIndex === 0 && $groupIndex > count($countCategories),
                    ])>{{ __('app.reports.released_columns.'.$gender.'_short') }}</th>
                @endforeach
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach (['ethiopian', 'foreign'] as $nationalityKey)
            @php($row = $report['nationality_rows'][$nationalityKey])
            <tr>
                @if ($loop->first)
                    <td rowspan="2" class="rel-cell-no">1</td>
                    <td rowspan="2" class="rel-cell-facility">{{ $report['facility'] }}</td>
                @endif
                <td class="rel-cell-nationality">{{ $row['label'] }}</td>
                @foreach ($countCategories as $category)
                    @foreach (['male', 'female', 'total'] as $gender)
                        <td class="rel-cell-num">{{ $displayCount($row['categories'][$category][$gender]) }}</td>
                    @endforeach
                @endforeach
                @foreach (['male', 'female', 'total'] as $gender)
                    <td class="rel-cell-num rel-section-start">{{ $displayCount($row['row_total'][$gender]) }}</td>
                @endforeach
                @foreach (['male', 'female', 'total'] as $gender)
                    <td class="rel-cell-num">{{ $displayCount($row['categories'][$transferCategory][$gender]) }}</td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        @foreach (['ethiopian', 'foreign'] as $nationalityKey)
            @php($footerRow = $report['footer'][$nationalityKey])
            <tr class="rel-total-row">
                <td colspan="2" class="rel-cell-total-label">{{ __('app.reports.released_columns.footer_total') }}</td>
                <td class="rel-cell-nationality">{{ $footerRow['label'] }}</td>
                @foreach ($countCategories as $category)
                    @foreach (['male', 'female', 'total'] as $gender)
                        <td class="rel-cell-num">{{ $displayCount($footerRow['categories'][$category][$gender]) }}</td>
                    @endforeach
                @endforeach
                @foreach (['male', 'female', 'total'] as $gender)
                    <td class="rel-cell-num rel-section-start">{{ $displayCount($footerRow['row_total'][$gender]) }}</td>
                @endforeach
                @foreach (['male', 'female', 'total'] as $gender)
                    <td class="rel-cell-num">{{ $displayCount($footerRow['categories'][$transferCategory][$gender]) }}</td>
                @endforeach
            </tr>
        @endforeach
        @php($grand = $report['footer']['grand'])
        <tr class="rel-grand-row">
            <td colspan="3" class="rel-cell-total-label">{{ __('app.reports.released_columns.grand_total') }}</td>
            @foreach ($countCategories as $category)
                @foreach (['male', 'female', 'total'] as $gender)
                    <td class="rel-cell-num">{{ $displayCount($grand['categories'][$category][$gender]) }}</td>
                @endforeach
            @endforeach
            @foreach (['male', 'female', 'total'] as $gender)
                <td class="rel-cell-num rel-section-start">{{ $displayCount($grand['row_total'][$gender]) }}</td>
            @endforeach
            @foreach (['male', 'female', 'total'] as $gender)
                <td class="rel-cell-num">{{ $displayCount($grand['categories'][$transferCategory][$gender]) }}</td>
            @endforeach
        </tr>
    </tfoot>
</table>
