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

    {{-- Recent Transactions --}}
    <div class="panel">
        <div class="flex items-center justify-between border-b border-border px-5 py-4">
            <div>
                <h3 class="font-semibold text-slate-900">Recent Transactions</h3>
                <p class="text-xs text-slate-500">Latest tilapia transactions</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('monitoring.tilapia.sales.create') }}" class="btn btn-primary py-1.5 text-xs">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    Add Sales
                </a>
                <a href="{{ route('monitoring.tilapia.expenses.create') }}" class="btn btn-secondary py-1.5 text-xs">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    Add Expenses
                </a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>PCV#</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentTransactions as $transaction)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('M d, Y') }}</td>
                            <td class="font-medium text-slate-900">{{ $transaction->pcv_number ?? '—' }}</td>
                            <td class="font-medium text-slate-900">{{ $transaction->name ?? '—' }}</td>
                            <td>
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $transaction->module_type === 'income' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                    {{ ucfirst($transaction->module_type) }}
                                </span>
                            </td>
                            <td class="text-sm text-slate-600">{{ $transaction->category ?? '—' }}</td>
                            <td class="font-medium text-slate-900">{{ $transaction->description ?? '—' }}</td>
                            <td class="font-semibold {{ $transaction->module_type === 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $transaction->module_type === 'income' ? '+' : '-' }}₱{{ number_format($transaction->amount, 2) }}
                            </td>
                            <td class="text-xs text-slate-500">{{ $transaction->notes ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-500">No transactions yet</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
