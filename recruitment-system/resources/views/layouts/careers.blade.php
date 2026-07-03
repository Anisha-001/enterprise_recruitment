<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Careers') | {{ config('recruitment.seo.company_name') }}</title>
    <meta name="description" content="@yield('meta_description', config('recruitment.seo.default_description'))">
    @yield('meta')
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
        .animate-fade-in { animation: fadeIn 0.5s ease-in-out; }
        .animate-slide-up { animation: slideUp 0.5s ease-out; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .gradient-hero { background: linear-gradient(135deg, #134e4a 0%, #0f766e 50%, #115e59 100%); }
        .glass-card { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); }
    </style>
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-900">
    <!-- Navigation -->
    <nav class="fixed w-full z-50 bg-white/95 backdrop-blur-md border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('careers.home') }}" class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-brand-500 to-brand-700 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <span class="text-xl font-bold bg-gradient-to-r from-brand-700 to-brand-500 bg-clip-text text-transparent">
                            {{ config('recruitment.seo.company_name') }}
                        </span>
                    </a>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('careers.home') }}" class="text-gray-600 hover:text-brand-700 font-medium transition">Home</a>
                    <a href="{{ route('careers.jobs.index') }}" class="text-gray-600 hover:text-brand-700 font-medium transition">Open Positions</a>
                    <a href="{{ route('careers.about') }}" class="text-gray-600 hover:text-brand-700 font-medium transition">About Us</a>
                    <a href="{{ route('careers.culture') }}" class="text-gray-600 hover:text-brand-700 font-medium transition">Culture</a>
                    <a href="{{ route('careers.jobs.index') }}" class="bg-brand-600 hover:bg-brand-700 text-white px-5 py-2.5 rounded-lg font-medium transition shadow-lg shadow-brand-500/25">
                        View Jobs
                    </a>
                </div>
                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center">
                    <button id="mobile-menu-btn" class="text-gray-600 hover:text-gray-900 p-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <!-- Mobile menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-gray-200">
            <div class="px-4 pt-2 pb-4 space-y-1">
                <a href="{{ route('careers.home') }}" class="block px-3 py-2 text-gray-600 hover:text-brand-700 font-medium">Home</a>
                <a href="{{ route('careers.jobs.index') }}" class="block px-3 py-2 text-gray-600 hover:text-brand-700 font-medium">Open Positions</a>
                <a href="{{ route('careers.about') }}" class="block px-3 py-2 text-gray-600 hover:text-brand-700 font-medium">About Us</a>
                <a href="{{ route('careers.culture') }}" class="block px-3 py-2 text-gray-600 hover:text-brand-700 font-medium">Culture</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="pt-16 min-h-screen">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-10 h-10 bg-gradient-to-br from-brand-500 to-brand-700 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <span class="text-xl font-bold">{{ config('recruitment.seo.company_name') }}</span>
                    </div>
                    <p class="text-gray-400 max-w-sm">
                        Join our world-class team and build the future with us. We're always looking for talented individuals who share our passion for excellence.
                    </p>
                </div>
                <div>
                    <h3 class="font-semibold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('careers.jobs.index') }}" class="text-gray-400 hover:text-white transition">All Jobs</a></li>
                        <li><a href="{{ route('careers.about') }}" class="text-gray-400 hover:text-white transition">About Us</a></li>
                        <li><a href="{{ route('careers.culture') }}" class="text-gray-400 hover:text-white transition">Our Culture</a></li>
                        <li><a href="{{ route('careers.benefits') }}" class="text-gray-400 hover:text-white transition">Benefits</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-semibold mb-4">Legal</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Privacy Policy</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Terms of Service</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Cookie Policy</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-12 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} {{ config('recruitment.seo.company_name') }}. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        document.getElementById('mobile-menu-btn').addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });
    </script>
    @stack('scripts')
</body>
</html>
