<x-admin-layout>
    <div class="flex items-center gap-3 mb-8">
        <a href="{{ route('admin.products.index') }}" class="p-2 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Add New Product</h1>
            <p class="text-sm text-slate-500 mt-0.5">Fill in all required fields to create a new product</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" x-data="productForm()" class="space-y-8">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Left: Main info --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Basic Info Card --}}
                <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-5">
                    <h2 class="font-semibold text-slate-800 text-base pb-2 border-b border-slate-100">Basic Information</h2>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Product Name *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 @error('name') border-rose-400 @enderror">
                        @error('name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">SKU *</label>
                            <input type="text" name="sku" value="{{ old('sku') }}" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-primary-400 @error('sku') border-rose-400 @enderror">
                            @error('sku') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Weight (grams) *</label>
                            <input type="number" name="weight" value="{{ old('weight', 500) }}" required min="0" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Sale Price (Rp) *</label>
                            <input type="number" name="price" value="{{ old('price') }}" required min="0" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 @error('price') border-rose-400 @enderror">
                            @error('price') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Compare Price (Rp)</label>
                            <input type="number" name="compare_at_price" value="{{ old('compare_at_price') }}" min="0" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Stock *</label>
                            <input type="number" name="stock" value="{{ old('stock', 0) }}" required min="0" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Short Description</label>
                        <textarea name="short_description" rows="2" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 resize-none">{{ old('short_description') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Full Description</label>
                        <textarea name="description" rows="6" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 resize-y">{{ old('description') }}</textarea>
                    </div>
                </div>

                {{-- Variants Card --}}
                <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                        <h2 class="font-semibold text-slate-800 text-base">Product Variants <span class="text-slate-400 font-normal text-sm">(optional)</span></h2>
                        <button type="button" @click="addVariant()" class="text-xs font-semibold text-primary-600 hover:text-primary-700 flex items-center gap-1">
                            <i class="fa-solid fa-plus"></i> Add Variant
                        </button>
                    </div>

                    <div class="space-y-3" id="variants-container">
                        <template x-if="variants.length === 0">
                            <p class="text-xs text-slate-400 text-center py-4">No variants added. Click "Add Variant" to add size/color options.</p>
                        </template>
                        <template x-for="(variant, index) in variants" :key="index">
                            <div class="grid grid-cols-5 gap-2 items-end bg-slate-50 p-3 rounded-xl">
                                <div class="col-span-5 sm:col-span-2">
                                    <label class="block text-xs text-slate-500 mb-1">Name *</label>
                                    <input type="text" :name="'variant_names[' + index + ']'" x-model="variant.name" placeholder="e.g. Red / XL" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-primary-400">
                                </div>
                                <div>
                                    <label class="block text-xs text-slate-500 mb-1">Price</label>
                                    <input type="number" :name="'variant_prices[' + index + ']'" x-model="variant.price" placeholder="0" min="0" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-primary-400">
                                </div>
                                <div>
                                    <label class="block text-xs text-slate-500 mb-1">Stock</label>
                                    <input type="number" :name="'variant_stocks[' + index + ']'" x-model="variant.stock" placeholder="0" min="0" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-1 focus:ring-primary-400">
                                </div>
                                <div class="flex items-end">
                                    <button type="button" @click="removeVariant(index)" class="w-full p-2 text-rose-400 hover:bg-rose-50 rounded-lg transition-colors">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Right: Images & Settings --}}
            <div class="space-y-6">
                {{-- Images Card --}}
                <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm">
                    <h2 class="font-semibold text-slate-800 text-base pb-2 border-b border-slate-100 mb-4">Images</h2>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Primary Image *</label>
                            <div class="border-2 border-dashed border-slate-200 rounded-xl p-4 text-center hover:border-primary-400 transition-colors cursor-pointer" @click="$refs.primaryImage.click()">
                                <i class="fa-solid fa-cloud-arrow-up text-slate-300 text-2xl mb-2"></i>
                                <p class="text-xs text-slate-400">Click to upload (JPG, PNG, WebP, max 2MB)</p>
                            </div>
                            <input type="file" name="primary_image" required accept="image/*" x-ref="primaryImage" class="hidden" @change="previewImage($event, 'primaryPreview')">
                            <img id="primaryPreview" src="" alt="" class="mt-3 w-full h-40 object-cover rounded-xl hidden border border-slate-100">
                            @error('primary_image') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Additional Images</label>
                            <input type="file" name="images[]" accept="image/*" multiple class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-slate-100 file:text-slate-600 file:font-semibold file:text-xs hover:file:bg-slate-200 transition-colors">
                        </div>
                    </div>
                </div>

                {{-- Categories Card --}}
                <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm">
                    <h2 class="font-semibold text-slate-800 text-base pb-2 border-b border-slate-100 mb-4">Categories *</h2>
                    <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
                        @foreach($categories as $category)
                        <label class="flex items-center gap-2.5 text-sm text-slate-700 cursor-pointer hover:text-primary-600">
                            <input type="checkbox" name="categories[]" value="{{ $category->id }}" {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }} class="rounded border-slate-300 text-primary-600 focus:ring-primary-400">
                            {{ $category->name }}
                        </label>
                        @endforeach
                    </div>
                    @error('categories') <p class="text-xs text-rose-500 mt-2">{{ $message }}</p> @enderror
                </div>

                {{-- Status Card --}}
                <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm">
                    <h2 class="font-semibold text-slate-800 text-base pb-2 border-b border-slate-100 mb-4">Settings</h2>
                    <div class="space-y-3">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <div class="relative">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-10 h-6 bg-slate-200 peer-checked:bg-primary-600 rounded-full peer transition-colors"></div>
                                <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow peer-checked:translate-x-4 transition-transform"></div>
                            </div>
                            <span class="text-sm text-slate-700 font-medium">Active (visible in store)</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <div class="relative">
                                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-10 h-6 bg-slate-200 peer-checked:bg-primary-600 rounded-full peer transition-colors"></div>
                                <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow peer-checked:translate-x-4 transition-transform"></div>
                            </div>
                            <span class="text-sm text-slate-700 font-medium">Featured product</span>
                        </label>
                    </div>
                </div>

                <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 rounded-xl transition-colors shadow-md shadow-primary-900/20 text-sm">
                    <i class="fa-solid fa-floppy-disk mr-2"></i> Save Product
                </button>
            </div>
        </div>
    </form>
</x-admin-layout>

@push('scripts')
<script>
function productForm() {
    return {
        variants: [],
        addVariant() {
            this.variants.push({ name: '', price: '', stock: 0, weight: 500 });
        },
        removeVariant(index) {
            this.variants.splice(index, 1);
        },
        previewImage(event, previewId) {
            const file = event.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = (e) => {
                const img = document.getElementById(previewId);
                img.src = e.target.result;
                img.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    }
}
</script>
@endpush
