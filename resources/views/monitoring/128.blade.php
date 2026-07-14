@extends('layouts.app')

@section('title', '128 Monitoring')
@section('page-title', '128 Monitoring')

@section('content')
    {{-- Stats Cards --}}
    <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-3">
        <x-stat-card label="Total Income" :value="'₱' . number_format($stats['total_income'])" icon="expense" color="emerald" />
        <x-stat-card label="Total Expenses" :value="'₱' . number_format($stats['total_expenses'])" icon="expense" color="amber" />
        <x-stat-card label="Balance" :value="'₱' . number_format($stats['balance'])" icon="payment" :color="$stats['balance'] >= 0 ? 'sky' : 'rose'" />
    </div>

    {{-- Deposits --}}
    <div class="panel mb-6">
        <div class="flex items-center justify-between border-b border-border px-5 py-4">
            <div>
                <h3 class="font-semibold text-slate-900">Deposits</h3>
                <p class="text-xs text-slate-500">128 account deposits</p>
            </div>
            <a href="{{ route('monitoring.128.deposit.create') }}" class="btn btn-primary py-1.5 text-xs">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                Add Deposit
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
                    </tr>
                </thead>
                <tbody>
                    @forelse($depositTransactions as $transaction)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('M d, Y') }}</td>
                            <td class="font-medium text-slate-900">{{ $transaction->name ?? '—' }}</td>
                            <td class="font-medium text-slate-900">{{ $transaction->description }}</td>
                            <td class="font-semibold text-emerald-600">
                                +₱{{ number_format($transaction->amount, 2) }}
                            </td>
                            <td class="text-xs text-slate-500">{{ $transaction->notes ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-500">No deposits recorded</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if($depositTransactions->hasPages())
                <div class="mt-4 flex justify-center">
                    {{ $depositTransactions->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Withdrawals --}}
    <div class="panel">
        <div class="flex items-center justify-between border-b border-border px-5 py-4">
            <div>
                <h3 class="font-semibold text-slate-900">Withdrawals</h3>
                <p class="text-xs text-slate-500">128 account withdrawals</p>
            </div>
            <a href="{{ route('monitoring.128.withdraw.create') }}" class="btn btn-secondary py-1.5 text-xs">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                Add Withdrawal
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
                    </tr>
                </thead>
                <tbody>
                    @forelse($withdrawTransactions as $transaction)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('M d, Y') }}</td>
                            <td class="font-medium text-slate-900">{{ $transaction->name ?? '—' }}</td>
                            <td class="font-medium text-slate-900">{{ $transaction->description }}</td>
                            <td class="font-semibold text-rose-600">
                                -₱{{ number_format($transaction->amount, 2) }}
                            </td>
                            <td class="text-xs text-slate-500">{{ $transaction->notes ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-500">No withdrawals recorded</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if($withdrawTransactions->hasPages())
                <div class="mt-4 flex justify-center">
                    {{ $withdrawTransactions->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
    <div id="transactionModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
        <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
            <h3 class="mb-4 text-lg font-semibold text-slate-900">Add Transaction</h3>
            
            <form method="POST" action="{{ route('monitoring.transaction.store') }}">
                @csrf
                <input type="hidden" name="account_type" value="128">
                
                <div class="mb-4">
                    <label class="mb-1.5 block text-sm font-medium text-slate-700" for="module_type">Type</label>
                    <select id="module_type" name="module_type" class="input-field w-full" required>
                        <option value="income">Income</option>
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
