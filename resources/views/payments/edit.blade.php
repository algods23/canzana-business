@extends('layouts.app')

@section('title', 'Edit Payment')
@section('page-title', 'Edit Payment')

@section('header-actions')
    <a href="{{ route('payments.index') }}" class="btn btn-secondary">Back</a>
@endsection

@section('content')
    <div class="panel p-6">
        <form method="POST" action="{{ route('payments.update', $payment) }}" class="space-y-6">
            @csrf
            @method('PUT')
            @include('payments.form', ['payment' => $payment])
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('payments.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
        </form>
    </div>
@endsection
