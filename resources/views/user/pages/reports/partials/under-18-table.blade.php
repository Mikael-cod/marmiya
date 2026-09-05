<table class="under-18-report-table">
    <thead>
        <tr>
            <th rowspan="2" class="u18-col-no">{{ __('app.reports.under_18_columns.no') }}</th>
            <th rowspan="2" class="u18-col-name">{{ __('app.reports.under_18_columns.full_name') }}</th>
            <th rowspan="2" class="u18-col-gender">{{ __('app.reports.under_18_columns.gender') }}</th>
            <th rowspan="2" class="u18-col-age">{{ __('app.reports.under_18_columns.age') }}</th>
            <th rowspan="2" class="u18-col-crime">{{ __('app.reports.under_18_columns.crime_type') }}</th>
            <th rowspan="2" class="u18-col-sentence">{{ __('app.reports.under_18_columns.sentence_duration') }}</th>
            <th colspan="3" class="u18-group-head">{{ __('app.reports.under_18_columns.admission_date') }}</th>
            <th colspan="3" class="u18-group-head">{{ __('app.reports.under_18_columns.stay_date') }}</th>
            <th rowspan="2" class="u18-col-family">{{ __('app.reports.under_18_columns.family_status') }}</th>
        </tr>
        <tr>
            <th class="u18-date-part">{{ __('app.reports.under_18_columns.day') }}</th>
            <th class="u18-date-part">{{ __('app.reports.under_18_columns.month') }}</th>
            <th class="u18-date-part">{{ __('app.reports.under_18_columns.year') }}</th>
            <th class="u18-date-part">{{ __('app.reports.under_18_columns.day') }}</th>
            <th class="u18-date-part">{{ __('app.reports.under_18_columns.month') }}</th>
            <th class="u18-date-part">{{ __('app.reports.under_18_columns.year') }}</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($report['rows'] as $row)
            <tr>
                <td class="u18-cell-no">{{ $row['no'] }}</td>
                <td class="u18-cell-name">{{ $row['full_name'] }}</td>
                <td class="u18-cell-gender">{{ $row['gender'] }}</td>
                <td class="u18-cell-age">{{ $row['age'] }}</td>
                <td class="u18-cell-crime">{{ $row['crime_type'] }}</td>
                <td class="u18-cell-sentence">{{ $row['sentence_duration'] }}</td>
                <td>{{ $row['admission']['day'] }}</td>
                <td>{{ $row['admission']['month'] }}</td>
                <td>{{ $row['admission']['year'] }}</td>
                <td>{{ $row['stay']['day'] }}</td>
                <td>{{ $row['stay']['month'] }}</td>
                <td>{{ $row['stay']['year'] }}</td>
                <td class="u18-cell-family">{{ $row['family_status'] }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="13" class="u18-empty-row">{{ __('app.reports.under_18_columns.no_records') }}</td>
            </tr>
        @endforelse
    </tbody>
    @if ($report['total'] > 0)
        <tfoot>
            <tr class="u18-total-row">
                <td colspan="13" class="u18-cell-total-label">
                    {{ __('app.reports.under_18_columns.total_count', ['count' => $report['total']]) }}
                </td>
            </tr>
        </tfoot>
    @endif
</table>
