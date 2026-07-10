@extends('layouts.app')

@section('title', 'Record Expense')
@section('page-title', 'Record Expense')

@section('header-actions')
    <a href="{{ route('expenses.index') }}" class="btn btn-secondary">Cancel</a>
@endsection

@section('content')
    <div class="panel p-6">
        <form method="POST" action="{{ route('expenses.store') }}" class="space-y-6">
            @if(request('redirect_to'))
                <input type="hidden" name="redirect_to" value="{{ request('redirect_to') }}">
            @endif
            @include('expenses.form', ['expense' => $expense, 'buildings' => $buildings])
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('expenses.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Record Expense</button>
            </div>
        </form>
    </div>
@endsection
