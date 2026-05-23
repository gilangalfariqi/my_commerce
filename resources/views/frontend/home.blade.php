<x-frontend-layout>
    <!-- Hero Slider -->
    @if($banners->isNotEmpty())
        <div class="relative overflow-hidden rounded-[2.5rem] mb-16 shadow-premium border border-slate-100/50" x-data="{ activeSlide: 0, count: {{ $banners->count() }}, timer: null, startTimer() { this.timer = setInterval(() => { this.activeSlide = (this.activeSlide + 1) % this.count; }, 6000); }, stopTimer() { clearInterval(this.timer); } }" x-init="startTimer()" @mouseenter="stopTimer()" @mouseleave="startTimer()">
            <!-- Slides -->
            <div class="relative h-[300px] sm:h-[480px] w-full overflow-hidden bg-slate-950">
                @foreach($banners as $index => $banner)
                    <div x-show="activeSlide === {{ $index }}" x-transition:enter="transition ease-out duration-700" x-transition:enter-start="opacity-0 scale-105" x-transition:enter-end="opacity-100 scale-100" class="absolute inset-0 w-full h-full">
                        <img src="{{ $banner->url }}" class="w-full h-full object-cover opacity-75 object-center" alt="{{ $banner->title }}">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-900/30 to-transparent flex flex-col justify-end p-8 sm:p-16 text-white">
                            <div class="max-w-xl">
                                <h2 class="text-2xl sm:text-5xl font-black font-heading leading-tight mb-3 tracking-tight">{{ $banner->title }}</h2>
                                <p class="text-xs sm:text-lg text-slate-200/90 font-medium mb-6 sm:mb-8 leading-relaxed">{{ $banner->subtitle }}</p>
                                @if($banner->click_url)
                                    <div>
                                        <a href="{{ $banner->click_url }}" class="inline-flex items-center gap-2.5 bg-white hover:bg-primary-600 hover:text-white text-slate-900 text-xs sm:text-sm font-bold px-7 py-3.5 rounded-full transition-all duration-300 shadow-md">
                                            Shop Collection <i class="fa-solid fa-arrow-right text-xs"></i>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Indicators -->
            @if($banners->count() > 1)
                <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex gap-2.5 z-10">
                    @foreach($banners as $index => $banner)
                        <button @click="activeSlide = {{ $index }}" class="h-2 rounded-full transition-all duration-300" :class="activeSlide === {{ $index }} ? 'bg-primary-500 w-8' : 'bg-white/50 w-2'"></button>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    <!-- Categories Grid -->
    <section class="mb-20">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight font-heading">Featured Categories</h2>
                <p class="text-xs text-slate-400 mt-1 font-medium">Explore premium items curated by category</p>
            </div>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-5">
            @foreach($categories as $category)
                <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="group bg-white border border-slate-100 rounded-3xl p-5 text-center shadow-premium hover:shadow-premium-hover hover:border-primary-100/50 transition-all duration-300">
                    <div class="w-14 h-14 rounded-2xl bg-primary-50/60 text-primary-600 flex items-center justify-center mx-auto mb-4 group-hover:scale-108 group-hover:bg-primary-600 group-hover:text-white transition-all duration-300 shadow-inner">
                        @if($category->image)
                            <img src="{{ $category->url }}" class="w-full h-full object-cover rounded-2xl">
                        @else
                            <i class="fa-solid fa-layer-group text-xl"></i>
                        @endif
                    </div>
                    <h3 class="text-sm font-bold text-slate-900 group-hover:text-primary-600 transition-colors font-heading truncate">{{ $category->name }}</h3>
                    <span class="inline-block mt-2 text-[10px] font-bold text-slate-400 bg-slate-50 border border-slate-100 rounded-full px-2.5 py-0.5" x-text="'{{ $category->products_count }} Products'"></span>
                </a>
            @endforeach
        </div>
    </section>

    <!-- Flash Sales section -->
    @if($flashSale)
        <section class="mb-20 bg-slate-950 text-white rounded-[2.5rem] p-8 sm:p-12 relative overflow-hidden shadow-premium border border-slate-900" id="flash-sale"
                 x-data="countdownTimer('{{ $flashSale->end_time->toIso8601String() }}')">
            <!-- Decorative light effect -->
            <div class="absolute top-0 right-0 w-96 h-96 bg-primary-600/10 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 mb-10 relative z-10 border-b border-slate-900 pb-8">
                <div class="flex items-center gap-4">
                    <span class="w-12 h-12 rounded-2xl bg-amber-500 text-slate-950 flex items-center justify-center text-xl font-bold shadow-lg shadow-amber-500/25 animate-pulse"><i class="fa-solid fa-bolt"></i></span>
                    <div>
                        <h2 class="text-2xl sm:text-3xl font-extrabold font-heading tracking-tight">{{ $flashSale->name }}</h2>
                        <p class="text-xs text-slate-400 mt-1 font-medium">Limited boutique collections at specialized offers. Grab yours now!</p>
                    </div>
                </div>
                <!-- Countdown -->
                <div class="flex items-center gap-3">
                    <span class="text-xs font-bold text-slate-400 tracking-wider uppercase mr-1">Time Remaining:</span>
                    <div class="flex gap-2 text-center font-bold text-sm">
                        <div class="bg-slate-900 border border-slate-800 rounded-xl px-2.5 py-2 w-12"><span class="text-amber-400 text-base" x-text="days">00</span><p class="text-[8px] text-slate-500 uppercase mt-0.5 font-bold">Days</p></div>
                        <div class="bg-slate-900 border border-slate-800 rounded-xl px-2.5 py-2 w-12"><span class="text-amber-400 text-base" x-text="hours">00</span><p class="text-[8px] text-slate-500 uppercase mt-0.5 font-bold">Hrs</p></div>
                        <div class="bg-slate-900 border border-slate-800 rounded-xl px-2.5 py-2 w-12"><span class="text-amber-400 text-base" x-text="minutes">00</span><p class="text-[8px] text-slate-500 uppercase mt-0.5 font-bold">Mins</p></div>
                        <div class="bg-slate-900 border border-slate-800 rounded-xl px-2.5 py-2 w-12"><span class="text-amber-400 text-base" x-text="seconds">00</span><p class="text-[8px] text-slate-500 uppercase mt-0.5 font-bold">Secs</p></div>
                    </div>
                </div>
            </div>

            <!-- Flash Sale items slider/grid -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 relative z-10">
                @foreach($flashSale->items as $item)
                    <div class="bg-slate-900 rounded-[1.8rem] sm:rounded-3xl border border-slate-800/80 overflow-hidden hover:border-amber-500/40 transition-all duration-300 flex flex-col justify-between group shadow-lg">
                        <div class="relative overflow-hidden aspect-square bg-slate-950">
                            <img src="{{ $item->product->primaryImage?->url ?? 'https://via.placeholder.com/300' }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-90" alt="{{ $item->product->name }}">
                            <!-- Discount Badge -->
                            <span class="absolute top-2.5 left-2.5 sm:top-4 sm:left-4 bg-amber-500 text-slate-950 text-[8px] sm:text-[10px] font-black tracking-wider uppercase px-2.5 py-0.5 sm:px-3 sm:py-1 rounded-full shadow-md">
                                {{ (int)(($item->product->price - $item->discounted_price) / $item->product->price * 100) }}% OFF
                            </span>
                        </div>
                        <div class="p-3 sm:p-5 flex-1 flex flex-col justify-between">
                            <div>
                                <h3 class="font-bold text-white text-xs sm:text-sm mb-1.5 truncate font-heading group-hover:text-amber-400 transition-colors">{{ $item->product->name }}</h3>
                                <div class="flex flex-wrap items-baseline gap-1 sm:gap-2 mb-3">
                                    <span class="text-amber-400 font-extrabold text-sm sm:text-base">Rp {{ number_format($item->discounted_price, 0, ',', '.') }}</span>
                                    <span class="text-slate-500 text-[10px] sm:text-xs line-through font-semibold">Rp {{ number_format($item->product->price, 0, ',', '.') }}</span>
                                </div>
                                <!-- Progress Sold bar -->
                                @php
                                    $percent = $item->stock_limit > 0 ? (int)(($item->stock_sold / $item->stock_limit) * 100) : 0;
                                @endphp
                                <div class="mb-4">
                                    <div class="flex justify-between text-[8px] sm:text-[9px] text-slate-400 mb-1 font-bold uppercase tracking-wider">
                                        <span>Sold {{ $item->stock_sold }}</span>
                                        <span>{{ $percent }}%</span>
                                    </div>
                                    <div class="w-full h-1 bg-slate-800 rounded-full overflow-hidden">
                                        <div class="h-full bg-gradient-to-r from-amber-500 to-orange-500 rounded-full shadow-[0_0_8px_rgba(245,158,11,0.5)]" style="width: {{ $percent }}%"></div>
                                    </div>
                                </div>
                            </div>
                            <button @click="$store.cart.addToCart({{ $item->product_id }}, null, 1)" class="w-full bg-white hover:bg-amber-500 text-slate-950 hover:text-slate-950 font-bold text-[10px] sm:text-xs py-2.5 sm:py-3 rounded-full transition-all duration-300 flex items-center justify-center gap-1.5">
                                <i class="fa-solid fa-basket-shopping text-[9px] sm:text-[10px]"></i> Add To Cart
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <!-- Featured Products Section -->
    <section class="mb-20">
        <div class="flex items-end justify-between mb-10">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight font-heading">Featured Collections</h2>
                <p class="text-xs text-slate-400 mt-1 font-medium">Handpicked premium items tailored for you</p>
            </div>
            <a href="{{ route('products.index') }}" class="text-xs font-bold text-primary-600 hover:text-primary-700 bg-primary-50 border border-primary-100 hover:border-primary-200 rounded-full px-4 py-2 flex items-center gap-1.5 transition-all">
                See All Products <i class="fa-solid fa-chevron-right text-[9px]"></i>
            </a>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            @foreach($featuredProducts as $product)
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
    </section>

    <!-- Latest Arrivals Section -->
    <section class="mb-20">
        <div class="flex items-end justify-between mb-10">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight font-heading">Latest Arrivals</h2>
                <p class="text-xs text-slate-400 mt-1 font-medium">Freshly stocked premium products</p>
            </div>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            @foreach($latestProducts as $product)
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
    </section>

    <!-- Floating WhatsApp Button -->
    <a href="https://wa.me/6281234567890" target="_blank" class="fixed bottom-24 md:bottom-8 right-6 w-14 h-14 bg-emerald-500 hover:bg-emerald-600 text-white rounded-full flex items-center justify-center shadow-premium hover:shadow-lg hover:scale-108 transition-all duration-300 z-30">
        <i class="fa-brands fa-whatsapp text-3xl"></i>
    </a>

    <!-- Countdown Helper script -->
    @push('scripts')
    <script>
        function countdownTimer(endTimeStr) {
            return {
                endTime: new Date(endTimeStr).getTime(),
                days: '00',
                hours: '00',
                minutes: '00',
                seconds: '00',
                init() {
                    this.update();
                    setInterval(() => this.update(), 1000);
                },
                update() {
                    const now = new Date().getTime();
                    const diff = this.endTime - now;

                    if (diff <= 0) {
                        this.days = '00';
                        this.hours = '00';
                        this.minutes = '00';
                        this.seconds = '00';
                        return;
                    }

                    const d = Math.floor(diff / (1000 * 60 * 60 * 24));
                    const h = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                    const s = Math.floor((diff % (1000 * 60)) / 1000);

                    this.days = d < 10 ? '0' + d : d;
                    this.hours = h < 10 ? '0' + h : h;
                    this.minutes = m < 10 ? '0' + m : m;
                    this.seconds = s < 10 ? '0' + s : s;
                }
            }
        }
    </script>
    @endpush
</x-frontend-layout>
