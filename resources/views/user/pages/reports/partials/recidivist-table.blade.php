<table class="rc-report-table">
    <thead>
        <tr>
            <th rowspan="2" class="rc-col-no">{{ __('app.reports.recidivist_columns.no') }}</th>
            <th rowspan="2" class="rc-col-name">{{ __('app.reports.recidivist_columns.full_name') }}</th>
            <th rowspan="2" class="rc-col-gender">{{ __('app.reports.recidivist_columns.gender') }}</th>
            <th rowspan="2" class="rc-col-crime">{{ __('app.reports.recidivist_columns.crime_type') }}</th>
            <th rowspan="2" class="rc-col-court">{{ __('app.reports.recidivist_columns.sentencing_court') }}</th>
            <th rowspan="2" class="rc-col-sentence">{{ __('app.reports.recidivist_columns.sentence_amount') }}</th>
            <th colspan="3" class="rc-group-head">{{ __('app.reports.recidivist_columns.admission_date') }}</th>
            <th rowspan="2" class="rc-col-remark">{{ __('app.reports.recidivist_columns.remark') }}</th>
        </tr>
        <tr>
            <th class="rc-date-part">{{ __('app.reports.recidivist_columns.day') }}</th>
            <th class="rc-date-part">{{ __('app.reports.recidivist_columns.month') }}</th>
            <th class="rc-date-part">{{ __('app.reports.recidivist_columns.year') }}</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($report['rows'] as $row)
            <tr>
                <td class="rc-cell-no">{{ $row['no'] }}</td>
                <td class="rc-cell-name">{{ $row['full_name'] }}</td>
                <td class="rc-cell-gender">{{ $row['gender'] }}</td>
                <td class="rc-cell-crime">{{ $row['crime_type'] }}</td>
                <td class="rc-cell-court">{{ $row['sentencing_court'] }}</td>
                <td class="rc-cell-sentence">{{ $row['sentence_amount'] }}</td>
                <td>{{ $row['admission']['day'] }}</td>
                <td>{{ $row['admission']['month'] }}</td>
                <td>{{ $row['admission']['year'] }}</td>
                <td class="rc-cell-remark">{{ $row['remark'] }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="10" class="rc-empty-row">{{ __('app.reports.recidivist_columns.no_records') }}</td>
            </tr>
        @endforelse
    </tbody>
    @if ($report['total'] > 0)
        <tfoot>
            <tr class="rc-total-row">
                <td colspan="10" class="rc-cell-total-label">
                    {{ __('app.reports.recidivist_columns.total_count', ['count' => $report['total']]) }}
                </td>
            </tr>
        </tfoot>
    @endif
</table>
