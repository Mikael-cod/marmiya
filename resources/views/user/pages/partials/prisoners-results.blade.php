@php
    $listQuery = request()->only(['q', 'gender', 'from', 'to', 'per_page', 'page']);
    $hasFilters = collect($filters)->filter(fn ($value, $key) => $key !== 'per_page' && filled($value))->isNotEmpty();
@endphp

@if ($files->total() > 0)
    <p class="mt-2 text-xs text-brand-muted" id="intake-results-summary">
        {{ __('app.prisoners.results_summary', [
            'from' => $files->firstItem(),
            'to' => $files->lastItem(),
            'total' => $files->total(),
        ]) }}
    </p>
@else
    <p class="mt-2 hidden text-xs text-brand-muted" id="intake-results-summary"></p>
@endif

@if ($files->isEmpty())
    <div class="mt-8 rounded-2xl border border-dashed border-brand-border px-6 py-12 text-center">
        <p class="text-sm text-brand-muted">
            {{ $hasFilters || filled($filters['q']) ? __('app.prisoners.no_search_results') : __('app.prisoners.no_records') }}
        </p>
        @if (! $hasFilters && ! filled($filters['q']))
            <a href="{{ route('user.prisoners') }}" data-intake-modal-open class="btn-primary-brand intake-register-btn mt-4">
                {{ __('app.prisoners.register') }}
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
                    <th class="px-3 py-3 font-medium">{{ __('app.prisoners.fields.inmate') }}</th>
                    <th class="px-3 py-3 font-medium">{{ __('app.income.fields.court_file_number') }}</th>
                    <th class="px-3 py-3 font-medium">{{ __('app.prisoners.fields.mother_name') }}</th>
                    <th class="px-3 py-3 font-medium">{{ __('app.prisoners.fields.age') }}</th>
                    <th class="px-3 py-3 font-medium">{{ __('app.prisoners.fields.gender') }}</th>
                    <th class="px-3 py-3 font-medium">{{ __('app.prisoners.fields.education_level') }}</th>
                    <th class="px-3 py-3 font-medium">{{ __('app.prisoners.registered_at') }}</th>
                    <th class="px-3 py-3 font-medium text-end">{{ __('app.prisoners.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($files as $file)
                    <tr class="border-b border-brand-border/70 last:border-0">
                        <td class="px-3 py-3 text-brand-muted">{{ $files->firstItem() + $loop->index }}</td>
                        <td class="px-3 py-3">
                            @if ($file->inmate)
                                <x-inmate-photo :registration="$file->inmate" />
                            @else
                                <span class="text-brand-muted">—</span>
                            @endif
                        </td>
                        <td class="px-3 py-3 font-medium text-brand-dark">{{ $file->inmate?->full_name ?? '—' }}</td>
                        <td class="px-3 py-3 text-brand-muted">{{ $file->inmate?->court_file_number ?: '—' }}</td>
                        <td class="px-3 py-3 text-brand-muted">{{ $file->mother_name ?: '—' }}</td>
                        <td class="px-3 py-3 text-brand-muted">{{ $file->age ?? '—' }}</td>
                        <td class="px-3 py-3 text-brand-muted">
                            @if ($file->gender === 'male')
                                {{ __('app.prisoners.gender_male') }}
                            @elseif ($file->gender === 'female')
                                {{ __('app.prisoners.gender_female') }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-3 py-3 text-brand-muted">{{ $file->education_level ?: '—' }}</td>
                        <td class="px-3 py-3 text-brand-muted"><x-eth.datetime :value="$file->created_at" /></td>
                        <td class="px-3 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <a
                                    href="{{ route('user.prisoners', array_merge($listQuery, ['documents' => $file->id])) }}"
                                    class="intake-action-btn intake-action-documents"
                                    title="{{ __('app.prisoners.documents') }}"
                                >
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                                    </svg>
                                    <span class="hidden sm:inline">{{ __('app.prisoners.documents') }}</span>
                                    @if (($file->pages_count ?? 0) > 0)
                                        <span class="intake-action-badge">{{ $file->pages_count }}</span>
                                    @endif
                                </a>

                                <a
                                    href="{{ route('user.prisoners', array_merge($listQuery, ['view' => $file->id])) }}"
                                    class="intake-action-btn intake-action-view"
                                    title="{{ __('app.prisoners.view') }}"
                                >
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span class="hidden sm:inline">{{ __('app.prisoners.view') }}</span>
                                </a>

                                <a
                                    href="{{ route('user.prisoners', array_merge($listQuery, ['edit' => $file->id])) }}"
                                    class="intake-action-btn intake-action-edit"
                                    title="{{ __('app.prisoners.edit') }}"
                                >
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 4H6a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-5M18.5 2.5a2.12 2.12 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                    <span class="hidden sm:inline">{{ __('app.prisoners.edit') }}</span>
                                </a>

                                <form
                                    action="{{ route('user.prisoners.destroy', $file) }}"
                                    method="POST"
                                    class="inline"
                                    onsubmit="return confirm(@js(__('app.prisoners.delete_confirm')))"
                                >
                                    @csrf
                                    @method('DELETE')
                                    @foreach ($listQuery as $key => $value)
                                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                    @endforeach
                                    <button type="submit" class="intake-action-btn intake-action-delete" title="{{ __('app.prisoners.delete') }}">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m-7 0h8l-1-3H9l-1 3z"/>
                                        </svg>
                                        <span class="hidden sm:inline">{{ __('app.prisoners.delete') }}</span>
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
        {{ $files->onEachSide(1)->links() }}
    </div>
@endif
