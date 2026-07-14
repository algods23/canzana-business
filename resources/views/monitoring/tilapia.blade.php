@extends('layouts.app')

@section('title', 'Tilapia Monitoring')
@section('page-title', 'Tilapia Monitoring')

@section('content')
    {{-- Date Filter --}}
    <div class="mb-6 panel p-4">
        <form method="GET" action="{{ route('monitoring.tilapia') }}" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="mb-1.5 block text-sm font-medium text-slate-700" for="date_from">From Date</label>
                <input id="date_from" name="date_from" type="date" value="{{ $filters['date_from'] ?? '' }}" class="input-field w-full">
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="mb-1.5 block text-sm font-medium text-slate-700" for="date_to">To Date</label>
                <input id="date_to" name="date_to" type="date" value="{{ $filters['date_to'] ?? '' }}" class="input-field w-full">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn btn-secondary">Filter</button>
                <a href="{{ route('monitoring.tilapia') }}" class="btn btn-secondary">Clear</a>
            </div>
        </form>
    </div>

    {{-- Stats Cards --}}
    <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-3">
        <x-stat-card label="Total Sales" :value="'₱' . number_format($stats['total_sales'])" icon="expense" color="emerald" />
        <x-stat-card label="Total Expenses" :value="'₱' . number_format($stats['total_expenses'])" icon="expense" color="amber" />
        <x-stat-card label="Net Income" :value="'₱' . number_format($stats['net_income'])" icon="expense" :color="$stats['net_income'] >= 0 ? 'emerald' : 'rose'" />
    </div>

    {{-- Sales --}}
    <div class="panel mb-6">
        <div class="flex items-center justify-between border-b border-border px-5 py-4">
            <div>
                <h3 class="font-semibold text-slate-900">Sales</h3>
                <p class="text-xs text-slate-500">Tilapia sales records</p>
            </div>
            <a href="{{ route('monitoring.tilapia.sales.create') }}" class="btn btn-primary py-1.5 text-xs">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                Add Sales
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>PCV#</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($salesTransactions as $transaction)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('M d, Y') }}</td>
                            <td class="font-medium text-slate-900">{{ $transaction->pcv_number ?? '—' }}</td>
                            <td class="font-medium text-slate-900">{{ $transaction->name ?? '—' }}</td>
                            <td class="font-medium text-slate-900">{{ $transaction->description ?? '—' }}</td>
                            <td class="font-semibold text-emerald-600">
                                +₱{{ number_format($transaction->amount, 2) }}
                            </td>
                            <td class="text-xs text-slate-500">{{ $transaction->notes ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-500">No sales recorded</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Expenses --}}
    <div class="panel">
        <div class="flex items-center justify-between border-b border-border px-5 py-4">
            <div>
                <h3 class="font-semibold text-slate-900">Expenses</h3>
                <p class="text-xs text-slate-500">Tilapia expense records</p>
            </div>
            <a href="{{ route('monitoring.tilapia.expenses.create') }}" class="btn btn-secondary py-1.5 text-xs">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                Add Expenses
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>PCV#</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenseTransactions as $transaction)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('M d, Y') }}</td>
                            <td class="font-medium text-slate-900">{{ $transaction->pcv_number ?? '—' }}</td>
                            <td class="font-medium text-slate-900">{{ $transaction->name ?? '—' }}</td>
                            <td class="text-sm text-slate-600">{{ $transaction->category ?? '—' }}</td>
                            <td class="font-medium text-slate-900">{{ $transaction->description ?? '—' }}</td>
                            <td class="font-semibold text-rose-600">
                                -₱{{ number_format($transaction->amount, 2) }}
                            </td>
                            <td class="text-xs text-slate-500">{{ $transaction->notes ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-500">No expenses recorded</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
