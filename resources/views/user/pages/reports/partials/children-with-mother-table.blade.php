<table class="cwm-report-table">
    <thead>
        <tr>
            <th rowspan="2" class="cwm-col-no">{{ __('app.reports.children_with_mother_columns.no') }}</th>
            <th rowspan="2" class="cwm-col-child-name">{{ __('app.reports.children_with_mother_columns.child_name') }}</th>
            <th rowspan="2" class="cwm-col-gender">{{ __('app.reports.children_with_mother_columns.gender') }}</th>
            <th rowspan="2" class="cwm-col-age">{{ __('app.reports.children_with_mother_columns.age') }}</th>
            <th rowspan="2" class="cwm-col-mother-name">{{ __('app.reports.children_with_mother_columns.mother_name') }}</th>
            <th rowspan="2" class="cwm-col-mother-crime">{{ __('app.reports.children_with_mother_columns.mother_crime_type') }}</th>
            <th rowspan="2" class="cwm-col-mother-sentence">{{ __('app.reports.children_with_mother_columns.mother_sentence') }}</th>
            <th colspan="3" class="cwm-group-head">{{ __('app.reports.children_with_mother_columns.admission_date') }}</th>
            <th colspan="3" class="cwm-group-head">{{ __('app.reports.children_with_mother_columns.stay_duration') }}</th>
            <th rowspan="2" class="cwm-col-remark">{{ __('app.reports.children_with_mother_columns.remark') }}</th>
        </tr>
        <tr>
            <th class="cwm-date-part">{{ __('app.reports.children_with_mother_columns.day') }}</th>
            <th class="cwm-date-part">{{ __('app.reports.children_with_mother_columns.month') }}</th>
            <th class="cwm-date-part">{{ __('app.reports.children_with_mother_columns.year') }}</th>
            <th class="cwm-date-part">{{ __('app.reports.children_with_mother_columns.day') }}</th>
            <th class="cwm-date-part">{{ __('app.reports.children_with_mother_columns.month') }}</th>
            <th class="cwm-date-part">{{ __('app.reports.children_with_mother_columns.year') }}</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($report['rows'] as $row)
            <tr>
                <td class="cwm-cell-no">{{ $row['no'] }}</td>
                <td class="cwm-cell-child-name">{{ $row['child_name'] }}</td>
                <td class="cwm-cell-gender">{{ $row['gender'] }}</td>
                <td class="cwm-cell-age">{{ $row['age'] }}</td>
                <td class="cwm-cell-mother-name">{{ $row['mother_name'] }}</td>
                <td class="cwm-cell-mother-crime">{{ $row['mother_crime_type'] }}</td>
                <td class="cwm-cell-mother-sentence">{{ $row['mother_sentence'] }}</td>
                <td>{{ $row['admission']['day'] }}</td>
                <td>{{ $row['admission']['month'] }}</td>
                <td>{{ $row['admission']['year'] }}</td>
                <td>{{ $row['stay']['day'] }}</td>
                <td>{{ $row['stay']['month'] }}</td>
                <td>{{ $row['stay']['year'] }}</td>
                <td class="cwm-cell-remark">{{ $row['remark'] }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="14" class="cwm-empty-row">{{ __('app.reports.children_with_mother_columns.no_records') }}</td>
            </tr>
        @endforelse
    </tbody>
    @if ($report['total'] > 0)
        <tfoot>
            <tr class="cwm-total-row">
                <td colspan="14" class="cwm-cell-total-label">
                    {{ __('app.reports.children_with_mother_columns.total_count', ['count' => $report['total']]) }}
                </td>
            </tr>
        </tfoot>
    @endif
</table>
