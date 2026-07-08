@extends('layouts.app')

@section('title', 'Select Business')
@section('page-title', 'Select Business')
@section('hide-sidebar', 'true')

@section('content')
    <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-900">Open a business</h2>
            <p class="mt-1 text-sm text-slate-500">Choose which business workspace you want to manage.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        @foreach($businesses as $business)
            @php
                $cardStyles = match($business->type) {
                    'rental' => ['bg' => 'bg-brand-50', 'text' => 'text-brand-700', 'ring' => 'ring-brand-600/20', 'initial' => 'R'],
                    'fishpond' => ['bg' => 'bg-sky-50', 'text' => 'text-sky-700', 'ring' => 'ring-sky-600/20', 'initial' => 'F'],
                    'fruits' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'ring' => 'ring-emerald-600/20', 'initial' => 'F'],
                    default => ['bg' => 'bg-slate-100', 'text' => 'text-slate-700', 'ring' => 'ring-slate-500/20', 'initial' => strtoupper(substr($business->name, 0, 1))],
                };
            @endphp
            <a href="{{ route('businesses.open', $business) }}" class="panel group block p-5 transition hover:-translate-y-0.5 hover:border-brand-200 hover:shadow-md">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg {{ $cardStyles['bg'] }} {{ $cardStyles['text'] }} ring-1 {{ $cardStyles['ring'] }}">
                            <span class="text-lg font-bold">{{ $cardStyles['initial'] }}</span>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 group-hover:text-brand-700">{{ $business->name }}</h3>
                            <p class="text-xs font-medium uppercase tracking-wider text-slate-400">{{ str_replace('-', ' ', $business->type) }}</p>
                        </div>
                    </div>
                    <span class="rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700 ring-1 ring-emerald-600/20">{{ ucfirst($business->status) }}</span>
                </div>

                <p class="mt-5 min-h-10 text-sm text-slate-500">{{ $business->description ?: 'Business workspace' }}</p>

                <div class="mt-5 flex items-center justify-between border-t border-border pt-4 text-sm">
                    <span class="font-medium text-slate-600">Open dashboard</span>
                    <span class="text-lg text-brand-600 transition group-hover:translate-x-1">-&gt;</span>
                </div>
            </a>
        @endforeach
    </div>
@endsection
