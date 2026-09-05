@php
    $listQuery = request()->only(['q', 'role', 'per_page', 'page']);
    $hasFilters = collect($filters)->filter(fn ($value, $key) => $key !== 'per_page' && filled($value))->isNotEmpty();
@endphp

@if ($users->total() > 0)
    <p class="mt-2 text-xs text-brand-muted" id="intake-results-summary">
        {{ __('app.admin.users.results_summary', [
            'from' => $users->firstItem(),
            'to' => $users->lastItem(),
            'total' => $users->total(),
        ]) }}
    </p>
@else
    <p class="mt-2 hidden text-xs text-brand-muted" id="intake-results-summary"></p>
@endif

@if ($users->isEmpty())
    <div class="mt-8 rounded-2xl border border-dashed border-brand-border px-6 py-12 text-center">
        <p class="text-sm text-brand-muted">
            {{ $hasFilters || filled($filters['q']) ? __('app.admin.users.no_search_results') : __('app.admin.users.no_records') }}
        </p>
        @if (! $hasFilters && ! filled($filters['q']))
            <a href="{{ route('admin.users') }}" data-intake-modal-open class="btn-primary-brand intake-register-btn mt-4">
                {{ __('app.admin.users.create') }}
            </a>
        @endif
    </div>
@else
    <div class="mt-6 overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="border-b border-brand-border text-start text-brand-muted">
                    <th class="px-3 py-3 font-medium">#</th>
                    <th class="px-3 py-3 font-medium">{{ __('app.admin.users.fields.name') }}</th>
                    <th class="px-3 py-3 font-medium">{{ __('app.admin.users.fields.email') }}</th>
                    <th class="px-3 py-3 font-medium">{{ __('app.admin.users.fields.role') }}</th>
                    <th class="px-3 py-3 font-medium">{{ __('app.admin.users.registered_at') }}</th>
                    <th class="px-3 py-3 font-medium text-end">{{ __('app.admin.users.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $managedUser)
                    <tr class="border-b border-brand-border/70 last:border-0">
                        <td class="px-3 py-3 text-brand-muted">{{ $users->firstItem() + $loop->index }}</td>
                        <td class="px-3 py-3">
                            <div class="flex items-center gap-3">
                                <div @class([
                                    'flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-xs font-bold',
                                    'bg-brand-teal/10 text-brand-teal' => $managedUser->isAdmin(),
                                    'bg-brand-blue/10 text-brand-blue' => $managedUser->isUser(),
                                ])>
                                    {{ mb_substr($managedUser->name, 0, 1) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="font-medium text-brand-dark">{{ $managedUser->name }}</p>
                                    @if ($managedUser->id === auth()->id())
                                        <p class="text-xs text-brand-muted">{{ __('app.admin.users.current_account') }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-3 py-3 text-brand-muted">{{ $managedUser->email }}</td>
                        <td class="px-3 py-3">
                            <span @class([
                                'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold',
                                'bg-brand-teal/10 text-brand-teal' => $managedUser->isAdmin(),
                                'bg-brand-blue/10 text-brand-blue' => $managedUser->isUser(),
                            ])>
                                {{ $managedUser->role->label() }}
                            </span>
                        </td>
                        <td class="px-3 py-3 text-brand-muted"><x-eth.datetime :value="$managedUser->created_at" /></td>
                        <td class="px-3 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <a
                                    href="{{ route('admin.users', array_merge($listQuery, ['edit' => $managedUser->id])) }}"
                                    class="intake-action-btn intake-action-edit"
                                    title="{{ __('app.admin.users.edit') }}"
                                >
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 4H6a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-5M18.5 2.5a2.12 2.12 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                    <span class="hidden sm:inline">{{ __('app.admin.users.edit') }}</span>
                                </a>

                                @if ($managedUser->id !== auth()->id())
                                    <form
                                        action="{{ route('admin.users.destroy', $managedUser) }}"
                                        method="POST"
                                        class="inline"
                                        onsubmit="return confirm(@js(__('app.admin.users.delete_confirm')))"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        @foreach ($listQuery as $key => $value)
                                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                        @endforeach
                                        <button type="submit" class="intake-action-btn intake-action-delete" title="{{ __('app.admin.users.delete') }}">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m-7 0h8l-1-3H9l-1 3z"/>
                                            </svg>
                                            <span class="hidden sm:inline">{{ __('app.admin.users.delete') }}</span>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6 border-t border-brand-border pt-4" data-intake-pagination>
        {{ $users->onEachSide(1)->links() }}
    </div>
@endif
