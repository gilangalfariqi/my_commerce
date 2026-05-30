<x-frontend-layout>

@push('styles')
<script type="application/ld+json">
    {!! app(\App\Services\SEO\SchemaService::class)->organizationSchema() !!}
</script>
<style>
    /* Continuous waving floating categories animation */
    @keyframes categoryFloat {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
    }
    .animate-category-float {
        animation: categoryFloat 4.5s ease-in-out infinite;
    }
    /* Hide scrollbars for custom swiper container */
    .scrollbar-none::-webkit-scrollbar {
        display: none;
    }
    .scrollbar-none {
        -ms-overflow-style: none; /* IE and Edge */
        scrollbar-width: none; /* Firefox */
    }
</style>
@endpush

{{-- ════════════════════════════════════════
     SECTION 1: HERO SLIDER
════════════════════════════════════════ --}}
@if($banners->isNotEmpty())
<section class="mb-12 -mx-4 sm:-mx-6 lg:-mx-8"
    x-data="{
        activeSlide: 0,
        count: {{ $banners->count() }},
        timer: null,
        progress: 0,
        progressTimer: null,
        start() {
            this.progress = 0;
            clearInterval(this.timer);
            clearInterval(this.progressTimer);
            this.progressTimer = setInterval(() => { this.progress = Math.min(100, this.progress + (100/50)); }, 100);
            this.timer = setInterval(() => {
                this.activeSlide = (this.activeSlide + 1) % this.count;
                this.progress = 0;
            }, 5000);
        },
        goTo(index) {
            this.activeSlide = index;
            this.start();
        }
    }"
    x-init="start()"
    @mouseenter="clearInterval(timer); clearInterval(progressTimer)"
    @mouseleave="start()"
>
    <div class="relative overflow-hidden h-[280px] sm:h-[420px] lg:h-[540px] bg-slate-950">

        {{-- Slides --}}
        @foreach($banners as $index => $banner)
        <div
            x-show="activeSlide === {{ $index }}"
            x-transition:enter="transition ease-out duration-700"
            x-transition:enter-start="opacity-0 scale-[1.03]"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-400"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="absolute inset-0 w-full h-full"
        >
            <img
                src="{{ $banner->image_path }}"
                alt="{{ $banner->title }}"
                class="w-full h-full object-cover object-center"
            >
            {{-- Gradient Overlay --}}
            <div class="absolute inset-0 bg-gradient-to-r from-slate-950/90 via-slate-950/50 to-transparent"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/70 via-transparent to-transparent"></div>

            {{-- Content --}}
            <div class="absolute inset-0 flex items-center px-6 sm:px-12 lg:px-16">
                <div class="max-w-2xl">
                    <span class="inline-block text-[10px] sm:text-xs font-black tracking-[0.2em] uppercase mb-3 opacity-90" style="color:{{ $settings->get('color_primary','#ef4444') }};">
                        {{ $settings->get('hero_badge_text', 'Toko Terpercaya') }}
                    </span>
                    <h2 class="text-2xl sm:text-4xl lg:text-5xl font-black text-white leading-tight tracking-tight mb-3 sm:mb-4 line-clamp-2">
                        {{ $banner->title }}
                    </h2>
                    @if($banner->subtitle)
                    <p class="text-sm sm:text-base text-slate-300 leading-relaxed mb-5 sm:mb-7 max-w-lg line-clamp-2 sm:line-clamp-none">
                        {{ $banner->subtitle }}
                    </p>
                    @endif
                    <a href="{{ $banner->click_url ?: route('products.index') }}"
                        class="inline-flex items-center gap-2 text-white font-bold text-sm px-6 py-3 sm:px-8 sm:py-3.5 rounded-full transition-all duration-300 shadow-lg hover:-translate-y-0.5"
                        style="background-color:{{ $settings->get('color_primary','#ef4444') }};">
                        {{ $settings->get('hero_cta_text', 'Belanja Sekarang') }}
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </a>
                </div>
            </div>
        </div>
        @endforeach

        {{-- Progress Bar --}}
        <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-slate-800">
            <div class="h-full bg-red-600 transition-none" :style="`width: ${progress}%`"></div>
        </div>

        {{-- Dot Indicators --}}
        @if($banners->count() > 1)
        <div class="absolute bottom-5 left-1/2 -translate-x-1/2 flex items-center gap-2 z-10">
            @foreach($banners as $index => $banner)
            <button
                @click="goTo({{ $index }})"
                class="transition-all duration-300 rounded-full"
                :class="activeSlide === {{ $index }} ? 'w-6 h-1.5 bg-red-500' : 'w-1.5 h-1.5 bg-white/30 hover:bg-white/60'"
                aria-label="Slide {{ $index + 1 }}"
            ></button>
            @endforeach
        </div>
        @endif

        {{-- Nav Arrows (desktop) --}}
        @if($banners->count() > 1)
        <button @click="goTo((activeSlide - 1 + count) % count)"
            class="hidden sm:flex absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-slate-900/70 backdrop-blur-sm border border-slate-700/60 rounded-full items-center justify-center text-slate-300 hover:text-white hover:bg-slate-800 transition-all z-10">
            <i class="fa-solid fa-chevron-left text-sm"></i>
        </button>
        <button @click="goTo((activeSlide + 1) % count)"
            class="hidden sm:flex absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-slate-900/70 backdrop-blur-sm border border-slate-700/60 rounded-full items-center justify-center text-slate-300 hover:text-white hover:bg-slate-800 transition-all z-10">
            <i class="fa-solid fa-chevron-right text-sm"></i>
        </button>
        @endif
    </div>
</section>
@endif

{{-- ════════════════════════════════════════
     SECTION 2: GARASI SAYA (Vehicle Filter)
════════════════════════════════════════ --}}
<section class="mb-14"
    x-data="{
        brands: [],
        models: [],
        years: [],
        brand: '',
        model: '',
        year: '',
        loadingBrands: false,
        loadingModels: false,
        loadingYears: false,

        async init() {
            this.brand = Alpine.store('garage').brand || '';
            this.model = Alpine.store('garage').model || '';
            this.year  = Alpine.store('garage').year || '';
            await this.fetchBrands();
            if (this.brand) await this.fetchModels();
            if (this.model) await this.fetchYears();

            window.addEventListener('garage-changed', (e) => {
                this.brand = e.detail.brand;
                this.model = e.detail.model;
                this.year  = e.detail.year;
            });
        },

        async fetchBrands() {
            this.loadingBrands = true;
            try {
                const r = await fetch('/api/fitment/brands');
                this.brands = await r.json();
            } catch(e) { console.error(e); }
            this.loadingBrands = false;
        },

        async fetchModels() {
            if (!this.brand) { this.models = []; this.years = []; return; }
            this.loadingModels = true;
            this.models = []; this.years = [];
            try {
                const r = await fetch('/api/fitment/models?brand=' + encodeURIComponent(this.brand));
                this.models = await r.json();
            } catch(e) { console.error(e); }
            this.loadingModels = false;
        },

        async fetchYears() {
            if (!this.model) { this.years = []; return; }
            this.loadingYears = true;
            this.years = [];
            try {
                const r = await fetch('/api/fitment/years?model=' + encodeURIComponent(this.model));
                this.years = await r.json();
            } catch(e) { console.error(e); }
            this.loadingYears = false;
        },

        onBrandChange() {
            this.model = '';
            this.year  = '';
            this.fetchModels();
        },
        onModelChange() {
            this.year = '';
            this.fetchYears();
        },

        submit() {
            if (!this.brand) return;
            const selectedBrand = this.brands.find(b => b.slug === this.brand);
            const selectedModel = this.models.find(m => m.slug === this.model);
            Alpine.store('garage').setVehicle(
                selectedBrand?.name || this.brand,
                selectedModel?.name || this.model,
                this.year
            );
            let url = '{{ route('products.index') }}?brand=' + encodeURIComponent(this.brand);
            if (this.model) url += '&model=' + encodeURIComponent(this.model);
            if (this.year)  url += '&year='  + encodeURIComponent(this.year);
            window.location.href = url;
        }
    }"
>
    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 relative overflow-hidden">
        {{-- Decorative glow --}}
        <div class="absolute -right-20 -top-20 w-56 h-56 bg-red-600/8 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-10 bottom-0 w-40 h-40 bg-red-900/10 rounded-full blur-2xl pointer-events-none"></div>

        <div class="relative z-10">
            {{-- Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center gap-4 mb-6">
                <div class="flex items-center gap-4">
                    <span class="w-12 h-12 rounded-2xl border flex items-center justify-center text-xl flex-shrink-0"
                          style="background-color:{{ $settings->get('color_primary','#ef4444') }}1a;border-color:{{ $settings->get('color_primary','#ef4444') }}33;color:{{ $settings->get('color_primary','#ef4444') }};">
                        <i class="fa-solid fa-motorcycle"></i>
                    </span>
                    <div>
                        <h2 class="text-lg sm:text-xl font-bold text-white tracking-tight">{{ $settings->get('garage_section_title','Cari Berdasarkan Kendaraan') }}</h2>
                        <p class="text-xs sm:text-sm text-slate-400 mt-0.5">{{ $settings->get('garage_section_subtitle','Filter produk yang 100% kompatibel dengan kendaraan Anda.') }}</p>
                    </div>
                </div>
            </div>

            {{-- Filter Form --}}
            <form @submit.prevent="submit()" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                {{-- Brand --}}
                <div class="relative">
                    <select
                        x-model="brand"
                        @change="onBrandChange()"
                        :disabled="loadingBrands"
                        class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-3 text-sm font-medium text-slate-200 focus:outline-none focus:ring-2 focus:ring-red-500/40 focus:border-red-500 appearance-none cursor-pointer disabled:opacity-50 transition-all"
                        aria-label="Pilih Merek Motor"
                    >
                        <option value="">Pilih Merek Motor</option>
                        <template x-for="b in brands" :key="b.slug">
                            <option :value="b.slug" :selected="brand === b.slug" x-text="b.name"></option>
                        </template>
                    </select>
                    <template x-if="!loadingBrands">
                        <i class="fa-solid fa-chevron-down absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-500 text-[10px] pointer-events-none"></i>
                    </template>
                    <template x-if="loadingBrands">
                        <div class="absolute right-3.5 top-1/2 -translate-y-1/2 w-3 h-3 border-2 border-red-500 border-t-transparent rounded-full animate-spin pointer-events-none"></div>
                    </template>
                </div>

                {{-- Model --}}
                <div class="relative">
                    <select
                        x-model="model"
                        @change="onModelChange()"
                        :disabled="!brand || loadingModels"
                        class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-3 text-sm font-medium text-slate-200 focus:outline-none focus:ring-2 focus:ring-red-500/40 focus:border-red-500 appearance-none cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed transition-all"
                        aria-label="Pilih Model Motor"
                    >
                        <option value="">Pilih Model</option>
                        <template x-for="m in models" :key="m.slug">
                            <option :value="m.slug" :selected="model === m.slug" x-text="m.name"></option>
                        </template>
                    </select>
                    <template x-if="!loadingModels">
                        <i class="fa-solid fa-chevron-down absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-500 text-[10px] pointer-events-none"></i>
                    </template>
                    <template x-if="loadingModels">
                        <div class="absolute right-3.5 top-1/2 -translate-y-1/2 w-3 h-3 border-2 border-red-500 border-t-transparent rounded-full animate-spin pointer-events-none"></div>
                    </template>
                </div>

                {{-- Year --}}
                <div class="relative">
                    <select
                        x-model="year"
                        :disabled="!model || years.length === 0 || loadingYears"
                        class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-3 text-sm font-medium text-slate-200 focus:outline-none focus:ring-2 focus:ring-red-500/40 focus:border-red-500 appearance-none cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed transition-all"
                        aria-label="Pilih Tahun"
                    >
                        <option value="">Pilih Tahun</option>
                        <template x-for="y in years" :key="y">
                            <option :value="y" :selected="year == y" x-text="y"></option>
                        </template>
                    </select>
                    <i class="fa-solid fa-chevron-down absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-500 text-[10px] pointer-events-none"></i>
                </div>

                {{-- Submit --}}
                <button
                    type="submit"
                    :disabled="!brand"
                    class="w-full disabled:bg-slate-800 disabled:text-slate-500 text-white font-bold text-sm py-3 px-6 rounded-xl shadow-md transition-all duration-300 flex items-center justify-center gap-2"
                    style="background-color:{{ $settings->get('color_primary','#ef4444') }};"
                >
                    <i class="fa-solid fa-magnifying-glass text-sm"></i>
                    {{ $settings->get('garage_cta_text','Cari Produk') }}
                </button>
            </form>

            {{-- Active Vehicle Badge --}}
            <template x-if="$store.garage.brand">
                <div class="mt-5 pt-5 border-t border-slate-800/60 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex flex-wrap items-center gap-2 text-sm">
                        <span class="inline-flex items-center gap-1.5 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-lg px-3 py-1 font-bold text-xs">
                            <i class="fa-solid fa-circle-check text-[10px]"></i> Garasi Aktif:
                        </span>
                        <span class="text-slate-200 font-semibold" x-text="$store.garage.brand + ' ' + $store.garage.model + ($store.garage.year ? ' (' + $store.garage.year + ')' : '')"></span>
                    </div>
                    <button
                        @click="$store.garage.clear(); brand=''; model=''; year=''; models=[]; years=[];"
                        class="text-xs text-rose-400 hover:text-rose-300 font-semibold flex items-center gap-1.5 transition-colors"
                    >
                        <i class="fa-solid fa-trash-can text-[10px]"></i> Hapus Pilihan
                    </button>
                </div>
            </template>
        </div>
    </div>
</section>

{{-- ════════════════════════════════════════
     SECTION 3: CATEGORIES GRID
════════════════════════════════════════ --}}
@if($categories->isNotEmpty())
<section class="mb-14">
    <div class="flex items-end justify-between mb-6 sm:mb-8">
        <div>
            <h2 class="text-2xl sm:text-3xl font-black text-white tracking-tight leading-tight">{{ $settings->get('category_section_title','Kategori Produk') }}</h2>
            <p class="text-sm text-slate-400 mt-1.5">{{ $settings->get('category_section_subtitle','Temukan produk berkualitas berdasarkan kategori') }}</p>
        </div>
        <a href="{{ route('products.index') }}" class="hidden sm:flex text-xs font-bold transition-colors gap-1.5 items-center whitespace-nowrap"
           style="color:{{ $settings->get('color_primary','#ef4444') }}">
            {{ $settings->get('category_view_all_text','Lihat Semua') }} <i class="fa-solid fa-arrow-right text-[10px]"></i>
        </a>
    </div>

    @php
        $categoryIconMap = [
            'mesin'       => 'fa-gear',
            'pengapian'   => 'fa-bolt-lightning',
            'kaki-kaki'   => 'fa-screwdriver-wrench',
            'pengereman'  => 'fa-circle-notch',
            'ban'         => 'fa-circle',
            'oli'         => 'fa-oil-can',
            'bodi'        => 'fa-motorcycle',
            'aksesoris'   => 'fa-wand-magic-sparkles',
            'knalpot'     => 'fa-wind',
            'transmisi'   => 'fa-arrow-right-arrow-left',
            'karburator'  => 'fa-droplet',
            'kelistrikan' => 'fa-plug',
            'filter'      => 'fa-filter',
        ];
    @endphp

    <div class="relative w-full overflow-hidden"
        x-data="{
            scrollInterval: null,
            scrollTimeout: null,
            init() {
                this.startAutoScroll();
            },
            startAutoScroll() {
                this.scrollInterval = setInterval(() => {
                    const swiper = this.$refs.swiper;
                    if (!swiper) return;
                    // Check if scroll is near the end
                    const isEnd = swiper.scrollLeft + swiper.clientWidth >= swiper.scrollWidth - 12;
                    if (isEnd) {
                        swiper.scrollTo({ left: 0, behavior: 'smooth' });
                    } else {
                        // Scroll by 1 card width + gap (96px + 12px = 108px)
                        swiper.scrollBy({ left: 108, behavior: 'smooth' });
                    }
                }, 3000);
            },
            stopAutoScroll() {
                clearInterval(this.scrollInterval);
            },
            handleInteraction() {
                this.stopAutoScroll();
                clearTimeout(this.scrollTimeout);
                this.scrollTimeout = setTimeout(() => {
                    this.startAutoScroll();
                }, 8000); // Resume auto scroll after 8s of inactivity
            }
        }"
        @touchstart="handleInteraction()"
        @mousedown="handleInteraction()"
    >
        {{-- Left Edge Gradient Shadow Fade (Mobile-only) --}}
        <div class="absolute left-0 top-0 bottom-0 w-8 bg-gradient-to-r from-[#0b0f19] to-transparent pointer-events-none z-10 block sm:hidden"></div>
        {{-- Right Edge Gradient Shadow Fade (Mobile-only) --}}
        <div class="absolute right-0 top-0 bottom-0 w-8 bg-gradient-to-l from-[#0b0f19] to-transparent pointer-events-none z-10 block sm:hidden"></div>

        {{-- Swipeable Row with continuous waving float motion --}}
        <div x-ref="swiper" class="flex overflow-x-auto gap-3 sm:gap-4 py-4 px-1 scrollbar-none snap-x snap-mandatory -mx-4 px-4 sm:mx-0 sm:px-0 scroll-smooth">
            @foreach($categories as $category)
            @php $icon = $categoryIconMap[$category->slug] ?? 'fa-gears'; @endphp
            <a
                href="{{ route('products.index', ['category' => $category->slug]) }}"
                class="group w-[96px] sm:w-[130px] flex-shrink-0 snap-start bg-slate-900 border border-slate-800 theme-card-hover rounded-2xl p-2.5 sm:p-5 text-center hover:bg-slate-800/70 transition-all duration-300 animate-category-float"
                style="animation-delay: {{ $loop->index * 0.25 }}s;"
            >
                @if($category->url)
                    <img src="{{ $category->url }}" alt="{{ $category->name }}" class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl object-cover mx-auto mb-2.5 sm:mb-3">
                @else
                    <div class="w-10 h-10 sm:w-14 sm:h-14 rounded-xl sm:rounded-2xl theme-icon-box flex items-center justify-center mx-auto mb-2.5 sm:mb-3">
                        <i class="fa-solid {{ $icon }} text-base sm:text-xl"></i>
                    </div>
                @endif
                <h3 class="text-[11px] sm:text-sm font-semibold text-slate-200 group-theme-text transition-colors leading-snug line-clamp-2 min-h-[32px] sm:min-h-0 flex items-center justify-center">{{ $category->name }}</h3>
                <span class="text-[9px] sm:text-[10px] text-slate-500 mt-1 block">{{ $category->products_count }} Produk</span>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ════════════════════════════════════════
     SECTION 4: FLASH SALE
════════════════════════════════════════ --}}
@if($flashSale && $flashSale->items->isNotEmpty())
<section class="mb-14" id="flash-sale"
    x-data="countdownTimer('{{ $flashSale->end_time->toIso8601String() }}')"
>
    <div class="bg-gradient-to-br from-slate-900 via-slate-900 to-slate-950 border border-slate-800 rounded-3xl p-6 sm:p-10 relative overflow-hidden">
        {{-- Decorative --}}
        <div class="absolute top-0 right-0 w-80 h-80 bg-red-600/5 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-10 left-10 w-60 h-60 bg-orange-600/5 rounded-full blur-3xl pointer-events-none"></div>

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-5 mb-8 pb-6 border-b border-slate-800 relative z-10">
            <div class="flex items-center gap-4">
                <span class="w-12 h-12 rounded-2xl theme-pulse text-white flex items-center justify-center text-xl font-black animate-pulse">
                    <i class="fa-solid fa-bolt"></i>
                </span>
                <div>
                    <h2 class="text-2xl sm:text-3xl font-black text-white tracking-tight">{{ $flashSale->name }}</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Penawaran terbatas — Dapatkan sebelum kehabisan!</p>
                </div>
            </div>

            {{-- Countdown --}}
            <div class="flex items-center gap-3">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Berakhir dalam:</span>
                <div class="flex gap-1.5">
                    @foreach([['days','Hari'],['hours','Jam'],['minutes','Mnt'],['seconds','Dtk']] as [$prop, $label])
                    <div class="bg-slate-950 border border-slate-800 rounded-xl px-2.5 py-2 text-center min-w-[46px]">
                        <span class="text-base font-black theme-countdown block" x-text="{{ $prop }}">00</span>
                        <span class="text-[9px] font-bold text-slate-500 uppercase tracking-wider">{{ $label }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Flash Sale Items --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-5 relative z-10">
            @foreach($flashSale->items as $item)
            @php
                $discPct = $item->product->price > 0
                    ? (int) round(($item->product->price - $item->discounted_price) / $item->product->price * 100)
                    : 0;
                $soldPct = $item->stock_limit > 0
                    ? min(100, (int)(($item->stock_sold / $item->stock_limit) * 100))
                    : 0;
            @endphp
            <div class="bg-slate-950 border border-slate-800 rounded-2xl overflow-hidden hover:border-red-500/30 hover:-translate-y-1 hover:shadow-lg hover:shadow-red-950/20 transition-all duration-300 flex flex-col group">
                {{-- Image --}}
                <div class="relative aspect-square overflow-hidden bg-slate-900">
                    <a href="{{ route('products.show', $item->product->slug) }}">
                        <img
                            src="{{ $item->product->thumbnail_url }}"
                            alt="{{ $item->product->name }}"
                            class="w-full h-full object-contain p-3 group-hover:scale-105 transition-transform duration-500"
                            loading="lazy"
                        >
                    </a>
                    @if($discPct > 0)
                    <span class="absolute top-2 left-2 theme-badge text-[9px] sm:text-[10px] font-black px-2 py-0.5 rounded-lg shadow-md">
                        -{{ $discPct }}%
                    </span>
                    @endif
                </div>
                {{-- Info --}}
                <div class="p-3 sm:p-4 flex-1 flex flex-col gap-2">
                    <h3 class="text-xs sm:text-sm font-semibold text-slate-200 line-clamp-2 leading-snug group-theme-text transition-colors">
                        <a href="{{ route('products.show', $item->product->slug) }}">{{ $item->product->name }}</a>
                    </h3>
                    <div class="flex flex-wrap items-baseline gap-1.5">
                        <span class="text-sm sm:text-base font-black theme-text">Rp {{ number_format($item->discounted_price, 0, ',', '.') }}</span>
                        <span class="text-xs text-slate-500 line-through">Rp {{ number_format($item->product->price, 0, ',', '.') }}</span>
                    </div>
                    {{-- Progress bar --}}
                    <div class="mt-auto">
                        <div class="flex justify-between text-[10px] text-slate-500 mb-1.5 font-medium">
                            <span>Terjual {{ $item->stock_sold }}</span>
                            <span>{{ $soldPct }}%</span>
                        </div>
                        <div class="w-full h-1.5 bg-slate-800 rounded-full overflow-hidden">
                            <div class="h-full theme-progress rounded-full" style="width: {{ $soldPct }}%"></div>
                        </div>
                    </div>
                    {{-- Buy button --}}
                    <button
                        @click="$store.cart.addToCart({{ $item->product_id }}, null, 1)"
                        class="w-full mt-1 theme-btn border border-transparent text-white font-bold text-[11px] sm:text-xs py-2.5 rounded-xl flex items-center justify-center gap-1.5"
                    >
                        <i class="fa-solid fa-basket-shopping"></i> Beli Sekarang
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ════════════════════════════════════════
     REUSABLE PRODUCT CARD MACRO
════════════════════════════════════════ --}}
@php
function renderProductCard($product) {
    // This is handled inline below via @foreach
}
@endphp

{{-- ════════════════════════════════════════
     SECTION 5: FEATURED PRODUCTS
════════════════════════════════════════ --}}
@if($featuredProducts->isNotEmpty())
<section class="mb-14">
    <div class="flex items-end justify-between mb-6 sm:mb-8">
        <div>
            <h2 class="text-2xl sm:text-3xl font-black text-white tracking-tight leading-tight">{{ $settings->get('featured_section_title', 'Produk Unggulan') }}</h2>
            <p class="text-sm text-slate-400 mt-1.5">{{ $settings->get('featured_section_subtitle', 'Pilihan terbaik yang sudah dipercaya ribuan pelanggan') }}</p>
        </div>
        <a href="{{ route('products.index', ['featured' => 1]) }}" class="flex text-xs font-bold text-slate-400 transition-colors gap-1.5 items-center whitespace-nowrap" style="color:{{ $settings->get('color_primary','#ef4444') }}" onmouseover="this.style.opacity='0.75'" onmouseout="this.style.opacity='1'">
            Lihat Semua <i class="fa-solid fa-arrow-right text-[10px]"></i>
        </a>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-5">
        @foreach($featuredProducts as $product)
        <article class="group bg-slate-900 border border-slate-800 theme-card-hover rounded-2xl overflow-hidden transition-all duration-300 flex flex-col">
            <a href="{{ route('products.show', $product->slug) }}" class="block relative aspect-square overflow-hidden bg-slate-950">
                <img
                    src="{{ $product->thumbnail_url }}"
                    alt="{{ $product->name }}"
                    class="w-full h-full object-contain p-3 group-hover:scale-105 transition-transform duration-500"
                    loading="lazy"
                >
                @if($product->is_on_sale)
                <span class="absolute top-2 left-2 theme-badge text-[9px] sm:text-[10px] font-black px-2 py-0.5 rounded-lg shadow-md">
                    -{{ $product->discount_percent }}%
                </span>
                @endif
                @if($product->is_featured)
                <span class="absolute top-2 right-2 bg-amber-500 text-white text-[9px] sm:text-[10px] font-black px-2 py-0.5 rounded-lg">
                    ⭐ Unggulan
                </span>
                @endif
            </a>
            <div class="p-3.5 sm:p-4 flex flex-col gap-2 flex-1">
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest truncate">{{ $product->category?->name ?? 'Sparepart' }}</p>
                <h3 class="text-xs sm:text-sm font-semibold text-slate-100 line-clamp-2 leading-snug group-theme-text transition-colors">
                    <a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a>
                </h3>
                <div class="flex items-baseline gap-2 mt-auto pt-1">
                    <span class="text-sm sm:text-base font-black text-slate-100">Rp {{ number_format($product->getFinalPrice(), 0, ',', '.') }}</span>
                    @if($product->is_on_sale)
                    <span class="text-xs text-slate-500 line-through">Rp {{ number_format($product->compare_at_price, 0, ',', '.') }}</span>
                    @endif
                </div>
                <div class="flex gap-2 mt-1">
                    @if($product->variants->isNotEmpty())
                    <a href="{{ route('products.show', $product->slug) }}"
                        class="flex-1 text-center bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-300 font-bold text-[11px] sm:text-xs py-2.5 rounded-xl transition-all duration-300">
                        Pilih Varian
                    </a>
                    @else
                    <button @click="$store.cart.addToCart({{ $product->id }}, null, 1)"
                        class="flex-1 theme-btn font-bold text-[11px] sm:text-xs py-2.5 rounded-xl flex items-center justify-center gap-1">
                        <i class="fa-solid fa-basket-shopping text-[10px]"></i> Beli
                    </button>
                    @endif
                    <a href="{{ route('products.show', $product->slug) }}"
                        class="bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-400 hover:text-slate-200 font-bold text-[11px] sm:text-xs py-2.5 px-3 rounded-xl transition-all duration-300">
                        Detail
                    </a>
                </div>
            </div>
        </article>
        @endforeach
    </div>
</section>
@endif

{{-- ════════════════════════════════════════
     SECTION 6: LATEST PRODUCTS
════════════════════════════════════════ --}}
@if($latestProducts->isNotEmpty())
<section class="mb-14">
    <div class="mb-6 sm:mb-8">
        <h2 class="text-2xl sm:text-3xl font-black text-white tracking-tight leading-tight">{{ $settings->get('latest_section_title', 'Produk Terbaru') }}</h2>
        <p class="text-sm text-slate-400 mt-1.5">{{ $settings->get('latest_section_subtitle', 'Produk baru yang baru saja kami tambahkan') }}</p>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-5">
        @foreach($latestProducts as $product)
        <article class="group bg-slate-900 border border-slate-800 theme-card-hover rounded-2xl overflow-hidden transition-all duration-300 flex flex-col">
            <a href="{{ route('products.show', $product->slug) }}" class="block relative aspect-square overflow-hidden bg-slate-950">
                <img
                    src="{{ $product->thumbnail_url }}"
                    alt="{{ $product->name }}"
                    class="w-full h-full object-contain p-3 group-hover:scale-105 transition-transform duration-500"
                    loading="lazy"
                >
                @if($product->is_on_sale)
                <span class="absolute top-2 left-2 theme-badge text-[9px] sm:text-[10px] font-black px-2 py-0.5 rounded-lg shadow-md">
                    -{{ $product->discount_percent }}%
                </span>
                @endif
                <span class="absolute top-2 right-2 bg-emerald-600 text-white text-[9px] sm:text-[10px] font-black px-2 py-0.5 rounded-lg">
                    ✦ Baru
                </span>
            </a>
            <div class="p-3.5 sm:p-4 flex flex-col gap-2 flex-1">
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest truncate">{{ $product->category?->name ?? 'Produk' }}</p>
                <h3 class="text-xs sm:text-sm font-semibold text-slate-100 line-clamp-2 leading-snug group-theme-text transition-colors">
                    <a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a>
                </h3>
                <div class="flex items-baseline gap-2 mt-auto pt-1">
                    <span class="text-sm sm:text-base font-black text-slate-100">Rp {{ number_format($product->getFinalPrice(), 0, ',', '.') }}</span>
                    @if($product->is_on_sale)
                    <span class="text-xs text-slate-500 line-through">Rp {{ number_format($product->compare_at_price, 0, ',', '.') }}</span>
                    @endif
                </div>
                <div class="flex gap-2 mt-1">
                    @if($product->variants->isNotEmpty())
                    <a href="{{ route('products.show', $product->slug) }}"
                        class="flex-1 text-center bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-300 font-bold text-[11px] sm:text-xs py-2.5 rounded-xl transition-all duration-300">
                        Pilih Varian
                    </a>
                    @else
                    <button @click="$store.cart.addToCart({{ $product->id }}, null, 1)"
                        class="flex-1 theme-btn font-bold text-[11px] sm:text-xs py-2.5 rounded-xl flex items-center justify-center gap-1">
                        <i class="fa-solid fa-basket-shopping text-[10px]"></i> Beli
                    </button>
                    @endif
                    <a href="{{ route('products.show', $product->slug) }}"
                        class="bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-400 hover:text-slate-200 font-bold text-[11px] sm:text-xs py-2.5 px-3 rounded-xl transition-all duration-300">
                        Detail
                    </a>
                </div>
            </div>
        </article>
        @endforeach
    </div>
</section>
@endif

{{-- ════════════════════════════════════════
     TRUST BADGES / USP BAR
════════════════════════════════════════ --}}
@php
$trustBadges = [
    ['fa-shield-halved', $settings->get('badge_1_title','Produk Original'),  $settings->get('badge_1_desc','100% produk resmi & bersertifikat')],
    ['fa-truck-fast',   $settings->get('badge_2_title','Pengiriman Cepat'), $settings->get('badge_2_desc','Estimasi 1–3 hari ke seluruh Indonesia')],
    ['fa-headset',      $settings->get('badge_3_title','Konsultasi Gratis'),$settings->get('badge_3_desc','Tanya via WhatsApp, respon cepat')],
    ['fa-rotate-left',  $settings->get('badge_4_title','Garansi Barang'),  $settings->get('badge_4_desc','Garansi kualitas dan keaslian produk')],
];
@endphp
<section class="mb-8">
    <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-5 sm:p-6">
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 sm:gap-6 text-center">
            @foreach($trustBadges as [$icon, $title, $desc])
            <div class="flex flex-col items-center gap-2">
                <span class="w-11 h-11 rounded-2xl flex items-center justify-center text-lg"
                      style="background-color:{{ $settings->get('color_primary','#ef4444') }}1a;color:{{ $settings->get('color_primary','#ef4444') }};">
                    <i class="fa-solid {{ $icon }}"></i>
                </span>
                <div>
                    <p class="text-sm font-bold text-slate-200">{{ $title }}</p>
                    <p class="text-[11px] text-slate-500 mt-0.5 leading-snug hidden sm:block">{{ $desc }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Floating WhatsApp --}}
@php $waNumber = $settings->get('store_whatsapp') ?: config('app.whatsapp_number', '6282174128947'); @endphp
<a href="https://wa.me/{{ $waNumber }}" target="_blank" rel="noopener noreferrer"
    class="fixed bottom-24 md:bottom-8 right-5 z-30 w-14 h-14 bg-emerald-500 hover:bg-emerald-600 text-white rounded-full flex items-center justify-center shadow-xl shadow-emerald-900/40 hover:scale-110 hover:shadow-emerald-900/60 transition-all duration-300"
    title="Hubungi via WhatsApp">
    <i class="fa-brands fa-whatsapp text-[28px]"></i>
</a>

@push('scripts')
<script>
function countdownTimer(endTimeStr) {
    return {
        endTime: new Date(endTimeStr).getTime(),
        days: '00', hours: '00', minutes: '00', seconds: '00',
        init() { this.update(); setInterval(() => this.update(), 1000); },
        update() {
            const diff = this.endTime - Date.now();
            if (diff <= 0) {
                this.days = this.hours = this.minutes = this.seconds = '00';
                return;
            }
            const d = Math.floor(diff / 86400000);
            const h = Math.floor(diff % 86400000 / 3600000);
            const m = Math.floor(diff % 3600000 / 60000);
            const s = Math.floor(diff % 60000 / 1000);
            this.days    = String(d).padStart(2, '0');
            this.hours   = String(h).padStart(2, '0');
            this.minutes = String(m).padStart(2, '0');
            this.seconds = String(s).padStart(2, '0');
        }
    };
}
</script>
@endpush

</x-frontend-layout>
