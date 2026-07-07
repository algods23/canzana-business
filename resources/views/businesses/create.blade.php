@extends('layouts.app')

@section('title', 'Add Business')
@section('page-title', 'Add Business')

@section('breadcrumb')
    <a href="{{ route('businesses.select') }}" class="hover:text-brand-600">Select Business</a>
    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
    <span class="text-slate-700">Add Business</span>
@endsection

@section('content')
    <div class="panel max-w-2xl p-6">
        <form method="POST" action="{{ route('businesses.store') }}" class="space-y-5">
            @csrf

            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700" for="name">Business Name</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" class="input-field" required autofocus>
                @error('name')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700" for="type">Business Type</label>
                <select id="type" name="type" class="input-field" required>
                    <option value="rental" @selected(old('type') === 'rental')>Rental</option>
                    <option value="fishpond" @selected(old('type') === 'fishpond')>Fishpond</option>
                    <option value="fruits" @selected(old('type') === 'fruits')>Fruits</option>
                    <option value="custom" @selected(old('type') === 'custom')>Custom</option>
                </select>
                @error('type')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700" for="description">Description</label>
                <input id="description" name="description" type="text" value="{{ old('description') }}" class="input-field" placeholder="Short note for this workspace">
                @error('description')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div class="flex justify-end gap-3 border-t border-border pt-5">
                <a href="{{ route('businesses.select') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Create Business</button>
            </div>
        </form>
    </div>
@endsection
