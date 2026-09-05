@php
    $schedule = config('parole_schedule');
@endphp

<x-layouts.user :title="$title">
    <section class="overflow-hidden rounded-3xl border border-brand-border bg-brand-surface shadow-auth-card">
        <div class="relative px-6 py-8 sm:px-8 sm:py-10">
            <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-brand-blue/5 via-transparent to-brand-teal/5"></div>
            <div class="relative">
                <h1 class="text-2xl font-bold text-brand-dark sm:text-3xl">{{ $title }}</h1>
                <p class="mt-3 max-w-3xl text-sm leading-relaxed text-brand-muted sm:text-base">
                    {{ $description }}
                </p>
            </div>
        </div>
    </section>

    <section class="card-surface parole-schedule-card mt-6 shadow-auth-card">
        <div class="parole-schedule-sheet">
            <h2 class="parole-schedule-title">{{ $schedule['title'] }}</h2>

            <div class="parole-schedule-table-wrap">
                <table class="parole-schedule-table">
                    <thead>
                        <tr>
                            <th rowspan="2" class="parole-col-no">{{ __('app.recommendations.table.no') }}</th>
                            <th rowspan="2" class="parole-col-sentence">{{ __('app.recommendations.table.sentence_amount') }}</th>
                            <th colspan="3" class="parole-group-head">{{ __('app.recommendations.table.deducted_by_parole') }}</th>
                            <th colspan="3" class="parole-group-head">{{ __('app.recommendations.table.to_be_served') }}</th>
                            <th rowspan="2" class="parole-col-remark">{{ __('app.recommendations.table.remark') }}</th>
                        </tr>
                        <tr>
                            <th>{{ __('app.recommendations.table.year') }}</th>
                            <th>{{ __('app.recommendations.table.month') }}</th>
                            <th>{{ __('app.recommendations.table.day') }}</th>
                            <th>{{ __('app.recommendations.table.year') }}</th>
                            <th>{{ __('app.recommendations.table.month') }}</th>
                            <th>{{ __('app.recommendations.table.day') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($schedule['rows'] as $row)
                            <tr>
                                <td class="parole-cell-no">{{ $row['no'] }}</td>
                                <td class="parole-cell-sentence">{{ $row['sentence'] }}</td>
                                <td class="parole-cell-num">{{ $row['deducted']['year'] }}</td>
                                <td class="parole-cell-num">{{ $row['deducted']['month'] }}</td>
                                <td class="parole-cell-num">{{ $row['deducted']['day'] }}</td>
                                <td class="parole-cell-num">{{ $row['served']['year'] }}</td>
                                <td class="parole-cell-num">{{ $row['served']['month'] }}</td>
                                <td class="parole-cell-num">{{ $row['served']['day'] }}</td>
                                <td class="parole-cell-remark">&nbsp;</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</x-layouts.user>
