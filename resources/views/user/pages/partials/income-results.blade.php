@php
    $listQuery = request()->only(['q', 'status', 'from', 'to', 'per_page', 'page']);
    $hasFilters = collect($filters)->filter(fn ($value, $key) => $key !== 'per_page' && filled($value))->isNotEmpty();
@endphp

@if ($registrations->total() > 0)
    <p class="mt-2 text-xs text-brand-muted" id="intake-results-summary">
        {{ __('app.income.results_summary', [
            'from' => $registrations->firstItem(),
            'to' => $registrations->lastItem(),
            'total' => $registrations->total(),
        ]) }}
    </p>
@else
    <p class="mt-2 hidden text-xs text-brand-muted" id="intake-results-summary"></p>
@endif

@if ($registrations->isEmpty())
    <div class="mt-8 rounded-2xl border border-dashed border-brand-border px-6 py-12 text-center">
        <p class="text-sm text-brand-muted">
            {{ $hasFilters || filled($filters['q']) ? __('app.income.no_search_results') : __('app.income.no_records') }}
        </p>
        @if (! $hasFilters && ! filled($filters['q']))
            <a href="{{ route('user.income') }}" data-intake-modal-open class="btn-primary-brand intake-register-btn mt-4">
                {{ __('app.income.register') }}
            </a>
        @endif
    </div>
@else
    <div class="mt-6 overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="border-b border-brand-border text-start text-brand-muted">
                    <th class="px-3 py-3 font-medium">#</th>
                    <th class="px-3 py-3 font-medium">{{ __('app.income.fields.photo') }}</th>
                    <th class="px-3 py-3 font-medium">{{ __('app.income.fields.full_name') }}</th>
                    <th class="px-3 py-3 font-medium">{{ __('app.income.fields.institution_file_number') }}</th>
                    <th class="px-3 py-3 font-medium">{{ __('app.income.fields.cell_number') }}</th>
                    <th class="px-3 py-3 font-medium">{{ __('app.income.fields.crime_type') }}</th>
                    <th class="px-3 py-3 font-medium">{{ __('app.income.fields.sentence_status') }}</th>
                    <th class="px-3 py-3 font-medium">{{ __('app.income.fields.admission_date') }}</th>
                    <th class="px-3 py-3 font-medium">{{ __('app.income.registered_at') }}</th>
                    <th class="px-3 py-3 font-medium text-end">{{ __('app.income.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($registrations as $registration)
                    <tr class="border-b border-brand-border/70 last:border-0">
                        <td class="px-3 py-3 text-brand-muted">{{ $registrations->firstItem() + $loop->index }}</td>
                        <td class="px-3 py-3">
                            <x-inmate-photo :registration="$registration" />
                        </td>
                        <td class="px-3 py-3 font-medium text-brand-dark">{{ $registration->full_name }}</td>
                        <td class="px-3 py-3 text-brand-muted">{{ $registration->institution_file_number ?: '—' }}</td>
                        <td class="px-3 py-3 text-brand-muted">{{ $registration->cell_number ?: '—' }}</td>
                        <td class="px-3 py-3 text-brand-muted">{{ $registration->crime_type ?: '—' }}</td>
                        <td class="px-3 py-3 text-brand-muted">
                            {{ $registration->sentence_status === 'convicted' ? __('app.income.status_convicted') : __('app.income.status_remand') }}
                        </td>
                        <td class="px-3 py-3 text-brand-muted"><x-eth.date :value="$registration->admission_date" /></td>
                        <td class="px-3 py-3 text-brand-muted"><x-eth.datetime :value="$registration->created_at" /></td>
                        <td class="px-3 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <a
                                    href="{{ route('user.income', array_merge($listQuery, ['edit' => $registration->id])) }}"
                                    class="intake-action-btn intake-action-edit"
                                    title="{{ __('app.income.edit') }}"
                                >
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 4H6a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-5M18.5 2.5a2.12 2.12 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                    <span class="hidden sm:inline">{{ __('app.income.edit') }}</span>
                                </a>

                                <form
                                    action="{{ route('user.income.destroy', $registration) }}"
                                    method="POST"
                                    class="inline"
                                    onsubmit="return confirm(@js(__('app.income.delete_confirm')))"
                                >
                                    @csrf
                                    @method('DELETE')
                                    @foreach ($listQuery as $key => $value)
                                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                    @endforeach
                                    <button type="submit" class="intake-action-btn intake-action-delete" title="{{ __('app.income.delete') }}">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m-7 0h8l-1-3H9l-1 3z"/>
                                        </svg>
                                        <span class="hidden sm:inline">{{ __('app.income.delete') }}</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6 border-t border-brand-border pt-4" data-intake-pagination>
        {{ $registrations->onEachSide(1)->links() }}
    </div>
@endif
