@php
    $displayCount = static fn (int $value): string => $value > 0 ? (string) $value : '';

    $statusSections = [
        'convicted' => __('app.reports.crime_type_columns.convicted'),
        'remand' => __('app.reports.crime_type_columns.remand'),
        'both' => __('app.reports.crime_type_columns.both'),
        'subtotal' => __('app.reports.crime_type_columns.subtotal'),
    ];
@endphp

<table class="crime-type-report-table">
    <thead>
        <tr>
            <th rowspan="3" class="crime-col-no">{{ __('app.reports.crime_type_columns.no') }}</th>
            <th rowspan="3" class="crime-col-type">{{ __('app.reports.crime_type_columns.crime_type') }}</th>
            @foreach ($statusSections as $sectionKey => $label)
                <th colspan="6" class="crime-group-head crime-group-{{ $sectionKey }}">{{ $label }}</th>
            @endforeach
            <th colspan="3" class="crime-group-head crime-group-grand">{{ __('app.reports.crime_type_columns.grand_total') }}</th>
        </tr>
        <tr>
            @foreach ($statusSections as $sectionKey => $section)
                <th colspan="3" class="crime-subhead-ethiopian crime-group-{{ $sectionKey }}">{{ __('app.reports.crime_type_columns.ethiopian') }}</th>
                <th colspan="3" class="crime-subhead-foreign crime-group-{{ $sectionKey }}">{{ __('app.reports.crime_type_columns.foreign') }}</th>
            @endforeach
            <th rowspan="2" class="crime-head-gender crime-group-grand">{{ __('app.reports.crime_type_columns.male_short') }}</th>
            <th rowspan="2" class="crime-head-gender crime-group-grand">{{ __('app.reports.crime_type_columns.female_short') }}</th>
            <th rowspan="2" class="crime-head-gender crime-group-grand">{{ __('app.reports.crime_type_columns.total_short') }}</th>
        </tr>
        <tr>
            @foreach ($statusSections as $sectionKey => $section)
                <th class="crime-head-gender crime-group-{{ $sectionKey }}">{{ __('app.reports.crime_type_columns.male_short') }}</th>
                <th class="crime-head-gender crime-group-{{ $sectionKey }}">{{ __('app.reports.crime_type_columns.female_short') }}</th>
                <th class="crime-head-gender crime-group-{{ $sectionKey }}">{{ __('app.reports.crime_type_columns.total_short') }}</th>
                <th class="crime-head-gender crime-group-{{ $sectionKey }}">{{ __('app.reports.crime_type_columns.male_short') }}</th>
                <th class="crime-head-gender crime-group-{{ $sectionKey }}">{{ __('app.reports.crime_type_columns.female_short') }}</th>
                <th class="crime-head-gender crime-group-{{ $sectionKey }}">{{ __('app.reports.crime_type_columns.total_short') }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($report['rows'] as $row)
            @php($counts = $row['counts'])
            <tr>
                <td class="crime-cell-no">{{ $row['no'] }}</td>
                <td class="crime-cell-type">{{ $row['crime_type'] }}</td>
                @foreach (['convicted', 'remand', 'both', 'subtotal'] as $section)
                    @foreach (['ethiopian', 'foreign'] as $nationalityIndex => $nationality)
                        @foreach (['male', 'female', 'total'] as $genderIndex => $gender)
                            <td @class([
                                'crime-cell-num',
                                'crime-section-start' => $nationalityIndex === 0 && $genderIndex === 0,
                            ])>{{ $displayCount($counts[$section][$nationality][$gender]) }}</td>
                        @endforeach
                    @endforeach
                @endforeach
                <td class="crime-cell-num crime-section-start">{{ $displayCount($counts['grand']['male']) }}</td>
                <td class="crime-cell-num">{{ $displayCount($counts['grand']['female']) }}</td>
                <td class="crime-cell-num">{{ $displayCount($counts['grand']['total']) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        @php($totals = $report['totals'])
        <tr class="crime-total-row">
            <td colspan="2" class="crime-cell-total-label">{{ __('app.reports.crime_type_columns.footer_total') }}</td>
            @foreach (['convicted', 'remand', 'both', 'subtotal'] as $section)
                @foreach (['ethiopian', 'foreign'] as $nationalityIndex => $nationality)
                    @foreach (['male', 'female', 'total'] as $genderIndex => $gender)
                        <td @class([
                            'crime-cell-num',
                            'crime-section-start' => $nationalityIndex === 0 && $genderIndex === 0,
                        ])>{{ $displayCount($totals[$section][$nationality][$gender]) }}</td>
                    @endforeach
                @endforeach
            @endforeach
            <td class="crime-cell-num crime-section-start">{{ $displayCount($totals['grand']['male']) }}</td>
            <td class="crime-cell-num">{{ $displayCount($totals['grand']['female']) }}</td>
            <td class="crime-cell-num">{{ $displayCount($totals['grand']['total']) }}</td>
        </tr>
    </tfoot>
</table>
