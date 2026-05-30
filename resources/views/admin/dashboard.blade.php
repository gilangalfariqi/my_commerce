<x-admin-layout>

    <style>
        /* ── Stat Cards ── */
        .stat-card {
            background: #fff;
            border: 1px solid rgba(226,232,240,0.8);
            border-radius: 20px;
            padding: 1.4rem 1.5rem;
            position: relative;
            overflow: hidden;
            transition: all 0.35s cubic-bezier(0.4,0,0.2,1);
            cursor: default;
        }
        .stat-card::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 20px;
            opacity: 0;
            transition: opacity 0.35s;
        }
        .stat-card:hover { transform: translateY(-4px); box-shadow: 0 16px 40px rgba(0,0,0,0.08); }
        .stat-card:hover::before { opacity: 1; }

        .stat-card-revenue::before { background: linear-gradient(135deg, rgba(16,185,129,0.03) 0%, transparent 60%); }
        .stat-card-orders::before  { background: linear-gradient(135deg, rgba(59,130,246,0.03) 0%, transparent 60%); }
        .stat-card-products::before{ background: linear-gradient(135deg, rgba(124,58,237,0.03) 0%, transparent 60%); }
        .stat-card-customers::before{ background: linear-gradient(135deg, rgba(99,102,241,0.04) 0%, transparent 60%); }

        .stat-icon {
            width: 48px; height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .stat-label {
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #94a3b8;
            margin-bottom: 0.3rem;
        }
        .stat-value {
            font-family: 'Outfit', sans-serif;
            font-size: 1.6rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.5px;
            line-height: 1.1;
        }
        .stat-sub {
            font-size: 0.72rem;
            color: #94a3b8;
            font-weight: 500;
            margin-top: 0.2rem;
        }
        .stat-trend {
            font-size: 0.72rem;
            font-weight: 700;
            padding: 0.2rem 0.5rem;
            border-radius: 99px;
            display: inline-flex;
            align-items: center;
            gap: 0.2rem;
            margin-top: 0.4rem;
        }
        .trend-up   { background: #ecfdf5; color: #059669; }
        .trend-down { background: #fff1f2; color: #e11d48; }

        /* ── Card shimmer (loading) ── */
        .stat-shimmer {
            position: absolute;
            top: -100%; left: -100%;
            width: 60%; height: 200%;
            background: linear-gradient(105deg, transparent 40%, rgba(255,255,255,0.5) 50%, transparent 60%);
            animation: shimmerPass 3s ease-in-out infinite;
        }
        @keyframes shimmerPass {
            0%   { top: -100%; left: -100%; }
            100% { top: 100%; left: 100%; }
        }

        /* ── Chart container ── */
        .chart-card {
            background: #fff;
            border: 1px solid rgba(226,232,240,0.8);
            border-radius: 20px;
            padding: 1.5rem;
            transition: box-shadow 0.3s;
        }
        .chart-card:hover { box-shadow: 0 12px 32px rgba(0,0,0,0.06); }

        .section-title {
            font-family: 'Outfit', sans-serif;
            font-size: 0.82rem;
            font-weight: 800;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: #475569;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .section-title-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        /* ── Low Stock items ── */
        .stock-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            padding: 0.65rem 0;
            border-bottom: 1px solid #f1f5f9;
            transition: all 0.2s;
        }
        .stock-item:last-child { border-bottom: none; }
        .stock-item:hover { padding-left: 4px; }

        /* ── Table ── */
        .data-table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .data-table thead tr th {
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: #94a3b8;
            padding: 0.75rem 1rem;
            border-bottom: 2px solid #f1f5f9;
            text-align: left;
            white-space: nowrap;
        }
        .data-table tbody tr {
            transition: all 0.2s;
        }
        .data-table tbody tr:hover td { background: #fafbff; }
        .data-table tbody tr:hover td:first-child { border-left: 3px solid #7c3aed; padding-left: calc(1rem - 3px); }
        .data-table tbody td {
            padding: 0.9rem 1rem;
            font-size: 0.82rem;
            border-bottom: 1px solid #f8fafc;
            transition: all 0.2s;
        }

        /* ── Status badges ── */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.68rem;
            font-weight: 700;
            padding: 0.2rem 0.6rem;
            border-radius: 99px;
            letter-spacing: 0.03em;
            text-transform: uppercase;
        }
        .badge::before {
            content: '';
            width: 4px; height: 4px;
            border-radius: 50%;
            background: currentColor;
        }
        .badge-pending    { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
        .badge-processing { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
        .badge-shipped    { background: #eef2ff; color: #4338ca; border: 1px solid #c7d2fe; }
        .badge-delivered  { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
        .badge-cancelled  { background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; }

        /* ── Action button ── */
        .action-btn {
            width: 32px; height: 32px;
            border-radius: 9px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            text-decoration: none;
            transition: all 0.2s;
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
        }
        .action-btn:hover {
            background: linear-gradient(135deg, #7c3aed, #4f46e5);
            color: #fff;
            border-color: transparent;
            box-shadow: 0 4px 10px rgba(124,58,237,0.3);
            transform: scale(1.05);
        }

        /* ── Counter animation ── */
        .counter { transition: none; }

        /* ── Empty state ── */
        .empty-state {
            text-align: center;
            padding: 2.5rem 1rem;
            color: #cbd5e1;
        }
        .empty-state i { font-size: 2rem; margin-bottom: 0.75rem; }
        .empty-state p { font-size: 0.8rem; font-weight: 500; }

        /* ── Manage link ── */
        .manage-link {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            font-size: 0.78rem;
            font-weight: 700;
            color: #7c3aed;
            text-decoration: none;
            padding: 0.4rem 0.8rem;
            border-radius: 8px;
            background: rgba(124,58,237,0.06);
            border: 1px solid rgba(124,58,237,0.12);
            transition: all 0.2s;
            margin-top: 0.75rem;
        }
        .manage-link:hover {
            background: rgba(124,58,237,0.12);
            color: #6d28d9;
        }

        /* ── Staggered card entrance ── */
        .anim-card { animation: cardEntrance 0.5s cubic-bezier(0.16,1,0.3,1) both; }
        .anim-card:nth-child(1) { animation-delay: 0.05s; }
        .anim-card:nth-child(2) { animation-delay: 0.10s; }
        .anim-card:nth-child(3) { animation-delay: 0.15s; }
        .anim-card:nth-child(4) { animation-delay: 0.20s; }
        @keyframes cardEntrance {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .section-entrance { animation: sectionEntrance 0.6s cubic-bezier(0.16,1,0.3,1) both; }
        @keyframes sectionEntrance {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── Chart gradient plugin ── */
        .chart-legend-item {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: #64748b;
        }
        .chart-legend-dot {
            width: 10px; height: 10px;
            border-radius: 50%;
        }
    </style>

    {{-- ── STAT CARDS ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-7">

        {{-- Revenue --}}
        <div class="stat-card stat-card-revenue anim-card">
            <div class="stat-shimmer"></div>
            <div class="flex items-start justify-between mb-3">
                <div>
                    <p class="stat-label">Total Revenue</p>
                    <h3 class="stat-value">
                        <span style="font-size:1rem;font-weight:600;color:#64748b;margin-right:1px;">Rp</span>
                        <span class="counter" data-target="{{ $totalRevenue }}" data-prefix="" data-suffix="">{{ number_format($totalRevenue, 0, ',', '.') }}</span>
                    </h3>
                    <div class="stat-trend trend-up">
                        <i class="fa-solid fa-arrow-trend-up"></i> Semua Waktu
                    </div>
                </div>
                <div class="stat-icon" style="background:linear-gradient(135deg,#d1fae5,#a7f3d0);color:#059669;">
                    <i class="fa-solid fa-money-bill-trend-up"></i>
                </div>
            </div>
        </div>

        {{-- Orders --}}
        <div class="stat-card stat-card-orders anim-card">
            <div class="stat-shimmer"></div>
            <div class="flex items-start justify-between mb-3">
                <div>
                    <p class="stat-label">Total Orders</p>
                    <h3 class="stat-value">
                        <span class="counter" data-target="{{ $totalOrders }}">{{ number_format($totalOrders) }}</span>
                    </h3>
                    <div class="stat-trend trend-up">
                        <i class="fa-solid fa-receipt"></i> Semua Pesanan
                    </div>
                </div>
                <div class="stat-icon" style="background:linear-gradient(135deg,#dbeafe,#bfdbfe);color:#1d4ed8;">
                    <i class="fa-solid fa-receipt"></i>
                </div>
            </div>
        </div>

        {{-- Products --}}
        <div class="stat-card stat-card-products anim-card">
            <div class="stat-shimmer"></div>
            <div class="flex items-start justify-between mb-3">
                <div>
                    <p class="stat-label">Active Products</p>
                    <h3 class="stat-value">
                        <span class="counter" data-target="{{ $totalProducts }}">{{ number_format($totalProducts) }}</span>
                    </h3>
                    <div class="stat-trend trend-up">
                        <i class="fa-solid fa-box-open"></i> Produk Aktif
                    </div>
                </div>
                <div class="stat-icon" style="background:linear-gradient(135deg,#ede9fe,#ddd6fe);color:#7c3aed;">
                    <i class="fa-solid fa-box-open"></i>
                </div>
            </div>
        </div>

        {{-- Customers --}}
        <div class="stat-card stat-card-customers anim-card">
            <div class="stat-shimmer"></div>
            <div class="flex items-start justify-between mb-3">
                <div>
                    <p class="stat-label">Total Customers</p>
                    <h3 class="stat-value">
                        <span class="counter" data-target="{{ $totalCustomers }}">{{ number_format($totalCustomers) }}</span>
                    </h3>
                    <div class="stat-trend trend-up">
                        <i class="fa-solid fa-users"></i> Pelanggan
                    </div>
                </div>
                <div class="stat-icon" style="background:linear-gradient(135deg,#eef2ff,#c7d2fe);color:#4338ca;">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- ── CHART + LOW STOCK ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-7 section-entrance" style="animation-delay:0.2s;">

        {{-- Sales Chart --}}
        <div class="chart-card lg:col-span-2">
            <div class="flex items-center justify-between mb-5">
                <div class="section-title" style="margin-bottom:0;">
                    <span class="section-title-dot" style="background:#7c3aed;"></span>
                    Revenue Overview
                </div>
                <div class="flex items-center gap-2">
                    <div class="chart-legend-item">
                        <div class="chart-legend-dot" style="background: linear-gradient(135deg,#7c3aed,#4f46e5);"></div>
                        6 Bulan Terakhir
                    </div>
                </div>
            </div>
            <div style="height: 240px; position: relative;">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        {{-- Low Stock --}}
        <div class="chart-card flex flex-col">
            <div class="section-title" style="color:#dc2626;">
                <span class="section-title-dot" style="background:#ef4444;"></span>
                <i class="fa-solid fa-triangle-exclamation" style="color:#ef4444;"></i>
                Stok Rendah
            </div>

            @if($lowStockProducts->isEmpty())
                <div class="empty-state flex-1 flex flex-col items-center justify-center">
                    <div style="width:48px;height:48px;border-radius:14px;background:#f0fdf4;color:#22c55e;display:flex;align-items:center;justify-content:center;font-size:1.2rem;margin-bottom:0.75rem;">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                    <p style="color:#94a3b8;font-size:0.8rem;font-weight:500;">Semua produk stok aman</p>
                </div>
            @else
                <div class="flex-1 overflow-y-auto" style="max-height:220px;">
                    @foreach($lowStockProducts as $p)
                        <div class="stock-item">
                            <div class="flex items-center gap-3 min-w-0">
                                <img src="{{ $p->primaryImage?->url ?? 'https://via.placeholder.com/80' }}"
                                     class="rounded-xl border border-slate-100 object-cover flex-shrink-0"
                                     style="width:38px;height:38px;">
                                <div class="min-w-0">
                                    <p style="font-size:0.8rem;font-weight:700;color:#0f172a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $p->name }}</p>
                                    <p style="font-size:0.68rem;color:#94a3b8;margin-top:1px;">SKU: {{ $p->sku }}</p>
                                </div>
                            </div>
                            <span style="font-size:0.72rem;font-weight:800;padding:0.2rem 0.55rem;border-radius:8px;background:#fff1f2;color:#be123c;border:1px solid #fecdd3;white-space:nowrap;flex-shrink:0;">
                                Qty {{ $p->stock }}
                            </span>
                        </div>
                    @endforeach
                </div>

                <div class="mt-3 text-center">
                    <a href="{{ route('admin.products.index') }}" class="manage-link">
                        <i class="fa-solid fa-arrow-right"></i> Kelola Produk
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- ── RECENT ORDERS TABLE ── --}}
    <div class="chart-card section-entrance" style="animation-delay:0.35s;">
        <div class="flex items-center justify-between mb-5">
            <div class="section-title" style="margin-bottom:0;">
                <span class="section-title-dot" style="background:#4f46e5;"></span>
                Pesanan Terbaru
            </div>
            <a href="{{ route('admin.orders.index') }}" class="manage-link">
                Lihat Semua <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No. Pesanan</th>
                        <th>Pelanggan</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Tanggal</th>
                        <th style="text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                        <tr>
                            <td>
                                <span style="font-weight:800;color:#0f172a;font-family:'Outfit',sans-serif;">
                                    {{ $order->order_number }}
                                </span>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div style="width:28px;height:28px;border-radius:8px;background:linear-gradient(135deg,#7c3aed,#4f46e5);display:flex;align-items:center;justify-content:center;font-size:0.72rem;font-weight:800;color:#fff;flex-shrink:0;">
                                        {{ strtoupper(substr($order->user?->name ?? 'G', 0, 1)) }}
                                    </div>
                                    <span style="font-weight:600;color:#334155;">{{ $order->user?->name ?? 'Guest' }}</span>
                                </div>
                            </td>
                            <td>
                                @php $status = $order->status->value; @endphp
                                @if($status === 'pending')
                                    <span class="badge badge-pending">Pending</span>
                                @elseif($status === 'processing')
                                    <span class="badge badge-processing">Processing</span>
                                @elseif($status === 'shipped')
                                    <span class="badge badge-shipped">Shipped</span>
                                @elseif($status === 'delivered')
                                    <span class="badge badge-delivered">Delivered</span>
                                @else
                                    <span class="badge badge-cancelled">Cancelled</span>
                                @endif
                            </td>
                            <td>
                                <span style="font-weight:800;color:#0f172a;font-family:'Outfit',sans-serif;">
                                    Rp {{ number_format($order->grand_total, 0, ',', '.') }}
                                </span>
                            </td>
                            <td style="color:#64748b;font-size:0.78rem;">
                                {{ $order->created_at->format('d M Y, H:i') }}
                            </td>
                            <td style="text-align:right;">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="action-btn">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <i class="fa-solid fa-inbox"></i>
                                    <p>Belum ada pesanan</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── CHART JS + COUNTER ANIMATION ── --}}
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // ── Chart.js ──
            const ctx = document.getElementById('salesChart').getContext('2d');
            const salesData = @json($salesData);
            const labels = salesData.map(item => item.month);
            const data   = salesData.map(item => item.total);

            // Gradient fill
            const gradient = ctx.createLinearGradient(0, 0, 0, 240);
            gradient.addColorStop(0, 'rgba(124,58,237,0.25)');
            gradient.addColorStop(1, 'rgba(124,58,237,0.00)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels.length > 0 ? labels : ['No Data'],
                    datasets: [{
                        label: 'Revenue (Rp)',
                        data: data.length > 0 ? data : [0],
                        borderColor: '#7c3aed',
                        backgroundColor: gradient,
                        borderWidth: 2.5,
                        tension: 0.45,
                        fill: true,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#7c3aed',
                        pointBorderWidth: 2.5,
                        pointRadius: 5,
                        pointHoverRadius: 8,
                        pointHoverBackgroundColor: '#7c3aed',
                        pointHoverBorderColor: '#fff',
                        pointHoverBorderWidth: 2.5,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: { duration: 1200, easing: 'easeInOutQuart' },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            titleColor: '#fff',
                            bodyColor: '#94a3b8',
                            padding: 12,
                            cornerRadius: 12,
                            titleFont: { family: 'Outfit', size: 13, weight: '700' },
                            bodyFont:  { family: 'Plus Jakarta Sans', size: 12 },
                            callbacks: {
                                label: ctx => ' Rp ' + ctx.raw.toLocaleString('id-ID')
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(241,245,249,0.8)', drawBorder: false },
                            ticks: {
                                font: { family: 'Plus Jakarta Sans', size: 11 },
                                color: '#94a3b8',
                                callback: v => v >= 1000000
                                    ? 'Rp ' + (v/1000000).toFixed(1) + 'M'
                                    : v >= 1000
                                        ? 'Rp ' + (v/1000).toFixed(0) + 'K'
                                        : 'Rp ' + v
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: {
                                font: { family: 'Plus Jakarta Sans', size: 11 },
                                color: '#94a3b8'
                            }
                        }
                    }
                }
            });

            // ── Counter animation ──
            function animateCounter(el) {
                const target  = parseFloat(el.getAttribute('data-target')) || 0;
                const duration = 1400;
                const start    = performance.now();
                const startVal = 0;

                function update(now) {
                    const elapsed  = now - start;
                    const progress = Math.min(elapsed / duration, 1);
                    // easeOutExpo
                    const ease = progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress);
                    const current = Math.floor(startVal + (target - startVal) * ease);
                    el.textContent = current.toLocaleString('id-ID');
                    if (progress < 1) requestAnimationFrame(update);
                }
                requestAnimationFrame(update);
            }

            // Intersection Observer for counters
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        animateCounter(entry.target);
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.5 });

            document.querySelectorAll('.counter').forEach(el => observer.observe(el));
        });
    </script>
    @endpush
</x-admin-layout>
