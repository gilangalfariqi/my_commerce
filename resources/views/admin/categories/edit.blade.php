<x-admin-layout>
    <div class="flex items-center gap-3 mb-8">
        <a href="{{ route('admin.categories.index') }}" class="p-2 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-bold text-slate-900">Edit Category: {{ $category->name }}</h1>
    </div>

    <div class="max-w-2xl">
        <form method="POST" action="{{ route('admin.categories.update', $category->id) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-5">
                @if($category->image)
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Current Image</label>
                    <img src="{{ Storage::url($category->image) }}" alt="{{ $category->name }}" class="h-24 w-24 object-cover rounded-2xl border border-slate-100">
                </div>
                @endif

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Category Name *</label>
                    <input type="text" name="name" value="{{ old('name', $category->name) }}" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 @error('name') border-rose-400 @enderror">
                    @error('name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Parent Category</label>
                    <select name="parent_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400">
                        <option value="">— None (Root Category) —</option>
                        @foreach($parentCategories as $parent)
                            <option value="{{ $parent->id }}" {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}>{{ $parent->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Description</label>
                    <textarea name="description" rows="3" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 resize-none">{{ old('description', $category->description) }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Sort Order *</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order) }}" min="0" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400">
                    </div>
                    <div class="flex items-end">
                        <label class="flex items-center gap-3 cursor-pointer pb-2.5">
                            <div class="relative">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-10 h-6 bg-slate-200 peer-checked:bg-primary-600 rounded-full peer transition-colors"></div>
                                <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow peer-checked:translate-x-4 transition-transform"></div>
                            </div>
                            <span class="text-sm text-slate-700 font-medium">Active</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Replace Image</label>
                    <input type="file" name="image" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-slate-100 file:text-slate-600 file:font-semibold hover:file:bg-slate-200 transition-colors">
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 rounded-xl transition-colors shadow-md shadow-primary-900/20 text-sm">
                    <i class="fa-solid fa-floppy-disk mr-2"></i> Update Category
                </button>
                <a href="{{ route('admin.categories.index') }}" class="px-6 py-3 border border-slate-200 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</x-admin-layout>
