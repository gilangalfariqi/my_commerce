<x-frontend-layout>
    <div class="mt-4" x-data="orderDetailsPage('{{ $order->order_number }}', '{{ $order->payment?->snap_token }}')">
        <!-- Back Link -->
        <a href="{{ route('orders.index') }}" class="inline-flex items-center gap-1.5 text-xs text-gray-500 hover:text-primary-600 transition-colors mb-6">
            <i class="fa-solid fa-arrow-left"></i> Back to Orders
        </a>

        <!-- Payment notification banners -->
        @if(request('payment') === 'success')
            <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-3xl p-4 flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-emerald-500 text-lg"></i>
                <div>
                    <p class="text-sm font-semibold">Payment Completed Successfully!</p>
                    <p class="text-xs text-emerald-600">Thank you for your purchase. We are processing your order.</p>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Left Panel: Invoice & Items -->
            <div class="lg:col-span-8 space-y-6">
                <!-- Order header card -->
                <div class="bg-white border border-gray-100 rounded-3xl p-6 sm:p-8">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                        <div>
                            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-1">Invoice details</h1>
                            <p class="text-xs text-gray-400">Order Number: <span class="font-bold text-gray-600">{{ $order->order_number }}</span></p>
                        </div>
                        <div class="flex items-center gap-2">
                            <!-- Sync Button -->
                            <button @click="syncStatus()" :disabled="syncing" class="px-3.5 py-1.5 border rounded-xl text-xs font-semibold hover:bg-gray-50 transition-colors disabled:opacity-50">
                                <i class="fa-solid fa-rotate" :class="syncing ? 'animate-spin' : ''"></i> Sync Status
                            </button>
                            
                            <!-- Status badges -->
                            @if($order->status->value === 'pending')
                                <span class="bg-amber-50 text-amber-700 border border-amber-200 text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider">Pending</span>
                            @elseif($order->status->value === 'processing')
                                <span class="bg-blue-50 text-blue-700 border border-blue-200 text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider">Processing</span>
                            @elseif($order->status->value === 'shipped')
                                <span class="bg-indigo-50 text-indigo-700 border border-indigo-200 text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider">Shipped</span>
                            @elseif($order->status->value === 'delivered')
                                <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider">Delivered</span>
                            @else
                                <span class="bg-gray-50 text-gray-700 border border-gray-200 text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider">Cancelled</span>
                            @endif
                        </div>
                    </div>

                    <!-- Items List -->
                    <div class="divide-y divide-gray-100">
                        @foreach($order->items as $item)
                            <div class="flex py-4 items-center gap-4">
                                @if($item->product)
                                    <a href="{{ route('products.show', $item->product->slug) }}" class="flex-shrink-0">
                                        <img src="{{ $item->product->primaryImage?->url ?? 'https://via.placeholder.com/150' }}" class="w-16 h-16 object-cover rounded-xl border border-gray-100 hover:opacity-85 transition-opacity">
                                    </a>
                                @else
                                    <img src="https://via.placeholder.com/150" class="w-16 h-16 object-cover rounded-xl border border-gray-100">
                                @endif
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-bold text-gray-900 text-sm truncate">
                                        @if($item->product)
                                            <a href="{{ route('products.show', $item->product->slug) }}" class="hover:text-primary-500 transition-colors">
                                                {{ $item->product_name }}
                                            </a>
                                        @else
                                            {{ $item->product_name }}
                                        @endif
                                    </h3>
                                    <p class="text-xs text-gray-500 mt-1">{{ $item->variant_name ?? '' }} (x{{ $item->quantity }})</p>
                                </div>
                                <span class="text-sm font-bold text-gray-900">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Recipient & Delivery Details -->
                <div class="bg-white border border-gray-100 rounded-3xl p-6 sm:p-8">
                    <h2 class="font-bold text-lg text-gray-900 mb-6">Delivery Address</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-sm">
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase mb-2">Recipient</p>
                            <p class="font-semibold text-gray-900">{{ $order->shippingAddress?->first_name }} {{ $order->shippingAddress?->last_name }}</p>
                            <p class="text-gray-500 mt-1">{{ $order->shippingAddress?->phone }}</p>
                            <p class="text-gray-500">{{ $order->shippingAddress?->email }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase mb-2">Street Address</p>
                            <p class="text-gray-700 leading-relaxed">{{ $order->shippingAddress?->address_line }}</p>
                            <p class="text-gray-500 mt-1">{{ $order->shippingAddress?->city_name }}, {{ $order->shippingAddress?->province_name }}</p>
                            <p class="text-gray-500">{{ $order->shippingAddress?->postal_code }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Panel: Payment Summary & Action -->
            <div class="lg:col-span-4 space-y-6">
                <!-- Payment box if pending -->
                @if($order->status->value === 'pending')
                    <div class="bg-gradient-to-br from-amber-500 to-orange-600 text-white rounded-3xl p-6 shadow-lg shadow-amber-100 space-y-4">
                        <div class="flex items-center gap-3">
                            <span class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center text-white"><i class="fa-solid fa-credit-card"></i></span>
                            <div>
                                <h3 class="font-bold text-sm">Action Required</h3>
                                <p class="text-[10px] text-white/80">Complete payment to process order</p>
                            </div>
                        </div>
                        <p class="text-xs leading-relaxed text-white/90">Please finalize your secure payment transaction using Midtrans Snap secure payment gateway.</p>
                        <button @click="payNow()" class="w-full bg-white text-gray-900 font-bold py-3 rounded-xl hover:bg-gray-50 transition-colors text-xs flex items-center justify-center gap-2 shadow-sm">
                            <i class="fa-solid fa-shield-halved text-primary-600"></i> Pay Securely Now
                        </button>
                    </div>
                @endif

                <!-- Shipping Status tracking -->
                @if($order->status->value === 'shipped' || $order->status->value === 'delivered')
                    <div class="bg-white border border-gray-100 rounded-3xl p-6 space-y-4">
                        <h3 class="font-bold text-gray-900 text-sm flex items-center gap-2">
                            <span class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-xs"><i class="fa-solid fa-truck-fast"></i></span>
                            Tracking Number
                        </h3>
                        <p class="text-xs text-gray-500 leading-relaxed">Your package was handed off to courier partner. You can track status using the tracking code below.</p>
                        <div class="bg-gray-50 rounded-xl p-3 border border-gray-100 flex items-center justify-between">
                            <span class="text-xs font-semibold text-gray-700 uppercase">{{ $order->courier }} ({{ $order->shipping_service }})</span>
                            <span class="font-bold text-sm text-primary-600 select-all">{{ $order->shipping_tracking_number ?? 'In Progress' }}</span>
                        </div>
                    </div>
                @endif

                <!-- Pricing breakdowns -->
                <div class="bg-white border border-gray-100 rounded-3xl p-6 space-y-4 text-sm">
                    <h3 class="font-bold text-gray-900 text-sm">Payment Details</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between text-gray-500">
                            <span>Subtotal</span>
                            <span class="text-gray-900 font-semibold">Rp {{ number_format($order->subtotal_amount, 0, ',', '.') }}</span>
                        </div>
                        @if($order->discount_amount > 0)
                            <div class="flex justify-between text-emerald-600">
                                <span>Discount</span>
                                <span>- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-gray-500">
                            <span>Shipping Cost</span>
                            <span class="text-gray-900 font-semibold">Rp {{ number_format($order->shipping_amount, 0, ',', '.') }}</span>
                        </div>
                        <hr class="border-gray-100 my-2">
                        <div class="flex justify-between text-base font-bold text-gray-900">
                            <span>Total</span>
                            <span class="text-primary-600 text-lg">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function orderDetailsPage(orderNumber, snapToken) {
            return {
                orderNumber: orderNumber,
                snapToken: snapToken,
                syncing: false,

                async syncStatus() {
                    this.syncing = true;
                    try {
                        const response = await fetch(`/orders/${this.orderNumber}/sync`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });
                        const res = await response.json();
                        if (res.success) {
                            window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: 'Status synchronized successfully!' } }));
                            // Refresh after 1.5 seconds to pull updated database states
                            setTimeout(() => window.location.reload(), 1500);
                        } else {
                            window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: res.message } }));
                        }
                    } catch (e) {
                        console.error(e);
                    } finally {
                        this.syncing = false;
                    }
                },

                payNow() {
                    if (!this.snapToken) return;
                    snap.pay(this.snapToken, {
                        onSuccess: (result) => {
                            window.location.href = window.location.pathname + '?payment=success';
                        },
                        onPending: (result) => {
                            window.location.href = window.location.pathname + '?payment=pending';
                        },
                        onError: (result) => {
                            window.location.href = window.location.pathname + '?payment=failed';
                        },
                        onClose: () => {
                            window.location.reload();
                        }
                    });
                }
            }
        }
    </script>
    @endpush
</x-frontend-layout>
