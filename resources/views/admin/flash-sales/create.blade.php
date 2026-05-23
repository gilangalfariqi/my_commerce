<x-admin-layout>
    <div class="flex items-center gap-3 mb-8">
        <a href="{{ route('admin.flash-sales.index') }}" class="p-2 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors"><i class="fa-solid fa-arrow-left"></i></a>
        <h1 class="text-2xl font-bold text-slate-900">New Flash Sale</h1>
    </div>

    <form method="POST" action="{{ route('admin.flash-sales.store') }}" x-data="flashSaleForm()" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="space-y-6">
                <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-5">
                    <h2 class="font-semibold text-slate-800 pb-2 border-b border-slate-100">Flash Sale Info</h2>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Name *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Mega Sale 11.11" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Start Time *</label>
                            <input type="datetime-local" name="start_time" value="{{ old('start_time') }}" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">End Time *</label>
                            <input type="datetime-local" name="end_time" value="{{ old('end_time') }}" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400">
                        </div>
                    </div>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <div class="relative">
                            <input type="checkbox" name="is_active" value="1" checked class="sr-only peer">
                            <div class="w-10 h-6 bg-slate-200 peer-checked:bg-primary-600 rounded-full transition-colors"></div>
                            <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow peer-checked:translate-x-4 transition-transform"></div>
                        </div>
                        <span class="text-sm text-slate-700 font-medium">Active</span>
                    </label>
                </div>

                <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 rounded-xl transition-colors text-sm">
                    <i class="fa-solid fa-bolt mr-2"></i> Create Flash Sale
                </button>
            </div>

            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm">
                <h2 class="font-semibold text-slate-800 pb-2 border-b border-slate-100 mb-4">Select Products & Prices</h2>
                <div class="relative mb-4">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" x-model="search" placeholder="Search products…" class="w-full pl-9 pr-4 py-2 border border-slate-200 rounded-xl text-xs bg-slate-50 focus:outline-none focus:ring-2 focus:ring-primary-400">
                </div>
                <div class="space-y-2 max-h-96 overflow-y-auto pr-1">
                    @foreach($products as $product)
                    <div x-show="search === '' || '{{ strtolower($product->name) }}'.includes(search.toLowerCase())"
                         x-data="{ selected: false }" class="border border-slate-100 rounded-xl overflow-hidden">
                        <label class="flex items-center gap-3 p-3 cursor-pointer" :class="selected ? 'bg-primary-50' : 'bg-white'">
                            <input type="checkbox" name="products[]" value="{{ $product->id }}" @change="selected = $event.target.checked" class="rounded border-slate-300 text-primary-600 focus:ring-primary-400">
                            <span class="flex-1 text-sm font-medium text-slate-700">{{ $product->name }}</span>
                            <span class="text-xs text-slate-400">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                        </label>
                        <div x-show="selected" class="px-3 pb-3 grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs text-slate-500 mb-1">Flash Price (Rp) *</label>
                                <input type="number" name="discounted_prices[{{ $product->id }}]" min="0" :required="selected" placeholder="{{ $product->price }}" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-primary-400">
                            </div>
                            <div>
                                <label class="block text-xs text-slate-500 mb-1">Stock Limit</label>
                                <input type="number" name="stock_limits[{{ $product->id }}]" min="0" placeholder="{{ $product->stock }}" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-primary-400">
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @error('products') <p class="text-xs text-rose-500 mt-2">{{ $message }}</p> @enderror
            </div>
        </div>
    </form>
</x-admin-layout>

@push('scripts')
<script>
function flashSaleForm() { return { search: '' }; }
</script>
@endpush
