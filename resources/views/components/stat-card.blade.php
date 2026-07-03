@props(['label', 'value', 'change' => null, 'changeType' => 'neutral', 'icon' => null, 'color' => 'brand'])

@php
    $iconColors = [
        'brand' => 'bg-brand-50 text-brand-700',
        'emerald' => 'bg-emerald-50 text-emerald-700',
        'amber' => 'bg-amber-50 text-amber-700',
        'rose' => 'bg-rose-50 text-rose-700',
        'sky' => 'bg-sky-50 text-sky-700',
    ];
@endphp

<div {{ $attributes->merge(['class' => 'stat-card']) }}>
    <div class="flex items-start justify-between">
        <div>
            <p class="text-sm font-medium text-slate-500">{{ $label }}</p>
            <p class="mt-2 text-2xl font-bold tracking-tight text-slate-900">{{ $value }}</p>
            @if($change)
                <p class="mt-1.5 flex items-center gap-1 text-xs font-medium {{ $changeType === 'up' ? 'text-emerald-600' : ($changeType === 'down' ? 'text-rose-600' : 'text-slate-500') }}">
                    @if($changeType === 'up')
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18" /></svg>
                    @elseif($changeType === 'down')
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5 12 21m0 0-7.5-7.5M12 21V3" /></svg>
                    @endif
                    {{ $change }}
                </p>
            @endif
        </div>
        @if($icon)
            <div class="flex h-10 w-10 items-center justify-center rounded-lg {{ $iconColors[$color] ?? $iconColors['brand'] }}">
                @include('components.icons.' . $icon, ['class' => 'h-5 w-5'])
            </div>
        @endif
    </div>
</div>
