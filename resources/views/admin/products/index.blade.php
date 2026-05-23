<x-admin-layout>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Products</h1>
            <p class="text-sm text-slate-500 mt-1">Manage your product catalog</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors shadow-sm shadow-primary-900/20">
            <i class="fa-solid fa-plus"></i> Add Product
        </a>
    </div>

    {{-- Search bar --}}
    <form method="GET" action="{{ route('admin.products.index') }}" class="mb-6">
        <div class="relative max-w-sm">
            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or SKU…" class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 bg-white">
        </div>
    </form>

    <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider hidden sm:table-cell">SKU</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider hidden md:table-cell">Price</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider hidden lg:table-cell">Stock</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($products as $product)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($product->primaryImage)
                                    <img src="{{ Storage::url($product->primaryImage->image_path) }}" alt="{{ $product->name }}" class="w-10 h-10 rounded-xl object-cover border border-slate-100 flex-shrink-0">
                                @else
                                    <span class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-image text-slate-300"></i></span>
                                @endif
                                <div class="min-w-0">
                                    <p class="font-semibold text-slate-900 truncate max-w-[180px]">{{ $product->name }}</p>
                                    <p class="text-xs text-slate-400 sm:hidden">{{ $product->sku }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-slate-500 font-mono text-xs hidden sm:table-cell">{{ $product->sku }}</td>
                        <td class="px-6 py-4 font-semibold text-slate-800 hidden md:table-cell">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 hidden lg:table-cell">
                            <span class="font-semibold {{ $product->stock <= 5 ? 'text-rose-600' : 'text-slate-700' }}">{{ $product->stock }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @if($product->is_active)
                                <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 text-xs font-semibold px-2.5 py-1 rounded-full border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 bg-slate-100 text-slate-500 text-xs font-semibold px-2.5 py-1 rounded-full">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Inactive
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.products.edit', $product->id) }}" class="p-2 rounded-lg text-slate-500 hover:bg-primary-50 hover:text-primary-600 transition-colors" title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.products.destroy', $product->id) }}" x-data onsubmit="return confirm('Are you sure you want to delete this product?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 rounded-lg text-slate-500 hover:bg-rose-50 hover:text-rose-600 transition-colors" title="Delete">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center text-slate-400">
                                <i class="fa-solid fa-box-open text-4xl mb-3"></i>
                                <p class="font-medium">No products found.</p>
                                <p class="text-sm mt-1">Try adjusting your search or <a href="{{ route('admin.products.create') }}" class="text-primary-600 hover:underline">add a new product</a>.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($products->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $products->withQueryString()->links() }}
        </div>
        @endif
    </div>
</x-admin-layout>
