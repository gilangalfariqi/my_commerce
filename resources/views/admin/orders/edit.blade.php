<x-admin-layout>
    <div class="flex items-center gap-3 mb-8">
        <a href="{{ route('admin.orders.show', $order->id) }}" class="p-2 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Update Order</h1>
            <p class="text-sm text-slate-500 font-mono mt-0.5">#{{ $order->order_number }}</p>
        </div>
    </div>

    <div class="max-w-lg">
        <form method="POST" action="{{ route('admin.orders.update', $order->id) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-5">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Order Status *</label>
                    <select name="status" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400">
                        @foreach($statuses as $status)
                            <option value="{{ $status->value }}" {{ old('status', $order->status->value) === $status->value ? 'selected' : '' }}>
                                {{ ucfirst($status->value) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Tracking Number</label>
                    <input type="text" name="shipping_tracking_number" value="{{ old('shipping_tracking_number', $order->shipping_tracking_number) }}" placeholder="e.g. JNE123456789ID" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-primary-400">
                    <p class="text-xs text-slate-400 mt-1">Enter the courier tracking number when marking as Shipped.</p>
                </div>

                {{-- Current order summary --}}
                <div class="bg-slate-50 rounded-xl p-4 text-sm space-y-2">
                    <p class="font-semibold text-slate-700 text-xs uppercase tracking-wider mb-2">Current Order Info</p>
                    <div class="flex justify-between text-slate-600">
                        <span>Customer</span><span class="font-medium">{{ $order->user?->name ?? 'Guest' }}</span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Grand Total</span><span class="font-bold">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Current Status</span><span class="font-semibold capitalize">{{ $order->status->value }}</span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Payment</span><span class="font-semibold capitalize {{ $order->payment?->status->value === 'settled' ? 'text-emerald-600' : 'text-amber-600' }}">{{ $order->payment?->status->value ?? '—' }}</span>
                    </div>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 rounded-xl transition-colors shadow-md shadow-primary-900/20 text-sm">
                    <i class="fa-solid fa-floppy-disk mr-2"></i> Save Changes
                </button>
                <a href="{{ route('admin.orders.show', $order->id) }}" class="px-6 py-3 border border-slate-200 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</x-admin-layout>
