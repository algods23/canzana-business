@props(['status'])

@php
    $classes = match($status) {
        'active', 'paid', 'occupied' => 'badge-success',
        'pending' => 'badge-warning',
        'overdue', 'maintenance' => 'badge-danger',
        'vacant' => 'badge-neutral',
        default => 'badge-info',
    };
    $label = ucfirst($status);
@endphp

<span {{ $attributes->merge(['class' => "badge {$classes}"]) }}>{{ $label }}</span>
