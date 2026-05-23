<x-admin-layout>
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900">Store Settings</h1>
        <p class="text-sm text-slate-500 mt-1">Configure your store details and integrations</p>
    </div>

    <form method="POST" action="{{ route('admin.settings.update') }}" x-data="settingsPage()" class="space-y-8">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            {{-- Store Info --}}
            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-5">
                <h2 class="font-semibold text-slate-800 pb-2 border-b border-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-store text-primary-600 w-5 text-center"></i> Store Information
                </h2>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Store Name *</label>
                    <input type="text" name="store_name" value="{{ old('store_name', $settings->get('store_name')) }}" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Email *</label>
                    <input type="email" name="store_email" value="{{ old('store_email', $settings->get('store_email')) }}" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Phone *</label>
                    <input type="text" name="store_phone" value="{{ old('store_phone', $settings->get('store_phone')) }}" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Address *</label>
                    <textarea name="store_address" rows="3" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 resize-none">{{ old('store_address', $settings->get('store_address')) }}</textarea>
                </div>
            </div>

            <div class="space-y-6">
                {{-- Shipping Origin --}}
                <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-5">
                    <h2 class="font-semibold text-slate-800 pb-2 border-b border-slate-100 flex items-center gap-2">
                        <i class="fa-solid fa-truck text-primary-600 w-5 text-center"></i> Shipping Origin (RajaOngkir)
                    </h2>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Province *</label>
                        <select name="rajaongkir_origin_province_id" x-model="selectedProvince" @change="loadCities()" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400">
                            <option value="">Select Province…</option>
                            @foreach($provinces as $province)
                                <option value="{{ $province['province_id'] }}" {{ old('rajaongkir_origin_province_id', $settings->get('rajaongkir_origin_province_id')) == $province['province_id'] ? 'selected' : '' }}>
                                    {{ $province['province'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">City *</label>
                        <select name="rajaongkir_origin_city_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-400">
                            <option value="">Select City…</option>
                            @foreach($cities as $city)
                                <option value="{{ $city['city_id'] }}" {{ old('rajaongkir_origin_city_id', $settings->get('rajaongkir_origin_city_id')) == $city['city_id'] ? 'selected' : '' }}>
                                    {{ $city['type'] }} {{ $city['city_name'] }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-slate-400 mt-1">Select a province first to load cities.</p>
                    </div>
                </div>

                {{-- Midtrans --}}
                <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-5">
                    <h2 class="font-semibold text-slate-800 pb-2 border-b border-slate-100 flex items-center gap-2">
                        <i class="fa-solid fa-credit-card text-primary-600 w-5 text-center"></i> Midtrans Payment
                    </h2>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Merchant ID</label>
                        <input type="text" name="midtrans_merchant_id" value="{{ old('midtrans_merchant_id', $settings->get('midtrans_merchant_id')) }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-primary-400">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Client Key</label>
                        <input type="text" name="midtrans_client_key" value="{{ old('midtrans_client_key', $settings->get('midtrans_client_key')) }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-primary-400">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Server Key</label>
                        <input type="password" name="midtrans_server_key" value="{{ old('midtrans_server_key', $settings->get('midtrans_server_key')) }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-primary-400">
                    </div>

                    <label class="flex items-center gap-3 cursor-pointer">
                        <div class="relative">
                            <input type="checkbox" name="midtrans_is_production" value="1" {{ $settings->get('midtrans_is_production') === '1' ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-10 h-6 bg-slate-200 peer-checked:bg-emerald-500 rounded-full transition-colors"></div>
                            <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow peer-checked:translate-x-4 transition-transform"></div>
                        </div>
                        <span class="text-sm font-medium text-slate-700">Production Mode (uncheck for Sandbox)</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-bold px-8 py-3 rounded-xl transition-colors shadow-md shadow-primary-900/20 text-sm">
                <i class="fa-solid fa-floppy-disk mr-2"></i> Save Settings
            </button>
        </div>
    </form>
</x-admin-layout>

@push('scripts')
<script>
function settingsPage() {
    return {
        selectedProvince: '{{ $settings->get('rajaongkir_origin_province_id') }}',
        loadCities() {
            if (!this.selectedProvince) return;
            const select = document.querySelector('select[name="rajaongkir_origin_city_id"]');
            select.innerHTML = '<option value="">Loading cities…</option>';
            fetch(`/api/cities?province_id=${this.selectedProvince}`)
                .then(r => r.json())
                .then(data => {
                    select.innerHTML = '<option value="">Select City…</option>';
                    (data.cities || []).forEach(city => {
                        select.innerHTML += `<option value="${city.city_id}">${city.type} ${city.city_name}</option>`;
                    });
                })
                .catch(() => {
                    select.innerHTML = '<option value="">Failed to load cities</option>';
                });
        }
    }
}
</script>
@endpush
