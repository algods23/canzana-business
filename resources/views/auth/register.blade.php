@extends('layouts.guest')

@section('title', 'Register')

@section('content')
    <div class="panel p-8 shadow-2xl shadow-slate-900/10">
        <div class="mb-8">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-brand-600">Create account</p>
            <h2 class="mt-2 text-3xl font-bold tracking-tight text-slate-900">Register a viewer account</h2>
            <p class="mt-2 text-sm text-slate-500">New users start as viewers. Admins can promote them later.</p>
        </div>

        <form method="POST" action="{{ route('register.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700" for="name">Full name</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" class="input-field w-full" required autofocus>
                @error('name')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700" for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" class="input-field w-full" required>
                @error('email')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700" for="password">Password</label>
                <input id="password" name="password" type="password" class="input-field w-full" required>
                @error('password')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700" for="password_confirmation">Confirm password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" class="input-field w-full" required>
            </div>

            <button type="submit" class="btn btn-primary w-full justify-center">Create account</button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-500">
            Already have an account? <a href="{{ route('login') }}" class="font-semibold text-brand-600 hover:text-brand-700">Sign in</a>
        </p>
    </div>
@endsection
