@extends('layouts.app')

@section('title', 'Record Payment')
@section('page-title', 'Record Payment')

@section('header-actions')
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
