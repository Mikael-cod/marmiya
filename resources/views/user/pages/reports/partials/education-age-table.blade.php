@php
    $displayCount = static fn (int $value): string => $value > 0 ? (string) $value : '';
    $educationLevels = config('education_age_report.education_levels', []);
    $ageGroups = config('education_age_report.age_groups', []);
    $educationColspan = (count($educationLevels) + 1) * 2;
    $ageColspan = (count($ageGroups) + 1) * 2;
@endphp

<table class="education-age-report-table">
    <thead>
        <tr>
            <th rowspan="3" class="edu-col-no">{{ __('app.reports.education_age_columns.no') }}</th>
            <th rowspan="3" class="edu-col-institution">{{ __('app.reports.education_age_columns.institution') }}</th>
            <th colspan="{{ $educationColspan }}" class="edu-group-head edu-group-education">{{ __('app.reports.education_age_columns.education_level') }}</th>
            <th colspan="{{ $ageColspan }}" class="edu-group-head edu-group-age">{{ __('app.reports.education_age_columns.age') }}</th>
        </tr>
        <tr>
            @foreach ($educationLevels as $level)
                <th colspan="2" class="edu-subhead edu-group-education">{{ $level }}</th>
            @endforeach
            <th colspan="2" class="edu-subhead edu-group-education">{{ __('app.reports.education_age_columns.subtotal') }}</th>

            @foreach ($ageGroups as $label)
                <th colspan="2" class="edu-subhead edu-group-age">{{ $label }}</th>
            @endforeach
            <th colspan="2" class="edu-subhead edu-group-age">{{ __('app.reports.education_age_columns.subtotal') }}</th>
        </tr>
        <tr>
            @foreach (range(1, count($educationLevels) + 1) as $i)
                <th class="edu-head-gender edu-group-education">{{ __('app.reports.education_age_columns.male_short') }}</th>
                <th class="edu-head-gender edu-group-education">{{ __('app.reports.education_age_columns.female_short') }}</th>
            @endforeach
            @foreach (range(1, count($ageGroups) + 1) as $i)
                <th class="edu-head-gender edu-group-age">{{ __('app.reports.education_age_columns.male_short') }}</th>
                <th class="edu-head-gender edu-group-age">{{ __('app.reports.education_age_columns.female_short') }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($report['rows'] as $row)
            <tr>
                <td class="edu-cell-no">{{ $row['no'] }}</td>
                <td class="edu-cell-institution">{{ $row['institution'] }}</td>
                @foreach ($educationLevels as $level)
                    <td>{{ $displayCount($row['education'][$level]['male']) }}</td>
                    <td>{{ $displayCount($row['education'][$level]['female']) }}</td>
                @endforeach
                <td>{{ $displayCount($row['education']['subtotal']['male']) }}</td>
                <td>{{ $displayCount($row['education']['subtotal']['female']) }}</td>
                @foreach (array_keys($ageGroups) as $group)
                    <td>{{ $displayCount($row['age'][$group]['male']) }}</td>
                    <td>{{ $displayCount($row['age'][$group]['female']) }}</td>
                @endforeach
                <td>{{ $displayCount($row['age']['subtotal']['male']) }}</td>
                <td>{{ $displayCount($row['age']['subtotal']['female']) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        @php($totals = $report['grand_total'])
        <tr class="edu-total-row">
            <td colspan="2" class="edu-cell-total-label">{{ __('app.reports.education_age_columns.grand_total') }}</td>
            @foreach ($educationLevels as $level)
                <td>{{ $displayCount($totals['education'][$level]['male']) }}</td>
                <td>{{ $displayCount($totals['education'][$level]['female']) }}</td>
            @endforeach
            <td>{{ $displayCount($totals['education']['subtotal']['male']) }}</td>
            <td>{{ $displayCount($totals['education']['subtotal']['female']) }}</td>
            @foreach (array_keys($ageGroups) as $group)
                <td>{{ $displayCount($totals['age'][$group]['male']) }}</td>
                <td>{{ $displayCount($totals['age'][$group]['female']) }}</td>
            @endforeach
            <td>{{ $displayCount($totals['age']['subtotal']['male']) }}</td>
            <td>{{ $displayCount($totals['age']['subtotal']['female']) }}</td>
        </tr>
    </tfoot>
</table>
