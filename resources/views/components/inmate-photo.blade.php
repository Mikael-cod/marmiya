@props(['registration'])

@if ($registration->photo_path)
    <img
        src="{{ $registration->photoUrl() }}"
        alt="{{ $registration->full_name }}"
        class="intake-photo-thumb"
        loading="lazy"
    >
@else
    <div class="intake-photo-thumb intake-photo-thumb-placeholder" aria-hidden="true">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a8.25 8.25 0 1115 0v.75H4.5v-.75z"/>
        </svg>
    </div>
@endif
