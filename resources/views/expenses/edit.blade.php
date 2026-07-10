@extends('layouts.app')

@section('title', 'Edit Expense')
@section('page-title', 'Edit Expense')

@section('header-actions')
    <a href="{{ route('expenses.index') }}" class="btn btn-secondary">Cancel</a>
@endsection

@section('content')
    <div class="panel p-6">
        <form method="POST" action="{{ route('expenses.update', $expense) }}" class="space-y-6">
            @method('PUT')
            @include('expenses.form', ['expense' => $expense, 'buildings' => $buildings])
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('expenses.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Expense</button>
            </div>
        </form>
    </div>
@endsection
