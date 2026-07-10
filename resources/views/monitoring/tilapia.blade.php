@extends('layouts.app')

@section('title', 'Tilapia Monitoring')
@section('page-title', 'Tilapia Monitoring')

@section('header-actions')
    <button type="button" onclick="openTransactionModal()" class="btn btn-primary">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
        Add Transaction
    </button>
@endsection

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
    <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
        <x-stat-card label="Total Sales" :value="'₱' . number_format($stats['total_sales'])" icon="expense" color="emerald" />
        <x-stat-card label="Total Expenses" :value="'₱' . number_format($stats['total_expenses'])" icon="expense" color="amber" />
        <x-stat-card label="Net Income" :value="'₱' . number_format($stats['net_income'])" icon="expense" :color="$stats['net_income'] >= 0 ? 'emerald' : 'rose'" />
        <x-stat-card label="Balance" :value="'₱' . number_format($stats['balance'])" icon="payment" color="sky" />
    </div>

    {{-- Recent Transactions --}}
    <div class="panel">
        <div class="border-b border-border px-5 py-4">
            <h3 class="font-semibold text-slate-900">Recent Transactions</h3>
            <p class="text-xs text-slate-500">Latest tilapia transactions</p>
        </div>
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentTransactions as $transaction)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('M d, Y') }}</td>
                            <td>
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $transaction->module_type === 'income' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                    {{ ucfirst($transaction->module_type) }}
                                </span>
                            </td>
                            <td class="font-medium text-slate-900">{{ $transaction->description }}</td>
                            <td class="font-semibold {{ $transaction->module_type === 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $transaction->module_type === 'income' ? '+' : '-' }}₱{{ number_format($transaction->amount, 2) }}
                            </td>
                            <td class="text-xs text-slate-500">{{ $transaction->notes ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-500">No transactions yet</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
    <div id="transactionModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
        <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
            <h3 class="mb-4 text-lg font-semibold text-slate-900">Add Transaction</h3>
            
            <form method="POST" action="{{ route('monitoring.transaction.store') }}">
                @csrf
                <input type="hidden" name="account_type" value="tilapia">
                
                <div class="mb-4">
                    <label class="mb-1.5 block text-sm font-medium text-slate-700" for="module_type">Type</label>
                    <select id="module_type" name="module_type" class="input-field w-full" required>
                        <option value="income">Income (Sales)</option>
                        <option value="expense">Expense</option>
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
