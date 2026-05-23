<x-admin-layout>
    <div class="flex items-center gap-3 mb-8">
        <a href="{{ route('admin.orders.index') }}" class="p-2 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Order Details</h1>
            <p class="text-sm text-slate-500 font-mono mt-0.5">#{{ $order->order_number }}</p>
        </div>
        <div class="ml-auto">
            <a href="{{ route('admin.orders.edit', $order->id) }}" class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors">
                <i class="fa-solid fa-pen-to-square"></i> Update Status
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Left: Items & Timeline --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Order Items --}}
            <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h2 class="font-semibold text-slate-800">Order Items</h2>
                </div>
                <div class="divide-y divide-slate-50">
                    @foreach($order->items as $item)
                    <div class="flex items-center gap-4 px-6 py-4">
                        @if($item->product?->primaryImage)
                            <img src="{{ Storage::url($item->product->primaryImage->image_path) }}" alt="{{ $item->product_name }}" class="w-14 h-14 rounded-2xl object-cover border border-slate-100 flex-shrink-0">
                        @else
                            <span class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-image text-slate-300 text-lg"></i></span>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-slate-900 text-sm">{{ $item->product_name }}</p>
                            @if($item->variant_name)
                                <p class="text-xs text-slate-500 mt-0.5">Variant: {{ $item->variant_name }}</p>
                            @endif
                            <p class="text-xs text-slate-400 mt-0.5">Rp {{ number_format($item->price, 0, ',', '.') }} × {{ $item->quantity }}</p>
                        </div>
                        <p class="font-bold text-slate-800 flex-shrink-0">Rp {{ number_format($item->total, 0, ',', '.') }}</p>
                    </div>
                    @endforeach
                </div>

                {{-- Totals --}}
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 space-y-2">
                    <div class="flex justify-between text-sm text-slate-500">
                        <span>Subtotal</span>
                        <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                    </div>
                    @if($order->discount_amount > 0)
                    <div class="flex justify-between text-sm text-emerald-600">
                        <span>Discount</span>
                        <span>- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between text-sm text-slate-500">
                        <span>Shipping ({{ strtoupper($order->courier ?? '—') }})</span>
                        <span>Rp {{ number_format($order->shipping_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-base font-bold text-slate-900 pt-2 border-t border-slate-200">
                        <span>Grand Total</span>
                        <span>Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            {{-- Shipping Address --}}
            @if($order->shippingAddress)
            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm">
                <h2 class="font-semibold text-slate-800 mb-4 pb-2 border-b border-slate-100">Shipping Address</h2>
                <div class="text-sm text-slate-600 space-y-1">
                    <p class="font-semibold text-slate-900">{{ $order->shippingAddress->first_name }} {{ $order->shippingAddress->last_name }}</p>
                    <p>{{ $order->shippingAddress->phone }}</p>
                    <p>{{ $order->shippingAddress->address_line }}</p>
                    <p>{{ $order->shippingAddress->city_name }}, {{ $order->shippingAddress->province_name }} {{ $order->shippingAddress->postal_code }}</p>
                </div>
            </div>
            @endif
        </div>

        {{-- Right: Metadata --}}
        <div class="space-y-6">
            {{-- Status Card --}}
            @php
                $statusColors = [
                    'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                    'processing' => 'bg-blue-50 text-blue-700 border-blue-200',
                    'shipped' => 'bg-violet-50 text-violet-700 border-violet-200',
                    'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                    'cancelled' => 'bg-rose-50 text-rose-700 border-rose-200',
                ];
                $statusClass = $statusColors[$order->status->value] ?? 'bg-slate-100 text-slate-600';
            @endphp
            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm">
                <h2 class="font-semibold text-slate-800 mb-4 pb-2 border-b border-slate-100">Order Status</h2>
                <span class="inline-flex items-center text-sm font-bold px-4 py-2 rounded-xl border {{ $statusClass }}">
                    {{ ucfirst($order->status->value) }}
                </span>
                <p class="text-xs text-slate-400 mt-3">Placed: {{ $order->created_at->format('d M Y, H:i') }}</p>
            </div>

            {{-- Payment Info --}}
            @if($order->payment)
            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm">
                <h2 class="font-semibold text-slate-800 mb-4 pb-2 border-b border-slate-100">Payment Info</h2>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Method</span>
                        <span class="font-medium text-slate-800 capitalize">{{ $order->payment->payment_type ?: '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Status</span>
                        <span class="font-bold capitalize {{ $order->payment->status->value === 'settled' ? 'text-emerald-600' : 'text-amber-600' }}">{{ $order->payment->status->value }}</span>
                    </div>
                    @if($order->payment->transaction_id)
                    <div class="flex justify-between">
                        <span class="text-slate-500">Transaction ID</span>
                        <span class="font-mono text-xs text-slate-600">{{ $order->payment->transaction_id }}</span>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Tracking --}}
            @if($order->shipping_tracking_number)
            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm">
                <h2 class="font-semibold text-slate-800 mb-4 pb-2 border-b border-slate-100">Tracking</h2>
                <p class="text-xs text-slate-500 mb-1">Courier: <strong class="text-slate-700 uppercase">{{ $order->courier }}</strong></p>
                <p class="font-mono font-bold text-slate-800">{{ $order->shipping_tracking_number }}</p>
            </div>
            @endif

            {{-- Customer --}}
            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm">
                <h2 class="font-semibold text-slate-800 mb-4 pb-2 border-b border-slate-100">Customer</h2>
                @if($order->user)
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center font-bold text-base flex-shrink-0">{{ substr($order->user->name, 0, 1) }}</span>
                    <div>
                        <p class="font-semibold text-slate-800 text-sm">{{ $order->user->name }}</p>
                        <p class="text-xs text-slate-400">{{ $order->user->email }}</p>
                    </div>
                </div>
                @else
                <p class="text-slate-400 text-sm italic">Guest order</p>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>
