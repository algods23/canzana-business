@extends('layouts.app')

@section('title', 'Rental Monitoring')
@section('page-title', 'Rental Monitoring')

@section('content')
    {{-- Date Filter --}}
    <div class="mb-6 panel p-4">
        <form method="GET" action="{{ route('monitoring.rental') }}" class="flex flex-wrap items-end gap-4">
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
                <a href="{{ route('monitoring.rental') }}" class="btn btn-secondary">Clear</a>
            </div>
        </form>
    </div>

    {{-- Stats Cards --}}
    <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
        <x-stat-card label="Total Payable" :value="'₱' . number_format($stats['total_payable'])" icon="user" color="rose" />
        <div class="panel p-4">
            <div class="flex items-center gap-2">
                @include('components.icons.expense', ['class' => 'h-5 w-5 text-emerald-600'])
                <span class="text-sm font-medium text-slate-600">Total Sales</span>
            </div>
            <p class="mt-2 text-2xl font-bold text-emerald-600">₱{{ number_format($stats['total_sales']) }}</p>
            @if(!empty($salesByMethod))
                <div class="mt-2 flex flex-wrap gap-2 text-xs">
                    @foreach($salesByMethod as $method => $amount)
                        <span class="text-slate-600">{{ ucfirst($method) }}: ₱{{ number_format($amount, 2) }}</span>
                    @endforeach
                </div>
            @endif
        </div>
        <x-stat-card label="Total Expenses" :value="'₱' . number_format($stats['total_expenses'])" icon="expense" color="amber" />
        <x-stat-card label="Net Income" :value="'₱' . number_format($stats['net_income'])" icon="expense" :color="$stats['net_income'] >= 0 ? 'emerald' : 'rose'" />
    </div>

    {{-- Revenue Overview --}}
    <div class="mb-6">
        <div class="panel">
            <div class="flex items-center justify-between border-b border-border px-5 py-4">
                <div>
                    <h3 class="font-semibold text-slate-900">Revenue Overview</h3>
                    <p class="text-xs text-slate-500">Collected vs expected — last 6 months</p>
                </div>
                <select class="input-field w-auto py-1.5 text-xs">
                    <option>Last 6 months</option>
                    <option>Last 12 months</option>
                </select>
            </div>
            <div class="p-5">
                <div class="rounded-2xl border border-border bg-slate-50 p-4">
                    <canvas id="revenueChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-1">
        {{-- Tenants List with Details --}}
        <div class="panel">
            <div class="flex items-center justify-between border-b border-border px-5 py-4">
                <div>
                    <h3 class="font-semibold text-slate-900">Tenants List</h3>
                    <p class="text-xs text-slate-500">All tenants with their details and payment history</p>
                </div>
                <a href="{{ route('properties.index') }}" class="btn btn-primary py-1.5 text-xs">
                    View Properties
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Property / Unit</th>
                            <th>Tenant</th>
                            <th>Contact</th>
                            <th>Monthly Rent</th>
                            <th>Balance</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tenants as $tenant)
                            <tr>
                                <td>
                                    <p class="text-sm text-slate-900">{{ $tenant->propertyModel?->name ?? 'N/A' }}</p>
                                    <p class="text-xs text-slate-500">{{ $tenant->roomModel?->unit ?? 'N/A' }}</p>
                                </td>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-brand-100 text-xs font-semibold text-brand-700">
                                            {{ strtoupper(substr($tenant->name, 0, 1)) }}
                                        </div>
                                        <span class="font-medium text-slate-900">{{ $tenant->name }}</span>
                                    </div>
                                </td>
                                <td>
                                    <p class="text-sm text-slate-900">{{ $tenant->email ?? 'N/A' }}</p>
                                    <p class="text-xs text-slate-500">{{ $tenant->phone ?? 'N/A' }}</p>
                                </td>
                                <td class="font-medium text-slate-900">₱{{ number_format($tenant->rent ?? 0, 2) }}</td>
                                <td class="font-semibold {{ $tenant->balance > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                    ₱{{ number_format($tenant->balance, 2) }}
                                </td>
                                <td>
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $tenant->status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-50 text-slate-700' }}">
                                        {{ ucfirst($tenant->status ?? 'Unknown') }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('tenants.show', $tenant) }}" class="btn btn-secondary py-1 text-xs">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-slate-500">No tenants found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="grid gap-6">

<br>
    {{-- Recent Transactions (Sales) --}}
        <div class="panel">
            <div class="border-b border-border px-5 py-4">
                <h3 class="font-semibold text-slate-900">Sales</h3>
                <p class="text-xs text-slate-500">Latest rental sales received</p>
            </div>
            <div class="overflow-y-auto overflow-x-auto" style="font-size: 10px;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Property / Unit</th>
                            <th>Tenant</th>
                            <th>Received</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTransactions as $transaction)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($transaction->date)->format('M d, Y') }}</td>
                                <td class="text-slate-700">{{ $transaction->property_unit ?: 'N/A' }}</td>
                                <td class="font-medium text-slate-900">{{ $transaction->tenant ?: 'N/A' }}</td>
                                <td class="font-semibold text-emerald-600">&#8369;{{ number_format($transaction->received, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-12 text-center text-slate-500">No transactions yet</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                @if($recentTransactions->hasPages())
                    <div class="mt-4 flex justify-center">
                        {{ $recentTransactions->links() }}
                    </div>
                @endif
            </div>
        </div>
        {{-- Expenses --}}
        <div class="panel">
            <div class="flex items-center justify-between border-b border-border px-5 py-4">
                <div>
                    <h3 class="font-semibold text-slate-900">Expenses</h3>
                    <p class="text-xs text-slate-500">Rental-related expenses</p>
                </div>
                <a href="{{ route('expenses.create') }}" class="btn btn-primary py-1.5 text-xs">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    Record Expense
                </a>
            </div>
            <div class="overflow-y-auto overflow-x-auto" style="font-size: 10px;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>PCV#</th>
                            <th>Building / Room</th>
                            <th>Recipient</th>
                            <th>Category</th>
                            <th>Description</th>
                            <th>Amount</th>
                            <th>Notes</th>
                            <th>Action</th>
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
                                <td class="font-medium text-slate-900">{{ $expense->pcv_number ?? '—' }}</td>
                                <td>
                                    <p>{{ $expense->buildingModel?->propertyModel?->name }} &middot; {{ $expense->building_name }}</p>
                                    @if($expense->room_id)
                                        <p class="text-xs text-slate-500">Unit {{ $expense->room_unit }}</p>
                                    @else
                                        <p class="text-xs italic text-slate-400">Whole building</p>
                                    @endif
                                </td>
                                <td class="text-sm text-slate-600">{{ $expense->recipient_name ?? 'N/A' }}</td>
                                <td>
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $catClass }}">
                                        {{ $expense->category }}
                                    </span>
                                </td>
                                <td class="font-medium text-slate-900">{{ $expense->description }}</td>
                                <td class="font-semibold text-rose-600">&#8369;{{ number_format($expense->amount, 2) }}</td>
                                <td class="max-w-32 truncate text-xs text-slate-500">{{ $expense->notes ?? 'N/A' }}</td>
                                <td>
                                    <div class="flex gap-1">
                                        <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-secondary py-1 text-xs">Edit</a>
                                        <form action="{{ route('expenses.destroy', $expense) }}" method="POST" class="inline" onsubmit="return confirm('Delete this expense?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-secondary border-rose-200 py-1 text-xs text-rose-600 hover:bg-rose-50 hover:text-rose-700">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-12 text-center text-slate-500">No expenses recorded</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                @if($expenses->hasPages())
                    <div class="mt-4 flex justify-center">
                        {{ $expenses->links() }}
                    </div>
                @endif
            </div>
        </div>

        
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script>
        const revenueData = @json($revenueChart);
        const revenueCtx = document.getElementById('revenueChart');

        if (revenueCtx) {
            new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: revenueData.map((entry) => entry.month),
                    datasets: [
                        {
                            label: 'Collected',
                            data: revenueData.map((entry) => entry.collected),
                            borderColor: '#2563eb',
                            backgroundColor: 'rgba(37, 99, 235, 0.15)',
                            fill: true,
                            tension: 0.35,
                        },
                        {
                            label: 'Expected',
                            data: revenueData.map((entry) => entry.expected),
                            borderColor: '#94a3b8',
                            backgroundColor: 'rgba(148, 163, 184, 0.10)',
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

    <div id="transactionModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
        <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
            <h3 class="mb-4 text-lg font-semibold text-slate-900">Add Transaction</h3>

            <form method="POST" action="{{ route('monitoring.transaction.store') }}">
                @csrf
                <input type="hidden" name="account_type" value="rental">

                <div class="mb-4">
                    <label class="mb-1.5 block text-sm font-medium text-slate-700" for="module_type">Type</label>
                    <select id="module_type" name="module_type" class="input-field w-full" required>
                        <option value="income">Income</option>
                        <option value="expense">Expense</option>
                        <option value="payment">Payment</option>
                        <option value="balance_adjustment">Balance Adjustment</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="mb-1.5 block text-sm font-medium text-slate-700" for="amount">Amount (₱)</label>
                    <input id="amount" name="amount" type="number" step="0.01" min="0.01" class="input-field w-full" required>
                </div>

                <div class="mb-4">
                    <label class="mb-1.5 block text-sm font-medium text-slate-700" for="description">Description</label>
                    <input id="description" name="description" type="text" class="input-field w-full" required>
                </div>

                <div class="mb-4">
                    <label class="mb-1.5 block text-sm font-medium text-slate-700" for="transaction_date">Date</label>
                    <input id="transaction_date" name="transaction_date" type="date" value="{{ now()->format('Y-m-d') }}" class="input-field w-full" required>
                </div>

                <div class="mb-4">
                    <label class="mb-1.5 block text-sm font-medium text-slate-700" for="notes">Notes (optional)</label>
                    <input id="notes" name="notes" type="text" class="input-field w-full">
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeTransactionModal()" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openTransactionModal() {
            document.getElementById('transactionModal').classList.remove('hidden');
            document.getElementById('transactionModal').classList.add('flex');
        }

        function closeTransactionModal() {
            document.getElementById('transactionModal').classList.add('hidden');
            document.getElementById('transactionModal').classList.remove('flex');
        }

        document.getElementById('transactionModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeTransactionModal();
            }
        });
    </script>
