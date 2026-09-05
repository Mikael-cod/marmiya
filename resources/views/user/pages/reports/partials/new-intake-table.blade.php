@php
    $displayCount = static fn (int $value): string => $value > 0 ? (string) $value : '';

    $statusSections = [
        'convicted' => __('app.reports.new_intake_columns.convicted'),
        'remand' => __('app.reports.new_intake_columns.remand'),
        'both' => __('app.reports.new_intake_columns.both'),
        'subtotal' => __('app.reports.new_intake_columns.subtotal'),
    ];

    $dataColumnCount = (count($statusSections) * 6) + 3;
    $counts = $report['counts'];
@endphp

<table class="crime-type-report-table new-intake-report-table">
    <thead>
        <tr>
            <th rowspan="4" class="crime-col-no">{{ __('app.reports.new_intake_columns.no') }}</th>
            <th rowspan="4" class="ni-col-institution">{{ __('app.reports.new_intake_columns.institution') }}</th>
            <th colspan="{{ $dataColumnCount }}" class="ni-banner-head">{{ __('app.reports.new_intake_columns.banner') }}</th>
        </tr>
        <tr>
            @foreach ($statusSections as $sectionKey => $label)
                <th colspan="6" class="crime-group-head crime-group-{{ $sectionKey }}">{{ $label }}</th>
            @endforeach
            <th colspan="3" class="crime-group-head crime-group-grand">{{ __('app.reports.new_intake_columns.grand_total') }}</th>
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
        <tr>
            <td class="crime-cell-no">1</td>
            <td class="ni-cell-institution">{{ $report['institution'] }}</td>
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
    </tbody>
    <tfoot>
        <tr class="crime-total-row">
            <td colspan="2" class="crime-cell-total-label">{{ __('app.reports.new_intake_columns.footer_total') }}</td>
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
    </tfoot>
</table>
