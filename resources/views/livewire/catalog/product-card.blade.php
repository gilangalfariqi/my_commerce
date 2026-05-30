{{--
    Product Card Partial — MotoPartHub
    Included via: @include('livewire.catalog.product-card', ['product' => $product])
    Requires: $product (App\Models\Product)
--}}
@php
    $thumbUrl  = $product->getFirstMediaUrl('product-images', 'thumb');
    $mediumUrl = $product->getFirstMediaUrl('product-images', 'medium');
    $hasMedia  = !empty($thumbUrl);
    $imgSrc    = $hasMedia ? $thumbUrl : $product->thumbnail_url;
    $finalPrice    = $product->getFinalPrice();
    $hasVariants   = $product->variants->isNotEmpty();
    $fitments      = $product->fitments->take(2);
    $extraFitments = max(0, $product->fitments->count() - 2);
    $isOnSale      = $product->is_on_sale && $product->compare_at_price > $finalPrice;
    $isOutOfStock  = $product->stock <= 0;
    $isLowStock    = $product->stock >= 1 && $product->stock <= 5;
@endphp

<article
    class="group relative bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden flex flex-col theme-card-hover transition-all duration-300"
    x-data="{ hovered: false }"
    @mouseenter="hovered = true"
    @mouseleave="hovered = false"
    role="article"
    aria-label="{{ $product->name }}"
>

    {{-- ──────────────────────────────────────────
         IMAGE AREA
    ────────────────────────────────────────── --}}
    <div class="relative aspect-square overflow-hidden bg-slate-950">

        <a href="{{ route('products.show', $product->slug) }}" tabindex="-1" aria-hidden="true">
            <picture>
                @if($hasMedia)
                    <source
                        srcset="{{ $mediumUrl }} 2x, {{ $thumbUrl }} 1x"
                        type="image/webp"
                    >
                @endif
                <img
                    src="{{ $imgSrc }}"
                    alt="{{ $product->name }}"
                    loading="lazy"
                    decoding="async"
                    class="w-full h-full object-contain p-3 group-hover:scale-105 transition-transform duration-500"
                >
            </picture>
        </a>

        {{-- Discount badge --}}
        @if($product->discount_percent > 0)
            <span class="absolute top-2 left-2 theme-badge text-[10px] font-black px-2 py-0.5 rounded-lg shadow-md z-10 pointer-events-none">
                -{{ $product->discount_percent }}%
            </span>
        @endif

        {{-- Featured badge --}}
        @if($product->is_featured)
            <span class="absolute top-2 right-2 bg-amber-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-lg shadow-md z-10 pointer-events-none">
                ★ Unggulan
            </span>
        @endif

        {{-- Out of stock badge --}}
        @if($isOutOfStock)
            <span class="absolute bottom-2 right-2 bg-slate-900/90 border border-slate-700 text-red-400 text-[10px] font-bold px-2 py-0.5 rounded-lg z-10 pointer-events-none">
                Habis
            </span>
        @elseif($isLowStock)
            {{-- Low stock badge --}}
            <span class="absolute bottom-2 left-2 bg-amber-500/15 border border-amber-500/30 text-amber-400 text-[10px] font-bold px-2 py-0.5 rounded-lg z-10 pointer-events-none">
                Sisa {{ $product->stock }}
            </span>
        @endif

        {{-- WhatsApp quick-action (hover reveal) --}}
        @unless($isOutOfStock)
            <div
                class="absolute bottom-2 right-2 transition-all duration-200 z-20"
                :class="hovered ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-2'"
            >
                <a
                    href="{{ $product->getWhatsAppLink() }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    aria-label="Tanya via WhatsApp"
                    class="w-9 h-9 bg-emerald-500 hover:bg-emerald-400 rounded-full flex items-center justify-center shadow-lg hover:scale-110 transition-transform"
                >
                    {{-- WhatsApp SVG 18px --}}
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" width="18" height="18" aria-hidden="true">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                        <path d="M12 0C5.373 0 0 5.373 0 12c0 2.124.558 4.118 1.528 5.845L.057 23.293a.75.75 0 0 0 .92.92l5.333-1.487A11.944 11.944 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.8 9.8 0 0 1-5.025-1.384l-.36-.214-3.733 1.04 1.005-3.638-.234-.374A9.818 9.818 0 1 1 12 21.818z"/>
                    </svg>
                </a>
            </div>
        @endunless

    </div>{{-- /image area --}}

    {{-- ──────────────────────────────────────────
         CARD BODY
    ────────────────────────────────────────── --}}
    <div class="p-3.5 sm:p-4 flex flex-col gap-2 flex-1">

        {{-- SKU --}}
        <p class="text-[10px] text-slate-500 font-mono truncate" title="{{ $product->sku }}">
            SKU: {{ $product->sku }}
        </p>

        {{-- Product Name --}}
        <h3 class="text-sm font-semibold text-slate-100 leading-snug line-clamp-2 group-theme-text transition-colors">
            <a href="{{ route('products.show', $product->slug) }}" class="group-theme-text">
                {{ Str::limit($product->name, 60) }}
            </a>
        </h3>

        {{-- Fitment chips --}}
        @if($product->fitments->isNotEmpty())
            <div class="flex flex-wrap gap-1">
                @foreach($fitments as $fitment)
                    <span class="inline-flex items-center text-[10px] bg-slate-800 text-slate-400 border border-slate-700 rounded-md px-1.5 py-0.5 leading-none">
                        {{ $fitment->bikeBrand?->name }} {{ $fitment->name }}
                    </span>
                @endforeach
                @if($extraFitments > 0)
                    <span class="text-[10px] text-slate-500 bg-slate-800 border border-slate-700 rounded-md px-1.5 py-0.5 leading-none">
                        +{{ $extraFitments }}
                    </span>
                @endif
            </div>
        @endif

        {{-- Price row --}}
        <div class="flex items-baseline gap-2 mt-auto pt-2">
            <span class="text-base font-bold text-slate-100">
                Rp {{ number_format($finalPrice, 0, ',', '.') }}
            </span>
            @if($isOnSale && $product->compare_at_price)
                <span class="text-xs text-slate-500 line-through">
                    Rp {{ number_format($product->compare_at_price, 0, ',', '.') }}
                </span>
            @endif
        </div>

        {{-- CTA buttons --}}
        <div class="flex flex-col sm:flex-row gap-1.5 mt-2">
            @if($hasVariants)
                {{-- Variant picker — go to product page --}}
                <a
                    href="{{ route('products.show', $product->slug) }}"
                    class="flex-1 inline-flex items-center justify-center gap-1.5 theme-btn text-xs font-bold rounded-xl px-3 py-2 transition-colors"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5" aria-hidden="true">
                        <path d="M2.5 3A1.5 1.5 0 0 0 1 4.5v4A1.5 1.5 0 0 0 2.5 10h4A1.5 1.5 0 0 0 8 8.5v-4A1.5 1.5 0 0 0 6.5 3h-4Zm9 0A1.5 1.5 0 0 0 10 4.5v4A1.5 1.5 0 0 0 11.5 10h4A1.5 1.5 0 0 0 17 8.5v-4A1.5 1.5 0 0 0 15.5 3h-4Zm-9 9A1.5 1.5 0 0 0 1 13.5v4A1.5 1.5 0 0 0 2.5 18h4A1.5 1.5 0 0 0 8 16.5v-4A1.5 1.5 0 0 0 6.5 12h-4Zm9 0A1.5 1.5 0 0 0 10 13.5v4A1.5 1.5 0 0 0 11.5 18h4A1.5 1.5 0 0 0 17 16.5v-4A1.5 1.5 0 0 0 15.5 12h-4Z"/>
                    </svg>
                    Pilih Varian
                </a>
            @else
                {{-- Direct add-to-cart --}}
                <button
                    @click="$store.cart.addToCart({{ $product->id }}, null, 1)"
                    @disabled($isOutOfStock)
                    aria-label="Beli Sekarang {{ $product->name }}"
                    class="flex-1 inline-flex items-center justify-center gap-1.5 theme-btn disabled:bg-slate-700 disabled:cursor-not-allowed text-white text-xs font-bold rounded-xl px-3 py-2 transition-colors"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5" aria-hidden="true">
                        <path d="M1 1.75A.75.75 0 0 1 1.75 1h1.628a1.75 1.75 0 0 1 1.734 1.51L5.18 3a65.25 65.25 0 0 1 13.36 1.412.75.75 0 0 1 .58.875 48.645 48.645 0 0 1-1.618 6.2.75.75 0 0 1-.712.513H6a2.5 2.5 0 0 0 0 5h8.25a.75.75 0 0 1 0 1.5H6a4 4 0 0 1-3.98-3.61L.85 3.605A.25.25 0 0 0 .604 3H1.75A.75.75 0 0 1 1 1.75ZM6 16.5a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Zm9.5 1.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z"/>
                    </svg>
                    Beli Sekarang
                </button>
            @endif

            {{-- Detail link --}}
            <a
                href="{{ route('products.show', $product->slug) }}"
                class="inline-flex items-center justify-center bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-300 text-xs font-semibold rounded-xl px-3 py-2 transition-colors"
            >
                Detail
            </a>
        </div>

    </div>{{-- /card body --}}

</article>
