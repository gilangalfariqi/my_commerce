{{--
    Livewire Component View — ProductFilter
    Component: App\Livewire\Catalog\ProductFilter
    Properties: $search, $selectedBrand, $selectedModel, $selectedYear,
                $selectedCategory, $priceMin, $priceMax, $sortBy,
                $inStockOnly, $perPage
    Computed:   $this->bikeBrands, $this->bikeModels, $this->availableYears,
                $this->categories, $this->products, $this->activeFilterCount
    Actions:    resetFilters()
--}}

@push('styles')
<style>
@keyframes shimmer {
    from { background-position: 200% 0; }
    to   { background-position: -200% 0; }
}
.shimmer-card {
    animation: shimmer 1.5s infinite;
    background: linear-gradient(90deg, #1e293b 25%, #334155 50%, #1e293b 75%);
    background-size: 200% 100%;
}
</style>
@endpush

<div
    class="py-6"
    x-data="catalogUI()"
>

    {{-- ══════════════════════════════════════════════
         OUTER LAYOUT: sidebar + main
    ══════════════════════════════════════════════ --}}
    <div class="lg:grid lg:grid-cols-[280px_1fr] lg:gap-8 lg:items-start">

        {{-- ──────────────────────────────────────────
             SIDEBAR
        ────────────────────────────────────────── --}}
        <aside>

            {{-- Mobile overlay --}}
            <div
                class="lg:hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-40"
                x-show="sidebarOpen"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="sidebarOpen = false"
                style="display:none;"
            ></div>

            {{-- Sidebar panel --}}
            <div
                class="fixed lg:sticky top-0 lg:top-24 left-0 h-screen lg:h-auto w-72 lg:w-auto z-50 lg:z-auto
                       bg-slate-900 border-r lg:border border-slate-800 lg:rounded-2xl
                       overflow-y-auto overscroll-contain
                       transition-transform duration-300 ease-in-out"
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
                style="-webkit-overflow-scrolling: touch;"
            >

                {{-- ── Sidebar Header ── --}}
                <div class="flex items-center justify-between p-5 border-b border-slate-800 sticky top-0 bg-slate-900 z-10">
                    <h2 class="text-sm font-bold text-slate-200 flex items-center gap-2">
                        <i class="fa-solid fa-sliders text-red-500"></i>
                        Filter Produk
                    </h2>
                    <div class="flex items-center gap-2">
                        @if($this->activeFilterCount > 0)
                            <button
                                wire:click="resetFilters"
                                class="text-xs text-red-400 bg-red-500/10 border border-red-500/20 hover:bg-red-500/20 rounded-full px-3 py-1 flex items-center gap-1 transition-colors"
                            >
                                <i class="fa-solid fa-rotate-right text-[10px]"></i>
                                Reset ({{ $this->activeFilterCount }})
                            </button>
                        @endif
                        <button
                            class="lg:hidden text-slate-400 hover:text-white transition-colors p-1"
                            @click="sidebarOpen = false"
                            aria-label="Tutup filter"
                        >
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>
                </div>

                {{-- ── Search ── --}}
                <div class="p-4 border-b border-slate-800/60">
                    <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2 block">
                        Cari Produk / OEM SKU
                    </label>
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-xs pointer-events-none"></i>
                        <input
                            type="search"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Cth: Kampas rem, 43105-KPH..."
                            autocomplete="off"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl pl-9 pr-9 py-2.5 text-sm text-slate-200 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-red-500/40 focus:border-red-500 transition-all"
                        >
                        <div
                            wire:loading.delay
                            wire:target="search"
                            class="absolute right-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 border-2 border-red-500 border-t-transparent rounded-full animate-spin"
                        ></div>
                    </div>
                </div>

                {{-- ── Vehicle Filter ── --}}
                <div class="p-4 border-b border-slate-800/60">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">🏍️ Filter Kendaraan</span>
                        @if($selectedBrand || $selectedModel)
                            <span class="text-[10px] bg-red-500/15 text-red-400 border border-red-500/20 rounded-full px-2 py-0.5 font-bold">
                                Aktif
                            </span>
                        @endif
                    </div>

                    {{-- Brand --}}
                    <div
                        class="relative mb-2 transition-opacity"
                        wire:loading.class="opacity-60"
                        wire:target="selectedBrand"
                    >
                        <select
                            wire:model.live="selectedBrand"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 pr-8 py-2.5 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-red-500/40 focus:border-red-500 appearance-none cursor-pointer transition-all"
                            aria-label="Pilih Merek Motor"
                        >
                            <option value="">— Pilih Merek —</option>
                            @foreach($this->bikeBrands as $brand)
                                <option value="{{ $brand->slug }}">{{ $brand->name }}</option>
                            @endforeach
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 text-[10px] pointer-events-none"></i>
                    </div>

                    {{-- Model --}}
                    <div
                        class="relative mb-2 transition-opacity"
                        wire:loading.class="opacity-60"
                        wire:target="selectedBrand,selectedModel"
                    >
                        <select
                            wire:model.live="selectedModel"
                            @disabled(blank($selectedBrand))
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 pr-8 py-2.5 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-red-500/40 focus:border-red-500 appearance-none cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed transition-all"
                            aria-label="Pilih Model"
                        >
                            <option value="">— Pilih Model —</option>
                            @foreach($this->bikeModels as $model)
                                <option value="{{ $model->slug }}">{{ $model->name }}</option>
                            @endforeach
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 text-[10px] pointer-events-none"></i>
                    </div>

                    {{-- Year --}}
                    <div
                        class="relative transition-opacity"
                        wire:loading.class="opacity-60"
                        wire:target="selectedModel,selectedYear"
                    >
                        <select
                            wire:model.live="selectedYear"
                            @disabled(blank($selectedModel) || empty($this->availableYears))
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 pr-8 py-2.5 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-red-500/40 focus:border-red-500 appearance-none cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed transition-all"
                            aria-label="Pilih Tahun"
                        >
                            <option value="">— Pilih Tahun —</option>
                            @foreach($this->availableYears as $year)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endforeach
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 text-[10px] pointer-events-none"></i>
                    </div>

                    {{-- Active fitment info --}}
                    @if($selectedBrand && $selectedModel)
                        <p class="mt-2 text-xs text-red-400 bg-red-500/8 border border-red-500/15 rounded-lg px-3 py-2 leading-snug">
                            Menampilkan sparepart untuk
                            <strong>{{ $this->bikeModels->firstWhere('slug', $selectedModel)?->name ?? $selectedModel }}</strong>{{ $selectedYear ? " tahun $selectedYear" : '' }}
                        </p>
                    @endif
                </div>

                {{-- ── Category ── --}}
                <div class="p-4 border-b border-slate-800/60">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-3 block">Kategori</span>
                    <div class="space-y-0.5">

                        {{-- All categories --}}
                        <button
                            wire:click="$set('selectedCategory', '')"
                            type="button"
                            class="w-full flex items-center justify-between px-3 py-2 rounded-xl cursor-pointer transition-colors text-left
                                {{ blank($selectedCategory) ? 'bg-red-500/10 text-red-400 font-semibold' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}"
                        >
                            <span class="text-sm">Semua Kategori</span>
                            @if(blank($selectedCategory))
                                <i class="fa-solid fa-check text-[10px] text-red-400 flex-shrink-0"></i>
                            @endif
                        </button>

                        @foreach($this->categories as $cat)
                            <button
                                wire:click="$set('selectedCategory', '{{ $cat->slug }}')"
                                type="button"
                                class="w-full flex items-center justify-between px-3 py-2 rounded-xl cursor-pointer transition-colors text-left
                                    {{ $selectedCategory === $cat->slug ? 'bg-red-500/10 text-red-400 font-semibold' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}"
                            >
                                <span class="text-sm">{{ $cat->name }}</span>
                                @if($selectedCategory === $cat->slug)
                                    <i class="fa-solid fa-check text-[10px] text-red-400 flex-shrink-0"></i>
                                @endif
                            </button>
                        @endforeach

                    </div>
                </div>

                {{-- ── Price Range ── --}}
                <div class="p-4 border-b border-slate-800/60">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-3 block">Rentang Harga (Rp)</span>
                    <div class="flex items-center gap-2">
                        <div class="relative flex-1">
                            <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-[10px] text-slate-500 pointer-events-none select-none">Rp</span>
                            <input
                                type="number"
                                wire:model.live.debounce.500ms="priceMin"
                                placeholder="Min"
                                min="0"
                                step="1000"
                                class="w-full bg-slate-950 border border-slate-700 rounded-xl pl-8 pr-3 py-2.5 text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-red-500/40 focus:border-red-500 transition-all"
                            >
                        </div>
                        <span class="text-slate-600 font-bold flex-shrink-0">–</span>
                        <div class="relative flex-1">
                            <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-[10px] text-slate-500 pointer-events-none select-none">Rp</span>
                            <input
                                type="number"
                                wire:model.live.debounce.500ms="priceMax"
                                placeholder="Max"
                                min="0"
                                step="1000"
                                class="w-full bg-slate-950 border border-slate-700 rounded-xl pl-8 pr-3 py-2.5 text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-red-500/40 focus:border-red-500 transition-all"
                            >
                        </div>
                    </div>
                </div>

                {{-- ── Stock Toggle ── --}}
                <div class="p-4">
                    <button
                        wire:click="$toggle('inStockOnly')"
                        type="button"
                        class="w-full flex items-center justify-between cursor-pointer gap-3 text-left"
                        aria-label="{{ $inStockOnly ? 'Nonaktifkan filter stok' : 'Aktifkan filter stok tersedia' }}"
                    >
                        <span class="text-sm text-slate-300 leading-snug">Tampilkan Stok Tersedia Saja</span>
                        <div class="relative flex-shrink-0">
                            <div class="w-10 h-5 {{ $inStockOnly ? 'bg-red-600 border-red-500' : 'bg-slate-700 border-slate-600' }} rounded-full transition-colors border">
                                <div class="absolute top-0.5 {{ $inStockOnly ? 'left-5' : 'left-0.5' }} w-4 h-4 bg-white rounded-full shadow-sm transition-all duration-200"></div>
                            </div>
                        </div>
                    </button>
                </div>

            </div>{{-- /sidebar panel --}}
        </aside>{{-- /aside --}}

        {{-- ──────────────────────────────────────────
             MAIN CONTENT
        ────────────────────────────────────────── --}}
        <main class="min-w-0 mt-4 lg:mt-0">

            {{-- ── Topbar ── --}}
            <div class="flex flex-wrap items-center justify-between gap-3 mb-6 pb-4 border-b border-slate-800">

                {{-- Result info --}}
                <div class="flex items-center gap-3 min-w-0">
                    <div
                        wire:loading.remove
                        wire:target="search,selectedBrand,selectedModel,selectedYear,selectedCategory,priceMin,priceMax,inStockOnly,isFeaturedOnly,sortBy"
                        class="flex items-center gap-2 flex-wrap"
                    >
                        <span class="text-sm text-slate-400">
                            <span class="font-bold text-slate-200">{{ number_format($this->products->total()) }}</span> produk
                        </span>
                        @if($isFeaturedOnly)
                            <span class="inline-flex items-center gap-1.5 text-xs bg-amber-500/10 text-amber-400 border border-amber-500/20 rounded-full px-2.5 py-0.5 font-semibold">
                                <span>⭐ Produk Unggulan</span>
                                <button type="button" wire:click="$set('isFeaturedOnly', false)" class="hover:text-amber-300 transition-colors ml-0.5 font-bold text-base leading-none" aria-label="Hapus filter produk unggulan">×</button>
                            </span>
                        @endif
                        @if($this->activeFilterCount > 0)
                            <span class="text-xs bg-red-500/10 text-red-400 border border-red-500/15 rounded-full px-2.5 py-0.5 font-semibold">
                                {{ $this->activeFilterCount }} filter aktif
                            </span>
                        @endif
                    </div>
                    <div
                        wire:loading
                        wire:target="search,selectedBrand,selectedModel,selectedYear,selectedCategory,priceMin,priceMax,inStockOnly,isFeaturedOnly,sortBy"
                        class="flex items-center gap-2 text-sm text-slate-500"
                    >
                        <div class="w-3.5 h-3.5 border-2 border-red-500 border-t-transparent rounded-full animate-spin"></div>
                        Memuat...
                    </div>
                </div>

                {{-- Controls --}}
                <div class="flex items-center gap-2 flex-wrap">

                    {{-- Mobile filter button --}}
                    <button
                        class="lg:hidden relative flex items-center gap-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 rounded-xl px-3 py-2 text-sm text-slate-300 font-semibold transition-colors"
                        @click="sidebarOpen = true"
                        aria-label="Buka filter"
                    >
                        <i class="fa-solid fa-sliders text-red-500 text-sm"></i>
                        Filter
                        @if($this->activeFilterCount > 0)
                            <span class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-red-600 text-white rounded-full text-[10px] font-bold flex items-center justify-center">
                                {{ $this->activeFilterCount }}
                            </span>
                        @endif
                    </button>

                    {{-- Sort --}}
                    <div class="relative">
                        <select
                            wire:model.live="sortBy"
                            class="bg-slate-900 border border-slate-700 rounded-xl px-3 pr-8 py-2 text-sm text-slate-300 focus:outline-none focus:ring-2 focus:ring-red-500/30 appearance-none cursor-pointer transition-all"
                            aria-label="Urutkan produk"
                        >
                            <option value="-created_at">Terbaru</option>
                            <option value="price_asc">Harga ↑</option>
                            <option value="price_desc">Harga ↓</option>
                            <option value="popular">Terpopuler</option>
                            <option value="name_asc">A–Z</option>
                            <option value="featured">Unggulan</option>
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-500 text-[10px] pointer-events-none"></i>
                    </div>

                    {{-- Per page --}}
                    <div class="relative">
                        <select
                            wire:model.live="perPage"
                            class="bg-slate-900 border border-slate-700 rounded-xl px-3 pr-8 py-2 text-sm text-slate-300 focus:outline-none focus:ring-2 focus:ring-red-500/30 appearance-none cursor-pointer transition-all"
                            aria-label="Produk per halaman"
                        >
                            <option value="12">12</option>
                            <option value="24">24</option>
                            <option value="48">48</option>
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-500 text-[10px] pointer-events-none"></i>
                    </div>

                </div>
            </div>{{-- /topbar --}}

            {{-- ── Product Grid ── --}}
            <div class="relative">

                {{-- Live grid --}}
                <div
                    class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-4"
                    wire:loading.class="opacity-50 pointer-events-none"
                    wire:target="search,selectedBrand,selectedModel,selectedYear,selectedCategory,priceMin,priceMax,inStockOnly,isFeaturedOnly,sortBy,perPage"
                >
                    @if($this->products->isEmpty())
                        {{-- Empty state --}}
                        <div class="col-span-full py-20 text-center" wire:loading.remove>
                            <div class="text-6xl mb-5 select-none">🔩</div>
                            <h3 class="text-lg font-bold text-slate-300 mb-2">Sparepart tidak ditemukan</h3>
                            <p class="text-sm text-slate-500 mb-5">
                                Coba ubah filter atau
                                <button
                                    wire:click="resetFilters"
                                    class="text-red-400 underline hover:text-red-300 transition-colors"
                                >hapus semua filter</button>
                            </p>
                            <div class="inline-flex items-center gap-2 text-xs text-slate-600 bg-slate-800/60 border border-slate-700 rounded-xl px-4 py-2.5">
                                <i class="fa-solid fa-circle-info text-red-500/70"></i>
                                Tidak ada produk yang cocok dengan kombinasi filter yang dipilih.
                            </div>
                        </div>
                    @else
                        @foreach($this->products as $product)
                            @include('livewire.catalog.product-card', ['product' => $product])
                        @endforeach
                    @endif
                </div>

                {{-- Loading skeleton overlay (pointer-events-none so it never blocks sidebar/controls) --}}
                <div
                    wire:loading
                    wire:target="search,selectedBrand,selectedModel,selectedYear,selectedCategory,priceMin,priceMax,inStockOnly,isFeaturedOnly,sortBy,perPage"
                    class="absolute inset-0 grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-4 pointer-events-none"
                    style="z-index: 1;"
                >
                    @for($i = 0; $i < $perPage; $i++)
                        <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden">
                            <div class="aspect-square shimmer-card"></div>
                            <div class="p-4 space-y-2.5">
                                <div class="h-2.5 bg-slate-800 rounded-full w-1/4 animate-pulse"></div>
                                <div class="h-3 bg-slate-800 rounded-full w-full animate-pulse"></div>
                                <div class="h-3 bg-slate-800 rounded-full w-3/4 animate-pulse"></div>
                                <div class="h-4 bg-slate-800 rounded-full w-2/5 mt-1 animate-pulse"></div>
                                <div class="h-8 bg-slate-800 rounded-xl mt-2 animate-pulse"></div>
                            </div>
                        </div>
                    @endfor
                </div>

            </div>{{-- /grid wrapper --}}

            {{-- ── Pagination ── --}}
            @if($this->products->hasPages())
                <div class="mt-8 flex justify-center" wire:loading.remove wire:target="perPage">
                    {{ $this->products->links() }}
                </div>
            @endif

        </main>{{-- /main --}}

    </div>{{-- /layout grid --}}

</div>{{-- /outer wrapper --}}

@push('scripts')
<script>
function catalogUI() {
    return {
        sidebarOpen: false,

        init() {
            // Close drawer on Escape
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.sidebarOpen) {
                    this.sidebarOpen = false;
                }
            });

            // Lock body scroll when mobile sidebar is open
            this.$watch('sidebarOpen', (open) => {
                document.body.style.overflow = open ? 'hidden' : '';
            });

            // Auto-close sidebar on lg+ resize
            const mq = window.matchMedia('(min-width: 1024px)');
            mq.addEventListener('change', (e) => {
                if (e.matches) this.sidebarOpen = false;
            });
        },

        destroy() {
            document.body.style.overflow = '';
        }
    };
}
</script>
@endpush
