<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Cansan Duruş Takip Sistemi')</title>

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Mobile Navigation Fix - !important overrides for responsive behavior -->
    <link rel="stylesheet" href="{{ asset('css/mobile-nav-fix.css') }}?v={{ time() }}">

    <!-- Alpine.js for mobile menu -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="flex flex-col min-h-screen bg-gray-50 overflow-x-hidden box-border">
    <div class="flex-1 flex flex-col w-full">
        <!-- Navigation -->
        @auth
            <nav>
                <div class="nav-container">
                    <div class="flex items-center justify-between w-full">
                        <!-- Logo -->
                        <span class="logo text-sm sm:text-base md:text-lg">CANSAN <span class="text-accent-500">||</span>
                            DURUŞ TAKİP</span>

                        <!-- Desktop Menu -->
                        <div class="hidden md:flex nav-menu">
                            <a href="{{ route('dashboard') }}"
                                class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                Dashboard
                            </a>

                            @if(auth()->user()->role !== 'manager')
                                <a href="{{ route('downtime.index') }}"
                                    class="nav-link {{ request()->routeIs('downtime.*') ? 'active' : '' }}">
                                    Duruşlar
                                </a>
                            @endif

                            @if(auth()->user()->role === 'manager' || auth()->user()->role === 'admin')
                                <a href="{{ route('reports.index') }}"
                                    class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                                    Raporlar
                                </a>
                            @endif

                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}"
                                    class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}">
                                    Yönetim
                                </a>
                            @endif
                        </div>

                        <!-- User Info & Mobile Menu -->
                        <div class="flex items-center gap-2 sm:gap-4">
                            <!-- Desktop User Info (only on desktop) -->
                            <div class="hidden md:flex items-center gap-3">
                                <div class="text-right">
                                    <p class="text-sm font-medium text-white">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-300">{{ ucfirst(auth()->user()->role) }}</p>
                                </div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-secondary btn-sm">
                                        Çıkış
                                    </button>
                                </form>
                            </div>


                            <!-- Mobile Menu Button -->
                            <div class="md:hidden relative">
                                <button onclick="toggleMobileMenu()" type="button" id="mobile-menu-button"
                                    class="text-white p-2 hover:bg-primary-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-white">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path id="hamburger-icon" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                                        <path id="close-icon" class="hidden" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>

                                <!-- Mobile Dropdown Menu -->
                                <div id="mobile-menu"
                                    class="hidden absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-2xl py-2 z-[100] border border-gray-200">
                                    <!-- User Info -->
                                    <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                                        <p class="text-sm font-semibold text-gray-900">{{ auth()->user()->name }}</p>
                                        <p class="text-xs text-gray-500 mt-0.5">{{ ucfirst(auth()->user()->role) }}</p>
                                    </div>

                                    <!-- Menu Items -->
                                    <div class="py-2">
                                        <a href="{{ route('dashboard') }}"
                                            class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                            <span class="text-lg">📊</span>
                                            <span class="font-medium">Dashboard</span>
                                        </a>

                                        @if(auth()->user()->role !== 'manager')
                                            <a href="{{ route('downtime.index') }}"
                                                class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                                <span class="text-lg">🚨</span>
                                                <span class="font-medium">Duruşlar</span>
                                            </a>
                                        @endif

                                        @if(auth()->user()->role === 'manager' || auth()->user()->role === 'admin')
                                            <a href="{{ route('reports.index') }}"
                                                class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                                <span class="text-lg">📈</span>
                                                <span class="font-medium">Raporlar</span>
                                            </a>
                                        @endif

                                        @if(auth()->user()->isAdmin())
                                            <a href="{{ route('admin.dashboard') }}"
                                                class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                                <span class="text-lg">⚙️</span>
                                                <span class="font-medium">Yönetim</span>
                                            </a>
                                        @endif
                                    </div>

                                    <!-- Logout -->
                                    <div class="border-t border-gray-200 mt-2 pt-2">
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit"
                                                class="flex items-center gap-3 w-full text-left px-4 py-3 text-sm text-red-600 hover:bg-red-50 font-medium transition-colors">
                                                <span class="text-lg">🚪</span>
                                                <span>Çıkış Yap</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <script>
                                function toggleMobileMenu() {
                                    const menu = document.getElementById('mobile-menu');
                                    const hamburgerIcon = document.getElementById('hamburger-icon');
                                    const closeIcon = document.getElementById('close-icon');

                                    if (menu.classList.contains('hidden')) {
                                        menu.classList.remove('hidden');
                                        hamburgerIcon.classList.add('hidden');
                                        closeIcon.classList.remove('hidden');
                                    } else {
                                        menu.classList.add('hidden');
                                        hamburgerIcon.classList.remove('hidden');
                                        closeIcon.classList.add('hidden');
                                    }
                                }

                                // Close menu when clicking outside
                                document.addEventListener('click', function (event) {
                                    const menu = document.getElementById('mobile-menu');
                                    const button = document.getElementById('mobile-menu-button');

                                    if (menu && button && !menu.contains(event.target) && !button.contains(event.target)) {
                                        menu.classList.add('hidden');
                                        document.getElementById('hamburger-icon').classList.remove('hidden');
                                        document.getElementById('close-icon').classList.add('hidden');
                                    }
                                });
                            </script>
                        </div>
                    </div>
                </div>
            </nav>
        @endauth

        <!-- Main Content -->
        <main class="flex-1">
            <div class="container py-4 sm:py-6 px-4">
                <!-- Alerts -->
                @if(session('success'))
                    <div class="alert alert-success">
                        <strong>Başarılı!</strong> {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-error">
                        <strong>Hata!</strong> {{ session('error') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-error">
                        <strong>Hata!</strong>
                        <ul class="mt-2 ml-4 list-disc">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Page Content -->
                @yield('content')
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-primary-900 text-white py-4 mt-8">
            <div class="container text-center">
                <p class="text-sm">&copy; {{ date('Y') }} CANSAN - Duruş Takip Sistemi</p>
            </div>
        </footer>
    </div>
</body>

</html>