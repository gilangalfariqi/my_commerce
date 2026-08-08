<x-frontend-layout>
    <div class="mt-4" x-data="orderDetailsPage('{{ $order->order_number }}')">
        <!-- Back Link -->
        <a href="{{ route('orders.index') }}" class="inline-flex items-center gap-1.5 text-xs text-gray-500 hover:text-primary-600 transition-colors mb-6">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Pesanan
        </a>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Left Panel: Invoice & Items -->
            <div class="lg:col-span-8 space-y-6">
                <!-- Order header card -->
                <div class="bg-white border border-gray-100 rounded-3xl p-6 sm:p-8">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                        <div>
                            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-1">Detail Pesanan</h1>
                            <p class="text-xs text-gray-400">Nomor Pesanan: <span class="font-bold text-gray-600">{{ $order->order_number }}</span></p>
                            <p class="text-xs text-gray-400 mt-0.5">Tanggal: {{ $order->created_at->translatedFormat('d F Y, H:i') }} WIB</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <!-- Status badge -->
                            @php
                                $statusVal = is_string($order->status) ? $order->status : $order->status->value;
                                $badges = [
                                    'pending'              => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'ordered_via_whatsapp' => 'bg-sky-50 text-sky-700 border-sky-200',
                                    'processing'           => 'bg-blue-50 text-blue-700 border-blue-200',
                                    'shipped'              => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                    'delivered'            => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'cancelled'            => 'bg-gray-50 text-gray-600 border-gray-200',
                                ];
                                $labelMap = [
                                    'pending'              => 'Menunggu Konfirmasi',
                                    'ordered_via_whatsapp' => 'Pesan via WhatsApp',
                                    'processing'           => 'Diproses',
                                    'shipped'              => 'Dikirim',
                                    'delivered'            => 'Terkirim',
                                    'cancelled'            => 'Dibatalkan',
                                ];
                                $badgeClass = $badges[$statusVal] ?? 'bg-gray-50 text-gray-700 border-gray-200';
                                $label = $labelMap[$statusVal] ?? ucfirst($statusVal);
                            @endphp
                            <span class="border text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider {{ $badgeClass }}">
                                {{ $label }}
                            </span>
                        </div>
                    </div>

                    <!-- Items List -->
                    <div class="divide-y divide-gray-100">
                        @foreach($order->items as $item)
                            <div class="flex py-4 items-center gap-4">
                                @if($item->product)
                                    <a href="{{ route('products.show', $item->product->slug) }}" class="flex-shrink-0">
                                        <img src="{{ $item->product->thumbnail_url ?? asset('images/placeholder-product.webp') }}"
                                             class="w-16 h-16 object-cover rounded-xl border border-gray-100 hover:opacity-85 transition-opacity"
                                             alt="{{ $item->product_name }}" loading="lazy">
                                    </a>
                                @else
                                    <img src="{{ asset('images/placeholder-product.webp') }}"
                                         class="w-16 h-16 object-cover rounded-xl border border-gray-100"
                                         alt="{{ $item->product_name }}" loading="lazy">
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
                                    @if($item->variant_name)
                                        <p class="text-xs text-gray-500 mt-0.5">Varian: {{ $item->variant_name }}</p>
                                    @endif
                                    <p class="text-xs text-gray-400 mt-0.5">Qty: {{ $item->quantity }}</p>
                                </div>
                                <span class="text-sm font-bold text-gray-900">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Shipping Address (jika tersimpan) -->
                @if($order->shippingAddress)
                <div class="bg-white border border-gray-100 rounded-3xl p-6 sm:p-8">
                    <h2 class="font-bold text-lg text-gray-900 mb-4 flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs">
                            <i class="fa-solid fa-location-dot"></i>
                        </span>
                        Alamat Pengiriman
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase mb-1">Penerima</p>
                            <p class="font-semibold text-gray-900">{{ $order->shippingAddress->first_name }} {{ $order->shippingAddress->last_name }}</p>
                            <p class="text-gray-500 mt-1">{{ $order->shippingAddress->phone }}</p>
                            <p class="text-gray-500">{{ $order->shippingAddress->email }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase mb-1">Alamat</p>
                            <p class="text-gray-700 leading-relaxed">{{ $order->shippingAddress->address_line }}</p>
                            <p class="text-gray-500 mt-1">{{ $order->shippingAddress->city_name }}, {{ $order->shippingAddress->province_name }}</p>
                            <p class="text-gray-500">{{ $order->shippingAddress->postal_code }}</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Right Panel: Order Summary & WhatsApp Action -->
            <div class="lg:col-span-4 space-y-6">

                <!-- WhatsApp Action Box: untuk pesanan via WA -->
                @if(in_array($statusVal, ['ordered_via_whatsapp', 'pending']))
                    <div class="bg-gradient-to-br from-emerald-500 to-teal-600 text-white rounded-3xl p-6 shadow-lg space-y-4">
                        <div class="flex items-center gap-3">
                            <span class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center text-white">
                                <i class="fa-brands fa-whatsapp text-lg"></i>
                            </span>
                            <div>
                                <h3 class="font-bold text-sm">Pesanan Dikonfirmasi!</h3>
                                <p class="text-[10px] text-white/80">Toko akan segera memproses pesanan Anda</p>
                            </div>
                        </div>
                        <p class="text-xs leading-relaxed text-white/90">
                            Pesanan Anda telah diterima melalui WhatsApp Checkout. Tim kami akan segera menghubungi Anda untuk konfirmasi pembayaran dan pengiriman.
                        </p>
                        <a href="{{ $waContactUrl ?? '#' }}"
                           target="_blank" rel="noopener noreferrer"
                           class="w-full bg-white text-emerald-700 font-bold py-3 rounded-xl hover:bg-gray-50 transition-colors text-xs flex items-center justify-center gap-2 shadow-sm">
                            <i class="fa-brands fa-whatsapp"></i> Hubungi Toko via WhatsApp
                        </a>
                    </div>
                @elseif($statusVal === 'shipped' || $statusVal === 'delivered')
                    <div class="bg-gradient-to-br from-indigo-500 to-blue-600 text-white rounded-3xl p-6 space-y-3">
                        <div class="flex items-center gap-3">
                            <span class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center text-white">
                                <i class="fa-solid fa-truck-fast"></i>
                            </span>
                            <div>
                                <h3 class="font-bold text-sm">{{ $statusVal === 'delivered' ? 'Pesanan Terkirim!' : 'Sedang Dikirim' }}</h3>
                                <p class="text-[10px] text-white/80">
                                    {{ $statusVal === 'delivered' ? 'Pesanan Anda telah sampai.' : 'Paket dalam perjalanan ke tujuan.' }}
                                </p>
                            </div>
                        </div>
                        @if($order->tracking_number)
                            <div class="bg-white/20 rounded-xl p-3">
                                <p class="text-[10px] text-white/70 uppercase font-semibold mb-1">Nomor Resi</p>
                                <p class="font-bold text-sm select-all">{{ $order->tracking_number }}</p>
                                <p class="text-[10px] text-white/70 mt-0.5">{{ strtoupper($order->courier ?? '') }}</p>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Pricing breakdown -->
                <div class="bg-white border border-gray-100 rounded-3xl p-6 space-y-4 text-sm">
                    <h3 class="font-bold text-gray-900 text-sm">Ringkasan Pembayaran</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between text-gray-500">
                            <span>Subtotal</span>
                            <span class="text-gray-900 font-semibold">Rp {{ number_format($order->total_amount ?? 0, 0, ',', '.') }}</span>
                        </div>
                        @if(($order->discount_amount ?? 0) > 0)
                            <div class="flex justify-between text-emerald-600">
                                <span>Diskon</span>
                                <span>- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        @if(($order->shipping_amount ?? 0) > 0)
                            <div class="flex justify-between text-gray-500">
                                <span>Ongkos Kirim</span>
                                <span class="text-gray-900 font-semibold">Rp {{ number_format($order->shipping_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <hr class="border-gray-100 my-2">
                        <div class="flex justify-between text-base font-bold text-gray-900">
                            <span>Total</span>
                            <span class="text-primary-600 text-lg">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Order notes -->
                @if($order->notes)
                <div class="bg-amber-50 border border-amber-100 rounded-2xl p-4 text-sm">
                    <p class="text-xs font-semibold text-amber-700 mb-1 flex items-center gap-1.5">
                        <i class="fa-solid fa-note-sticky"></i> Catatan Pesanan
                    </p>
                    <p class="text-amber-800 leading-relaxed">{{ $order->notes }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function orderDetailsPage(orderNumber) {
            return {
                orderNumber: orderNumber,
            };
        }
    </script>
    @endpush
</x-frontend-layout>
