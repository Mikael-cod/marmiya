@php
    $displayCount = static fn (int $value): string => $value > 0 ? (string) $value : '';
@endphp

<table class="sentence-type-report-table">
    <thead>
        <tr>
            <th rowspan="2" class="st-col-no">{{ __('app.reports.sentence_type_columns.no') }}</th>
            <th rowspan="2" class="st-col-type">{{ __('app.reports.sentence_type_columns.sentence_type') }}</th>
            <th colspan="3" class="st-group-head">{{ __('app.reports.sentence_type_columns.prisoner_count') }}</th>
            <th rowspan="2" class="st-col-remark">{{ __('app.reports.sentence_type_columns.remark') }}</th>
        </tr>
        <tr>
            <th class="st-head-gender">{{ __('app.reports.sentence_type_columns.male') }}</th>
            <th class="st-head-gender">{{ __('app.reports.sentence_type_columns.female') }}</th>
            <th class="st-head-gender">{{ __('app.reports.sentence_type_columns.total') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($report['rows'] as $row)
            <tr>
                <td class="st-cell-no">{{ $row['no'] }}</td>
                <td class="st-cell-type">{{ $row['sentence_type'] }}</td>
                <td>{{ $displayCount($row['male']) }}</td>
                <td>{{ $displayCount($row['female']) }}</td>
                <td>{{ $displayCount($row['total']) }}</td>
                <td class="st-cell-remark"></td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        @php($totals = $report['grand_total'])
        <tr class="st-total-row">
            <td colspan="2" class="st-cell-total-label">{{ __('app.reports.sentence_type_columns.grand_total') }}</td>
            <td>{{ $displayCount($totals['male']) }}</td>
            <td>{{ $displayCount($totals['female']) }}</td>
            <td>{{ $displayCount($totals['total']) }}</td>
            <td class="st-cell-remark"></td>
        </tr>
    </tfoot>
</table>
