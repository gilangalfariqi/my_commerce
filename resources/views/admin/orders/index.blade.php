<x-admin-layout>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Orders</h1>
            <p class="text-sm text-slate-500 mt-1">Manage and fulfill customer orders</p>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.orders.index') }}" class="mb-6 flex flex-wrap gap-3">
        <div class="relative flex-1 min-w-[200px]">
            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by order number…" class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 bg-white">
        </div>
        <select name="status" class="bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400">
            <option value="">All Statuses</option>
            @foreach($statuses as $status)
                <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>{{ ucfirst($status->value) }}</option>
            @endforeach
        </select>
        <button type="submit" class="bg-slate-800 text-white text-sm font-semibold px-5 py-2.5 rounded-xl hover:bg-slate-700 transition-colors">
            Filter
        </button>
    </form>

    <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Order</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider hidden sm:table-cell">Customer</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider hidden md:table-cell">Total</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider hidden lg:table-cell">Payment</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($orders as $order)
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
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <p class="font-bold text-slate-900 font-mono text-xs">{{ $order->order_number }}</p>
                            <p class="text-xs text-slate-400 mt-0.5">{{ $order->created_at->format('d M Y, H:i') }}</p>
                        </td>
                        <td class="px-6 py-4 hidden sm:table-cell">
                            <p class="font-medium text-slate-700">{{ $order->user?->name ?? 'Guest' }}</p>
                            <p class="text-xs text-slate-400">{{ $order->user?->email }}</p>
                        </td>
                        <td class="px-6 py-4 font-semibold text-slate-800 hidden md:table-cell">
                            Rp {{ number_format($order->grand_total, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full border {{ $statusClass }}">
                                {{ ucfirst($order->status->value) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 hidden lg:table-cell">
                            @if($order->payment)
                            @php
                                $payColors = ['settled' => 'text-emerald-600', 'pending' => 'text-amber-600', 'expired' => 'text-rose-600', 'failed' => 'text-rose-600'];
                                $payColor = $payColors[$order->payment->status->value] ?? 'text-slate-500';
                            @endphp
                            <span class="font-semibold text-xs {{ $payColor }}">{{ ucfirst($order->payment->status->value) }}</span>
                            @else
                            <span class="text-slate-400 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="p-2 rounded-lg text-slate-500 hover:bg-blue-50 hover:text-blue-600 transition-colors" title="View">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.orders.edit', $order->id) }}" class="p-2 rounded-lg text-slate-500 hover:bg-primary-50 hover:text-primary-600 transition-colors" title="Update Status">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center text-slate-400">
                            <i class="fa-solid fa-receipt text-4xl mb-3 block"></i>
                            <p class="font-medium">No orders found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $orders->withQueryString()->links() }}
        </div>
        @endif
    </div>
</x-admin-layout>
