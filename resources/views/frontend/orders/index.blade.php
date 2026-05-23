<x-frontend-layout>
    <div class="mt-4">
        <h1 class="text-2xl font-bold text-gray-900 mb-8">My Orders</h1>

        @if($orders->isEmpty())
            <div class="text-center py-20 bg-white border border-gray-100 rounded-3xl">
                <span class="w-16 h-16 rounded-2xl bg-gray-50 text-gray-400 flex items-center justify-center mx-auto mb-4"><i class="fa-solid fa-box text-2xl"></i></span>
                <h3 class="font-bold text-gray-900 mb-1">No orders yet</h3>
                <p class="text-sm text-gray-500 mb-6">Looks like you haven't placed any orders yet.</p>
                <a href="{{ route('products.index') }}" class="bg-primary-600 hover:bg-primary-700 text-white font-semibold text-xs px-6 py-3 rounded-full shadow-md shadow-primary-200 transition-all">Shop Now</a>
            </div>
        @else
            <div class="space-y-4">
                @foreach($orders as $order)
                    <div class="bg-white border border-gray-100 rounded-3xl p-5 hover:shadow-md transition-all flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-sm font-bold text-gray-900">{{ $order->order_number }}</span>
                                <span class="text-xs text-gray-400">•</span>
                                <span class="text-xs text-gray-500">{{ $order->created_at->format('d M Y, H:i') }}</span>
                            </div>
                            <!-- Items preview -->
                            <p class="text-xs text-gray-500 mb-3 truncate max-w-md">
                                @foreach($order->items as $item)
                                    {{ $item->product_name }}{{ !$loop->last ? ', ' : '' }}
                                @endforeach
                            </p>
                            <div class="flex items-center gap-3">
                                <!-- Order status badge -->
                                @if($order->status->value === 'pending')
                                    <span class="bg-amber-50 text-amber-700 border border-amber-200 text-[10px] font-bold px-2.5 py-0.5 rounded-full uppercase tracking-wider">Pending Payment</span>
                                @elseif($order->status->value === 'processing')
                                    <span class="bg-blue-50 text-blue-700 border border-blue-200 text-[10px] font-bold px-2.5 py-0.5 rounded-full uppercase tracking-wider">Processing</span>
                                @elseif($order->status->value === 'shipped')
                                    <span class="bg-indigo-50 text-indigo-700 border border-indigo-200 text-[10px] font-bold px-2.5 py-0.5 rounded-full uppercase tracking-wider">Shipped</span>
                                @elseif($order->status->value === 'delivered')
                                    <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-bold px-2.5 py-0.5 rounded-full uppercase tracking-wider">Delivered</span>
                                @else
                                    <span class="bg-gray-50 text-gray-700 border border-gray-200 text-[10px] font-bold px-2.5 py-0.5 rounded-full uppercase tracking-wider">Cancelled</span>
                                @endif
                            </div>
                        </div>

                        <div class="flex sm:flex-col items-start sm:items-end justify-between w-full sm:w-auto border-t sm:border-t-0 pt-3 sm:pt-0 border-gray-100">
                            <span class="text-base font-bold text-primary-600 mb-2">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
                            <a href="{{ route('orders.show', $order->order_number) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-900 hover:text-primary-600 transition-colors">
                                View Details <i class="fa-solid fa-arrow-right text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</x-frontend-layout>
