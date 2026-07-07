@extends('layouts.app')

@section('title', 'Payments')
@section('page-title', 'Payments')

@section('header-actions')
    <a href="{{ route('payments.create') }}" class="btn btn-primary">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
        Record Payment
    </a>
@endsection

@section('content')
    {{-- Payment Stats --}}
    <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
        <x-stat-card label="Total Payments" :value="$stats['total']" icon="payment" />
        <x-stat-card label="Collected" :value="'₱' . number_format($stats['collected'])" change="{{ $stats['paid'] }} paid" change-type="up" icon="revenue" color="emerald" />
        <x-stat-card label="Pending" :value="$stats['pending']" change="₱{{ number_format($stats['outstanding']) }} outstanding" change-type="neutral" icon="payment" color="amber" />
        <x-stat-card label="Overdue" :value="$stats['overdue']" change="Requires action" change-type="down" icon="alert" color="rose" />
    </div>

    {{-- Status Tabs + Filters --}}
    <div class="panel mb-6">
        <div class="flex flex-col gap-4 border-b border-border p-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex gap-1 overflow-x-auto">
                @foreach(['' => 'All', 'paid' => 'Paid', 'pending' => 'Pending', 'overdue' => 'Overdue'] as $value => $label)
                    <a href="{{ route('payments.index', array_merge($filters, ['status' => $value ?: null])) }}"
                       class="whitespace-nowrap rounded-lg px-3 py-1.5 text-sm font-medium transition-colors {{ ($filters['status'] ?? '') === $value ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:bg-slate-100' }}">
                        {{ $label }}
                        @if($value === 'overdue' && $stats['overdue'] > 0)
                            <span class="ml-1 rounded-full bg-rose-100 px-1.5 py-0.5 text-xs text-rose-700">{{ $stats['overdue'] }}</span>
                        @endif
                    </a>
                @endforeach
            </div>
            <form method="GET" action="{{ route('payments.index') }}" class="flex gap-2">
                @if($filters['status'] ?? false)
                    <input type="hidden" name="status" value="{{ $filters['status'] }}">
                @endif
                <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search..." class="input-field w-full sm:w-48">
                <button type="submit" class="btn btn-secondary">Search</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Due Date</th>
                        <th>Tenant</th>
                        <th>Property / Unit</th>
                        <th>Amount</th>
                        <th>Paid Date</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr class="{{ $payment['status'] === 'overdue' ? 'bg-rose-50/30' : '' }}">
                        <td>{{ $payment['paid_date'] ? \Carbon\Carbon::parse($payment['paid_date'])->format('M d, Y') : '—' }}</td>    
                        <td class="font-medium text-slate-900">{{ $payment['tenant'] }}</td>
                            <td>
                                <p>{{ $payment['property'] }}</p>
                                <p class="text-xs text-slate-500">Unit {{ $payment['unit'] }}</p>
                            </td>
                            <td class="font-semibold">₱{{ number_format($payment['amount']) }}</td>
                            <td>{{ \Carbon\Carbon::parse($payment['due_date'])->format('M d, Y') }}</td>
                            <td>{{ $payment['method'] ?? '—' }}</td>
                            <td><x-status-badge :status="$payment['status']" /></td>
                            <td>
                                @if($payment['status'] !== 'paid')
                                    <button type="button" class="btn btn-primary py-1 text-xs">Mark Paid</button>
                                @else
                                    <button type="button" class="btn btn-ghost py-1 text-xs">Receipt</button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center">
                                <p class="text-slate-500">No payments match your filters</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
