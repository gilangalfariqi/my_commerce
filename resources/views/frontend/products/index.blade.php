<x-frontend-layout>
    <div class="flex flex-col lg:flex-row gap-8 mt-6" x-data="{ mobileFiltersOpen: false }">
        <!-- Sidebar Filters (Desktop) -->
        <aside class="hidden lg:block w-68 flex-shrink-0">
            <div class="bg-white border border-slate-100 rounded-[2rem] p-6 shadow-premium sticky top-28 space-y-6">
                <form action="{{ route('products.index') }}" method="GET" class="space-y-6">
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif

                    <div>
                        <h3 class="font-extrabold text-slate-900 text-xs uppercase tracking-wider mb-4 font-heading">Categories</h3>
                        <div class="space-y-1.5">
                            <a href="{{ route('products.index', ['sort' => request('sort'), 'search' => request('search')]) }}" 
                               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-300 {{ !request('category') ? 'bg-primary-50 text-primary-700 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <span>All Collections</span>
                                <i class="fa-solid fa-chevron-right text-[10px] opacity-60"></i>
                            </a>
                            @foreach($categories as $category)
                                <a href="{{ route('products.index', ['category' => $category->slug, 'sort' => request('sort'), 'search' => request('search')]) }}" 
                                   class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-300 {{ request('category') === $category->slug ? 'bg-primary-50 text-primary-700 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                    <span class="truncate pr-2">{{ $category->name }}</span>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-100/80 border border-slate-200/40 text-slate-400 group-hover:bg-white" x-text="'{{ $category->products_count }}'"></span>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <hr class="border-slate-100">

                    <div>
                        <h3 class="font-extrabold text-slate-900 text-xs uppercase tracking-wider mb-4 font-heading">Sort Order</h3>
                        <div class="relative">
                            <select name="sort" onchange="this.form.submit()" class="w-full bg-slate-50 border border-slate-200/80 rounded-2xl px-4 py-3 text-sm font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all appearance-none cursor-pointer">
                                <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Latest Arrivals</option>
                                <option value="price_low" {{ request('sort') === 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                                <option value="price_high" {{ request('sort') === 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>
                        </div>
                    </div>
                </form>
            </div>
        </aside>

        <!-- Main Product Grid Section -->
        <div class="flex-1">
            <!-- Filter Bar / Search Results Header -->
            <div class="bg-white border border-slate-100 rounded-[2rem] p-6 mb-8 shadow-premium flex flex-col md:flex-row items-center justify-between gap-5">
                <div>
                    <h2 class="font-extrabold text-xl text-slate-900 font-heading">
                        @if(request('search'))
                            Search Results for "{{ request('search') }}"
                        @elseif(request('category'))
                            Category: {{ $categories->where('slug', request('category'))->first()?->name ?? 'Products' }}
                        @else
                            Boutique Catalog
                        @endif
                    </h2>
                    <p class="text-xs text-slate-400 mt-1 font-medium">Showing {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }} of {{ $products->total() }} premium items</p>
                </div>
                
                <div class="flex items-center gap-3 w-full md:w-auto">
                    <!-- Mobile Filter Toggle Button -->
                    <button @click="mobileFiltersOpen = true" class="lg:hidden flex-1 flex items-center justify-center gap-2 bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-700 px-5 py-3 rounded-2xl text-sm font-bold transition-all shadow-sm">
                        <i class="fa-solid fa-filter text-xs"></i> Filters
                    </button>
                    
                    <!-- Search Input inside grid header -->
                    <form action="{{ route('products.index') }}" method="GET" class="flex-1 md:flex-none relative group">
                        @if(request('category'))
                            <input type="hidden" name="category" value="{{ request('category') }}">
                        @endif
                        @if(request('sort'))
                            <input type="hidden" name="sort" value="{{ request('sort') }}">
                        @endif
                        <input type="text" name="search" placeholder="Search catalog..." value="{{ request('search') }}" class="w-full md:w-56 bg-slate-50 border border-slate-200/80 rounded-2xl px-4 py-2.5 pl-10 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all shadow-inner">
                        <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        @if(request('search'))
                            <a href="{{ route('products.index', ['category' => request('category'), 'sort' => request('sort')]) }}" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-xs"></i></a>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Products Grid -->
            @if($products->isEmpty())
                <div class="text-center py-24 bg-white border border-slate-100 rounded-[2rem] shadow-premium">
                    <span class="w-16 h-16 rounded-3xl bg-slate-50 border border-slate-100 text-slate-300 flex items-center justify-center mx-auto mb-4 text-2xl"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <h3 class="font-bold text-slate-900 mb-1 font-heading">No items match your search</h3>
                    <p class="text-sm text-slate-400 font-medium">Try checking your spelling or adjusting your filter categories.</p>
                    <a href="{{ route('products.index') }}" class="mt-5 inline-flex items-center gap-2 bg-slate-950 hover:bg-primary-600 text-white text-xs font-bold px-6 py-3 rounded-full shadow-md transition-colors">Clear All Filters</a>
                </div>
            @else
                <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                    @foreach($products as $product)
                        <div class="group bg-white border border-slate-100 rounded-[1.8rem] sm:rounded-3xl overflow-hidden shadow-premium hover:shadow-premium-hover hover:-translate-y-1.5 transition-all duration-300 flex flex-col justify-between relative">
                            <a href="{{ route('products.show', $product->slug) }}" class="block relative overflow-hidden aspect-square bg-slate-50">
                                <img src="{{ $product->primaryImage?->url ?? 'https://via.placeholder.com/300' }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="{{ $product->name }}">
                                @if($product->compare_at_price > $product->price)
                                    <span class="absolute top-2.5 left-2.5 sm:top-4 sm:left-4 bg-rose-500 text-white text-[8px] sm:text-[9px] font-black uppercase tracking-wider px-2.5 py-0.5 sm:px-3 sm:py-1 rounded-full shadow-sm">
                                        Save {{ $product->discount_percent }}%
                                    </span>
                                @endif
                            </a>
                            <div class="p-3.5 sm:p-5 flex-1 flex flex-col justify-between">
                                <div>
                                    <p class="text-[8px] sm:text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1 sm:mb-1.5">{{ $product->category?->name ?? 'Uncategorized' }}</p>
                                    <h3 class="font-bold text-slate-900 text-xs sm:text-sm mb-1.5 sm:mb-2 group-hover:text-primary-600 transition-colors line-clamp-1 font-heading">
                                        <a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a>
                                    </h3>
                                    <div class="flex flex-wrap items-baseline gap-1 sm:gap-1.5 mb-3 sm:mb-4">
                                        <span class="text-primary-600 font-extrabold text-sm sm:text-base">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                        @if($product->compare_at_price > $product->price)
                                            <span class="text-slate-400 text-[10px] sm:text-xs line-through font-semibold">Rp {{ number_format($product->compare_at_price, 0, ',', '.') }}</span>
                                        @endif
                                    </div>
                                </div>

                                @if($product->variants->isNotEmpty())
                                    <a href="{{ route('products.show', $product->slug) }}" class="w-full bg-slate-50 border border-slate-200/80 hover:bg-slate-900 hover:text-white text-slate-700 font-bold text-[10px] sm:text-xs py-2.5 sm:py-3 rounded-full transition-all duration-300 flex items-center justify-center gap-1.5">
                                        Select Options
                                    </a>
                                @else
                                    <button @click="$store.cart.addToCart({{ $product->id }}, null, 1)" class="w-full bg-slate-900 hover:bg-primary-600 text-white font-bold text-[10px] sm:text-xs py-2.5 sm:py-3 rounded-full shadow-sm hover:shadow-glow transition-all duration-300 flex items-center justify-center gap-1.5">
                                        <i class="fa-solid fa-basket-shopping text-[9px] sm:text-[10px]"></i> Add To Cart
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-14 shadow-premium bg-white border border-slate-100 rounded-3xl p-4">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Mobile Filters Overlay / Drawer -->
    <div x-show="mobileFiltersOpen" x-cloak class="relative z-50 lg:hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div x-show="mobileFiltersOpen" x-transition:enter="ease-in-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in-out duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
        <div class="fixed inset-0 z-50 flex justify-end">
            <div x-show="mobileFiltersOpen" x-transition:enter="transform transition ease-in-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transform transition ease-in-out duration-300" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full" class="w-full max-w-xs bg-white h-full p-6 shadow-2xl flex flex-col justify-between overflow-y-auto border-l border-slate-100">
                <div>
                    <div class="flex items-center justify-between mb-8 pb-4 border-b border-slate-100">
                        <h3 class="font-bold text-slate-900 text-base font-heading">Filter Directory</h3>
                        <button @click="mobileFiltersOpen = false" class="text-slate-400 hover:text-slate-600 rounded-full p-1 hover:bg-slate-50"><i class="fa-solid fa-xmark text-lg"></i></button>
                    </div>

                    <form action="{{ route('products.index') }}" method="GET" class="space-y-6">
                        @if(request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif

                        <div>
                            <h4 class="font-bold text-xs uppercase tracking-wider text-slate-400 mb-4 font-heading">Collections</h4>
                            <div class="space-y-1.5">
                                <a href="{{ route('products.index', ['sort' => request('sort'), 'search' => request('search')]) }}" 
                                   class="flex items-center justify-between px-3 py-2 rounded-xl text-sm font-semibold transition-colors {{ !request('category') ? 'bg-primary-50 text-primary-700 font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
                                    <span>All Collections</span>
                                </a>
                                @foreach($categories as $category)
                                    <a href="{{ route('products.index', ['category' => $category->slug, 'sort' => request('sort'), 'search' => request('search')]) }}" 
                                       class="flex items-center justify-between px-3 py-2 rounded-xl text-sm font-semibold transition-colors {{ request('category') === $category->slug ? 'bg-primary-50 text-primary-700 font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
                                        <span>{{ $category->name }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <hr class="border-slate-100">

                        <div>
                            <h4 class="font-bold text-xs uppercase tracking-wider text-slate-400 mb-4 font-heading">Sort Order</h4>
                            <div class="relative">
                                <select name="sort" onchange="this.form.submit()" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white appearance-none cursor-pointer">
                                    <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Latest Arrivals</option>
                                    <option value="price_low" {{ request('sort') === 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                                    <option value="price_high" {{ request('sort') === 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                                </select>
                                <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs"></i>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-frontend-layout>
