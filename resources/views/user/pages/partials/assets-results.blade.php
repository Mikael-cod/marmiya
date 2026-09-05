@php
    $listQuery = request()->only(['q', 'from', 'to', 'per_page', 'page']);
    $hasFilters = collect($filters)->filter(fn ($value, $key) => $key !== 'per_page' && filled($value))->isNotEmpty();

    $formatMoney = fn ($value) => filled($value) ? number_format((float) $value, 2).' '.__('app.assets.currency') : '—';
@endphp

@if ($properties->total() > 0)
    <p class="mt-2 text-xs text-brand-muted" id="intake-results-summary">
        {{ __('app.assets.results_summary', [
            'from' => $properties->firstItem(),
            'to' => $properties->lastItem(),
            'total' => $properties->total(),
        ]) }}
    </p>
@else
    <p class="mt-2 hidden text-xs text-brand-muted" id="intake-results-summary"></p>
@endif

@if ($properties->isEmpty())
    <div class="mt-8 rounded-2xl border border-dashed border-brand-border px-6 py-12 text-center">
        <p class="text-sm text-brand-muted">
            {{ $hasFilters || filled($filters['q']) ? __('app.assets.no_search_results') : __('app.assets.no_records') }}
        </p>
        @if (! $hasFilters && ! filled($filters['q']))
            <a href="{{ route('user.assets') }}" data-intake-modal-open class="btn-primary-brand intake-register-btn mt-4">
                {{ __('app.assets.register') }}
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
                    <th class="px-3 py-3 font-medium">{{ __('app.assets.fields.inmate') }}</th>
                    <th class="px-3 py-3 font-medium">{{ __('app.assets.fields.entry_cash_amount') }}</th>
                    <th class="px-3 py-3 font-medium">{{ __('app.assets.fields.form_85_number') }}</th>
                    <th class="px-3 py-3 font-medium">{{ __('app.assets.fields.deposit_amount') }}</th>
                    <th class="px-3 py-3 font-medium">{{ __('app.assets.fields.form_86_number') }}</th>
                    <th class="px-3 py-3 font-medium">{{ __('app.assets.fields.withdrawal_amount') }}</th>
                    <th class="px-3 py-3 font-medium">{{ __('app.assets.fields.other_property_receipt_number') }}</th>
                    <th class="px-3 py-3 font-medium">{{ __('app.assets.registered_at') }}</th>
                    <th class="px-3 py-3 font-medium text-end">{{ __('app.assets.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($properties as $property)
                    <tr class="border-b border-brand-border/70 last:border-0">
                        <td class="px-3 py-3 text-brand-muted">{{ $properties->firstItem() + $loop->index }}</td>
                        <td class="px-3 py-3">
                            @if ($property->inmate)
                                <x-inmate-photo :registration="$property->inmate" />
                            @else
                                <span class="text-brand-muted">—</span>
                            @endif
                        </td>
                        <td class="px-3 py-3 font-medium text-brand-dark">{{ $property->inmate?->full_name ?? '—' }}</td>
                        <td class="px-3 py-3 text-brand-muted">{{ $formatMoney($property->entry_cash_amount) }}</td>
                        <td class="px-3 py-3 text-brand-muted">{{ $property->form_85_number ?: '—' }}</td>
                        <td class="px-3 py-3 text-brand-muted">{{ $formatMoney($property->deposit_amount) }}</td>
                        <td class="px-3 py-3 text-brand-muted">{{ $property->form_86_number ?: '—' }}</td>
                        <td class="px-3 py-3 text-brand-muted">{{ $formatMoney($property->withdrawal_amount) }}</td>
                        <td class="px-3 py-3 text-brand-muted">{{ $property->other_property_receipt_number ?: '—' }}</td>
                        <td class="px-3 py-3 text-brand-muted"><x-eth.datetime :value="$property->created_at" /></td>
                        <td class="px-3 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <a
                                    href="{{ route('user.assets', array_merge($listQuery, ['edit' => $property->id])) }}"
                                    class="intake-action-btn intake-action-edit"
                                    title="{{ __('app.assets.edit') }}"
                                >
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 4H6a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-5M18.5 2.5a2.12 2.12 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                    <span class="hidden sm:inline">{{ __('app.assets.edit') }}</span>
                                </a>

                                <form
                                    action="{{ route('user.assets.destroy', $property) }}"
                                    method="POST"
                                    class="inline"
                                    onsubmit="return confirm(@js(__('app.assets.delete_confirm')))"
                                >
                                    @csrf
                                    @method('DELETE')
                                    @foreach ($listQuery as $key => $value)
                                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                    @endforeach
                                    <button type="submit" class="intake-action-btn intake-action-delete" title="{{ __('app.assets.delete') }}">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m-7 0h8l-1-3H9l-1 3z"/>
                                        </svg>
                                        <span class="hidden sm:inline">{{ __('app.assets.delete') }}</span>
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
        {{ $properties->onEachSide(1)->links() }}
    </div>
@endif
