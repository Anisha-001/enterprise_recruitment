<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Portal') | Candidate Portal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#f0fdfa',
                            100: '#ccfbf1',
                            500: '#14b8a6',
                            600: '#0d9488',
                            700: '#0f766e',
                            800: '#115e59',
                            900: '#134e4a',
                        },
                        brand: {
                            50: '#f0fdfa',
                            500: '#14b8a6',
                            600: '#0d9488',
                            700: '#0f766e',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .glass-card { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); }
        .gradient-bg { background: linear-gradient(135deg, #134e4a 0%, #0f766e 100%); }
    </style>
    @stack('styles')
</head>
<body class="h-full flex flex-col font-sans antialiased text-gray-900">

    <!-- Header Navigation -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-40 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Left Section: Branding & Links -->
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="{{ route('candidate.dashboard') }}" class="flex items-center space-x-2">
                            <div class="w-9 h-9 bg-gradient-to-br from-brand-500 to-brand-700 rounded-lg flex items-center justify-center text-white font-bold">
                                C
                            </div>
                            <span class="text-lg font-bold text-gray-900">Candidate Portal</span>
                        </a>
                    </div>
                    @auth('candidate')
                    <nav class="hidden md:ml-8 md:flex md:space-x-6 items-center">
                        <a href="{{ route('candidate.dashboard') }}" class="px-1 pt-1 text-sm font-medium {{ request()->routeIs('candidate.dashboard') ? 'text-brand-700 border-b-2 border-brand-600 h-full flex items-center' : 'text-gray-500 hover:text-gray-700' }}">Dashboard</a>
                        <a href="{{ route('candidate.applications.index') }}" class="px-1 pt-1 text-sm font-medium {{ request()->routeIs('candidate.applications.*') ? 'text-brand-700 border-b-2 border-brand-600 h-full flex items-center' : 'text-gray-500 hover:text-gray-700' }}">Applications</a>
                        <a href="{{ route('candidate.interviews.index') }}" class="px-1 pt-1 text-sm font-medium {{ request()->routeIs('candidate.interviews.index') ? 'text-brand-700 border-b-2 border-brand-600 h-full flex items-center' : 'text-gray-500 hover:text-gray-700' }}">Interviews</a>
                        <a href="{{ route('candidate.documents.index') }}" class="px-1 pt-1 text-sm font-medium {{ request()->routeIs('candidate.documents.index') ? 'text-brand-700 border-b-2 border-brand-600 h-full flex items-center' : 'text-gray-500 hover:text-gray-700' }}">Documents</a>
                        <a href="{{ route('candidate.profile.edit') }}" class="px-1 pt-1 text-sm font-medium {{ request()->routeIs('candidate.profile.edit') ? 'text-brand-700 border-b-2 border-brand-600 h-full flex items-center' : 'text-gray-500 hover:text-gray-700' }}">My Profile</a>
                    </nav>
                    @endauth
                </div>

                <!-- Right Section: Account Details / Login -->
                <div class="flex items-center">
                    @auth('candidate')
                    <div class="flex items-center space-x-4">
                        <div class="hidden md:flex flex-col text-right">
                            <span class="text-sm font-semibold text-gray-800">{{ auth('candidate')->user()->full_name }}</span>
                            <span class="text-xs text-gray-500">{{ auth('candidate')->user()->candidate_number }}</span>
                        </div>
                        <div class="relative group">
                            <button class="w-10 h-10 rounded-full bg-brand-50 border border-brand-200 overflow-hidden flex items-center justify-center font-bold text-brand-700">
                                @if(auth('candidate')->user()->photograph)
                                    <img src="{{ asset('storage/' . auth('candidate')->user()->photograph) }}" alt="Avatar" class="w-full h-full object-cover">
                                @else
                                    {{ auth('candidate')->user()->initials }}
                                @endif
                            </button>
                            <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition duration-150 z-50">
                                <a href="{{ route('candidate.profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">My Profile</a>
                                <form method="POST" action="{{ route('candidate.logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- Mobile Hamburger -->
                    <div class="md:hidden flex items-center ml-4">
                        <button id="mobile-menu-btn" class="text-gray-500 hover:text-gray-700 p-1">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                    </div>
                    @else
                    <a href="{{ route('candidate.login') }}" class="text-sm font-medium text-brand-700 hover:text-brand-800">Login</a>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Mobile Navigation Menu -->
        @auth('candidate')
        <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-gray-200 px-4 pt-2 pb-4 space-y-1">
            <a href="{{ route('candidate.dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-50">Dashboard</a>
            <a href="{{ route('candidate.applications.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-50">Applications</a>
            <a href="{{ route('candidate.interviews.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-50">Interviews</a>
            <a href="{{ route('candidate.documents.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-50">Documents</a>
            <a href="{{ route('candidate.profile.edit') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-50">My Profile</a>
            <hr class="border-gray-200 my-2">
            <form method="POST" action="{{ route('candidate.logout') }}">
                @csrf
                <button type="submit" class="block w-full text-left px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-50">Logout</button>
            </form>
        </div>
        @endauth
    </header>

    <!-- Main Container -->
    <main class="flex-grow">
        <!-- Flash Alert Messages -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
            @if(session('success'))
                <div class="p-4 mb-4 rounded-lg bg-green-50 text-green-800 border border-green-200 flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="p-4 mb-4 rounded-lg bg-red-50 text-red-800 border border-red-200 flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                </div>
            @endif
        </div>

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-auto py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-sm text-gray-500">
            &copy; {{ date('Y') }} {{ config('recruitment.seo.company_name') }}. All rights reserved.
        </div>
    </footer>

    <script>
        const btn = document.getElementById('mobile-menu-btn');
        if (btn) {
            btn.addEventListener('click', () => {
                document.getElementById('mobile-menu').classList.toggle('hidden');
            });
        }
    </script>
    @stack('scripts')
</body>
</html>
