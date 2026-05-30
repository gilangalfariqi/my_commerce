<x-frontend-layout>

@push('styles')
<script type="application/ld+json">{!! $productSchema !!}</script>
<script type="application/ld+json">{!! $breadcrumb !!}</script>
<style>
    .pdp-zoom-img { cursor: crosshair; }
    @keyframes blink-stock { 50% { opacity: 0.4; } }
    .blink-anim { animation: blink-stock 1.5s ease infinite; }

    /* Staggered fade-in animations for premium feel */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(12px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up {
        opacity: 0;
        animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    /* WhatsApp Pulse ring animation */
    @keyframes pulse-ring {
        0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); }
        70% { box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
        100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }
    .animate-pulse-wa {
        animation: pulse-ring 2s infinite;
    }

    /* Hide scrollbars for swiper row */
    .scrollbar-none::-webkit-scrollbar {
        display: none;
    }
    .scrollbar-none {
        -ms-overflow-style: none; /* IE and Edge */
        scrollbar-width: none; /* Firefox */
    }
</style>
@endpush

<div x-data="pdpPage()" class="pb-16 lg:pb-12">

    {{-- ── Breadcrumb ── --}}
    <nav aria-label="Breadcrumb" class="-mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8 py-3 bg-slate-950/60 border-b border-slate-900/60 mb-6 sm:mb-8">
        <ol class="flex items-center flex-wrap gap-x-2 gap-y-1 text-xs text-slate-500 max-w-7xl mx-auto">
            <li>
                <a href="{{ url('/') }}" class="text-slate-400 hover:theme-text font-medium transition-colors">Home</a>
            </li>
            <li class="text-slate-700"><i class="fa-solid fa-chevron-right text-[9px]"></i></li>
            @if($product->categories->first())
            <li>
                <a href="{{ route('products.index', ['category' => $product->categories->first()->slug]) }}"
                    class="text-slate-400 hover:theme-text font-medium transition-colors">
                    {{ $product->categories->first()->name }}
                </a>
            </li>
            <li class="text-slate-700"><i class="fa-solid fa-chevron-right text-[9px]"></i></li>
            @endif
            <li class="text-slate-300 font-semibold truncate max-w-[200px] sm:max-w-xs">{{ Str::limit($product->name, 55) }}</li>
        </ol>
    </nav>

    {{-- ── Main PDP Grid ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 mb-12">

        {{-- ════ LEFT: Image Gallery ════ --}}
        <div class="lg:sticky lg:top-24 h-fit">

            @php
                $allMedia    = $product->getMedia('product-images');
                $primaryMedia = $allMedia->first();
                $hdUrl       = $primaryMedia?->getUrl('hd') ?? $product->thumbnail_url;
                $thumbUrl    = $primaryMedia?->getUrl('thumb') ?? $product->thumbnail_url;
            @endphp

            {{-- 1. Desktop Image Gallery (Zoomable) --}}
            <div class="hidden lg:block relative bg-slate-900 border border-slate-800 rounded-3xl overflow-hidden aspect-square group animate-fade-in-up"
                @mousemove="handleZoom($event, $el)"
                @mouseleave="zoomActive = false">

                <img
                    :src="activeImage || '{{ $hdUrl }}'"
                    alt="{{ $product->name }}"
                    class="w-full h-full object-contain p-6 pdp-zoom-img select-none transition-all duration-300"
                    :class="imageLoading ? 'opacity-40 scale-95' : 'opacity-100 scale-100'"
                    id="pdp-main-image"
                    width="600" height="600"
                >

                {{-- Zoom Overlay --}}
                <div
                    class="absolute inset-0 pointer-events-none rounded-3xl opacity-95 bg-no-repeat z-10"
                    x-show="zoomActive"
                    :style="`background-image: url('${activeImage || '{{ $hdUrl }}'}'); background-position: ${zoomX}% ${zoomY}%; background-size: 260%;`"
                ></div>

                {{-- Badges --}}
                @if($product->is_on_sale)
                <div class="absolute top-4 left-4 theme-badge text-xs font-black px-3 py-1 rounded-xl shadow-lg z-20">
                    -{{ $product->discount_percent }}% OFF
                </div>
                @endif
                @if($product->is_featured)
                <div class="absolute top-4 right-4 bg-amber-500 text-white text-xs font-black px-3 py-1 rounded-xl shadow-lg z-20">
                    ⭐ Unggulan
                </div>
                @endif

                {{-- Zoom hint --}}
                <div class="absolute bottom-4 right-4 z-20 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    <span class="bg-slate-950/80 backdrop-blur-sm border border-slate-700 text-slate-400 text-[10px] font-medium px-2.5 py-1.5 rounded-lg flex items-center gap-1.5">
                        <i class="fa-solid fa-magnifying-glass-plus text-[10px]"></i> Geser untuk zoom
                    </span>
                </div>
            </div>

            {{-- 2. Mobile Image Gallery (Swipeable) --}}
            <div class="lg:hidden relative bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden aspect-square animate-fade-in-up">
                <div 
                    id="mobile-gallery-scroller"
                    class="flex h-full overflow-x-auto snap-x snap-mandatory scroll-smooth scrollbar-none"
                    @scroll.debounce.50ms="activeIndex = Math.round($el.scrollLeft / $el.clientWidth)"
                >
                    @foreach($allMedia as $index => $media)
                    <div class="w-full h-full flex-shrink-0 snap-start flex items-center justify-center p-6 relative">
                        <img src="{{ $media->getUrl('hd') }}" alt="{{ $product->name }}" class="w-full h-full object-contain select-none" loading="lazy">
                    </div>
                    @endforeach
                    @if($allMedia->isEmpty())
                    <div class="w-full h-full flex-shrink-0 snap-start flex items-center justify-center p-6 relative">
                        <img src="{{ $product->thumbnail_url }}" alt="{{ $product->name }}" class="w-full h-full object-contain select-none">
                    </div>
                    @endif
                </div>

                {{-- Mobile Badges --}}
                @if($product->is_on_sale)
                <div class="absolute top-3 left-3 theme-badge text-[10px] font-black px-2 py-0.5 rounded-lg shadow-md z-20">
                    -{{ $product->discount_percent }}% OFF
                </div>
                @endif
                @if($product->is_featured)
                <div class="absolute top-3 right-3 bg-amber-500 text-white text-[10px] font-black px-2 py-0.5 rounded-lg shadow-md z-20">
                    ⭐ Unggulan
                </div>
                @endif

                {{-- Page Counter Indicator --}}
                @if($allMedia->count() > 1)
                <div class="absolute bottom-3 right-3 bg-slate-950/80 backdrop-blur-sm border border-slate-800 text-slate-300 text-[10px] font-semibold px-2.5 py-1 rounded-full z-20 flex items-center gap-1 shadow-sm">
                    <i class="fa-regular fa-image text-slate-500"></i>
                    <span><span x-text="activeIndex + 1"></span>/{{ $allMedia->count() }}</span>
                </div>
                @endif
            </div>

            {{-- Thumbnail Strip (Shared) --}}
            @if($allMedia->count() > 1)
            <div class="flex gap-2 mt-3 overflow-x-auto scrollbar-none snap-x -mx-4 px-4 sm:mx-0 sm:px-0 py-1">
                @foreach($allMedia as $index => $media)
                <button
                    class="w-14 h-14 sm:w-16 sm:h-16 rounded-xl overflow-hidden border-2 transition-all duration-200 bg-slate-900 flex-shrink-0 snap-start"
                    :class="activeIndex === {{ $index }} ? 'scale-105 theme-border shadow-md' : 'border-slate-800 hover:border-slate-600'"
                    :style="activeIndex === {{ $index }} ? 'box-shadow: 0 4px 12px color-mix(in srgb, var(--c-primary) 25%, transparent)' : ''"
                    @click="setImage('{{ $media->getUrl('hd') }}', {{ $index }})"
                    aria-label="Foto produk {{ $index + 1 }}"
                >
                    <picture>
                        <source srcset="{{ $media->getUrl('thumb') }}" type="image/webp">
                        <img src="{{ $media->getUrl('thumb') }}" alt="Thumbnail {{ $index + 1 }}" class="w-full h-full object-contain p-1" loading="lazy">
                    </picture>
                </button>
                @endforeach
            </div>
            @endif
        </div>

        {{-- ════ RIGHT: Product Info ════ --}}
        <div class="flex flex-col gap-4 sm:gap-5 animate-fade-in-up" style="animation-delay: 150ms;">

            {{-- Category tags --}}
            <div class="flex flex-wrap gap-1.5">
                @foreach($product->categories as $cat)
                <a href="{{ route('products.index', ['category' => $cat->slug]) }}"
                    class="text-[10px] sm:text-[11px] font-bold theme-text theme-bg-10 border theme-border-20 hover:theme-bg-15 rounded-full px-2.5 py-0.5 sm:px-3 sm:py-1 uppercase tracking-widest transition-colors">
                    {{ $cat->name }}
                </a>
                @endforeach
            </div>

            {{-- Product Name --}}
            <h1 class="text-xl sm:text-2xl lg:text-3xl font-black text-white tracking-tight leading-tight">
                {{ $product->name }}
            </h1>

            {{-- SKU / OEM --}}
            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-xs text-slate-500 font-medium">OEM / SKU:</span>
                <code id="pdp-sku" class="font-mono text-xs sm:text-sm theme-text theme-bg-5 border theme-border-10 px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-lg">{{ $product->sku }}</code>
                <button
                    @click="copyToClipboard('{{ $product->sku }}')"
                    class="flex items-center gap-1.5 text-xs text-slate-400 hover:text-white border border-slate-800 hover:border-slate-700 bg-slate-900/40 rounded-lg px-2.5 py-1 transition-all"
                    title="Salin SKU"
                >
                    <i class="fa-regular fa-copy text-[10px]"></i>
                    <span x-show="!copied">Salin</span>
                    <span x-show="copied" x-transition class="text-emerald-400 font-semibold">✓ Disalin!</span>
                </button>
            </div>

            {{-- Price Block --}}
            <div class="flex items-baseline flex-wrap gap-2.5 pb-4 border-b border-slate-900">
                <span class="text-2xl sm:text-3xl font-black text-white tracking-tight">Rp {{ number_format($product->getFinalPrice(), 0, ',', '.') }}</span>
                @if($product->is_on_sale)
                <span class="text-sm sm:text-base text-slate-500 line-through font-medium">Rp {{ number_format($product->compare_at_price, 0, ',', '.') }}</span>
                <span class="inline-flex items-center gap-1 text-[11px] sm:text-xs font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 rounded-lg px-2.5 py-0.5">
                    <i class="fa-solid fa-tag text-[10px]"></i>
                    Hemat Rp {{ number_format($product->compare_at_price - $product->getFinalPrice(), 0, ',', '.') }}
                </span>
                @endif
            </div>

            {{-- Stock Indicator --}}
            <div class="flex items-center gap-2">
                @if($product->stock > 10)
                    <span class="w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.8)] animate-pulse flex-shrink-0"></span>
                    <span class="text-xs sm:text-sm font-semibold text-emerald-400">Stok Tersedia — {{ $product->stock }} pcs</span>
                @elseif($product->stock > 0)
                    <span class="w-2 h-2 rounded-full bg-amber-500 blink-anim flex-shrink-0"></span>
                    <span class="text-xs sm:text-sm font-semibold text-amber-400">Stok Terbatas — Tinggal {{ $product->stock }} lagi!</span>
                @else
                    <span class="w-2 h-2 rounded-full theme-bg flex-shrink-0"></span>
                    <span class="text-xs sm:text-sm font-semibold theme-text">Stok Habis — Silakan Pre-Order</span>
                @endif
            </div>

            {{-- Short Description --}}
            @if($product->short_description)
            <div class="text-xs sm:text-sm text-slate-400 leading-relaxed border-l border-slate-800 pl-3">
                {!! nl2br(e($product->short_description)) !!}
            </div>
            @endif

            {{-- Vehicle Fitment Table / List --}}
            @if($product->fitments->isNotEmpty())
            <div class="bg-slate-900/60 border border-slate-850 rounded-2xl overflow-hidden shadow-inner">
                <div class="px-4 py-3 bg-slate-900/80 border-b border-slate-850 flex items-center gap-2">
                    <span class="text-sm">🏍️</span>
                    <h2 class="text-xs sm:text-sm font-bold text-slate-300">Kompatibilitas Kendaraan</h2>
                    <span class="ml-auto text-[10px] text-slate-500 font-semibold">{{ $product->fitments->count() }} motor</span>
                </div>
                
                {{-- Desktop View Table --}}
                <div class="hidden sm:block overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-850">
                                <th class="text-left px-4 py-2 text-[10px] font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap">Merek</th>
                                <th class="text-left px-4 py-2 text-[10px] font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap">Model</th>
                                <th class="text-left px-4 py-2 text-[10px] font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap">Tahun</th>
                                <th class="text-left px-4 py-2 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($product->fitments->sortBy('bikeBrand.name') as $fitment)
                            <tr class="border-b border-slate-850/60 last:border-0 hover:bg-slate-850/30 transition-colors">
                                <td class="px-4 py-2">
                                    <span class="text-[10px] font-semibold theme-text theme-bg-10 border theme-border-15 rounded px-2 py-0.5">{{ $fitment->bikeBrand?->name ?? '—' }}</span>
                                </td>
                                <td class="px-4 py-2 text-slate-300 text-xs font-medium">{{ $fitment->name }}</td>
                                <td class="px-4 py-2">
                                    <span class="text-[10px] font-semibold text-emerald-400 bg-emerald-500/10 border border-emerald-500/15 rounded px-2 py-0.5">
                                        {{ $fitment->pivot->year ?? 'Semua' }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-xs text-slate-500 italic">{{ $fitment->pivot->notes ?? '—' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Mobile View Collapsible Compact List --}}
                <div class="block sm:hidden divide-y divide-slate-850" x-data="{ showAllFitments: false }">
                    @foreach($product->fitments->sortBy('bikeBrand.name') as $index => $fitment)
                    <div 
                        class="p-3 flex items-center justify-between gap-3 text-xs"
                        x-show="showAllFitments || {{ $index }} < 4"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                    >
                        <div class="min-w-0">
                            <span class="inline-block font-bold theme-text theme-bg-10 border theme-border-15 rounded px-1.5 py-0.5 mr-1.5 text-[9px]">{{ $fitment->bikeBrand?->name ?? '—' }}</span>
                            <span class="text-slate-200 font-semibold">{{ $fitment->name }}</span>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <span class="font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/15 rounded px-1.5 py-0.5 text-[9px]">{{ $fitment->pivot->year ?? 'Semua' }}</span>
                            @if($fitment->pivot->notes)
                            <p class="text-[9px] text-slate-500 mt-0.5 italic">{{ $fitment->pivot->notes }}</p>
                            @endif
                        </div>
                    </div>
                    @endforeach

                    @if($product->fitments->count() > 4)
                    <div class="p-2.5 text-center bg-slate-900/30">
                        <button 
                            @click="showAllFitments = !showAllFitments"
                            class="text-xs font-bold theme-text hover:theme-text-light inline-flex items-center gap-1.5 focus:outline-none transition-all py-1 px-3 rounded-lg hover:bg-slate-800/40"
                        >
                            <span x-text="showAllFitments ? 'Sembunyikan' : 'Lihat Semua ({{ $product->fitments->count() }} Motor)'"></span>
                            <i class="fa-solid transition-transform duration-200" :class="showAllFitments ? 'rotate-180' : ''"><i class="fa-solid fa-chevron-down text-[10px]"></i></i>
                        </button>
                    </div>
                    @endif
                </div>

                <p class="text-[10px] text-slate-500 italic px-4 py-2 border-t border-slate-850">
                    * Pastikan nomor seri atau kode part sesuai sebelum checkout.
                </p>
            </div>
            @endif

            {{-- ── WhatsApp CTA Block ── --}}
            <div class="flex flex-col gap-3 pt-1">

                {{-- Garasi Selector (for WA context) --}}
                @if($product->fitments->isNotEmpty())
                
                {{-- Active selected badge --}}
                <div x-show="fitmentSelected" class="bg-emerald-500/5 border border-emerald-500/20 rounded-xl p-3 flex items-center justify-between text-xs transition-all animate-fade-in-up" x-cloak>
                    <span class="text-slate-300">
                        🛵 Dipilih: <strong class="text-emerald-400"><span x-text="garageBrand"></span> <span x-text="garageModel"></span> <span x-text="garageYear ? '(' + garageYear + ')' : ''"></span></strong>
                    </span>
                    <button 
                        @click="fitmentSelected = false; garageBrand=''; garageModel=''; garageYear=''" 
                        class="text-xs theme-text hover:theme-text-light font-bold transition-all px-2.5 py-1 rounded-lg hover:theme-bg-5 border theme-border-10"
                    >
                        Ubah
                    </button>
                </div>

                {{-- Manual Selector Widget --}}
                <div x-show="!fitmentSelected" class="bg-slate-950/40 border border-slate-850 rounded-2xl p-4 transition-all duration-300 hover:border-slate-800" x-data="{ showGarageSelector: false }">
                    <div class="flex items-center justify-between cursor-pointer" @click="showGarageSelector = !showGarageSelector">
                        <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider flex items-center gap-2 select-none cursor-pointer">
                            🔧 Sesuaikan dengan Motor Saya (Opsional)
                        </label>
                        <span class="text-[10px] text-slate-400 hover:theme-text transition-colors flex items-center gap-1">
                            <span x-text="showGarageSelector ? 'Tutup' : 'Pilih'"></span>
                            <i class="fa-solid text-[9px] transition-transform duration-250" :class="showGarageSelector ? 'rotate-180' : ''"><i class="fa-solid fa-chevron-down"></i></i>
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 mt-3" x-show="showGarageSelector" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-cloak>
                        {{-- Brand --}}
                        <div class="relative">
                            <select x-model="garageBrand" @change="garageModel=''; garageYear=''"
                                class="w-full bg-slate-900 border border-slate-850 rounded-xl px-3 py-2 text-xs font-semibold text-slate-200 focus:outline-none focus:ring-1 theme-ring-focus-40 appearance-none cursor-pointer transition-all hover:bg-slate-800"
                                aria-label="Merek motor">
                                <option value="">Pilih Merek</option>
                                @foreach($product->fitments->groupBy('bikeBrand.name') as $brandName => $models)
                                <option value="{{ $brandName }}">{{ $brandName }}</option>
                                @endforeach
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 text-[9px] pointer-events-none"></i>
                        </div>

                        {{-- Model --}}
                        <div class="relative">
                            <select x-model="garageModel" :disabled="!garageBrand"
                                class="w-full bg-slate-900 border border-slate-850 rounded-xl px-3 py-2 text-xs font-semibold text-slate-200 focus:outline-none focus:ring-1 theme-ring-focus-40 appearance-none cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed transition-all hover:bg-slate-800"
                                aria-label="Model motor">
                                <option value="">Pilih Model</option>
                                @foreach($product->fitments as $fitment)
                                <option
                                    value="{{ $fitment->name }}"
                                    x-show="garageBrand === '{{ $fitment->bikeBrand?->name }}'">{{ $fitment->name }}</option>
                                @endforeach
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 text-[9px] pointer-events-none"></i>
                        </div>

                        {{-- Year --}}
                        <div class="relative">
                            <select x-model="garageYear" :disabled="!garageModel"
                                class="w-full bg-slate-900 border border-slate-850 rounded-xl px-3 py-2 text-xs font-semibold text-slate-200 focus:outline-none focus:ring-1 theme-ring-focus-40 appearance-none cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed transition-all hover:bg-slate-800"
                                aria-label="Tahun motor">
                                <option value="">Tahun</option>
                                @foreach($product->fitments as $fitment)
                                    @if($fitment->pivot->year)
                                    <option value="{{ $fitment->pivot->year }}" x-show="garageModel === '{{ $fitment->name }}'">{{ $fitment->pivot->year }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 text-[9px] pointer-events-none"></i>
                        </div>
                    </div>
                </div>
                @endif

                {{-- WhatsApp CTA Button --}}
                <a
                    :href="buildWaLink()"
                    target="_blank"
                    rel="noopener noreferrer"
                    id="pdp-wa-cta"
                    class="flex items-center gap-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white rounded-2xl px-5 py-4 font-semibold transition-all duration-300 hover:-translate-y-0.5 hover:shadow-xl hover:shadow-emerald-900/30 group animate-pulse-wa"
                    aria-label="Pesan atau tanya via WhatsApp"
                >
                    <i class="fa-brands fa-whatsapp text-2xl flex-shrink-0 animate-pulse"></i>
                    <div class="flex flex-col flex-1 min-w-0">
                        <span class="text-base font-bold leading-tight">Pesan / Tanya via WhatsApp</span>
                        <span class="text-xs text-emerald-100 font-normal" x-text="waSubtitle"></span>
                    </div>
                    <i class="fa-solid fa-arrow-right text-sm ml-auto group-hover:translate-x-1 transition-transform"></i>
                </a>

                {{-- Secondary Actions --}}
                <div class="flex items-center justify-between gap-3 mt-1">
                    <button @click="shareProduct()"
                        class="flex-1 flex items-center justify-center gap-2 text-xs sm:text-sm text-slate-400 hover:text-slate-200 bg-slate-900 hover:bg-slate-800 border border-slate-800 hover:border-slate-700 rounded-xl px-4 py-2.5 font-semibold transition-all">
                        <i class="fa-regular fa-share-from-square text-sm"></i> Bagikan
                    </button>
                    <span class="flex items-center gap-1.5 text-xs text-slate-500 bg-slate-900 border border-slate-900 rounded-xl px-4 py-2.5">
                        <i class="fa-regular fa-eye text-sm"></i>
                        {{ number_format($product->views_count) }} dilihat
                    </span>
                </div>
            </div>

        </div>{{-- /.pdp-info --}}
    </div>{{-- /.pdp-grid --}}

    {{-- ── Full Description ── --}}
    @if($product->description)
    <section class="bg-slate-900/40 border border-slate-850 rounded-3xl p-5 sm:p-8 mb-10 animate-fade-in-up" style="animation-delay: 200ms;">
        <h2 class="text-lg sm:text-xl font-bold text-white mb-4 pb-3 border-b border-slate-850 flex items-center gap-3">
            <span class="w-7 h-7 rounded-lg theme-bg-10 theme-text flex items-center justify-center text-xs flex-shrink-0">
                <i class="fa-solid fa-file-lines"></i>
            </span>
            Deskripsi Produk
        </h2>
        <div class="prose prose-invert prose-xs sm:prose-sm max-w-3xl prose-p:text-slate-400 prose-headings:text-slate-200 prose-strong:text-slate-200 prose-a-theme-text prose-li:text-slate-400 leading-relaxed text-justify">
            {!! $product->description !!}
        </div>
    </section>
    @endif

    {{-- ── Related Products ── --}}
    @if($relatedProducts->isNotEmpty())
    <section class="animate-fade-in-up" style="animation-delay: 250ms;">
        <h2 class="text-lg sm:text-xl font-bold text-white mb-5 flex items-center gap-3">
            <span class="w-7 h-7 rounded-lg theme-bg-10 theme-text flex items-center justify-center text-xs flex-shrink-0">
                <i class="fa-solid fa-layer-group"></i>
            </span>
            Produk Serupa
        </h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-5">
            @foreach($relatedProducts as $related)
            <article class="group bg-slate-900 border border-slate-850 rounded-2xl overflow-hidden theme-card-hover transition-all duration-300 flex flex-col">
                <a href="{{ route('products.show', $related->slug) }}" class="block relative aspect-square overflow-hidden bg-slate-950">
                    <img
                        src="{{ $related->thumbnail_url }}"
                        alt="{{ $related->name }}"
                        class="w-full h-full object-contain p-3 group-hover:scale-105 transition-transform duration-500"
                        loading="lazy"
                    >
                    @if($related->is_on_sale)
                    <span class="absolute top-2 left-2 theme-badge text-[9px] font-black px-1.5 py-0.5 rounded shadow-sm">-{{ $related->discount_percent }}%</span>
                    @endif
                </a>
                <div class="p-3 flex flex-col gap-1.5 flex-1">
                    <h3 class="text-xs font-semibold text-slate-200 line-clamp-2 leading-snug group-theme-text transition-colors">
                        <a href="{{ route('products.show', $related->slug) }}">{{ $related->name }}</a>
                    </h3>
                    <div class="flex items-baseline gap-1 mt-auto pt-1">
                        <span class="text-xs sm:text-sm font-black text-slate-100">Rp {{ number_format($related->getFinalPrice(), 0, ',', '.') }}</span>
                        @if($related->is_on_sale)
                        <span class="text-[10px] text-slate-500 line-through">Rp {{ number_format($related->compare_at_price, 0, ',', '.') }}</span>
                        @endif
                    </div>
                    <a href="{{ route('products.show', $related->slug) }}"
                        class="mt-1 text-center bg-slate-800 hover:theme-bg border border-slate-850 hover:border-transparent text-slate-300 hover:text-white font-bold text-[10px] py-1.5 rounded-xl transition-all duration-200">
                        Lihat Detail
                    </a>
                </div>
            </article>
            @endforeach
        </div>
    </section>
    @endif

    {{-- ── Mobile Sticky Bottom WA CTA Bar ── --}}
    <div 
        x-show="showStickyCta"
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="translate-y-full"
        x-transition:enter-end="translate-y-0"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="translate-y-0"
        x-transition:leave-end="translate-y-full"
        class="fixed bottom-0 left-0 right-0 z-40 lg:hidden bg-slate-950/95 backdrop-blur-md border-t border-slate-850 p-3 flex items-center justify-between gap-3 shadow-2xl pb-[calc(0.75rem+env(safe-area-inset-bottom,0px))]"
        x-cloak
    >
        <div class="flex items-center gap-2.5 min-w-0">
            <img 
                src="{{ $product->thumbnail_url }}" 
                alt="{{ $product->name }}" 
                class="w-10 h-10 object-contain bg-slate-900 border border-slate-800 rounded-lg p-1 flex-shrink-0"
            >
            <div class="min-w-0">
                <h4 class="text-xs font-bold text-slate-200 truncate pr-2">{{ $product->name }}</h4>
                <p class="text-xs font-black theme-text mt-0.5">Rp {{ number_format($product->getFinalPrice(), 0, ',', '.') }}</p>
            </div>
        </div>
        <a 
            :href="buildWaLink()"
            target="_blank"
            rel="noopener noreferrer"
            class="flex items-center gap-1.5 bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-xl px-4 py-2.5 text-xs font-bold transition-all shadow-md active:scale-95 flex-shrink-0"
        >
            <i class="fa-brands fa-whatsapp text-sm animate-pulse"></i>
            Tanya WA
        </a>
    </div>

</div>{{-- /x-data --}}

@push('scripts')
<script>
function pdpPage() {
    return {
        activeImage: null,
        activeIndex: 0,
        imageLoading: false,
        zoomActive: false,
        zoomX: 50,
        zoomY: 50,
        copied: false,
        showStickyCta: false,
        garageBrand: '{{ $fitmentContext["brand"] ?? "" }}',
        garageModel: '{{ $fitmentContext["model"] ?? "" }}',
        garageYear:  '{{ $fitmentContext["year"] ?? "" }}',
        fitmentSelected: {{ !empty($fitmentContext) ? 'true' : 'false' }},

        init() {
            this.activeImage = '{{ $hdUrl }}';
            
            // Wait for DOM layout, setup Intersection Observer for Mobile Sticky Bottom CTA
            setTimeout(() => {
                const target = document.getElementById('pdp-wa-cta');
                if (target) {
                    const observer = new IntersectionObserver((entries) => {
                        this.showStickyCta = !entries[0].isIntersecting;
                    }, { 
                        threshold: 0,
                        rootMargin: '-80px 0px 0px 0px' // Offset header
                    });
                    observer.observe(target);
                }
            }, 100);
        },

        get waSubtitle() {
            if (this.garageBrand && this.garageModel) {
                return `Untuk ${this.garageBrand} ${this.garageModel}${this.garageYear ? ' ' + this.garageYear : ''}`;
            }
            return 'Respon cepat — kami siap membantu!';
        },

        setImage(url, index) {
            this.imageLoading = true;
            this.activeIndex = index;
            this.activeImage = url;
            
            // On mobile, scroll the swiper container
            const scroller = document.getElementById('mobile-gallery-scroller');
            if (scroller) {
                scroller.scrollLeft = scroller.clientWidth * index;
            }
            setTimeout(() => { this.imageLoading = false; }, 150);
        },

        handleZoom(event, el) {
            if (window.innerWidth < 1024) return;
            const rect = el.getBoundingClientRect();
            this.zoomX = ((event.clientX - rect.left) / rect.width) * 100;
            this.zoomY = ((event.clientY - rect.top)  / rect.height) * 100;
            this.zoomActive = true;
        },

        copyToClipboard(text) {
            navigator.clipboard?.writeText(text).then(() => {
                this.copied = true;
                setTimeout(() => this.copied = false, 2200);
            });
        },

        buildWaLink() {
            const waNumber = '{{ config("app.whatsapp_number", "6282174128947") }}';
            let msg = `Halo, saya tertarik dengan produk:\n`;
            msg += `🔧 *{{ $product->name }}*\n`;
            msg += `📦 SKU/OEM: \`{{ $product->sku }}\`\n`;
            msg += `💰 Harga: Rp {{ number_format($product->getFinalPrice(), 0, ',', '.') }}\n`;
            if (this.garageBrand) {
                msg += `\n🏍️ Kendaraan saya:\n`;
                msg += `   Merk  : ${this.garageBrand}\n`;
                if (this.garageModel) msg += `   Model : ${this.garageModel}\n`;
                if (this.garageYear)  msg += `   Tahun : ${this.garageYear}\n`;
            }
            msg += `\n🔗 {{ route('products.show', $product->slug) }}\n`;
            msg += `\nApakah sparepart ini tersedia dan cocok untuk kendaraan saya?`;
            return `https://wa.me/${waNumber}?text=${encodeURIComponent(msg)}`;
        },

        shareProduct() {
            const data = {
                title: '{{ $product->name }}',
                text: 'Cek sparepart ini di {{ $siteSettings->get('store_name', 'MyCommerce') }}: {{ $product->name }} (SKU: {{ $product->sku }})',
                url: window.location.href,
            };
            if (navigator.share) {
                navigator.share(data).catch(() => {});
            } else {
                navigator.clipboard?.writeText(window.location.href).then(() => {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: 'Link disalin ke clipboard!' } }));
                });
            }
        }
    };
}
</script>
@endpush
</x-frontend-layout>
