<x-admin-layout>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Coupons</h1>
            <p class="text-sm text-slate-500 mt-1">Manage discount codes and promotions</p>
        </div>
        <a href="{{ route('admin.coupons.create') }}" class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors shadow-sm shadow-primary-900/20">
            <i class="fa-solid fa-plus"></i> Add Coupon
        </a>
    </div>

    <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Code</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider hidden sm:table-cell">Discount</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider hidden md:table-cell">Usage</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider hidden lg:table-cell">Valid Period</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($coupons as $coupon)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <span class="font-bold font-mono text-slate-900 bg-slate-100 px-2 py-1 rounded-lg text-xs">{{ $coupon->code }}</span>
                        </td>
                        <td class="px-6 py-4 hidden sm:table-cell">
                            @if($coupon->type === 'percentage')
                                <span class="font-semibold text-emerald-600">{{ $coupon->value }}%</span> off
                            @else
                                <span class="font-semibold text-emerald-600">Rp {{ number_format($coupon->value, 0, ',', '.') }}</span> off
                            @endif
                            @if($coupon->min_spend > 0)
                                <p class="text-xs text-slate-400 mt-0.5">Min. spend: Rp {{ number_format($coupon->min_spend, 0, ',', '.') }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-600 hidden md:table-cell">
                            {{ $coupon->usage_count }} / {{ $coupon->usage_limit ?? '∞' }}
                        </td>
                        <td class="px-6 py-4 text-xs text-slate-500 hidden lg:table-cell">
                            @if($coupon->start_time || $coupon->end_time)
                                {{ $coupon->start_time?->format('d M Y') ?? '—' }} → {{ $coupon->end_time?->format('d M Y') ?? '—' }}
                            @else
                                <span class="text-slate-300 italic">No expiry</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($coupon->is_active)
                                <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 text-xs font-semibold px-2.5 py-1 rounded-full border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 bg-slate-100 text-slate-500 text-xs font-semibold px-2.5 py-1 rounded-full">
                                    Inactive
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.coupons.edit', $coupon->id) }}" class="p-2 rounded-lg text-slate-500 hover:bg-primary-50 hover:text-primary-600 transition-colors">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.coupons.destroy', $coupon->id) }}" onsubmit="return confirm('Delete coupon {{ $coupon->code }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 rounded-lg text-slate-500 hover:bg-rose-50 hover:text-rose-600 transition-colors">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center text-slate-400">
                            <i class="fa-solid fa-ticket text-4xl mb-3 block"></i>
                            <p class="font-medium">No coupons found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($coupons->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">{{ $coupons->links() }}</div>
        @endif
    </div>
</x-admin-layout>
