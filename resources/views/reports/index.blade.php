@extends('layouts.app')

@section('title', 'Reports')
@section('page-title', 'Reports & Analytics')

@section('header-actions')
    <button type="button" class="btn btn-secondary">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
        Export PDF
    </button>
    <button type="button" class="btn btn-primary">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
        Export CSV
    </button>
@endsection

@section('content')
    {{-- Report Type Cards --}}
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
        @foreach([
            ['title' => 'Occupancy Report', 'desc' => 'Room occupancy by property', 'icon' => 'building', 'iconClass' => 'bg-brand-50 text-brand-700'],
            ['title' => 'Revenue Report', 'desc' => 'Monthly collection summary', 'icon' => 'revenue', 'iconClass' => 'bg-emerald-50 text-emerald-700'],
            ['title' => 'Expense Report', 'desc' => 'Expense breakdown & trends', 'icon' => 'expense', 'iconClass' => 'bg-amber-50 text-amber-700'],
            ['title' => 'Overdue Report', 'desc' => 'Outstanding payments', 'icon' => 'alert', 'iconClass' => 'bg-rose-50 text-rose-700'],
            ['title' => 'Tenant Report', 'desc' => 'Active leases & contracts', 'icon' => 'users', 'iconClass' => 'bg-sky-50 text-sky-700'],
        ] as $report)
            <button type="button" class="panel p-5 text-left transition-all hover:border-brand-300 hover:shadow-md">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg {{ $report['iconClass'] }}">
                    @include('components.icons.' . $report['icon'], ['class' => 'h-5 w-5'])
                </div>
                <h3 class="mt-3 font-semibold text-slate-900">{{ $report['title'] }}</h3>
                <p class="mt-1 text-xs text-slate-500">{{ $report['desc'] }}</p>
            </button>
        @endforeach
    </div>

    {{-- Financial Summary --}}
    <div class="mb-6 panel">
        <div class="border-b border-border px-5 py-4">
            <h3 class="font-semibold text-slate-900">Financial Summary</h3>
            <p class="text-xs text-slate-500">Revenue vs Expenses overview</p>
        </div>
        <div class="grid grid-cols-2 gap-4 p-5 sm:grid-cols-4">
            <div class="rounded-lg bg-brand-50 p-4">
                <p class="text-xs font-medium text-brand-600">Expected Revenue</p>
                <p class="mt-1 text-xl font-bold text-brand-900">₱{{ number_format($stats['monthly_revenue']) }}</p>
            </div>
            <div class="rounded-lg bg-emerald-50 p-4">
                <p class="text-xs font-medium text-emerald-600">Collected This Month</p>
                <p class="mt-1 text-xl font-bold text-emerald-900">₱{{ number_format($stats['collected_this_month']) }}</p>
            </div>
            <div class="rounded-lg bg-amber-50 p-4">
                <p class="text-xs font-medium text-amber-600">Total Expenses</p>
                <p class="mt-1 text-xl font-bold text-amber-900">₱{{ number_format($stats['total_expenses']) }}</p>
            </div>
            <div class="rounded-lg {{ ($stats['collected_this_month'] - $stats['expenses_this_month']) >= 0 ? 'bg-emerald-50' : 'bg-rose-50' }} p-4">
                <p class="text-xs font-medium {{ ($stats['collected_this_month'] - $stats['expenses_this_month']) >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">Net Income (This Month)</p>
                <p class="mt-1 text-xl font-bold {{ ($stats['collected_this_month'] - $stats['expenses_this_month']) >= 0 ? 'text-emerald-900' : 'text-rose-900' }}">
                    ₱{{ number_format($stats['collected_this_month'] - $stats['expenses_this_month']) }}
                </p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        {{-- Revenue vs Expenses Chart --}}
        <div class="panel">
            <div class="border-b border-border px-5 py-4">
                <h3 class="font-semibold text-slate-900">Revenue vs Expenses</h3>
                <p class="text-xs text-slate-500">Last 6 months comparison</p>
            </div>
            <div class="p-5">
                <div class="rounded-2xl border border-border bg-slate-50 p-4">
                    <canvas id="reportRevenueChart" height="140"></canvas>
                </div>
            </div>
        </div>

        {{-- Occupancy Breakdown --}}
        <div class="panel">
            <div class="border-b border-border px-5 py-4">
                <h3 class="font-semibold text-slate-900">Occupancy Breakdown</h3>
            </div>
            <div class="p-5">
                <div class="rounded-2xl border border-border bg-slate-50 p-4">
                    <canvas id="reportOccupancyChart" height="220"></canvas>
                </div>
            </div>
        </div>

        {{-- Expense by Category --}}
        <div class="panel">
            <div class="border-b border-border px-5 py-4">
                <h3 class="font-semibold text-slate-900">Expenses by Category</h3>
                <p class="text-xs text-slate-500">All-time breakdown</p>
            </div>
            @if(count($expensesByCategory) > 0)
                <div class="p-5">
                    <div class="rounded-2xl border border-border bg-slate-50 p-4">
                        <canvas id="expenseCategoryChart" height="220"></canvas>
                    </div>
                    <div class="mt-4 space-y-2">
                        @php
                            $categoryColors = [
                                'Maintenance' => 'bg-amber-500',
                                'Utilities' => 'bg-sky-500',
                                'Repairs' => 'bg-rose-500',
                                'Supplies' => 'bg-emerald-500',
                                'Cleaning' => 'bg-teal-500',
                                'Insurance' => 'bg-indigo-500',
                                'Tax' => 'bg-purple-500',
                            ];
                            $totalExp = array_sum(array_column($expensesByCategory, 'total'));
                        @endphp
                        @foreach($expensesByCategory as $ec)
                            <div class="flex items-center gap-3">
                                <div class="h-2 w-2 rounded-full {{ $categoryColors[$ec['category']] ?? 'bg-slate-500' }}"></div>
                                <span class="flex-1 text-sm text-slate-700">{{ $ec['category'] }}</span>
                                <span class="text-sm font-semibold text-slate-900">₱{{ number_format($ec['total']) }}</span>
                                <span class="text-xs text-slate-400">{{ $totalExp > 0 ? round(($ec['total'] / $totalExp) * 100, 1) : 0 }}%</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="p-5 text-center text-sm text-slate-500">
                    <p>No expenses recorded yet.</p>
                    <a href="{{ route('expenses.create') }}" class="btn btn-primary mt-3 text-sm">Record First Expense</a>
                </div>
            @endif
        </div>

        {{-- Recent Expenses --}}
        <div class="panel">
            <div class="flex items-center justify-between border-b border-border px-5 py-4">
                <div>
                    <h3 class="font-semibold text-slate-900">Recent Expenses</h3>
                    <p class="text-xs text-slate-500">Latest 10 recorded expenses</p>
                </div>
                <a href="{{ route('expenses.index') }}" class="text-xs font-medium text-brand-600 hover:text-brand-700">View all</a>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentExpenses as $expense)
                            <tr>
                                <td class="text-xs">{{ \Carbon\Carbon::parse($expense->expense_date)->format('M d') }}</td>
                                <td>
                                    <p class="text-sm font-medium text-slate-900">{{ $expense->description }}</p>
                                    <p class="text-xs text-slate-500">{{ $expense->building_name }}{{ $expense->room_id ? ' · Unit '.$expense->room_unit : '' }}</p>
                                </td>
                                <td class="font-semibold text-rose-600">₱{{ number_format($expense->amount) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-6 text-center text-sm text-slate-500">No expenses recorded</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Overdue Accounts --}}
        <div class="panel lg:col-span-2">
            <div class="border-b border-border px-5 py-4">
                <h3 class="font-semibold text-slate-900">Overdue Accounts</h3>
                <p class="text-xs text-slate-500">{{ count($overduePayments) }} overdue payment records</p>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tenant</th>
                            <th>Property / Unit</th>
                            <th>Amount</th>
                            <th>Due Date</th>
                            <th>Days Overdue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($overduePayments as $payment)
                            @php $daysOverdue = now()->diffInDays(\Carbon\Carbon::parse($payment['due_date'])); @endphp
                            <tr>
                                <td class="font-medium">{{ $payment['tenant'] }}</td>
                                <td>{{ $payment['property'] }} · {{ $payment['unit'] }}</td>
                                <td class="font-semibold text-rose-600">₱{{ number_format($payment['amount']) }}</td>
                                <td>{{ \Carbon\Carbon::parse($payment['due_date'])->format('M d, Y') }}</td>
                                <td><span class="badge badge-danger">{{ $daysOverdue }} days</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script>
        const reportRevenue = @json($revenueChart);
        const reportRevenueCtx = document.getElementById('reportRevenueChart');

        if (reportRevenueCtx) {
            new Chart(reportRevenueCtx, {
                type: 'bar',
                data: {
                    labels: reportRevenue.map((entry) => entry.month),
                    datasets: [
                        {
                            label: 'Collected',
                            data: reportRevenue.map((entry) => entry.collected),
                            backgroundColor: '#2563eb',
                        },
                        {
                            label: 'Expected',
                            data: reportRevenue.map((entry) => entry.expected),
                            backgroundColor: '#94a3b8',
                        },
                        {
                            label: 'Expenses',
                            data: reportRevenue.map((entry) => entry.expenses),
                            backgroundColor: '#f59e0b',
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: (value) => '₱' + Number(value).toLocaleString(),
                            },
                        },
                    },
                    plugins: {
                        legend: { position: 'bottom' },
                    },
                },
            });
        }

        const reportOccupancyCtx = document.getElementById('reportOccupancyChart');
        const reportOccupancy = @json($properties->map(fn ($property) => ['name' => $property['name'], 'rate' => $property['occupancy_rate']])->values());

        if (reportOccupancyCtx) {
            new Chart(reportOccupancyCtx, {
                type: 'polarArea',
                data: {
                    labels: reportOccupancy.map((entry) => entry.name),
                    datasets: [{
                        data: reportOccupancy.map((entry) => entry.rate),
                        backgroundColor: ['#2563eb', '#0f766e', '#ea580c', '#be123c', '#7c3aed'],
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' },
                    },
                },
            });
        }

        // Expense by category chart
        const expenseCategoryCtx = document.getElementById('expenseCategoryChart');
        const expenseCategoryData = @json($expensesByCategory);

        if (expenseCategoryCtx && expenseCategoryData.length > 0) {
            const catColors = {
                'Maintenance': '#f59e0b',
                'Utilities': '#0ea5e9',
                'Repairs': '#ef4444',
                'Supplies': '#10b981',
                'Cleaning': '#14b8a6',
                'Insurance': '#6366f1',
                'Tax': '#a855f7',
            };
            const defaultColor = '#64748b';

            new Chart(expenseCategoryCtx, {
                type: 'doughnut',
                data: {
                    labels: expenseCategoryData.map(e => e.category),
                    datasets: [{
                        data: expenseCategoryData.map(e => e.total),
                        backgroundColor: expenseCategoryData.map(e => catColors[e.category] || defaultColor),
                        borderWidth: 0,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    plugins: {
                        legend: { position: 'bottom' },
                    },
                },
            });
        }
    </script>
@endpush
