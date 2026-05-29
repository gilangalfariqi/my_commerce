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
                            50: '#fef2f2',
                            100: '#fee2e2',
                            200: '#fecaca',
                            300: '#fca5a5',
                            400: '#f87171',
                            500: '#ef4444',
                            600: '#dc2626',
                            700: '#b91c1c',
                            800: '#991b1b',
                            900: '#7f1d1d',
                        },
                        brand: {
                            gold: '#D4AF37',
                            dark: '#05070c',
                            card: '#0d1321',
                            alabaster: '#0b0f19',
                        }
                    },
                    boxShadow: {
                        'premium': '0 8px 30px rgb(0,0,0,0.2)',
                        'premium-hover': '0 20px 40px rgb(0,0,0,0.4)',
                        'glow': '0 0 20px rgba(220, 38, 38, 0.3)',
                    }
                }
            }
        }
    </script>
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>



    <style>
        [x-cloak] { display: none !important; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #0b0f19; /* Sleek dark grey/black */
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Outfit', sans-serif;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #0d1321;
        }
        ::-webkit-scrollbar-thumb {
            background: #1e293b;
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #334155;
        }
    </style>
    @stack('styles')
</head>
<body class="antialiased text-slate-200 pb-28 md:pb-0" x-data="{ mobileSearchOpen: false }">

    <!-- Main Navigation Header (Glassmorphic Dark) -->
    <header class="sticky top-0 z-40 bg-slate-950/80 backdrop-blur-xl border-b border-slate-900/80 shadow-premium transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between gap-6">
            
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                <span class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-primary-600 to-red-500 flex items-center justify-center text-white shadow-premium group-hover:scale-105 group-hover:rotate-3 transition-all duration-300">
                    <i class="fa-solid fa-gears text-base"></i>
                </span>
                <span class="font-extrabold text-xl sm:text-2xl tracking-tight text-white">
                    MotoPart<span class="text-primary-600">Hub</span>
                </span>
            </a>

            <!-- Search Bar (Desktop) -->
            <div class="hidden md:flex flex-1 max-w-lg relative" x-data="searchAutocomplete()">
                <div class="relative w-full group">
                    <input 
                        type="text" 
                        placeholder="Cari suku cadang, kode part, atau motor..." 
                        class="w-full bg-slate-900/60 border border-slate-800 rounded-full px-6 py-2.5 pl-12 text-sm text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-slate-900 focus:border-transparent transition-all duration-300 shadow-inner"
                        x-model="query"
                        @input.debounce.300ms="fetchSuggestions()"
                    >
                    <i class="fa-solid fa-magnifying-glass absolute left-5 top-1/2 -translate-y-1/2 text-slate-500 text-sm group-focus-within:text-primary-500 transition-colors"></i>
                </div>
                <!-- Suggestions Dropdown -->
                <div x-show="suggestions.length > 0" x-cloak class="absolute left-0 right-0 top-full mt-3 bg-slate-900/95 backdrop-blur-md border border-slate-800 rounded-3xl shadow-premium z-50 overflow-hidden" @click.away="suggestions = []">
                    <div class="px-4 py-2 bg-slate-950 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Hasil Pencarian</div>
                    <template x-for="item in suggestions" :key="item.slug">
                        <a :href="item.url" class="flex items-center gap-4 p-3.5 hover:bg-slate-800/80 border-b border-slate-850 last:border-0 transition-colors duration-200">
                            <img :src="item.image" class="w-12 h-12 object-cover rounded-xl border border-slate-800 flex-shrink-0">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-slate-200 truncate" x-text="item.name"></p>
                                <p class="text-xs text-primary-500 font-bold" x-text="'Rp ' + item.price"></p>
                            </div>
                            <i class="fa-solid fa-chevron-right text-xs text-slate-600 pr-2"></i>
                        </a>
                    </template>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-2 sm:gap-3">
                
                <!-- Search Button (Mobile-only) -->
                <button @click="mobileSearchOpen = !mobileSearchOpen" class="md:hidden w-11 h-11 flex items-center justify-center text-slate-300 hover:text-white hover:bg-slate-900 rounded-full transition-all duration-300">
                    <i class="fa-solid fa-magnifying-glass text-base"></i>
                </button>

                <!-- Account Dropdown -->
                @auth
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-2 text-sm font-semibold text-slate-300 hover:text-white focus:outline-none p-1.5 rounded-full hover:bg-slate-900 transition-all duration-300">
                            <span class="w-9 h-9 rounded-full bg-gradient-to-br from-primary-600/20 to-red-600/20 text-primary-400 flex items-center justify-center font-bold shadow-sm">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </span>
                            <span class="hidden lg:inline pr-1">{{ auth()->user()->name }}</span>
                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-500 hidden lg:inline"></i>
                        </button>
                        <div x-show="open" x-cloak @click.away="open = false" class="absolute right-0 mt-3 w-56 bg-slate-900 border border-slate-800 rounded-3xl shadow-premium py-2.5 z-50 overflow-hidden">
                            @if(auth()->user()->hasAnyRole(['super-admin', 'admin']))
                                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-300 hover:bg-slate-800 hover:text-white font-medium">
                                    <i class="fa-solid fa-gauge-high text-primary-500 text-base"></i> Admin Panel
                                </a>
                            @endif
                            <a href="{{ route('orders.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-300 hover:bg-slate-800 hover:text-white font-medium">
                                <i class="fa-solid fa-box text-slate-500 text-base"></i> My Orders
                            </a>
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-300 hover:bg-slate-800 hover:text-white font-medium">
                                <i class="fa-solid fa-user-gear text-slate-500 text-base"></i> Settings
                            </a>
                            <hr class="my-2 border-slate-800">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left flex items-center gap-3 px-4 py-2.5 text-sm text-red-500 hover:bg-red-950/20 font-semibold">
                                    <i class="fa-solid fa-right-from-bracket text-red-400 text-base"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="hidden md:inline-flex text-sm font-semibold text-slate-300 hover:text-primary-500 transition-colors">
                        Sign In
                    </a>
                    <a href="{{ route('register') }}" class="hidden md:inline-flex items-center justify-center bg-primary-600 hover:bg-primary-700 text-white px-6 py-2.5 rounded-full text-sm font-semibold shadow-premium hover:shadow-glow transition-all duration-300">
                        Register
                    </a>
                @endauth

                <!-- Cart Button -->
                <button @click="$store.cart.toggleCart()" class="relative w-11 h-11 flex items-center justify-center text-slate-300 hover:text-primary-500 hover:bg-slate-900 rounded-full transition-all duration-300">
                    <i class="fa-solid fa-basket-shopping text-lg"></i>
                    <span x-show="$store.cart.item_count > 0" class="absolute -top-0.5 -right-0.5 w-5 h-5 bg-primary-600 text-white rounded-full flex items-center justify-center text-[10px] font-extrabold ring-2 ring-slate-950 animate-bounce" x-text="$store.cart.item_count"></span>
                </button>
            </div>
        </div>
    </header>

    <!-- Mobile Search Panel (Slide down) -->
    <div x-show="mobileSearchOpen" x-cloak x-transition:enter="transition ease-out duration-250" x-transition:enter-start="-translate-y-4 opacity-0" x-transition:enter-end="translate-y-0 opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-y-0 opacity-100" x-transition:leave-end="-translate-y-4 opacity-0" class="md:hidden bg-slate-900/95 backdrop-blur-md border-b border-slate-800 px-4 py-3.5 shadow-md sticky top-20 z-30">
        <div class="relative w-full" x-data="searchAutocomplete()">
            <input 
                type="text" 
                placeholder="Cari suku cadang..." 
                class="w-full bg-slate-950 border border-slate-800 rounded-full px-5 py-2.5 pl-10 text-sm font-semibold text-slate-100 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-slate-950 transition-all shadow-inner"
                x-model="query"
                @input.debounce.300ms="fetchSuggestions()"
            >
            <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 text-sm"></i>
            
            <!-- Autocomplete Suggestions -->
            <div x-show="suggestions.length > 0" x-cloak class="absolute left-0 right-0 top-full mt-2 bg-slate-950/95 backdrop-blur-md border border-slate-800 rounded-2xl shadow-premium z-50 overflow-hidden" @click.away="suggestions = []">
                <template x-for="item in suggestions" :key="item.slug">
                    <a :href="item.url" class="flex items-center gap-3.5 p-3 hover:bg-slate-900 border-b border-slate-850 last:border-0 transition-colors">
                        <img :src="item.image" class="w-10 h-10 object-cover rounded-xl border border-slate-800 flex-shrink-0">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-bold text-slate-200 truncate" x-text="item.name"></p>
                            <p class="text-xs text-primary-500 font-extrabold" x-text="'Rp ' + item.price"></p>
                        </div>
                    </a>
                </template>
            </div>
        </div>
    </div>

    <!-- Main View Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        @if(session('success'))
            <div class="mb-8 bg-emerald-950/20 border border-emerald-900/50 text-emerald-400 rounded-3xl p-5 flex items-center gap-3.5 shadow-premium">
                <span class="w-9 h-9 rounded-2xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-lg"><i class="fa-solid fa-circle-check"></i></span>
                <span class="text-sm font-semibold">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-8 bg-rose-950/20 border border-rose-900/50 text-rose-400 rounded-3xl p-5 flex items-center gap-3.5 shadow-premium">
                <span class="w-9 h-9 rounded-2xl bg-rose-500/10 text-rose-400 flex items-center justify-center text-lg"><i class="fa-solid fa-triangle-exclamation"></i></span>
                <span class="text-sm font-semibold">{{ session('error') }}</span>
            </div>
        @endif

        {{ $slot ?? '' }}
    </main>

    <!-- Footer -->
    <footer class="bg-brand-dark text-slate-400 pt-20 pb-12 mt-28 border-t border-slate-900/80 relative overflow-hidden">
        <!-- Subtle glow element -->
        <div class="absolute top-0 left-1/4 -translate-y-1/2 w-96 h-96 bg-primary-900/5 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-4 gap-12 relative z-10">
            <div class="space-y-6">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-2xl bg-primary-600 flex items-center justify-center text-white shadow-glow">
                        <i class="fa-solid fa-gears"></i>
                    </span>
                    <span class="font-bold text-white text-2xl tracking-tight">MotoPart<span class="text-primary-500">Hub</span></span>
                </div>
                <p class="text-sm leading-relaxed text-slate-400/80">Destinasi utama suku cadang asli dan aftermarket performa tinggi terbaik di Indonesia. Rawat motor Anda dengan produk berstandar internasional.</p>
                <div class="flex gap-3">
                    <a href="#" class="w-9 h-9 rounded-xl bg-slate-900 border border-slate-800 hover:border-primary-500 text-slate-400 hover:text-white flex items-center justify-center text-sm transition-all duration-300"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="w-9 h-9 rounded-xl bg-slate-900 border border-slate-800 hover:border-primary-500 text-slate-400 hover:text-white flex items-center justify-center text-sm transition-all duration-300"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" class="w-9 h-9 rounded-xl bg-slate-900 border border-slate-800 hover:border-primary-500 text-slate-400 hover:text-white flex items-center justify-center text-sm transition-all duration-300"><i class="fa-brands fa-twitter"></i></a>
                </div>
            </div>
            <div>
                <h3 class="font-bold text-white text-sm uppercase tracking-wider mb-6">Navigasi</h3>
                <ul class="space-y-3.5 text-sm">
                    <li><a href="{{ route('products.index') }}" class="hover:text-white hover:underline decoration-primary-500 decoration-2 transition-all">Semua Produk</a></li>
                    <li><a href="{{ route('products.index', ['sort' => 'latest']) }}" class="hover:text-white hover:underline decoration-primary-500 decoration-2 transition-all">Terbaru</a></li>
                    <li><a href="{{ route('home') }}#flash-sale" class="hover:text-white hover:underline decoration-primary-500 decoration-2 transition-all">Promo Flash Sale</a></li>
                </ul>
            </div>
            <div>
                <h3 class="font-bold text-white text-sm uppercase tracking-wider mb-6">Kontak & Bantuan</h3>
                <ul class="space-y-3.5 text-sm">
                    <li>
                        <a href="mailto:support@motoparthub.com" class="hover:text-white hover:underline decoration-primary-500 decoration-2 transition-all">
                            Email: support@motoparthub.com
                        </a>
                    </li>
                    <li>
                        <a href="tel:+6281234567890" class="hover:text-white hover:underline decoration-primary-500 decoration-2 transition-all">
                            Telepon: +62 812-3456-7890
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('orders.track') }}" class="hover:text-white hover:underline decoration-primary-500 decoration-2 transition-all">
                            Bantuan: Lacak Pesanan
                        </a>
                    </li>
                    <li>
                        <a href="#" class="hover:text-white hover:underline decoration-primary-500 decoration-2 transition-all">
                            FAQ & Kebijakan Layanan
                        </a>
                    </li>
                </ul>
            </div>

        </div>
        <hr class="my-12 border-slate-900 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-6 text-xs text-slate-500">
            <p>&copy; {{ date('Y') }} MotoPart Hub. Built with excellence.</p>
            <div class="flex gap-6">
                <a href="#" class="hover:text-slate-300 transition-colors">Privacy Policy</a>
                <a href="#" class="hover:text-slate-300 transition-colors">Terms of Service</a>
            </div>
        </div>
    </footer>

    <!-- Slide-out Cart Drawer -->
    <div x-show="$store.cart.cartOpen" x-cloak class="relative z-50" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
        <div x-show="$store.cart.cartOpen" x-transition:enter="ease-in-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in-out duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm transition-opacity" @click="$store.cart.toggleCart()"></div>

        <div class="fixed inset-0 overflow-hidden">
            <div class="absolute inset-0 overflow-hidden">
                <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-4 sm:pl-10">
                    <div x-show="$store.cart.cartOpen" x-transition:enter="transform transition ease-in-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transform transition ease-in-out duration-300" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full" class="pointer-events-auto w-screen max-w-md">
                        <div class="flex h-full flex-col overflow-y-scroll bg-slate-900 text-slate-100 shadow-2xl border-l border-slate-800">
                            <div class="flex-1 overflow-y-auto px-4 sm:px-6 py-6 sm:py-8">
                                <div class="flex items-start justify-between">
                                    <h2 class="text-xl font-bold text-white" id="slide-over-title">Keranjang Belanja</h2>
                                    <div class="ml-3 flex h-7 items-center">
                                        <button type="button" class="relative -m-2 p-2 text-slate-400 hover:text-white rounded-full hover:bg-slate-800 transition-colors" @click="$store.cart.toggleCart()">
                                            <span class="absolute -m-2"></span>
                                            <i class="fa-solid fa-xmark text-lg"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="mt-8">
                                    <div class="flow-root">
                                        <ul role="list" class="-my-6 divide-y divide-slate-800">
                                            <template x-for="item in $store.cart.items" :key="item.id">
                                                <li class="flex py-6 group">
                                                    <div class="h-20 w-20 sm:h-24 sm:w-24 flex-shrink-0 overflow-hidden rounded-2xl border border-slate-800 bg-slate-950 shadow-sm relative">
                                                        <img :src="item.image" class="h-full w-full object-cover object-center group-hover:scale-105 transition-transform duration-300">
                                                    </div>

                                                    <div class="ml-4 flex flex-1 flex-col justify-between">
                                                        <div>
                                                            <div class="flex justify-between text-sm font-semibold text-white">
                                                                <h3>
                                                                    <a :href="item.product_slug" class="hover:text-primary-500 transition-colors" x-text="item.product_name"></a>
                                                                </h3>
                                                                <p class="ml-4 text-primary-500 font-bold" x-text="'Rp ' + item.formatted_subtotal"></p>
                                                            </div>
                                                            <p class="mt-1.5 text-xs font-medium text-slate-500" x-text="item.variant_name ?? ''"></p>
                                                        </div>
                                                        <div class="flex flex-1 items-end justify-between text-xs mt-2">
                                                            <div class="flex items-center border border-slate-800 rounded-full bg-slate-950 overflow-hidden">
                                                                <button @click="$store.cart.updateQty(item.id, item.quantity - 1)" class="px-2.5 py-1.5 hover:bg-slate-800 text-slate-400 transition-colors"><i class="fa-solid fa-minus text-[9px]"></i></button>
                                                                <span class="px-3 font-bold text-slate-200 text-xs" x-text="item.quantity"></span>
                                                                <button @click="$store.cart.updateQty(item.id, item.quantity + 1)" class="px-2.5 py-1.5 hover:bg-slate-800 text-slate-400 transition-colors"><i class="fa-solid fa-plus text-[9px]"></i></button>
                                                            </div>

                                                            <div class="flex">
                                                                <button type="button" @click="$store.cart.removeCartItem(item.id)" class="font-semibold text-rose-500 hover:text-rose-400 hover:bg-rose-950/20 px-2.5 py-1.5 rounded-xl transition-all flex items-center gap-1.5">
                                                                    <i class="fa-solid fa-trash-can text-[10px]"></i> Hapus
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                            </template>
                                            <div x-show="$store.cart.items && $store.cart.items.length === 0" class="text-center py-20">
                                                <span class="w-16 h-16 bg-slate-950 text-slate-700 rounded-3xl flex items-center justify-center mx-auto mb-4 text-2xl"><i class="fa-solid fa-basket-shopping"></i></span>
                                                <p class="text-sm font-semibold text-slate-500">Keranjang belanja Anda masih kosong.</p>
                                                <button @click="$store.cart.toggleCart()" class="mt-4 text-xs font-bold text-primary-500 hover:underline">Mulai Belanja</button>
                                            </div>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="border-t border-slate-800 px-6 py-6 bg-slate-950/50">
                                <div class="flex justify-between text-sm text-slate-400 mb-2">
                                    <p class="font-medium">Subtotal</p>
                                    <p class="text-white font-bold" x-text="'Rp ' + $store.cart.formatted_subtotal"></p>
                                </div>
                                <div x-show="$store.cart.discount > 0" class="flex justify-between text-sm text-emerald-500 mb-2 font-medium">
                                    <p>Diskon</p>
                                    <p x-text="'- Rp ' + $store.cart.formatted_discount"></p>
                                </div>
                                <hr class="my-4 border-slate-800">
                                <div class="flex justify-between text-base font-extrabold text-white mb-6">
                                    <p>Total Akhir</p>
                                    <p class="text-primary-500 text-lg" x-text="'Rp ' + $store.cart.formatted_grand_total"></p>
                                </div>

                                <div class="space-y-3">

<a href="{{ route('checkout.whatsappFast') }}" class="flex items-center justify-center rounded-full bg-primary-600 hover:bg-primary-700 text-white px-6 py-3.5 text-sm font-bold shadow-premium hover:shadow-glow transition-all duration-300">
                                        Checkout Sekarang <i class="fa-solid fa-arrow-right ml-2 text-xs"></i>
                                    </a>

                                    <button @click="$store.cart.toggleCart()" class="w-full flex items-center justify-center rounded-full border border-slate-800 bg-transparent px-6 py-3.5 text-sm font-semibold text-slate-300 hover:bg-slate-800 transition-colors">
                                        Kembali Belanja
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
    <div class="md:hidden fixed bottom-6 left-1/2 -translate-x-1/2 bg-slate-900/90 backdrop-blur-xl border border-slate-800/80 flex justify-around py-3 px-4 z-40 shadow-premium-hover rounded-full w-[90%] max-w-sm">
        <a href="{{ route('home') }}" class="flex flex-col items-center gap-0.5 text-slate-500 hover:text-primary-500 transition-colors {{ request()->routeIs('home') ? 'text-primary-500 font-bold' : '' }}">
            <i class="fa-solid fa-house text-base"></i>
            <span class="text-[9px] tracking-wide uppercase font-semibold">Home</span>
        </a>
        <a href="{{ route('products.index') }}" class="flex flex-col items-center gap-0.5 text-slate-500 hover:text-primary-500 transition-colors {{ request()->routeIs('products.index') ? 'text-primary-500 font-bold' : '' }}">
            <i class="fa-solid fa-magnifying-glass text-base"></i>
            <span class="text-[9px] tracking-wide uppercase font-semibold">Cari</span>
        </a>
        <button @click="$store.cart.toggleCart()" class="flex flex-col items-center gap-0.5 text-slate-500 hover:text-primary-500 relative transition-colors">
            <i class="fa-solid fa-basket-shopping text-base"></i>
            <span x-show="$store.cart.item_count > 0" class="absolute -top-1 -right-1 bg-primary-600 text-white rounded-full w-4 h-4 text-[8px] flex items-center justify-center font-black" x-text="$store.cart.item_count"></span>
            <span class="text-[9px] tracking-wide uppercase font-semibold">Cart</span>
        </button>
        <a href="{{ route('orders.index') }}" class="flex flex-col items-center gap-0.5 text-slate-500 hover:text-primary-500 transition-colors {{ request()->routeIs('orders.index') ? 'text-primary-500 font-bold' : '' }}">
            <i class="fa-solid fa-box text-base"></i>
            <span class="text-[9px] tracking-wide uppercase font-semibold">Orders</span>
        </a>
        @auth
            <a href="{{ route('profile.edit') }}" class="flex flex-col items-center gap-0.5 text-slate-500 hover:text-primary-500 transition-colors">
                <i class="fa-solid fa-user text-base"></i>
                <span class="text-[9px] tracking-wide uppercase font-semibold">Profil</span>
            </a>
        @else
            <a href="{{ route('login') }}" class="flex flex-col items-center gap-0.5 text-slate-500 hover:text-primary-500 transition-colors">
                <i class="fa-solid fa-right-to-bracket text-base"></i>
                <span class="text-[9px] tracking-wide uppercase font-semibold">Masuk</span>
            </a>
        @endauth
    </div>

    <!-- Dynamic Toast Notification Manager -->
    <div x-data="toastManager()" @toast.window="add($event.detail)" class="fixed bottom-24 md:bottom-8 right-4 z-50 flex flex-col gap-3 max-w-sm w-[90%] pointer-events-none">
        <template x-for="t in toasts" :key="t.id">
            <div x-transition class="p-4 rounded-2xl shadow-premium border border-slate-800 pointer-events-auto transition-all duration-300"
                 :class="{
                    'bg-slate-900 text-emerald-400 border-emerald-500/25': t.type === 'success',
                    'bg-slate-900 text-rose-400 border-rose-500/25': t.type === 'error'
                 }">
                <div class="flex items-center gap-3">
                    <i class="fa-solid" :class="t.type === 'success' ? 'fa-circle-check text-emerald-500' : 'fa-circle-exclamation text-rose-500'"></i>
                    <p class="text-xs font-semibold" x-text="t.message"></p>
                </div>
            </div>
        </template>
    </div>

    <!-- Global Cart & Garage Scripts -->
    <script>
        document.addEventListener('alpine:init', () => {
            // Global Garage Store for Vehicle Compatibility Filter
            Alpine.store('garage', {
                brand: localStorage.getItem('garage_brand') || '',
                model: localStorage.getItem('garage_model') || '',
                year: localStorage.getItem('garage_year') || '',
                
                setVehicle(brand, model, year) {
                    this.brand = brand;
                    this.model = model;
                    this.year = year;
                    localStorage.setItem('garage_brand', brand);
                    localStorage.setItem('garage_model', model);
                    localStorage.setItem('garage_year', year);
                    window.dispatchEvent(new CustomEvent('garage-changed', { detail: { brand, model, year } }));
                },
                clear() {
                    this.brand = '';
                    this.model = '';
                    this.year = '';
                    localStorage.removeItem('garage_brand');
                    localStorage.removeItem('garage_model');
                    localStorage.removeItem('garage_year');
                    window.dispatchEvent(new CustomEvent('garage-changed', { detail: { brand: '', model: '', year: '' } }));
                }
            });

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

        // Global function to evaluate compatibility (Universal vs Specific)
        window.checkProductCompatibility = function(compatibilityArray) {
            const active = Alpine.store('garage');
            if (!active || !active.brand) return null; // No vehicle selected (null state)
            if (!compatibilityArray || compatibilityArray.length === 0) return false;
            
            return compatibilityArray.some(c => {
                if (c.brand.toLowerCase() === 'universal' || c.brand.toLowerCase() === 'all') {
                    return true;
                }
                return c.brand.toLowerCase() === active.brand.toLowerCase() &&
                    (!active.model || c.model.toLowerCase() === active.model.toLowerCase()) &&
                    (!active.year || c.year.toString() === active.year.toString());
            });
        };

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
