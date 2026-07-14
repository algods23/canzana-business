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
                Deposit
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
                    @forelse($depositTransactions as $transaction)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('M d, Y') }}</td>
                            <td class="font-medium text-slate-900">{{ $transaction->name ?? '—' }}</td>
                            <td class="font-medium text-slate-900">{{ $transaction->description }}</td>
                            <td class="font-semibold text-emerald-600">
                                +₱{{ number_format($transaction->amount, 2) }}
                            </td>
                            <td class="text-xs text-slate-500">{{ $transaction->notes ?? '—' }}</td>
                            <td>
                                <div class="flex gap-1">
                                    <a href="{{ route('monitoring.128.deposit.edit', $transaction) }}" class="btn btn-secondary py-1 text-xs">Edit</a>
                                    <button type="button" onclick="confirmDelete('{{ route('monitoring.128.deposit.destroy', $transaction) }}')" class="btn btn-secondary border-rose-200 py-1 text-xs text-rose-600 hover:bg-rose-50 hover:text-rose-700">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-500">No deposits recorded</td>
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
                Withdrawal
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
                    @forelse($withdrawTransactions as $transaction)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('M d, Y') }}</td>
                            <td class="font-medium text-slate-900">{{ $transaction->name ?? '—' }}</td>
                            <td class="font-medium text-slate-900">{{ $transaction->description }}</td>
                            <td class="font-semibold text-rose-600">
                                -₱{{ number_format($transaction->amount, 2) }}
                            </td>
                            <td class="text-xs text-slate-500">{{ $transaction->notes ?? '—' }}</td>
                            <td>
                                <div class="flex gap-1">
                                    <a href="{{ route('monitoring.128.withdraw.edit', $transaction) }}" class="btn btn-secondary py-1 text-xs">Edit</a>
                                    <button type="button" onclick="confirmDelete('{{ route('monitoring.128.withdraw.destroy', $transaction) }}')" class="btn btn-secondary border-rose-200 py-1 text-xs text-rose-600 hover:bg-rose-50 hover:text-rose-700">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-500">No withdrawals recorded</td>
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

@push('scripts')
    <script>
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
    </script>
@endpush
