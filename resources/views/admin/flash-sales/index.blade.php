<x-admin-layout>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Flash Sales</h1>
            <p class="text-sm text-slate-500 mt-1">Manage time-limited promotional sales</p>
        </div>
        <a href="{{ route('admin.flash-sales.create') }}" class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors shadow-sm shadow-primary-900/20">
            <i class="fa-solid fa-plus"></i> New Flash Sale
        </a>
    </div>

    <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider hidden sm:table-cell">Products</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider hidden md:table-cell">Period</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($flashSales as $sale)
                    @php
                        $now = now();
                        $isLive = $sale->is_active && $now->between($sale->start_time, $sale->end_time);
                        $isExpired = $now->gt($sale->end_time);
                    @endphp
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <p class="font-semibold text-slate-900">{{ $sale->name }}</p>
                        </td>
                        <td class="px-6 py-4 text-slate-600 hidden sm:table-cell">{{ $sale->items_count }} products</td>
                        <td class="px-6 py-4 text-xs text-slate-500 hidden md:table-cell">
                            {{ $sale->start_time->format('d M Y H:i') }} →<br>{{ $sale->end_time->format('d M Y H:i') }}
                        </td>
                        <td class="px-6 py-4">
                            @if($isLive)
                                <span class="inline-flex items-center gap-1 bg-rose-50 text-rose-700 text-xs font-bold px-2.5 py-1 rounded-full border border-rose-200 animate-pulse">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> LIVE
                                </span>
                            @elseif($isExpired)
                                <span class="inline-flex items-center gap-1 bg-slate-100 text-slate-400 text-xs font-semibold px-2.5 py-1 rounded-full">Expired</span>
                            @elseif(!$sale->is_active)
                                <span class="inline-flex items-center gap-1 bg-slate-100 text-slate-500 text-xs font-semibold px-2.5 py-1 rounded-full">Inactive</span>
                            @else
                                <span class="inline-flex items-center gap-1 bg-amber-50 text-amber-700 text-xs font-semibold px-2.5 py-1 rounded-full border border-amber-200">Scheduled</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.flash-sales.edit', $sale->id) }}" class="p-2 rounded-lg text-slate-500 hover:bg-primary-50 hover:text-primary-600 transition-colors">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.flash-sales.destroy', $sale->id) }}" onsubmit="return confirm('Delete this flash sale?')">
                                    @csrf @method('DELETE')
                                    <button class="p-2 rounded-lg text-slate-500 hover:bg-rose-50 hover:text-rose-600 transition-colors">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-6 py-16 text-center text-slate-400">
                        <i class="fa-solid fa-bolt text-4xl mb-3 block"></i>
                        <p class="font-medium">No flash sales yet.</p>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($flashSales->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">{{ $flashSales->links() }}</div>
        @endif
    </div>
</x-admin-layout>
