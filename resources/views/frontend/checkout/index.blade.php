<x-frontend-layout>
    <div class="mt-6 font-sans" x-data="checkoutPage()">
        <h1 class="text-3xl font-extrabold font-heading text-slate-900 mb-10 tracking-tight">Secure Checkout</h1>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">
            <!-- Left Panel: Details Form -->
            <div class="lg:col-span-8 space-y-8">
                <!-- Shipping Address Card -->
                <div class="bg-white border border-slate-100 rounded-[2.5rem] p-6 sm:p-10 shadow-premium">
                    <h2 class="font-extrabold font-heading text-xl text-slate-900 mb-8 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-2xl bg-primary-50 text-primary-600 flex items-center justify-center text-sm shadow-sm"><i class="fa-solid fa-truck"></i></span>
                        Shipping Information
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-2 font-heading">First Name</label>
                            <input type="text" x-model="form.first_name" placeholder="John" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all shadow-inner">
                        </div>
                        <div>
                            <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-2 font-heading">Last Name</label>
                            <input type="text" x-model="form.last_name" placeholder="Doe" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all shadow-inner">
                        </div>
                        <div>
                            <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-2 font-heading">Email Address</label>
                            <input type="email" x-model="form.email" placeholder="john.doe@example.com" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all shadow-inner">
                        </div>
                        <div>
                            <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-2 font-heading">Phone Number</label>
                            <input type="text" x-model="form.phone" placeholder="+62 812-3456-7890" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all shadow-inner">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-2 font-heading">Address Line</label>
                            <textarea x-model="form.address_line" rows="3" placeholder="Street name, building, unit number..." class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all shadow-inner"></textarea>
                        </div>
                        
                        <!-- Province Selector -->
                        <div>
                            <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-2 font-heading">Province</label>
                            <div class="relative">
                                <select x-model="form.province_id" @change="onProvinceChange($event)" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all appearance-none cursor-pointer shadow-inner">
                                    <option value="">Select Province</option>
                                    @foreach($provinces as $prov)
                                        <option value="{{ $prov['province_id'] }}">{{ $prov['province'] }}</option>
                                    @endforeach
                                </select>
                                <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-450 pointer-events-none text-xs"></i>
                            </div>
                        </div>

                        <!-- City Selector -->
                        <div>
                            <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-2 font-heading">City</label>
                            <div class="relative">
                                <select x-model="form.city_id" @change="onCityChange($event)" :disabled="!form.province_id || loadingCities" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all appearance-none cursor-pointer disabled:opacity-50 shadow-inner">
                                    <option value="">Select City</option>
                                    <template x-for="city in cities" :key="city.city_id">
                                        <option :value="city.city_id" x-text="city.type + ' ' + city.city_name"></option>
                                    </template>
                                </select>
                                <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-450 pointer-events-none text-xs"></i>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-2 font-heading">Postal Code</label>
                            <input type="text" x-model="form.postal_code" placeholder="12345" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all shadow-inner">
                        </div>

                        <!-- Courier Selection -->
                        <div>
                            <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-2 font-heading">Courier Partner</label>
                            <div class="relative">
                                <select x-model="form.courier" @change="onCourierChange()" :disabled="!form.city_id" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all appearance-none cursor-pointer disabled:opacity-50 shadow-inner">
                                    <option value="">Select Courier</option>
                                    <option value="jne">JNE (Jalur Nugraha Ekakurir)</option>
                                    <option value="pos">POS Indonesia</option>
                                    <option value="tiki">TIKI (Titipan Kilat)</option>
                                </select>
                                <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-455 pointer-events-none text-xs"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Shipping Services Card -->
                <div x-show="form.courier && shippingServices.length > 0" x-cloak class="bg-white border border-slate-100 rounded-[2.5rem] p-6 sm:p-10 shadow-premium transition-all duration-350">
                    <h2 class="font-extrabold font-heading text-xl text-slate-900 mb-8 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm shadow-sm animate-pulse"><i class="fa-solid fa-truck-fast"></i></span>
                        Select Delivery Service
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <template x-for="service in shippingServices" :key="service.service">
                            <label class="border rounded-2xl p-5 flex items-center justify-between cursor-pointer hover:border-primary-500 hover:shadow-premium transition-all duration-350"
                                   :class="form.shipping_service === service.service ? 'border-primary-500 bg-primary-50/30 ring-2 ring-primary-100' : 'border-slate-100 bg-white'">
                                <div class="flex items-center gap-3.5">
                                    <input type="radio" name="shipping_service" :value="service.service" 
                                           @change="selectShippingService(service)" 
                                           :checked="form.shipping_service === service.service"
                                           class="text-primary-600 focus:ring-primary-500 w-4 h-4">
                                    <div>
                                        <p class="text-sm font-bold text-slate-900 font-heading" x-text="service.service"></p>
                                        <p class="text-xs text-slate-505 font-semibold" x-text="service.description"></p>
                                        <p class="text-[10px] text-slate-400 mt-1.5 font-bold tracking-wider" x-text="'ETD: ' + service.cost[0].etd + ' DAYS'"></p>
                                    </div>
                                </div>
                                <span class="text-sm font-extrabold text-primary-700 font-heading" x-text="'Rp ' + formatPrice(service.cost[0].value)"></span>
                            </label>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Right Panel: Order Summary & Payment Button -->
            <div class="lg:col-span-4">
                <div class="bg-white border border-slate-100 rounded-[2.5rem] p-6 sm:p-8 sticky top-28 space-y-6 shadow-premium">
                    <h2 class="font-extrabold font-heading text-lg text-slate-900 border-b border-slate-100 pb-4">Order Summary</h2>

                    <!-- Items List -->
                    <div class="divide-y divide-slate-100 max-h-60 overflow-y-auto pr-1">
                        @foreach($cart->items as $item)
                            <div class="flex py-3.5 items-center gap-3.5 text-xs">
                                <img src="{{ $item->product->primaryImage?->url ?? 'https://via.placeholder.com/100' }}" class="w-12 h-12 object-cover rounded-xl border border-slate-100 shadow-sm flex-shrink-0">
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-slate-900 truncate font-heading text-sm">{{ $item->product->name }}</p>
                                    <p class="text-slate-400 font-semibold mt-0.5">{{ $item->variant?->name ?? '' }} (x{{ $item->quantity }})</p>
                                </div>
                                <p class="font-bold text-slate-900">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</p>
                            </div>
                        @endforeach
                    </div>

                    <!-- Discount code box -->
                    <div class="pt-6 border-t border-slate-100 space-y-2.5">
                        <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest font-heading">Coupon Code</label>
                        <div class="flex gap-2">
                            <input type="text" x-model="couponCode" placeholder="Enter code" class="flex-1 bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-bold uppercase tracking-wider focus:outline-none focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all shadow-inner">
                            <button @click="applyCoupon()" class="bg-slate-900 hover:bg-primary-600 text-white font-bold text-xs px-5 rounded-xl transition-all shadow-sm">Apply</button>
                        </div>
                    </div>

                    <!-- Final billing breakdown -->
                    <div class="pt-6 border-t border-slate-100 space-y-3.5 text-sm">
                        <div class="flex justify-between text-slate-500 font-semibold">
                            <span>Subtotal</span>
                            <span class="text-slate-950 font-extrabold">Rp {{ number_format($cart->getSubtotal(), 0, ',', '.') }}</span>
                        </div>
                        <div x-show="discount > 0" class="flex justify-between text-emerald-600 font-semibold">
                            <span>Discount</span>
                            <span class="font-extrabold" x-text="'- Rp ' + formatPrice(discount)"></span>
                        </div>
                        <div class="flex justify-between text-slate-500 font-semibold">
                            <span>Shipping Cost</span>
                            <span class="text-slate-950 font-extrabold" x-text="form.shipping_cost > 0 ? 'Rp ' + formatPrice(form.shipping_cost) : 'Rp 0'"></span>
                        </div>
                        <hr class="border-slate-100 my-2">
                        <div class="flex justify-between text-base font-extrabold text-slate-950 font-heading">
                            <span>Total Payment</span>
                            <span class="text-primary-600 text-lg" x-text="'Rp ' + formatPrice(calculateTotal())"></span>
                        </div>
                    </div>

                    <!-- Checkout CTA -->
                    <button @click="submitOrder()" 
                            :disabled="submitting || !form.shipping_service"
                            class="w-full bg-slate-900 hover:bg-primary-600 disabled:bg-slate-100 disabled:text-slate-400 text-white font-bold py-4 rounded-full shadow-sm hover:shadow-glow hover:scale-[1.01] active:scale-[0.99] transition-all flex items-center justify-center gap-2 text-sm">
                        <i class="fa-solid fa-shield-halved text-xs"></i>
                        <span x-text="submitting ? 'Processing Order...' : 'Pay Securely Now'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function checkoutPage() {
            return {
                form: {
                    first_name: '{{ auth()->user()->name }}',
                    last_name: '',
                    email: '{{ auth()->user()->email }}',
                    phone: '',
                    address_line: '',
                    province_id: '',
                    province_name: '',
                    city_id: '',
                    city_name: '',
                    postal_code: '',
                    courier: '',
                    shipping_service: '',
                    shipping_cost: 0
                },
                cities: [],
                shippingServices: [],
                loadingCities: false,
                submitting: false,
                couponCode: '',
                discount: {{ $cart->getDiscountAmount() }},
                subtotal: {{ $cart->getSubtotal() }},

                async onProvinceChange(event) {
                    if (!this.form.province_id) {
                        this.cities = [];
                        return;
                    }
                    
                    // Capture province name
                    const select = event.target;
                    this.form.province_name = select.options[select.selectedIndex].text;

                    this.loadingCities = true;
                    this.form.city_id = '';
                    this.form.courier = '';
                    this.shippingServices = [];
                    this.form.shipping_service = '';
                    this.form.shipping_cost = 0;

                    try {
                        const response = await fetch(`/checkout/cities?province_id=${this.form.province_id}`);
                        if (response.ok) {
                            this.cities = await response.json();
                        }
                    } catch (e) {
                        console.error('Failed to load cities', e);
                    } finally {
                        this.loadingCities = false;
                    }
                },

                onCityChange(event) {
                    if (!this.form.city_id) return;
                    const select = event.target;
                    this.form.city_name = select.options[select.selectedIndex].text;
                    this.form.courier = '';
                    this.shippingServices = [];
                    this.form.shipping_service = '';
                    this.form.shipping_cost = 0;
                },

                async onCourierChange() {
                    if (!this.form.courier) {
                        this.shippingServices = [];
                        return;
                    }
                    this.form.shipping_service = '';
                    this.form.shipping_cost = 0;

                    try {
                        const response = await fetch('/checkout/shipping', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                city_id: this.form.city_id,
                                courier: this.form.courier
                            })
                        });

                        if (response.ok) {
                            this.shippingServices = await response.json();
                        }
                    } catch (e) {
                        console.error('Failed to load shipping costs', e);
                    }
                },

                selectShippingService(service) {
                    this.form.shipping_service = service.service;
                    this.form.shipping_cost = service.cost[0].value;
                },

                calculateTotal() {
                    return this.subtotal - this.discount + parseFloat(this.form.shipping_cost);
                },

                async applyCoupon() {
                    if (!this.couponCode) return;
                    try {
                        const response = await fetch('/cart/coupon', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({ code: this.couponCode })
                        });
                        const res = await response.json();
                        if (res.success) {
                            window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: res.message } }));
                            this.discount = res.discount;
                        } else {
                            window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: res.message } }));
                        }
                    } catch (e) {
                        console.error(e);
                    }
                },

                async submitOrder() {
                    this.submitting = true;
                    try {
                        const response = await fetch('/checkout', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify(this.form)
                        });

                        const res = await response.json();
                        if (res.success) {
                            // Launch Midtrans Snap Popup
                            snap.pay(res.snap_token, {
                                onSuccess: (result) => {
                                    window.location.href = res.redirect_url + '?payment=success';
                                },
                                onPending: (result) => {
                                    window.location.href = res.redirect_url + '?payment=pending';
                                },
                                onError: (result) => {
                                    window.location.href = res.redirect_url + '?payment=failed';
                                },
                                onClose: () => {
                                    window.location.href = res.redirect_url;
                                }
                            });
                        } else {
                            window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: res.message } }));
                        }
                    } catch (e) {
                        window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: 'Failed to place order.' } }));
                    } finally {
                        this.submitting = false;
                    }
                },

                formatPrice(price) {
                    return new Intl.NumberFormat('id-ID').format(price);
                }
            }
        }
    </script>
    @endpush
</x-frontend-layout>
