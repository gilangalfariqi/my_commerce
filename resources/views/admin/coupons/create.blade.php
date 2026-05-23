<x-admin-layout>
    <div class="flex items-center gap-3 mb-8">
        <a href="{{ route('admin.coupons.index') }}" class="p-2 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors"><i class="fa-solid fa-arrow-left"></i></a>
        <h1 class="text-2xl font-bold text-slate-900">Add Coupon</h1>
    </div>

    <div class="max-w-2xl">
        <form method="POST" action="{{ route('admin.coupons.store') }}" x-data="{ type: '{{ old('type', 'percentage') }}' }" class="space-y-6">
            @csrf
            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-5">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Coupon Code *</label>
                        <input type="text" name="code" value="{{ old('code') }}" required placeholder="e.g. SAVE20" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-mono uppercase focus:outline-none focus:ring-2 focus:ring-primary-400 @error('code') border-rose-400 @enderror">
                        @error('code') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Type *</label>
                        <select name="type" x-model="type" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400">
                            <option value="percentage">Percentage (%)</option>
                            <option value="fixed">Fixed Amount (Rp)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Discount Value *</label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm font-semibold" x-text="type === 'percentage' ? '%' : 'Rp'"></span>
                            <input type="number" name="value" value="{{ old('value') }}" required min="0" class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400">
                        </div>
                    </div>
                    <div x-show="type === 'percentage'">
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Max Discount (Rp)</label>
                        <input type="number" name="max_discount" value="{{ old('max_discount') }}" min="0" placeholder="No limit" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Min. Spend (Rp)</label>
                        <input type="number" name="min_spend" value="{{ old('min_spend', 0) }}" min="0" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Usage Limit</label>
                        <input type="number" name="usage_limit" value="{{ old('usage_limit') }}" min="1" placeholder="Unlimited" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Start Date</label>
                        <input type="datetime-local" name="start_time" value="{{ old('start_time') }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">End Date</label>
                        <input type="datetime-local" name="end_time" value="{{ old('end_time') }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400">
                    </div>
                </div>

                <label class="flex items-center gap-3 cursor-pointer">
                    <div class="relative">
                        <input type="checkbox" name="is_active" value="1" checked class="sr-only peer">
                        <div class="w-10 h-6 bg-slate-200 peer-checked:bg-primary-600 rounded-full peer transition-colors"></div>
                        <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow peer-checked:translate-x-4 transition-transform"></div>
                    </div>
                    <span class="text-sm text-slate-700 font-medium">Active</span>
                </label>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 rounded-xl transition-colors text-sm"><i class="fa-solid fa-floppy-disk mr-2"></i> Save Coupon</button>
                <a href="{{ route('admin.coupons.index') }}" class="px-6 py-3 border border-slate-200 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50">Cancel</a>
            </div>
        </form>
    </div>
</x-admin-layout>
