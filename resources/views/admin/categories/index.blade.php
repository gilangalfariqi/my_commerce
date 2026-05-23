<x-admin-layout>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Categories</h1>
            <p class="text-sm text-slate-500 mt-1">Manage product categories and subcategories</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors shadow-sm shadow-primary-900/20">
            <i class="fa-solid fa-plus"></i> Add Category
        </a>
    </div>

    <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider hidden sm:table-cell">Parent</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider hidden md:table-cell">Sort</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($categories as $category)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($category->image)
                                    <img src="{{ Storage::url($category->image) }}" alt="{{ $category->name }}" class="w-10 h-10 rounded-xl object-cover border border-slate-100 flex-shrink-0">
                                @else
                                    <span class="w-10 h-10 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center flex-shrink-0 text-lg">
                                        <i class="fa-solid fa-tag"></i>
                                    </span>
                                @endif
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $category->name }}</p>
                                    <p class="text-xs text-slate-400 font-mono">{{ $category->slug }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-slate-500 hidden sm:table-cell">
                            @if($category->parent)
                                {{ $category->parent->name }}
                            @else
                                <span class="text-slate-300 italic">Root</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-500 hidden md:table-cell">{{ $category->sort_order }}</td>
                        <td class="px-6 py-4">
                            @if($category->is_active)
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
                                <a href="{{ route('admin.categories.edit', $category->id) }}" class="p-2 rounded-lg text-slate-500 hover:bg-primary-50 hover:text-primary-600 transition-colors">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.categories.destroy', $category->id) }}" onsubmit="return confirm('Delete this category?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 rounded-lg text-slate-500 hover:bg-rose-50 hover:text-rose-600 transition-colors">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center text-slate-400">
                            <i class="fa-solid fa-tags text-4xl mb-3 block"></i>
                            <p class="font-medium">No categories found.</p>
                            <a href="{{ route('admin.categories.create') }}" class="text-primary-600 hover:underline text-sm mt-1 inline-block">Create your first category</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($categories->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $categories->links() }}
        </div>
        @endif
    </div>
</x-admin-layout>
