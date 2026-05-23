<x-admin-layout>
    <!-- Stats Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Revenue Card -->
        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Total Revenue</p>
                <h3 class="text-2xl font-bold text-slate-900">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
            </div>
            <span class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg"><i class="fa-solid fa-money-bill-trend-up"></i></span>
        </div>

        <!-- Orders Card -->
        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Total Orders</p>
                <h3 class="text-2xl font-bold text-slate-900">{{ number_format($totalOrders) }}</h3>
            </div>
            <span class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg"><i class="fa-solid fa-receipt"></i></span>
        </div>

        <!-- Products Card -->
        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Active Products</p>
                <h3 class="text-2xl font-bold text-slate-900">{{ number_format($totalProducts) }}</h3>
            </div>
            <span class="w-12 h-12 rounded-2xl bg-primary-50 text-primary-600 flex items-center justify-center text-lg"><i class="fa-solid fa-box-open"></i></span>
        </div>

        <!-- Customers Card -->
        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Total Customers</p>
                <h3 class="text-2xl font-bold text-slate-900">{{ number_format($totalCustomers) }}</h3>
            </div>
            <span class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg"><i class="fa-solid fa-users"></i></span>
        </div>
    </div>

    <!-- Chart & Low Stock Alerts Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Sales Chart (Line Chart) -->
        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm lg:col-span-2">
            <h3 class="font-bold text-slate-900 text-sm mb-6 uppercase tracking-wider">Revenue Overview (Last 6 Months)</h3>
            <div class="h-64">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <!-- Low Stock Alerts -->
        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm flex flex-col justify-between">
            <div>
                <h3 class="font-bold text-slate-900 text-sm mb-6 uppercase tracking-wider text-red-600 flex items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation"></i> Low Stock Alerts
                </h3>
                @if($lowStockProducts->isEmpty())
                    <div class="text-center py-8 text-xs text-slate-400">All products have sufficient stock levels.</div>
                @else
                    <div class="space-y-4">
                        @foreach($lowStockProducts as $p)
                            <div class="flex items-center justify-between gap-3 text-xs border-b border-slate-50 pb-3 last:border-0 last:pb-0">
                                <div class="flex items-center gap-3 min-w-0">
                                    <img src="{{ $p->primaryImage?->url ?? 'https://via.placeholder.com/100' }}" class="w-10 h-10 object-cover rounded-xl border">
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-900 truncate">{{ $p->name }}</p>
                                        <p class="text-slate-400 mt-0.5">SKU: {{ $p->sku }}</p>
                                    </div>
                                </div>
                                <span class="bg-red-50 text-red-600 border border-red-100 px-2 py-0.5 rounded-lg font-bold">Qty: {{ $p->stock }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            @if($lowStockProducts->isNotEmpty())
                <a href="{{ route('admin.products.index') }}" class="w-full text-center text-xs font-semibold text-primary-600 hover:text-primary-700 mt-4 block">Manage Products</a>
            @endif
        </div>
    </div>

    <!-- Recent Orders Table -->
    <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm">
        <h3 class="font-bold text-slate-900 text-sm mb-6 uppercase tracking-wider">Recent Orders</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="text-slate-400 uppercase tracking-wider font-semibold border-b border-slate-100">
                        <th class="py-4">Order Number</th>
                        <th class="py-4">Customer</th>
                        <th class="py-4">Status</th>
                        <th class="py-4">Amount</th>
                        <th class="py-4">Date</th>
                        <th class="py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($recentOrders as $order)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-4 font-bold text-slate-900">{{ $order->order_number }}</td>
                            <td class="py-4">{{ $order->user?->name ?? 'Guest' }}</td>
                            <td class="py-4">
                                @if($order->status->value === 'pending')
                                    <span class="bg-amber-50 text-amber-700 border border-amber-200 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase">Pending</span>
                                @elseif($order->status->value === 'processing')
                                    <span class="bg-blue-50 text-blue-700 border border-blue-200 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase">Processing</span>
                                @elseif($order->status->value === 'shipped')
                                    <span class="bg-indigo-50 text-indigo-700 border border-indigo-200 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase">Shipped</span>
                                @elseif($order->status->value === 'delivered')
                                    <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase">Delivered</span>
                                @else
                                    <span class="bg-slate-50 text-slate-700 border border-slate-200 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase">Cancelled</span>
                                @endif
                            </td>
                            <td class="py-4 font-bold text-slate-900">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</td>
                            <td class="py-4 text-slate-500">{{ $order->created_at->format('d M Y, H:i') }}</td>
                            <td class="py-4 text-right">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors"><i class="fa-solid fa-eye"></i></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Chart JS CDN & Logic -->
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('salesChart').getContext('2d');
            
            const salesData = @json($salesData);
            const labels = salesData.map(item => item.month);
            const data = salesData.map(item => item.total);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels.length > 0 ? labels : ['No Data'],
                    datasets: [{
                        label: 'Monthly Revenue (Rp)',
                        data: data.length > 0 ? data : [0],
                        borderColor: '#8b5cf6',
                        backgroundColor: 'rgba(139, 92, 246, 0.1)',
                        borderWidth: 3,
                        tension: 0.3,
                        fill: true,
                        pointBackgroundColor: '#8b5cf6',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#f1f5f9'
                            },
                            ticks: {
                                font: {
                                    family: 'Outfit'
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    family: 'Outfit'
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</x-admin-layout>
