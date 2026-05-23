<x-frontend-layout>
    <div class="max-w-xl mx-auto mt-8">
        <div class="bg-white border border-gray-100 rounded-3xl p-6 sm:p-8 shadow-sm">
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-2 text-center">Track Order</h1>
            <p class="text-xs text-gray-500 text-center mb-8">Enter your order number to look up status details</p>

            <form action="{{ route('orders.track') }}" method="GET" class="space-y-4 mb-8">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Order Number</label>
                    <div class="flex gap-2">
                        <input type="text" name="order_number" value="{{ request('order_number') }}" placeholder="e.g. ORD-60912A34" class="flex-1 bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-bold px-6 rounded-xl text-xs transition-colors flex items-center justify-center gap-1.5"><i class="fa-solid fa-location-crosshairs"></i> Track</button>
                    </div>
                </div>
            </form>

            @if(request('order_number') && !$order)
                <div class="text-center py-6 bg-rose-50 border border-rose-100 rounded-2xl text-rose-800 text-xs font-medium">
                    <i class="fa-solid fa-triangle-exclamation text-rose-500 text-sm mb-1.5 block"></i>
                    No order found matching "{{ request('order_number') }}".
                </div>
            @endif

            @if($order)
                <div class="border-t border-gray-100 pt-8 space-y-6 text-sm">
                    <div class="flex justify-between items-center bg-gray-50 p-4 rounded-2xl border border-gray-100">
                        <div>
                            <p class="text-xs text-gray-400">Current Status</p>
                            <p class="font-bold text-gray-900 uppercase mt-0.5 text-xs">{{ $order->status->value }}</p>
                        </div>
                        <span class="w-9 h-9 rounded-xl bg-primary-100 text-primary-700 flex items-center justify-center"><i class="fa-solid fa-box-open"></i></span>
                    </div>

                    <div class="space-y-4">
                        <div class="flex gap-4">
                            <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-[10px] flex-shrink-0 mt-0.5"><i class="fa-solid fa-check"></i></span>
                            <div>
                                <h3 class="font-semibold text-gray-900">Order Placed</h3>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $order->created_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>

                        @if($order->status->value !== 'pending' && $order->status->value !== 'cancelled')
                            <div class="flex gap-4">
                                <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-[10px] flex-shrink-0 mt-0.5"><i class="fa-solid fa-check"></i></span>
                                <div>
                                    <h3 class="font-semibold text-gray-900">Payment Confirmed</h3>
                                    <p class="text-xs text-gray-500 mt-0.5">Payment successfully processed via Midtrans.</p>
                                </div>
                            </div>
                        @endif

                        @if(in_array($order->status->value, ['shipped', 'delivered']))
                            <div class="flex gap-4">
                                <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-[10px] flex-shrink-0 mt-0.5"><i class="fa-solid fa-check"></i></span>
                                <div>
                                    <h3 class="font-semibold text-gray-900">Shipped Out</h3>
                                    <p class="text-xs text-gray-500 mt-0.5">Package handed over to {{ strtoupper($order->courier) }} ({{ $order->shipping_service }}).</p>
                                    @if($order->shipping_tracking_number)
                                        <p class="text-xs text-primary-600 font-bold mt-1.5 bg-primary-50 px-2.5 py-1.5 rounded-lg border border-primary-100 select-all w-fit">Tracking Code: {{ $order->shipping_tracking_number }}</p>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if($order->status->value === 'delivered')
                            <div class="flex gap-4">
                                <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-[10px] flex-shrink-0 mt-0.5"><i class="fa-solid fa-check"></i></span>
                                <div>
                                    <h3 class="font-semibold text-gray-900">Delivered</h3>
                                    <p class="text-xs text-gray-500 mt-0.5">Order has successfully reached destination address.</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-frontend-layout>
