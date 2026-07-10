@extends('layouts.app')

@section('title', 'Expenses')
@section('page-title', 'Expenses')

@section('header-actions')
    <a href="{{ route('expenses.create') }}" class="btn btn-primary">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
        Record Expense
    </a>
@endsection

@section('content')
    {{-- Expense Stats --}}
    <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
        <x-stat-card label="Total Expenses" :value="'₱' . number_format($stats['total'])" icon="expense" />
        <x-stat-card label="This Month" :value="'₱' . number_format($stats['this_month'])" icon="expense" color="amber" />
        <x-stat-card label="Building Level" :value="'₱' . number_format($stats['building_level'])" icon="building" color="sky" />
        <x-stat-card label="Room Level" :value="'₱' . number_format($stats['room_level'])" icon="room" color="rose" />
    </div>

    {{-- Filters --}}
    <div class="panel mb-6">
        <div class="flex flex-col gap-4 border-b border-border p-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('expenses.index') }}"
                   class="whitespace-nowrap rounded-lg px-3 py-1.5 text-sm font-medium transition-colors {{ empty($filters['category']) ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:bg-slate-100' }}">
                    All
                </a>
                @foreach($categories as $cat)
                    <a href="{{ route('expenses.index', array_merge($filters, ['category' => $cat])) }}"
                       class="whitespace-nowrap rounded-lg px-3 py-1.5 text-sm font-medium transition-colors {{ ($filters['category'] ?? '') === $cat ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:bg-slate-100' }}">
                        {{ $cat }}
                    </a>
                @endforeach
            </div>
            <form method="GET" action="{{ route('expenses.index') }}" class="flex gap-2">
                @if($filters['category'] ?? false)
                    <input type="hidden" name="category" value="{{ $filters['category'] }}">
                @endif
                <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search..." class="input-field w-full sm:w-48">
                <button type="submit" class="btn btn-secondary">Search</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Building / Room</th>
                        <th>Amount</th>
                        <th>Notes</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $expense)
                        @php
                            $categoryColors = [
                                'Maintenance' => 'bg-amber-50 text-amber-700',
                                'Utilities' => 'bg-sky-50 text-sky-700',
                                'Repairs' => 'bg-rose-50 text-rose-700',
                                'Supplies' => 'bg-emerald-50 text-emerald-700',
                                'Cleaning' => 'bg-teal-50 text-teal-700',
                                'Insurance' => 'bg-indigo-50 text-indigo-700',
                                'Tax' => 'bg-purple-50 text-purple-700',
                            ];
                            $catClass = $categoryColors[$expense->category] ?? 'bg-slate-50 text-slate-700';
                        @endphp
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($expense->expense_date)->format('M d, Y') }}</td>
                            <td>
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $catClass }}">
                                    {{ $expense->category }}
                                </span>
                            </td>
                            <td class="font-medium text-slate-900">{{ $expense->description }}</td>
                            <td>
                                <p>{{ $expense->buildingModel?->propertyModel?->name }} · {{ $expense->building_name }}</p>
                                @if($expense->room_id)
                                    <p class="text-xs text-slate-500">Unit {{ $expense->room_unit }}</p>
                                @else
                                    <p class="text-xs text-slate-400 italic">Whole building</p>
                                @endif
                            </td>
                            <td class="font-semibold text-rose-600">₱{{ number_format($expense->amount, 2) }}</td>
                            <td class="text-xs text-slate-500 max-w-32 truncate">{{ $expense->notes ?? '—' }}</td>
                            <td>
                                <div class="flex gap-1">
                                    <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-secondary py-1 text-xs">Edit</a>
                                    <form action="{{ route('expenses.destroy', $expense) }}" method="POST" class="inline" onsubmit="return confirm('Delete this expense?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-secondary py-1 text-xs text-rose-600 hover:text-rose-700 hover:bg-rose-50 border-rose-200">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center">
                                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-100">
                                    @include('components.icons.expense', ['class' => 'h-7 w-7 text-slate-400'])
                                </div>
                                <p class="mt-3 text-slate-500">No expenses recorded yet</p>
                                <a href="{{ route('expenses.create') }}" class="btn btn-primary mt-3 text-sm">Record First Expense</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
