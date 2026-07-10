@extends('layouts.app')

@section('title', 'Rental Monitoring')
@section('page-title', 'Rental Monitoring')

@section('header-actions')
    <button type="button" onclick="openTransactionModal()" class="btn btn-primary">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
        Add Transaction
    </button>
@endsection

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

    <div class="grid gap-6 lg:grid-cols-1">
        {{-- Tenants List with Details --}}
        <div class="panel">
            <div class="border-b border-border px-5 py-4">
                <h3 class="font-semibold text-slate-900">Tenants List</h3>
                <p class="text-xs text-slate-500">All tenants with their details and payment history</p>
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
                                    <div class="flex gap-1">
                                        <a href="{{ route('tenants.show', $tenant) }}" class="btn btn-secondary py-1 text-xs">View</a>
                                        <a href="{{ route('tenants.edit', $tenant) }}" class="btn btn-secondary py-1 text-xs">Edit</a>
                                    </div>
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

        {{-- Recent Transactions --}}
        <div class="panel">
            <div class="border-b border-border px-5 py-4">
                <h3 class="font-semibold text-slate-900">Recent Transactions</h3>
                <p class="text-xs text-slate-500">Latest rental transactions</p>
            </div>
            <div class="max-h-96 overflow-y-auto">
                @forelse($recentTransactions as $transaction)
                    <div class="flex items-center justify-between border-b border-border px-5 py-3 last:border-0">
                        <div>
                            <p class="font-medium text-slate-900">{{ $transaction->description }}</p>
                            <p class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('M d, Y') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold {{ $transaction->module_type === 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $transaction->module_type === 'income' ? '+' : '-' }}₱{{ number_format($transaction->amount, 2) }}
                            </p>
                            <p class="text-xs text-slate-500">{{ ucfirst($transaction->module_type) }}</p>
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-8 text-center text-slate-500">
                        <p>No transactions yet</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Expenses --}}
        <div class="panel">
            <div class="border-b border-border px-5 py-4">
                <h3 class="font-semibold text-slate-900">Expenses</h3>
                <p class="text-xs text-slate-500">Rental-related expenses</p>
            </div>
            <div class="max-h-96 overflow-y-auto">
                @forelse($expenses as $expense)
                    <div class="flex items-center justify-between border-b border-border px-5 py-3 last:border-0">
                        <div>
                            <p class="font-medium text-slate-900">{{ $expense->description }}</p>
                            <p class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($expense->expense_date)->format('M d, Y') }}</p>
                            @if($expense->category)
                                <p class="text-xs text-slate-400">{{ ucfirst($expense->category) }}</p>
                            @endif
                            @if($expense->notes)
                                <p class="text-xs text-slate-400">{{ $expense->notes }}</p>
                            @endif
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-rose-600">
                                -₱{{ number_format($expense->amount, 2) }}
                            </p>
                            <p class="text-xs text-slate-500">Expense</p>
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-8 text-center text-slate-500">
                        <p>No expenses recorded</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection

@section('scripts')
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
@endsection
