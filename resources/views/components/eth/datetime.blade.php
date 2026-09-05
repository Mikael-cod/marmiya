@props(['value'])

<span {{ $attributes }}>{{ eth_datetime($value) ?? '—' }}</span>
