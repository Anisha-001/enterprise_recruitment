@extends('layouts.portal')

@section('title', 'Forgot Password')

@section('content')
<div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto mt-8 sm:w-full sm:max-w-md">
        <h2 class="text-center text-3xl font-extrabold text-gray-900 tracking-tight">
            Reset your password
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
            Enter your email and we'll send you a password reset link.
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow-xl border border-gray-100 rounded-xl sm:px-10 glass-card">
            @if (session('status'))
                <div class="p-4 mb-4 rounded-md bg-green-50 text-green-800 border border-green-200 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <form class="space-y-6" action="{{ route('candidate.forgot-password.email') }}" method="POST">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">
                        Email address
                    </label>
                    <div class="mt-1">
                        <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}"
                            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-brand-500 focus:border-brand-500 sm:text-sm">
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <button type="submit"
                        class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-brand-600 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition duration-150">
                        Send Password Reset Link
                    </button>
                </div>
            </form>

            <div class="mt-6 text-center text-sm">
                <a href="{{ route('candidate.login') }}" class="font-medium text-brand-600 hover:text-brand-500">
                    Back to login
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
