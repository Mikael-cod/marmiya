<table class="parole-released-report-table">
    <thead>
        <tr>
            <th rowspan="2" class="pr-col-no">{{ __('app.reports.parole_released_columns.no') }}</th>
            <th rowspan="2" class="pr-col-inst-file">{{ __('app.reports.parole_released_columns.institution_file_number') }}</th>
            <th rowspan="2" class="pr-col-court-file">{{ __('app.reports.parole_released_columns.court_file_number') }}</th>
            <th rowspan="2" class="pr-col-name">{{ __('app.reports.parole_released_columns.full_name') }}</th>
            <th rowspan="2" class="pr-col-gender">{{ __('app.reports.parole_released_columns.gender') }}</th>
            <th colspan="3" class="pr-group-head">{{ __('app.reports.parole_released_columns.admission_date') }}</th>
            <th colspan="3" class="pr-group-head">{{ __('app.reports.parole_released_columns.release_date') }}</th>
            <th rowspan="2" class="pr-col-crime">{{ __('app.reports.parole_released_columns.crime_type') }}</th>
            <th rowspan="2" class="pr-col-sentence">{{ __('app.reports.parole_released_columns.sentence_duration') }}</th>
            <th colspan="3" class="pr-group-head">{{ __('app.reports.parole_released_columns.stay_duration') }}</th>
            <th colspan="3" class="pr-group-head">{{ __('app.reports.parole_released_columns.reduction') }}</th>
            <th rowspan="2" class="pr-col-institution">{{ __('app.reports.parole_released_columns.institution') }}</th>
            <th rowspan="2" class="pr-col-court">{{ __('app.reports.parole_released_columns.release_court') }}</th>
        </tr>
        <tr>
            @foreach (range(1, 4) as $group)
                <th class="pr-date-part">{{ __('app.reports.parole_released_columns.day') }}</th>
                <th class="pr-date-part">{{ __('app.reports.parole_released_columns.month') }}</th>
                <th class="pr-date-part">{{ __('app.reports.parole_released_columns.year') }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @forelse ($report['rows'] as $row)
            <tr>
                <td class="pr-cell-no">{{ $row['no'] }}</td>
                <td>{{ $row['institution_file_number'] }}</td>
                <td>{{ $row['court_file_number'] }}</td>
                <td class="pr-cell-name">{{ $row['full_name'] }}</td>
                <td class="pr-cell-gender">{{ $row['gender'] }}</td>
                <td>{{ $row['admission']['day'] }}</td>
                <td>{{ $row['admission']['month'] }}</td>
                <td>{{ $row['admission']['year'] }}</td>
                <td>{{ $row['release']['day'] }}</td>
                <td>{{ $row['release']['month'] }}</td>
                <td>{{ $row['release']['year'] }}</td>
                <td class="pr-cell-crime">{{ $row['crime_type'] }}</td>
                <td class="pr-cell-sentence">{{ $row['sentence_duration'] }}</td>
                <td>{{ $row['stay']['day'] }}</td>
                <td>{{ $row['stay']['month'] }}</td>
                <td>{{ $row['stay']['year'] }}</td>
                <td>{{ $row['reduction']['day'] }}</td>
                <td>{{ $row['reduction']['month'] }}</td>
                <td>{{ $row['reduction']['year'] }}</td>
                <td>{{ $row['institution'] }}</td>
                <td class="pr-cell-court">{{ $row['release_court'] }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="21" class="pr-empty-row">{{ __('app.reports.parole_released_columns.no_records') }}</td>
            </tr>
        @endforelse
    </tbody>
    @if ($report['total'] > 0)
        <tfoot>
            <tr class="pr-total-row">
                <td colspan="21" class="pr-cell-total-label">
                    {{ __('app.reports.parole_released_columns.total_count', ['count' => $report['total']]) }}
                </td>
            </tr>
        </tfoot>
    @endif
</table>
