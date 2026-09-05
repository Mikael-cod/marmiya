@props(['value', 'weekday' => false])

<span {{ $attributes }}>{{ eth_date($value, $weekday) ?? '—' }}</span>
