<x-frontend-layout>
    @push('styles')
    <script type="application/ld+json">
        {!! $productSchema !!}
    </script>
    <script type="application/ld+json">
        {!! $breadcrumb !!}
    </script>
    @endpush

    <div class="mt-6 font-sans" x-data="{ 
        qty: 1,
        selectedVariantId: null,
        selectedPrice: {{ $product->price }},
        selectedSku: '{{ $product->sku }}',
        selectedStock: {{ $product->stock }},
        variants: {{ $product->variants->toJson() }},
        init() {
            if(this.variants.length > 0) {
                this.selectVariant(this.variants[0]);
            }
        },
        selectVariant(v) {
            this.selectedVariantId = v.id;
            this.selectedPrice = v.price ? v.price : {{ $product->price }};
            this.selectedSku = v.sku;
            this.selectedStock = v.stock;
            this.qty = 1;
        },
        formatPrice(price) {
            return new Intl.NumberFormat('id-ID').format(price);
        }
    }">
        <!-- Breadcrumb UI -->
        <nav class="flex text-xs font-semibold text-slate-400 mb-8 gap-2.5 items-center">
            <a href="{{ route('home') }}" class="hover:text-primary-600 transition-colors">Home</a>
            <i class="fa-solid fa-chevron-right text-[8px] opacity-60"></i>
            <a href="{{ route('products.index', ['category' => $product->category?->slug]) }}" class="hover:text-primary-600 transition-colors">{{ $product->category?->name ?? 'Products' }}</a>
            <i class="fa-solid fa-chevron-right text-[8px] opacity-60"></i>
            <span class="text-slate-800 truncate font-bold">{{ $product->name }}</span>
        </nav>

        <!-- Product Core Details -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-16 bg-white border border-slate-100 rounded-[2rem] sm:rounded-[2.5rem] p-4 sm:p-10 shadow-premium">
            <!-- Image Gallery -->
            <div x-data="{ activeImg: '{{ $product->primaryImage?->url ?? 'https://via.placeholder.com/600' }}' }" class="space-y-4">
                <div class="overflow-hidden rounded-2xl sm:rounded-3xl border border-slate-100 aspect-square bg-slate-50 relative group shadow-inner">
                    <img :src="activeImg" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" alt="{{ $product->name }}">
                </div>
                <!-- Thumbnails -->
                @if($product->images->count() > 1)
                    <div class="flex flex-wrap gap-2.5">
                        @foreach($product->images as $img)
                            <button @click="activeImg = '{{ $img->url }}'" 
                                    class="w-14 h-14 sm:w-20 sm:h-20 overflow-hidden rounded-xl sm:rounded-2xl border-2 transition-all duration-300 hover:scale-102"
                                    :class="activeImg === '{{ $img->url }}' ? 'border-primary-500 scale-95 shadow-sm bg-primary-50/10' : 'border-slate-100 hover:border-slate-300 bg-white'">
                                <img src="{{ $img->url }}" class="w-full h-full object-cover rounded-lg sm:rounded-xl">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Product Specs / Purchase form -->
            <div class="flex flex-col justify-between mt-6 md:mt-0">
                <div>
                    <!-- Badge / Category -->
                    <div class="flex items-center gap-2 mb-4">
                        <span class="inline-block bg-primary-55 border border-primary-100/50 text-primary-700 text-[9px] sm:text-[10px] font-extrabold uppercase tracking-wider px-3 py-1 sm:px-3.5 sm:py-1.5 rounded-full shadow-sm">
                            {{ $product->category?->name ?? 'Uncategorized' }}
                        </span>
                        @if($product->compare_at_price > $product->price)
                            <span class="inline-block bg-rose-55 border border-rose-100/50 text-rose-600 text-[9px] sm:text-[10px] font-extrabold uppercase tracking-wider px-3 py-1 sm:px-3.5 sm:py-1.5 rounded-full shadow-sm">
                                Save {{ $product->discount_percent }}%
                            </span>
                        @endif
                    </div>

                    <h1 class="text-xl sm:text-4xl font-extrabold font-heading text-slate-900 tracking-tight mb-1.5 leading-tight">{{ $product->name }}</h1>
                    <p class="text-[10px] sm:text-xs text-slate-400 font-medium mb-4 sm:mb-6">SKU: <span class="text-slate-600 font-bold" x-text="selectedSku"></span></p>

                    <!-- Price -->
                    <div class="flex items-baseline gap-2.5 mb-4 sm:mb-6 pb-4 sm:pb-6 border-b border-slate-100">
                        <span class="text-primary-600 font-extrabold text-2xl sm:text-3.5xl">Rp <span x-text="formatPrice(selectedPrice)"></span></span>
                        @if($product->compare_at_price > $product->price)
                            <span class="text-slate-400 text-xs sm:text-sm line-through font-semibold">Rp {{ number_format($product->compare_at_price, 0, ',', '.') }}</span>
                        @endif
                    </div>

                    <p class="text-xs sm:text-sm text-slate-500 leading-relaxed mb-6 sm:mb-8 font-medium">{{ $product->short_description }}</p>

                    <!-- Variants Section -->
                    <template x-if="variants.length > 0">
                        <div class="mb-6 sm:mb-8">
                            <h3 class="font-extrabold text-[10px] sm:text-xs text-slate-900 uppercase tracking-wider mb-3 font-heading">Select Options</h3>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="v in variants" :key="v.id">
                                    <button @click="selectVariant(v)" 
                                            class="px-3 py-2 sm:px-4.5 sm:py-2.5 border rounded-xl sm:rounded-2xl text-[11px] sm:text-xs font-bold transition-all duration-300 hover:scale-[1.02]"
                                            :class="selectedVariantId === v.id ? 'border-primary-500 bg-primary-50 text-primary-700 font-extrabold shadow-sm ring-2 ring-primary-100' : 'border-slate-200 text-slate-600 bg-white hover:border-slate-300 hover:text-slate-800'">
                                        <span x-text="v.name"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </template>

                    <!-- Stock Status -->
                    <div class="mb-6 sm:mb-8 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full animate-pulse" :class="selectedStock > 0 ? 'bg-emerald-500' : 'bg-rose-500'"></span>
                        <span class="text-[11px] sm:text-xs font-bold" :class="selectedStock > 0 ? 'text-emerald-700' : 'text-rose-700'">
                            <span x-text="selectedStock > 0 ? 'In Stock (Available: ' + selectedStock + ')' : 'Out of Stock'"></span>
                        </span>
                    </div>
                </div>

                <!-- Action Button Block -->
                <div class="border-t border-slate-100 pt-6 sm:pt-8">
                    <div class="flex flex-row items-center gap-3">
                        <!-- Qty selector -->
                        <div class="flex items-center justify-between border border-slate-200 rounded-full overflow-hidden h-12 sm:h-14 bg-slate-50 w-28 sm:w-36 px-1.5 flex-shrink-0">
                            <button @click="qty = Math.max(1, qty - 1)" class="w-8 h-8 sm:w-10 sm:h-10 rounded-full hover:bg-white hover:shadow-sm text-slate-500 flex items-center justify-center transition-all"><i class="fa-solid fa-minus text-[10px] sm:text-xs"></i></button>
                            <span class="font-extrabold text-slate-800 text-xs sm:text-sm" x-text="qty"></span>
                            <button @click="qty = Math.min(selectedStock, qty + 1)" class="w-8 h-8 sm:w-10 sm:h-10 rounded-full hover:bg-white hover:shadow-sm text-slate-500 flex items-center justify-center transition-all"><i class="fa-solid fa-plus text-[10px] sm:text-xs"></i></button>
                        </div>

                        <!-- Add to Cart -->
                        <button @click="$store.cart.addToCart({{ $product->id }}, selectedVariantId, qty)" 
                                :disabled="selectedStock <= 0"
                                class="flex-1 h-12 sm:h-14 bg-slate-900 hover:bg-primary-600 disabled:bg-slate-100 disabled:text-slate-400 text-white font-bold text-xs sm:text-sm px-4 sm:px-8 rounded-full shadow-sm hover:shadow-glow hover:scale-[1.01] active:scale-[0.99] transition-all flex items-center justify-center gap-2">
                            <i class="fa-solid fa-basket-shopping text-[10px] sm:text-xs"></i> Add To Cart
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Description Tab -->
        <div class="mt-12 bg-white border border-slate-100 rounded-[2.5rem] p-6 sm:p-10 shadow-premium">
            <h2 class="font-extrabold font-heading text-xl text-slate-900 mb-6">Product Description</h2>
            <div class="prose prose-slate max-w-none text-slate-600 leading-relaxed text-sm font-medium">
                {!! $product->description !!}
            </div>
        </div>

        <!-- Related Products Section -->
        @if($relatedProducts->isNotEmpty())
            <div class="mt-20">
                <h2 class="font-extrabold font-heading text-2xl text-slate-900 mb-8 tracking-tight">You may also like</h2>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($relatedProducts as $rel)
                        <div class="group bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-premium hover:shadow-premium-hover hover:-translate-y-1.5 transition-all duration-300 flex flex-col justify-between relative">
                            <a href="{{ route('products.show', $rel->slug) }}" class="block relative overflow-hidden aspect-square bg-slate-50">
                                <img src="{{ $rel->primaryImage?->url ?? 'https://via.placeholder.com/300' }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="{{ $rel->name }}">
                            </a>
                            <div class="p-5 flex-1 flex flex-col justify-between">
                                <div>
                                    <h3 class="font-bold text-slate-900 text-sm mb-2 group-hover:text-primary-600 transition-colors line-clamp-1 font-heading">
                                        <a href="{{ route('products.show', $rel->slug) }}">{{ $rel->name }}</a>
                                    </h3>
                                    <p class="text-primary-600 font-extrabold text-base mb-4">Rp {{ number_format($rel->price, 0, ',', '.') }}</p>
                                </div>
                                <a href="{{ route('products.show', $rel->slug) }}" class="w-full bg-slate-900 hover:bg-primary-600 text-white font-bold text-xs py-3 rounded-full shadow-sm hover:shadow-glow transition-all duration-300 flex items-center justify-center">
                                    View Details
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-frontend-layout>
