@extends('layouts.app')

@section('title', $business->name.' Dashboard')
@section('page-title', $business->name.' Dashboard')

@section('header-actions')
    <a href="{{ route('businesses.select') }}" class="btn btn-secondary">Switch Business</a>
@endsection

@section('content')
    <div class="panel p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wider text-brand-600">{{ str_replace('-', ' ', $business->type) }}</p>
                <h2 class="mt-1 text-2xl font-bold text-slate-900">{{ $business->name }}</h2>
                <p class="mt-2 max-w-2xl text-sm text-slate-500">{{ $business->description ?: 'This dashboard is ready for business-specific tools.' }}</p>
            </div>
            <span class="badge badge-success">{{ ucfirst($business->status) }}</span>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="rounded-lg border border-border bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Month Sales</p>
                <p class="mt-2 text-lg font-bold text-slate-900">₱{{ number_format($monthlySales, 2) }}</p>
            </div>
            <div class="rounded-lg border border-border bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Month Disbursement</p>
                <p class="mt-2 text-lg font-bold text-slate-900">₱{{ number_format($monthlyDisbursements, 2) }}</p>
            </div>
            <div class="rounded-lg border border-border bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Month Net</p>
                <p class="mt-2 text-lg font-bold text-slate-900">₱{{ number_format($monthlySales - $monthlyDisbursements, 2) }}</p>
            </div>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,420px)_1fr]">
        <div class="panel p-6">
            <h3 class="text-lg font-bold text-slate-900">Daily Entry</h3>
            <p class="mt-1 text-sm text-slate-500">Record the sales of the day and disbursement expenses.</p>

            <form method="POST" action="{{ route('businesses.daily-entry.store', $business) }}" class="mt-5 space-y-4">
                @csrf
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700" for="entry_date">Date</label>
                    <input id="entry_date" name="entry_date" type="date" value="{{ old('entry_date', optional($todayEntry?->entry_date)->toDateString() ?? now()->toDateString()) }}" class="input-field w-full" required>
                    @error('entry_date')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700" for="sales_amount">Sales of the Day</label>
                    <input id="sales_amount" name="sales_amount" type="number" step="0.01" min="0" value="{{ old('sales_amount', $todayEntry?->sales_amount ?? '0.00') }}" class="input-field w-full" required>
                    @error('sales_amount')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700" for="disbursement_amount">Disbursement (Expenses)</label>
                    <input id="disbursement_amount" name="disbursement_amount" type="number" step="0.01" min="0" value="{{ old('disbursement_amount', $todayEntry?->disbursement_amount ?? '0.00') }}" class="input-field w-full" required>
                    @error('disbursement_amount')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700" for="notes">Notes</label>
                    <textarea id="notes" name="notes" rows="3" class="input-field w-full">{{ old('notes', $todayEntry?->notes) }}</textarea>
                    @error('notes')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>

                <button type="submit" class="btn btn-primary w-full justify-center">Save Daily Entry</button>
            </form>
        </div>

        <div class="panel overflow-hidden">
            <div class="border-b border-border p-6">
                <h3 class="text-lg font-bold text-slate-900">Recent Entries</h3>
                <p class="mt-1 text-sm text-slate-500">Latest sales and disbursement records for {{ $business->name }}.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-border text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-6 py-3">Date</th>
                            <th class="px-6 py-3">Sales</th>
                            <th class="px-6 py-3">Disbursement</th>
                            <th class="px-6 py-3">Net</th>
                            <th class="px-6 py-3">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border bg-white">
                        @forelse($recentEntries as $entry)
                            <tr>
                                <td class="whitespace-nowrap px-6 py-4 font-medium text-slate-900">{{ $entry->entry_date->format('M d, Y') }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-slate-700">₱{{ number_format((float) $entry->sales_amount, 2) }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-slate-700">₱{{ number_format((float) $entry->disbursement_amount, 2) }}</td>
                                <td class="whitespace-nowrap px-6 py-4 font-semibold text-slate-900">₱{{ number_format((float) $entry->sales_amount - (float) $entry->disbursement_amount, 2) }}</td>
                                <td class="max-w-xs truncate px-6 py-4 text-slate-500">{{ $entry->notes ?: 'None' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-sm text-slate-500">No daily entries yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
