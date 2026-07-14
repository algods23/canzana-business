@extends('layouts.app')

@section('title', 'Edit Tilapia Expenses')
@section('page-title', 'Edit Tilapia Expenses')

@section('header-actions')
    <a href="{{ route('monitoring.tilapia') }}" class="btn btn-secondary">Cancel</a>
@endsection

@section('content')
    <div class="panel p-6">
        <form method="POST" action="{{ route('monitoring.tilapia.expenses.update', $transaction) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="mb-1.5 block text-sm font-medium text-slate-700" for="name">Name</label>
                <input id="name" name="name" type="text" value="{{ old('name', $transaction->name ?? '') }}" class="input-field w-full" required>
            </div>

            <div class="mb-4">
                <label class="mb-1.5 block text-sm font-medium text-slate-700" for="pcv_number">PCV#</label>
                <input id="pcv_number" name="pcv_number" type="text" value="{{ old('pcv_number', $transaction->pcv_number ?? '') }}" class="input-field w-full" required>
            </div>

            <div class="mb-4">
                <label class="mb-1.5 block text-sm font-medium text-slate-700" for="amount">Amount (₱)</label>
                <input id="amount" name="amount" type="number" step="0.01" min="0.01" value="{{ old('amount', $transaction->amount ?? '') }}" class="input-field w-full" required>
            </div>

            <div class="mb-4">
                <label class="mb-1.5 block text-sm font-medium text-slate-700" for="category">Category</label>
                <select id="category" name="category" class="input-field w-full" required>
                    <option value="">Select a category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}" {{ old('category', $transaction->category ?? '') === $category ? 'selected' : '' }}>{{ $category }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="mb-1.5 block text-sm font-medium text-slate-700" for="description">Description</label>
                <input id="description" name="description" type="text" value="{{ old('description', $transaction->description ?? '') }}" class="input-field w-full" required>
            </div>

            <div class="mb-4">
                <label class="mb-1.5 block text-sm font-medium text-slate-700" for="transaction_date">Date</label>
                <input id="transaction_date" name="transaction_date" type="date" value="{{ old('transaction_date', $transaction->transaction_date) }}" class="input-field w-full" required>
            </div>

            <div class="mb-4">
                <label class="mb-1.5 block text-sm font-medium text-slate-700" for="notes">Notes (optional)</label>
                <input id="notes" name="notes" type="text" value="{{ old('notes', $transaction->notes ?? '') }}" class="input-field w-full">
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('monitoring.tilapia') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Expense</button>
            </div>
        </form>
    </div>
@endsection
