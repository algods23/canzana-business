@extends('layouts.app')

@section('title', 'Agriculture Monitoring')
@section('page-title', 'Agriculture Monitoring')

@section('content')
    {{-- Date Filter --}}
    <div class="mb-6 panel p-4">
        <form method="GET" action="{{ route('monitoring.agriculture') }}" class="flex flex-wrap items-end gap-4">
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
                <a href="{{ route('monitoring.agriculture') }}" class="btn btn-secondary">Clear</a>
            </div>
        </form>
    </div>

    {{-- Stats Cards --}}
    <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-3">
        <x-stat-card label="Total Sales" :value="'₱' . number_format($stats['total_sales'])" icon="expense" color="emerald" />
        <x-stat-card label="Total Expenses" :value="'₱' . number_format($stats['total_expenses'])" icon="expense" color="amber" />
        <x-stat-card label="Net Income" :value="'₱' . number_format($stats['net_income'])" icon="expense" :color="$stats['net_income'] >= 0 ? 'emerald' : 'rose'" />
    </div>

    {{-- Revenue Overview --}}
    <div class="mb-6">
        <div class="panel">
            <div class="flex items-center justify-between border-b border-border px-5 py-4">
                <div>
                    <h3 class="font-semibold text-slate-900">Revenue Overview</h3>
                    <p class="text-xs text-slate-500">Sales vs expenses — last 6 months</p>
                </div>
            </div>
            <div class="p-5">
                <div class="rounded-2xl border border-border bg-slate-50 p-4">
                    <canvas id="revenueChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Sales --}}
    <div class="panel mb-6">
        <div class="flex items-center justify-between border-b border-border px-5 py-4">
            <div>
                <h3 class="font-semibold text-slate-900">Sales</h3>
                <p class="text-xs text-slate-500">Agriculture sales records</p>
            </div>
            <a href="{{ route('monitoring.agriculture.sales.create') }}" class="btn btn-primary py-1.5 text-xs">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                Add Sales
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Notes</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($salesTransactions as $transaction)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('M d, Y') }}</td>
                            <td class="font-medium text-slate-900">{{ $transaction->name ?? '—' }}</td>
                            <td class="font-medium text-slate-900">{{ $transaction->description ?? '—' }}</td>
                            <td class="font-semibold text-emerald-600">
                                +₱{{ number_format($transaction->amount, 2) }}
                            </td>
                            <td class="text-xs text-slate-500">{{ $transaction->notes ?? '—' }}</td>
                            <td>
                                <div class="flex gap-1">
                                    <a href="{{ route('monitoring.agriculture.sales.edit', $transaction) }}" class="btn btn-secondary py-1 text-xs">Edit</a>
                                    <button type="button" onclick="confirmDelete('{{ route('monitoring.agriculture.sales.destroy', $transaction) }}')" class="btn btn-secondary border-rose-200 py-1 text-xs text-rose-600 hover:bg-rose-50 hover:text-rose-700">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-500">No sales recorded</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if($salesTransactions->hasPages())
                <div class="mt-4 flex justify-center">
                    {{ $salesTransactions->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Expenses --}}
    <div class="panel">
        <div class="flex items-center justify-between border-b border-border px-5 py-4">
            <div>
                <h3 class="font-semibold text-slate-900">Expenses</h3>
                <p class="text-xs text-slate-500">Agriculture expense records</p>
            </div>
            <a href="{{ route('monitoring.agriculture.expenses.create') }}" class="btn btn-secondary py-1.5 text-xs">
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
                        <th>Action</th>
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
                            <td>
                                <div class="flex gap-1">
                                    <a href="{{ route('monitoring.agriculture.expenses.edit', $transaction) }}" class="btn btn-secondary py-1 text-xs">Edit</a>
                                    <button type="button" onclick="confirmDelete('{{ route('monitoring.agriculture.expenses.destroy', $transaction) }}')" class="btn btn-secondary border-rose-200 py-1 text-xs text-rose-600 hover:bg-rose-50 hover:text-rose-700">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-500">No expenses recorded</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if($expenseTransactions->hasPages())
                <div class="mt-4 flex justify-center">
                    {{ $expenseTransactions->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script>
        const revenueData = @json($revenueChart);
        const revenueCtx = document.getElementById('revenueChart');

        function confirmDelete(url) {
            const password = prompt('Enter your password to confirm deletion:');
            if (password === null) return false;
            
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);
            
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);
            
            const passwordInput = document.createElement('input');
            passwordInput.type = 'hidden';
            passwordInput.name = 'password';
            passwordInput.value = password;
            form.appendChild(passwordInput);
            
            document.body.appendChild(form);
            form.submit();
        }

        if (revenueCtx) {
            new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: revenueData.map((entry) => entry.month),
                    datasets: [
                        {
                            label: 'Sales',
                            data: revenueData.map((entry) => entry.sales),
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.15)',
                            fill: true,
                            tension: 0.35,
                        },
                        {
                            label: 'Expenses',
                            data: revenueData.map((entry) => entry.expenses),
                            borderColor: '#f59e0b',
                            backgroundColor: 'rgba(245, 158, 11, 0.15)',
                            fill: true,
                            tension: 0.35,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' },
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: (value) => '₱' + Number(value).toLocaleString(),
                            },
                        },
                    },
                },
            });
        }
    </script>
@endpush
