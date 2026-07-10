@extends('layouts.app')

@section('title', 'Record Payment')
@section('page-title', 'Record Payment')

@section('header-actions')
    <button type="button" onclick="history.back()" class="btn btn-secondary">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
        Back
    </button>
@endsection

@section('content')
    <div class="panel p-6">
        <form method="POST" action="{{ route('payments.store') }}" class="space-y-6">
            @include('payments.form', ['payment' => $payment])
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('payments.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Record payment</button>
            </div>
        </form>
    </div>
@endsection
