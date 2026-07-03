@extends('layouts.guest')

@section('title', 'Login')

@section('content')
    <div class="panel p-8 shadow-2xl shadow-slate-900/10">
        <div class="mb-8">
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-brand-600">Welcome back</p>
            <h2 class="mt-2 text-3xl font-bold tracking-tight text-slate-900">Sign in to Canzana</h2>
            <p class="mt-2 text-sm text-slate-500">Use the seeded admin account or your own registered user.</p>
        </div>

        <form method="POST" action="{{ route('login.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700" for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" class="input-field w-full" required autofocus>
                @error('email')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700" for="password">Password</label>
                <input id="password" name="password" type="password" class="input-field w-full" required>
                @error('password')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
            </div>

            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" name="remember" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                Remember me
            </label>

            <button type="submit" class="btn btn-primary w-full justify-center">Sign in</button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-500">
            Need an account? <a href="{{ route('register') }}" class="font-semibold text-brand-600 hover:text-brand-700">Register</a>
        </p>
    </div>
@endsection
