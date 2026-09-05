<table class="ds-report-table">
    <thead>
        <tr>
            <th rowspan="2" class="ds-col-no">{{ __('app.reports.death_sentenced_columns.no') }}</th>
            <th rowspan="2" class="ds-col-name">{{ __('app.reports.death_sentenced_columns.full_name') }}</th>
            <th rowspan="2" class="ds-col-gender">{{ __('app.reports.death_sentenced_columns.gender') }}</th>
            <th rowspan="2" class="ds-col-age">{{ __('app.reports.death_sentenced_columns.age') }}</th>
            <th rowspan="2" class="ds-col-nationality">{{ __('app.reports.death_sentenced_columns.nationality') }}</th>
            <th rowspan="2" class="ds-col-crime">{{ __('app.reports.death_sentenced_columns.crime_type') }}</th>
            <th rowspan="2" class="ds-col-file">{{ __('app.reports.death_sentenced_columns.file_number') }}</th>
            <th rowspan="2" class="ds-col-court">{{ __('app.reports.death_sentenced_columns.verdict_court') }}</th>
            <th rowspan="2" class="ds-col-sentence">{{ __('app.reports.death_sentenced_columns.sentence_amount') }}</th>
            <th colspan="3" class="ds-group-head">{{ __('app.reports.death_sentenced_columns.admission_date') }}</th>
            <th rowspan="2" class="ds-col-station">{{ __('app.reports.death_sentenced_columns.station_stay') }}</th>
            <th rowspan="2" class="ds-col-total">{{ __('app.reports.death_sentenced_columns.total_prison_time') }}</th>
            <th rowspan="2" class="ds-col-death">{{ __('app.reports.death_sentenced_columns.death_separation') }}</th>
        </tr>
        <tr>
            <th class="ds-date-part">{{ __('app.reports.death_sentenced_columns.day') }}</th>
            <th class="ds-date-part">{{ __('app.reports.death_sentenced_columns.month') }}</th>
            <th class="ds-date-part">{{ __('app.reports.death_sentenced_columns.year') }}</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($report['rows'] as $row)
            <tr>
                <td class="ds-cell-no">{{ $row['no'] }}</td>
                <td class="ds-cell-name">{{ $row['full_name'] }}</td>
                <td class="ds-cell-gender">{{ $row['gender'] }}</td>
                <td class="ds-cell-age">{{ $row['age'] }}</td>
                <td class="ds-cell-nationality">{{ $row['nationality'] }}</td>
                <td class="ds-cell-crime">{{ $row['crime_type'] }}</td>
                <td class="ds-cell-file">{{ $row['file_number'] }}</td>
                <td class="ds-cell-court">{{ $row['verdict_court'] }}</td>
                <td class="ds-cell-sentence">{{ $row['sentence_amount'] }}</td>
                <td>{{ $row['admission']['day'] }}</td>
                <td>{{ $row['admission']['month'] }}</td>
                <td>{{ $row['admission']['year'] }}</td>
                <td class="ds-cell-station">{{ $row['station_stay'] }}</td>
                <td class="ds-cell-total">{{ $row['total_prison_time'] }}</td>
                <td class="ds-cell-death">{{ $row['death_separation'] }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="15" class="ds-empty-row">{{ __('app.reports.death_sentenced_columns.no_records') }}</td>
            </tr>
        @endforelse
    </tbody>
    @if ($report['total'] > 0)
        <tfoot>
            <tr class="ds-total-row">
                <td colspan="15" class="ds-cell-total-label">
                    {{ __('app.reports.death_sentenced_columns.total_count', ['count' => $report['total']]) }}
                </td>
            </tr>
        </tfoot>
    @endif
</table>
