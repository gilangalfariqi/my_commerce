<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {!! SEO::generate() !!}

    <!-- Google Fonts: Plus Jakarta Sans for UI/body, Outfit for Headings -->
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
                            gold: '#D4AF37',
                            dark: '#090D16',
                            alabaster: '#FAF9F6',
                        }
                    },
                    boxShadow: {
                        'premium': '0 8px 30px rgb(0,0,0,0.03)',
                        'premium-hover': '0 20px 40px rgb(0,0,0,0.06)',
                        'glow': '0 0 20px rgba(124, 58, 237, 0.15)',
                    }
                }
            }
        }
    </script>
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Midtrans Snap Script -->
    <script type="text/javascript" src="{{ config('midtrans.snap_url') }}" data-client-key="{{ config('midtrans.client_key') }}"></script>

    <style>
        [x-cloak] { display: none !important; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #FAF9F6; /* Alabaster base */
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Outfit', sans-serif;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
    @stack('styles')
</head>
<body class="antialiased text-slate-800 pb-28 md:pb-0" x-data="{ mobileSearchOpen: false }">

    <!-- Top Bar Notice -->
    <div class="bg-gradient-to-r from-brand-dark via-slate-900 to-brand-dark text-white py-2 text-center text-xs font-medium tracking-wider px-4 border-b border-white/5 uppercase">
        <i class="fa-solid fa-sparkles text-amber-400 mr-1.5"></i> Free Shipping across Java for orders above Rp 500.000!
    </div>

    <!-- Main Navigation Header (Glassmorphic) -->
    <header class="sticky top-0 z-40 bg-white/75 backdrop-blur-xl border-b border-slate-100/80 shadow-premium transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between gap-6">
            
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                <span class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-primary-600 to-indigo-500 flex items-center justify-center text-white shadow-premium group-hover:scale-105 group-hover:rotate-3 transition-all duration-300">
                    <i class="fa-solid fa-bag-shopping text-base"></i>
                </span>
                <span class="font-extrabold text-xl sm:text-2xl tracking-tight bg-gradient-to-r from-slate-950 to-slate-700 bg-clip-text text-transparent">
                    {{ config('app.name', 'MyCommerce') }}
                </span>
            </a>

            <!-- Search Bar (Desktop) -->
            <div class="hidden md:flex flex-1 max-w-lg relative" x-data="searchAutocomplete()">
                <div class="relative w-full group">
                    <input 
                        type="text" 
                        placeholder="Search for premium products..." 
                        class="w-full bg-slate-50/50 border border-slate-200/80 rounded-full px-6 py-2.5 pl-12 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white focus:border-transparent transition-all duration-300 shadow-inner"
                        x-model="query"
                        @input.debounce.300ms="fetchSuggestions()"
                    >
                    <i class="fa-solid fa-magnifying-glass absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 text-sm group-focus-within:text-primary-500 transition-colors"></i>
                </div>
                <!-- Suggestions Dropdown -->
                <div x-show="suggestions.length > 0" x-cloak class="absolute left-0 right-0 top-full mt-3 bg-white/95 backdrop-blur-md border border-slate-100 rounded-3xl shadow-premium-hover z-50 overflow-hidden" @click.away="suggestions = []">
                    <div class="px-4 py-2 bg-slate-50 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Suggestions</div>
                    <template x-for="item in suggestions" :key="item.slug">
                        <a :href="item.url" class="flex items-center gap-4 p-3.5 hover:bg-primary-50 border-b border-slate-50 last:border-0 transition-colors duration-200">
                            <img :src="item.image" class="w-12 h-12 object-cover rounded-xl border border-slate-100">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-slate-900 truncate" x-text="item.name"></p>
                                <p class="text-xs text-primary-600 font-bold" x-text="'Rp ' + item.price"></p>
                            </div>
                            <i class="fa-solid fa-chevron-right text-xs text-slate-300 pr-2"></i>
                        </a>
                    </template>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-2 sm:gap-3">
                
                <!-- Search Button (Mobile-only) -->
                <button @click="mobileSearchOpen = !mobileSearchOpen" class="md:hidden w-11 h-11 flex items-center justify-center text-slate-700 hover:text-primary-600 hover:bg-primary-50 rounded-full transition-all duration-300">
                    <i class="fa-solid fa-magnifying-glass text-base"></i>
                </button>

                <!-- Account Dropdown -->
                @auth
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-2 text-sm font-semibold text-slate-700 hover:text-primary-600 focus:outline-none p-1.5 rounded-full hover:bg-slate-50 transition-all duration-300">
                            <span class="w-9 h-9 rounded-full bg-gradient-to-br from-primary-100 to-indigo-100 text-primary-700 flex items-center justify-center font-bold shadow-sm">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </span>
                            <span class="hidden lg:inline pr-1">{{ auth()->user()->name }}</span>
                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 hidden lg:inline"></i>
                        </button>
                        <div x-show="open" x-cloak @click.away="open = false" class="absolute right-0 mt-3 w-56 bg-white border border-slate-100 rounded-3xl shadow-premium-hover py-2.5 z-50">
                            @if(auth()->user()->hasAnyRole(['super-admin', 'admin']))
                                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-primary-50 hover:text-primary-700 font-medium">
                                    <i class="fa-solid fa-gauge-high text-primary-500 text-base"></i> Admin Panel
                                </a>
                            @endif
                            <a href="{{ route('orders.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-primary-50 hover:text-primary-700 font-medium">
                                <i class="fa-solid fa-box text-slate-400 text-base"></i> My Orders
                            </a>
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-primary-50 hover:text-primary-700 font-medium">
                                <i class="fa-solid fa-user-gear text-slate-400 text-base"></i> Settings
                            </a>
                            <hr class="my-2 border-slate-100">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 font-semibold">
                                    <i class="fa-solid fa-right-from-bracket text-red-400 text-base"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="hidden md:inline-flex text-sm font-semibold text-slate-600 hover:text-primary-600 transition-colors">
                        Sign In
                    </a>
                    <a href="{{ route('register') }}" class="hidden md:inline-flex items-center justify-center bg-slate-900 hover:bg-primary-600 text-white px-6 py-2.5 rounded-full text-sm font-semibold shadow-premium hover:shadow-glow transition-all duration-300">
                        Register
                    </a>
                @endauth

                <!-- Cart Button -->
                <button @click="$store.cart.toggleCart()" class="relative w-11 h-11 flex items-center justify-center text-slate-700 hover:text-primary-600 hover:bg-primary-50 rounded-full transition-all duration-300">
                    <i class="fa-solid fa-basket-shopping text-lg"></i>
                    <span x-show="$store.cart.item_count > 0" class="absolute -top-0.5 -right-0.5 w-5 h-5 bg-primary-600 text-white rounded-full flex items-center justify-center text-[10px] font-extrabold ring-2 ring-white animate-bounce" x-text="$store.cart.item_count"></span>
                </button>
            </div>
        </div>
    </header>

    <!-- Mobile Search Panel (Slide down) -->
    <div x-show="mobileSearchOpen" x-cloak x-transition:enter="transition ease-out duration-250" x-transition:enter-start="-translate-y-4 opacity-0" x-transition:enter-end="translate-y-0 opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-y-0 opacity-100" x-transition:leave-end="-translate-y-4 opacity-0" class="md:hidden bg-white/95 backdrop-blur-md border-b border-slate-100 px-4 py-3.5 shadow-md sticky top-20 z-30">
        <div class="relative w-full" x-data="searchAutocomplete()">
            <input 
                type="text" 
                placeholder="Search products..." 
                class="w-full bg-slate-50 border border-slate-200 rounded-full px-5 py-2.5 pl-10 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all shadow-inner"
                x-model="query"
                @input.debounce.300ms="fetchSuggestions()"
            >
            <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            
            <!-- Autocomplete Suggestions -->
            <div x-show="suggestions.length > 0" x-cloak class="absolute left-0 right-0 top-full mt-2 bg-white/95 backdrop-blur-md border border-slate-100 rounded-2xl shadow-premium z-50 overflow-hidden" @click.away="suggestions = []">
                <template x-for="item in suggestions" :key="item.slug">
                    <a :href="item.url" class="flex items-center gap-3.5 p-3 hover:bg-primary-50 border-b border-slate-50 last:border-0 transition-colors">
                        <img :src="item.image" class="w-10 h-10 object-cover rounded-xl border border-slate-100 flex-shrink-0">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-bold text-slate-900 truncate" x-text="item.name"></p>
                            <p class="text-xs text-primary-600 font-extrabold" x-text="'Rp ' + item.price"></p>
                        </div>
                    </a>
                </template>
            </div>
        </div>
    </div>

    <!-- Main View Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        @if(session('success'))
            <div class="mb-8 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-3xl p-5 flex items-center gap-3.5 shadow-premium">
                <span class="w-9 h-9 rounded-2xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center text-lg"><i class="fa-solid fa-circle-check"></i></span>
                <span class="text-sm font-semibold">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-8 bg-rose-50 border border-rose-100 text-rose-800 rounded-3xl p-5 flex items-center gap-3.5 shadow-premium">
                <span class="w-9 h-9 rounded-2xl bg-rose-500/10 text-rose-600 flex items-center justify-center text-lg"><i class="fa-solid fa-triangle-exclamation"></i></span>
                <span class="text-sm font-semibold">{{ session('error') }}</span>
            </div>
        @endif

        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-brand-dark text-slate-400 pt-20 pb-12 mt-28 border-t border-slate-800/60 relative overflow-hidden">
        <!-- Subtle glow element -->
        <div class="absolute top-0 left-1/4 -translate-y-1/2 w-96 h-96 bg-primary-900/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-4 gap-12 relative z-10">
            <div class="space-y-6">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-2xl bg-primary-600 flex items-center justify-center text-white shadow-glow">
                        <i class="fa-solid fa-bag-shopping"></i>
                    </span>
                    <span class="font-bold text-white text-2xl tracking-tight">{{ config('app.name') }}</span>
                </div>
                <p class="text-sm leading-relaxed text-slate-400/80">Experience luxury, curated quality, and seamless secure payments locally.</p>
                <div class="flex gap-3">
                    <a href="#" class="w-9 h-9 rounded-xl bg-slate-800 hover:bg-primary-600 hover:text-white flex items-center justify-center text-sm transition-all duration-300"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="w-9 h-9 rounded-xl bg-slate-800 hover:bg-primary-600 hover:text-white flex items-center justify-center text-sm transition-all duration-300"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" class="w-9 h-9 rounded-xl bg-slate-800 hover:bg-primary-600 hover:text-white flex items-center justify-center text-sm transition-all duration-300"><i class="fa-brands fa-twitter"></i></a>
                </div>
            </div>
            <div>
                <h3 class="font-bold text-white text-sm uppercase tracking-wider mb-6">Shop</h3>
                <ul class="space-y-3.5 text-sm">
                    <li><a href="{{ route('products.index') }}" class="hover:text-white hover:underline decoration-primary-500 decoration-2 transition-all">All Products</a></li>
                    <li><a href="{{ route('products.index', ['sort' => 'latest']) }}" class="hover:text-white hover:underline decoration-primary-500 decoration-2 transition-all">New Arrivals</a></li>
                    <li><a href="{{ route('home') }}#flash-sale" class="hover:text-white hover:underline decoration-primary-500 decoration-2 transition-all">Flash Sales</a></li>
                </ul>
            </div>
            <div>
                <h3 class="font-bold text-white text-sm uppercase tracking-wider mb-6">Support</h3>
                <ul class="space-y-3.5 text-sm">
                    <li><a href="{{ route('orders.track') }}" class="hover:text-white hover:underline decoration-primary-500 decoration-2 transition-all">Track Order</a></li>
                    <li><a href="#" class="hover:text-white hover:underline decoration-primary-500 decoration-2 transition-all">Shipping Info</a></li>
                    <li><a href="#" class="hover:text-white hover:underline decoration-primary-500 decoration-2 transition-all">Return Policy</a></li>
                </ul>
            </div>
            <div>
                <h3 class="font-bold text-white text-sm uppercase tracking-wider mb-6">Contacts</h3>
                <p class="text-sm">Email: <span class="text-slate-300">support@mycommerce.com</span></p>
                <p class="text-sm mt-2">Phone: <span class="text-slate-300">+62 812-3456-7890</span></p>
                <p class="text-xs text-slate-500 mt-4 leading-relaxed">Office Hours:<br>Mon - Fri / 9:00 AM - 6:00 PM</p>
            </div>
        </div>
        <hr class="my-12 border-slate-800/80 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-6 text-xs text-slate-500">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Built with elegance.</p>
            <div class="flex gap-6">
                <a href="#" class="hover:text-slate-300 transition-colors">Privacy Policy</a>
                <a href="#" class="hover:text-slate-300 transition-colors">Terms of Service</a>
            </div>
        </div>
    </footer>

    <!-- Slide-out Cart Drawer -->
    <div x-show="$store.cart.cartOpen" x-cloak class="relative z-50" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
        <div x-show="$store.cart.cartOpen" x-transition:enter="ease-in-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in-out duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="$store.cart.toggleCart()"></div>

        <div class="fixed inset-0 overflow-hidden">
            <div class="absolute inset-0 overflow-hidden">
                <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                    <div x-show="$store.cart.cartOpen" x-transition:enter="transform transition ease-in-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transform transition ease-in-out duration-300" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full" class="pointer-events-auto w-screen max-w-md">
                        <div class="flex h-full flex-col overflow-y-scroll bg-white shadow-2xl border-l border-slate-100">
                            <div class="flex-1 overflow-y-auto px-6 py-8">
                                <div class="flex items-start justify-between">
                                    <h2 class="text-xl font-bold text-slate-900" id="slide-over-title">Shopping Cart</h2>
                                    <div class="ml-3 flex h-7 items-center">
                                        <button type="button" class="relative -m-2 p-2 text-slate-400 hover:text-slate-600 rounded-full hover:bg-slate-50 transition-colors" @click="$store.cart.toggleCart()">
                                            <span class="absolute -m-2"></span>
                                            <i class="fa-solid fa-xmark text-lg"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="mt-8">
                                    <div class="flow-root">
                                        <ul role="list" class="-my-6 divide-y divide-slate-100">
                                            <template x-for="item in $store.cart.items" :key="item.id">
                                                <li class="flex py-6 group">
                                                    <div class="h-24 w-24 flex-shrink-0 overflow-hidden rounded-2xl border border-slate-100 shadow-sm relative">
                                                        <img :src="item.image" class="h-full w-full object-cover object-center group-hover:scale-105 transition-transform duration-300">
                                                    </div>

                                                    <div class="ml-4 flex flex-1 flex-col justify-between">
                                                        <div>
                                                            <div class="flex justify-between text-sm font-semibold text-slate-900">
                                                                <h3>
                                                                    <a :href="item.product_slug" class="hover:text-primary-600 transition-colors" x-text="item.product_name"></a>
                                                                </h3>
                                                                <p class="ml-4 text-primary-600 font-bold" x-text="'Rp ' + item.formatted_subtotal"></p>
                                                            </div>
                                                            <p class="mt-1.5 text-xs font-medium text-slate-400" x-text="item.variant_name ?? ''"></p>
                                                        </div>
                                                        <div class="flex flex-1 items-end justify-between text-xs mt-2">
                                                            <div class="flex items-center border border-slate-200/80 rounded-full bg-slate-50 overflow-hidden shadow-inner">
                                                                <button @click="$store.cart.updateQty(item.id, item.quantity - 1)" class="px-2.5 py-1.5 hover:bg-slate-100 text-slate-500 transition-colors"><i class="fa-solid fa-minus text-[9px]"></i></button>
                                                                <span class="px-3 font-bold text-slate-800 text-xs" x-text="item.quantity"></span>
                                                                <button @click="$store.cart.updateQty(item.id, item.quantity + 1)" class="px-2.5 py-1.5 hover:bg-slate-100 text-slate-500 transition-colors"><i class="fa-solid fa-plus text-[9px]"></i></button>
                                                            </div>

                                                            <div class="flex">
                                                                <button type="button" @click="$store.cart.removeCartItem(item.id)" class="font-semibold text-rose-500 hover:text-rose-600 hover:bg-rose-50 px-2.5 py-1.5 rounded-xl transition-all flex items-center gap-1.5">
                                                                    <i class="fa-solid fa-trash-can text-[10px]"></i> Remove
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                            </template>
                                            <div x-show="$store.cart.items && $store.cart.items.length === 0" class="text-center py-20">
                                                <span class="w-16 h-16 bg-slate-50 text-slate-300 rounded-3xl flex items-center justify-center mx-auto mb-4 text-2xl"><i class="fa-solid fa-basket-shopping"></i></span>
                                                <p class="text-sm font-semibold text-slate-500">Your cart is currently empty.</p>
                                                <button @click="$store.cart.toggleCart()" class="mt-4 text-xs font-bold text-primary-600 hover:underline">Start Shopping</button>
                                            </div>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="border-t border-slate-100 px-6 py-6 bg-slate-50/50">
                                <div class="flex justify-between text-sm text-slate-500 mb-2">
                                    <p class="font-medium">Subtotal</p>
                                    <p class="text-slate-900 font-bold" x-text="'Rp ' + $store.cart.formatted_subtotal"></p>
                                </div>
                                <div x-show="$store.cart.discount > 0" class="flex justify-between text-sm text-emerald-600 mb-2 font-medium">
                                    <p>Discount</p>
                                    <p x-text="'- Rp ' + $store.cart.formatted_discount"></p>
                                </div>
                                <hr class="my-4 border-slate-200/60">
                                <div class="flex justify-between text-base font-extrabold text-slate-900 mb-6">
                                    <p>Grand Total</p>
                                    <p class="text-primary-600 text-lg" x-text="'Rp ' + $store.cart.formatted_grand_total"></p>
                                </div>

                                <div class="space-y-3">
                                    <a href="{{ route('checkout.index') }}" class="flex items-center justify-center rounded-full bg-slate-900 hover:bg-primary-600 text-white px-6 py-3.5 text-sm font-bold shadow-premium hover:shadow-glow transition-all duration-300">
                                        Checkout Now <i class="fa-solid fa-arrow-right ml-2 text-xs"></i>
                                    </a>
                                    <button @click="$store.cart.toggleCart()" class="w-full flex items-center justify-center rounded-full border border-slate-200 bg-white px-6 py-3.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                                        Continue Shopping
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sticky Floating Bottom Navigation (Mobile-first Navigation) -->
    <div class="md:hidden fixed bottom-6 left-1/2 -translate-x-1/2 bg-white/90 backdrop-blur-xl border border-slate-200/50 flex justify-around py-3 px-4 z-40 shadow-premium-hover rounded-full w-[90%] max-w-sm">
        <a href="{{ route('home') }}" class="flex flex-col items-center gap-0.5 text-slate-400 hover:text-primary-600 transition-colors {{ request()->routeIs('home') ? 'text-primary-600 font-bold' : '' }}">
            <i class="fa-solid fa-house text-base"></i>
            <span class="text-[9px] tracking-wide uppercase font-semibold">Home</span>
        </a>
        <a href="{{ route('products.index') }}" class="flex flex-col items-center gap-0.5 text-slate-400 hover:text-primary-600 transition-colors {{ request()->routeIs('products.index') ? 'text-primary-600 font-bold' : '' }}">
            <i class="fa-solid fa-magnifying-glass text-base"></i>
            <span class="text-[9px] tracking-wide uppercase font-semibold">Search</span>
        </a>
        <button @click="$store.cart.toggleCart()" class="flex flex-col items-center gap-0.5 text-slate-400 hover:text-primary-600 relative transition-colors">
            <i class="fa-solid fa-basket-shopping text-base"></i>
            <span x-show="$store.cart.item_count > 0" class="absolute -top-1 -right-1 bg-primary-600 text-white rounded-full w-4 h-4 text-[8px] flex items-center justify-center font-black" x-text="$store.cart.item_count"></span>
            <span class="text-[9px] tracking-wide uppercase font-semibold">Cart</span>
        </button>
        <a href="{{ route('orders.index') }}" class="flex flex-col items-center gap-0.5 text-slate-400 hover:text-primary-600 transition-colors {{ request()->routeIs('orders.index') ? 'text-primary-600 font-bold' : '' }}">
            <i class="fa-solid fa-box text-base"></i>
            <span class="text-[9px] tracking-wide uppercase font-semibold">Orders</span>
        </a>
        @auth
            <a href="{{ route('profile.edit') }}" class="flex flex-col items-center gap-0.5 text-slate-400 hover:text-primary-600 transition-colors">
                <i class="fa-solid fa-user text-base"></i>
                <span class="text-[9px] tracking-wide uppercase font-semibold">Profile</span>
            </a>
        @else
            <a href="{{ route('login') }}" class="flex flex-col items-center gap-0.5 text-slate-400 hover:text-primary-600 transition-colors">
                <i class="fa-solid fa-right-to-bracket text-base"></i>
                <span class="text-[9px] tracking-wide uppercase font-semibold">Login</span>
            </a>
        @endauth
    </div>

    <!-- Dynamic Toast Notification Manager -->
    <div x-data="toastManager()" @toast.window="add($event.detail)" class="fixed bottom-24 md:bottom-8 right-4 z-50 flex flex-col gap-3 max-w-sm w-[90%] pointer-events-none">
        <template x-for="t in toasts" :key="t.id">
            <div x-transition class="p-4 rounded-2xl shadow-premium-hover flex items-center gap-3 border pointer-events-auto transition-all duration-300"
                 :class="{
                    'bg-emerald-50 border-emerald-100 text-emerald-800': t.type === 'success',
                    'bg-rose-50 border-rose-100 text-rose-800': t.type === 'error'
                 }">
                <i class="fa-solid" :class="t.type === 'success' ? 'fa-circle-check text-emerald-500' : 'fa-circle-exclamation text-rose-500'"></i>
                <p class="text-xs font-semibold" x-text="t.message"></p>
            </div>
        </template>
    </div>

    <!-- Global Cart Scripts -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('cart', {
                cartOpen: false,
                items: [],
                subtotal: 0,
                formatted_subtotal: '0',
                discount: 0,
                formatted_discount: '0',
                grand_total: 0,
                formatted_grand_total: '0',
                item_count: 0,

                init() {
                    this.fetchCart();
                },
                toggleCart() {
                    this.cartOpen = !this.cartOpen;
                    if (this.cartOpen) this.fetchCart();
                },
                async fetchCart() {
                    try {
                        const response = await fetch('{{ route("cart.index") }}');
                        if (response.ok) {
                            const data = await response.json();
                            this.items = data.items || [];
                            this.subtotal = data.subtotal || 0;
                            this.formatted_subtotal = data.formatted_subtotal || '0';
                            this.discount = data.discount || 0;
                            this.formatted_discount = data.formatted_discount || '0';
                            this.grand_total = data.grand_total || 0;
                            this.formatted_grand_total = data.formatted_grand_total || '0';
                            this.item_count = data.item_count || 0;
                        }
                    } catch (e) {
                        console.error('Failed to fetch cart', e);
                    }
                },
                async addToCart(productId, variantId = null, quantity = 1) {
                    try {
                        const response = await fetch('{{ route("cart.store") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                product_id: productId,
                                product_variant_id: variantId,
                                quantity: quantity
                            })
                        });
                        const res = await response.json();
                        if (res.success) {
                            window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: res.message } }));
                            await this.fetchCart();
                            this.cartOpen = true;
                        } else {
                            window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: res.message } }));
                        }
                    } catch (e) {
                        window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: 'Failed to add item.' } }));
                    }
                },
                async updateQty(itemId, quantity) {
                    try {
                        const response = await fetch(`/cart/${itemId}`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({ quantity })
                        });
                        const res = await response.json();
                        if (res.success) {
                            await this.fetchCart();
                        }
                    } catch (e) {
                        console.error('Update qty failed', e);
                    }
                },
                async removeCartItem(itemId) {
                    try {
                        const response = await fetch(`/cart/${itemId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });
                        const res = await response.json();
                        if (res.success) {
                            window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: res.message } }));
                            await this.fetchCart();
                        }
                    } catch (e) {
                        console.error('Remove failed', e);
                    }
                }
            });
        });

        function searchAutocomplete() {
            return {
                query: '',
                suggestions: [],
                async fetchSuggestions() {
                    if (this.query.length < 2) {
                        this.suggestions = [];
                        return;
                    }
                    try {
                        const response = await fetch(`/products/autocomplete?q=${encodeURIComponent(this.query)}`);
                        if (response.ok) {
                            this.suggestions = await response.json();
                        }
                    } catch(e) {
                        console.error(e);
                    }
                }
            }
        }

        function toastManager() {
            return {
                toasts: [],
                add(detail) {
                    const id = Date.now();
                    this.toasts.push({
                        id: id,
                        type: detail.type,
                        message: detail.message
                    });
                    setTimeout(() => {
                        this.toasts = this.toasts.filter(t => t.id !== id);
                    }, 4000);
                }
            }
        }
    </script>
    @stack('scripts')
</body>
</html>
