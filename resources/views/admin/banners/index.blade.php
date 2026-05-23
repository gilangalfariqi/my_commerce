<x-admin-layout>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Slider Banners</h1>
            <p class="text-sm text-slate-500 mt-1">Manage homepage hero banners</p>
        </div>
        <a href="{{ route('admin.banners.create') }}" class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors shadow-sm shadow-primary-900/20">
            <i class="fa-solid fa-plus"></i> Add Banner
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($banners as $banner)
        <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm">
            <div class="relative">
                <img src="{{ Storage::url($banner->image_path) }}" alt="{{ $banner->title }}" class="w-full h-40 object-cover">
                <div class="absolute top-3 right-3 flex gap-2">
                    <span class="text-xs font-bold bg-slate-900/70 text-white px-2 py-1 rounded-lg">Sort: {{ $banner->sort_order }}</span>
                    @if($banner->is_active)
                        <span class="text-xs font-bold bg-emerald-500 text-white px-2 py-1 rounded-lg">Active</span>
                    @else
                        <span class="text-xs font-bold bg-slate-500 text-white px-2 py-1 rounded-lg">Inactive</span>
                    @endif
                </div>
            </div>
            <div class="p-5">
                <h3 class="font-bold text-slate-900 mb-0.5">{{ $banner->title }}</h3>
                @if($banner->subtitle)
                <p class="text-xs text-slate-500 mb-3">{{ $banner->subtitle }}</p>
                @endif
                @if($banner->click_url)
                <p class="text-xs text-primary-600 truncate mb-3">→ {{ $banner->click_url }}</p>
                @endif
                <div class="flex items-center gap-2 pt-3 border-t border-slate-100">
                    <a href="{{ route('admin.banners.edit', $banner->id) }}" class="flex-1 flex items-center justify-center gap-1.5 py-2 text-xs font-semibold text-primary-600 border border-primary-200 rounded-xl hover:bg-primary-50 transition-colors">
                        <i class="fa-solid fa-pen-to-square"></i> Edit
                    </a>
                    <form method="POST" action="{{ route('admin.banners.destroy', $banner->id) }}" onsubmit="return confirm('Delete this banner?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-2 rounded-xl text-rose-400 hover:bg-rose-50 border border-rose-100 transition-colors">
                            <i class="fa-solid fa-trash text-xs"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-3 py-16 text-center text-slate-400 bg-white border border-slate-100 rounded-3xl">
            <i class="fa-solid fa-image text-4xl mb-3 block"></i>
            <p class="font-medium">No banners yet.</p>
            <a href="{{ route('admin.banners.create') }}" class="text-primary-600 hover:underline text-sm mt-1 inline-block">Add your first banner</a>
        </div>
        @endforelse
    </div>

    @if($banners->hasPages())
    <div class="mt-6">{{ $banners->links() }}</div>
    @endif
</x-admin-layout>
