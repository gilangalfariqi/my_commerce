<x-frontend-layout>

@push('styles')
<style>
    .catalog-hero-gradient {
        background: linear-gradient(135deg, #0f172a 0%, #1a0a0a 50%, #0f172a 100%);
    }
    @keyframes dot-pulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(220,38,38,0.4); }
        50% { box-shadow: 0 0 0 6px rgba(220,38,38,0); }
    }
    .dot-pulse { animation: dot-pulse 2s infinite; }
</style>
@endpush

{{-- ── Page Hero ── --}}
<section class="catalog-hero-gradient -mx-4 sm:-mx-6 lg:-mx-8 px-6 sm:px-12 lg:px-16 py-10 sm:py-14 mb-0 border-b border-slate-800/60">
    <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 flex-wrap">
        {{-- Text --}}
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 mb-3">
                <span class="w-2 h-2 rounded-full bg-red-600 dot-pulse"></span>
                <span class="text-xs font-bold text-red-500 uppercase tracking-widest">
                    {{ isset($totalActiveCount) ? number_format($totalActiveCount) : 0 }} Produk Tersedia
                </span>
            </div>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white tracking-tight leading-tight mb-3">
                Katalog <span class="bg-gradient-to-r from-red-500 to-orange-400 bg-clip-text text-transparent">Sparepart</span> Motor
            </h1>
            <p class="text-sm sm:text-base text-slate-400 max-w-xl leading-relaxed">
                Temukan sparepart OEM &amp; aftermarket berkualitas untuk Honda, Yamaha, Kawasaki, Suzuki, dan lebih banyak merek.
                Filter berdasarkan kendaraanmu untuk hasil yang presisi.
            </p>
        </div>

        {{-- Active fitment context banner --}}
        @if(isset($activeBrand) && $activeBrand)
        <div class="flex items-center gap-3 bg-red-500/10 border border-red-500/20 backdrop-blur-sm rounded-2xl px-4 py-3 flex-shrink-0">
            <span class="text-2xl">🏍️</span>
            <div class="min-w-0">
                <span class="block text-[10px] font-bold text-red-400 uppercase tracking-widest">Sparepart untuk:</span>
                <strong class="text-sm text-slate-100 font-bold">
                    {{ $activeBrand->name }}
                    @if(isset($activeModel) && $activeModel)
                        — {{ $activeModel->name }}
                    @endif
                </strong>
            </div>
            <a href="{{ route('products.index') }}" class="ml-2 flex items-center gap-1 text-xs text-slate-400 hover:text-red-400 border border-slate-700/60 hover:border-red-500/30 rounded-xl px-2.5 py-1.5 transition-all whitespace-nowrap">
                <i class="fa-solid fa-xmark text-[10px]"></i> Hapus
            </a>
        </div>
        @endif
    </div>
</section>

{{-- ── Livewire Catalog Component ── --}}
@livewire('catalog.product-filter')

</x-frontend-layout>
