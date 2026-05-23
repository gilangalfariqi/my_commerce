<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Admin Dashboard - {{ config('app.name', 'MyCommerce') }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- CSS / Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        heading: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#f5f3ff',
                            100: '#ede9fe',
                            200: '#ddd6fe',
                            300: '#c4b5fd',
                            400: '#a78bfa',
                            500: '#8b5cf6',
                            600: '#7c3aed',
                            700: '#6d28d9',
                            800: '#5b21b6',
                            900: '#4c1d95',
                        },
                        brand: {
                            dark: '#0B0F19',
                            slate: '#1E293B',
                        }
                    },
                    boxShadow: {
                        'premium': '0 8px 30px rgb(0,0,0,0.02)',
                        'premium-hover': '0 20px 40px rgb(0,0,0,0.05)',
                        'sidebar-active': '0 4px 20px rgba(124, 58, 237, 0.25)',
                    }
                }
            }
        }
    </script>

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- AlpineJS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Outfit', sans-serif;
        }
    </style>
    @stack('styles')
</head>
<body class="antialiased text-slate-800" x-data="{ sidebarOpen: false }">

    <!-- Mobile Header -->
    <div class="lg:hidden flex items-center justify-between bg-brand-dark text-white p-4 sticky top-0 z-30 shadow-md">
        <div class="flex items-center gap-3">
            <span class="w-9 h-9 rounded-xl bg-gradient-to-tr from-primary-600 to-indigo-500 flex items-center justify-center text-white shadow-sm"><i class="fa-solid fa-gauge-high"></i></span>
            <span class="font-bold text-lg tracking-tight">MyCommerce Admin</span>
        </div>
        <button @click="sidebarOpen = !sidebarOpen" class="p-2 hover:bg-slate-800 rounded-xl text-white transition-colors">
            <i class="fa-solid fa-bars text-xl"></i>
        </button>
    </div>

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 z-40 w-66 bg-brand-dark text-slate-400 transform -translate-x-full lg:translate-x-0 lg:static lg:flex-shrink-0 transition-transform duration-300 ease-in-out flex flex-col justify-between border-r border-slate-800/40 shadow-xl"
               :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}">
            
            <div class="p-6">
                <!-- Branding -->
                <div class="hidden lg:flex items-center gap-3.5 mb-10">
                    <span class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-primary-600 to-indigo-500 flex items-center justify-center text-white shadow-sidebar-active"><i class="fa-solid fa-gauge-high text-base"></i></span>
                    <span class="font-black text-white text-xl tracking-tight">Admin Console</span>
                </div>

                <!-- Navigation links -->
                <nav class="space-y-1">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3.5 px-4.5 py-3 rounded-2xl text-sm font-semibold transition-all duration-300 {{ request()->routeIs('admin.dashboard') ? 'bg-primary-600 text-white shadow-sidebar-active font-bold' : 'hover:bg-slate-800/60 hover:text-white' }}">
                        <i class="fa-solid fa-chart-pie w-5 text-center text-base"></i> Dashboard
                    </a>
                    <a href="{{ route('admin.products.index') }}" class="flex items-center gap-3.5 px-4.5 py-3 rounded-2xl text-sm font-semibold transition-all duration-300 {{ request()->routeIs('admin.products.*') ? 'bg-primary-600 text-white shadow-sidebar-active font-bold' : 'hover:bg-slate-800/60 hover:text-white' }}">
                        <i class="fa-solid fa-box-open w-5 text-center text-base"></i> Products
                    </a>
                    <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-3.5 px-4.5 py-3 rounded-2xl text-sm font-semibold transition-all duration-300 {{ request()->routeIs('admin.categories.*') ? 'bg-primary-600 text-white shadow-sidebar-active font-bold' : 'hover:bg-slate-800/60 hover:text-white' }}">
                        <i class="fa-solid fa-tags w-5 text-center text-base"></i> Categories
                    </a>
                    <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-3.5 px-4.5 py-3 rounded-2xl text-sm font-semibold transition-all duration-300 {{ request()->routeIs('admin.orders.*') ? 'bg-primary-600 text-white shadow-sidebar-active font-bold' : 'hover:bg-slate-800/60 hover:text-white' }}">
                        <i class="fa-solid fa-receipt w-5 text-center text-base"></i> Orders
                    </a>
                    <a href="{{ route('admin.coupons.index') }}" class="flex items-center gap-3.5 px-4.5 py-3 rounded-2xl text-sm font-semibold transition-all duration-300 {{ request()->routeIs('admin.coupons.*') ? 'bg-primary-600 text-white shadow-sidebar-active font-bold' : 'hover:bg-slate-800/60 hover:text-white' }}">
                        <i class="fa-solid fa-ticket w-5 text-center text-base"></i> Coupons
                    </a>
                    <a href="{{ route('admin.flash-sales.index') }}" class="flex items-center gap-3.5 px-4.5 py-3 rounded-2xl text-sm font-semibold transition-all duration-300 {{ request()->routeIs('admin.flash-sales.*') ? 'bg-primary-600 text-white shadow-sidebar-active font-bold' : 'hover:bg-slate-800/60 hover:text-white' }}">
                        <i class="fa-solid fa-bolt w-5 text-center text-base"></i> Flash Sales
                    </a>
                    <a href="{{ route('admin.banners.index') }}" class="flex items-center gap-3.5 px-4.5 py-3 rounded-2xl text-sm font-semibold transition-all duration-300 {{ request()->routeIs('admin.banners.*') ? 'bg-primary-600 text-white shadow-sidebar-active font-bold' : 'hover:bg-slate-800/60 hover:text-white' }}">
                        <i class="fa-solid fa-image w-5 text-center text-base"></i> Slider Banners
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3.5 px-4.5 py-3 rounded-2xl text-sm font-semibold transition-all duration-300 {{ request()->routeIs('admin.users.*') ? 'bg-primary-600 text-white shadow-sidebar-active font-bold' : 'hover:bg-slate-800/60 hover:text-white' }}">
                        <i class="fa-solid fa-users w-5 text-center text-base"></i> Users
                    </a>
                    <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-3.5 px-4.5 py-3 rounded-2xl text-sm font-semibold transition-all duration-300 {{ request()->routeIs('admin.settings.*') ? 'bg-primary-600 text-white shadow-sidebar-active font-bold' : 'hover:bg-slate-800/60 hover:text-white' }}">
                        <i class="fa-solid fa-sliders w-5 text-center text-base"></i> Store Settings
                    </a>
                </nav>
            </div>

            <div class="p-6 border-t border-slate-800/50">
                <!-- User card -->
                <div class="flex items-center gap-3 mb-5">
                    <span class="w-10 h-10 rounded-full bg-slate-800 border border-slate-700/60 flex items-center justify-center font-bold text-white shadow-inner">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Super Admin</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2.5 px-4.5 py-3 rounded-2xl border border-slate-800 text-sm font-bold text-rose-400 hover:bg-slate-800/50 hover:text-rose-300 transition-all duration-300">
                        <i class="fa-solid fa-right-from-bracket"></i> Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 overflow-x-hidden">
            <!-- Header bar -->
            <header class="bg-white border-b border-slate-100/80 h-20 hidden lg:flex items-center justify-between px-8 shadow-premium">
                <h2 class="text-lg font-bold text-slate-900">
                    @if(request()->routeIs('admin.dashboard')) Dashboard
                    @elseif(request()->routeIs('admin.products.*')) Products Management
                    @elseif(request()->routeIs('admin.categories.*')) Category Directory
                    @elseif(request()->routeIs('admin.orders.*')) Orders Ledger
                    @elseif(request()->routeIs('admin.coupons.*')) Promotional Coupons
                    @elseif(request()->routeIs('admin.flash-sales.*')) Active Flash Sales
                    @elseif(request()->routeIs('admin.banners.*')) Media Slider Banners
                    @elseif(request()->routeIs('admin.users.*')) User Accounts
                    @elseif(request()->routeIs('admin.settings.*')) Console Store Settings
                    @else Control Panel
                    @endif
                </h2>
                <div class="flex items-center gap-5">
                    <a href="{{ route('home') }}" target="_blank" class="text-sm text-primary-600 hover:text-primary-700 font-semibold flex items-center gap-1.5 hover:underline transition-colors">
                        <i class="fa-solid fa-square-arrow-up-right"></i> View Shopfront
                    </a>
                    <span class="text-slate-200">|</span>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest bg-slate-50 border border-slate-100 rounded-md px-2.5 py-1">Active Session</span>
                </div>
            </header>

            <!-- Main view layout -->
            <main class="flex-1 p-6 md:p-8 bg-slate-50/50">
                @if(session('success'))
                    <div class="mb-6 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-3xl p-5 flex items-center gap-3.5 shadow-premium">
                        <span class="w-9 h-9 rounded-2xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center text-lg"><i class="fa-solid fa-circle-check"></i></span>
                        <span class="text-sm font-semibold">{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 bg-rose-50 border border-rose-100 text-rose-800 rounded-3xl p-5 flex items-center gap-3.5 shadow-premium">
                        <span class="w-9 h-9 rounded-2xl bg-rose-500/10 text-rose-600 flex items-center justify-center text-lg"><i class="fa-solid fa-triangle-exclamation"></i></span>
                        <span class="text-sm font-semibold">{{ session('error') }}</span>
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>

    <!-- Mobile Close overlay -->
    <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-30 lg:hidden"></div>

    @stack('scripts')
</body>
</html>
