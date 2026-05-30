<x-frontend-layout>
    <div x-data="whatsappFastCheckoutPage()" class="mt-4 max-w-5xl mx-auto">

        <div class="mb-6">
            <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 text-xs text-slate-400 theme-text-hover transition-colors">
                <i class="fa-solid fa-arrow-left"></i> Kembali Belanja
            </a>
            <h1 class="mt-3 text-xl sm:text-2xl font-extrabold text-white">Checkout via WhatsApp</h1>
            <p class="text-xs text-slate-400 mt-1">
                Centang konfirmasi, lalu sistem akan membuka chat WhatsApp berisi rincian item &amp; total.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Items panel -->
            <div class="lg:col-span-8">
                <div class="bg-slate-950/70 border border-slate-800 rounded-3xl p-5 sm:p-6 shadow-premium">
                    <h2 class="text-sm font-bold text-white">Barang di Keranjang</h2>
                    <p class="text-xs text-slate-400 mt-1">Review item sebelum menghubungi WhatsApp.</p>

                    <div class="mt-4 divide-y divide-slate-800">
                        @forelse($cart->items as $item)
                            <div class="py-4 flex gap-4 items-start">
                                <a href="{{ route('products.show', $item->product->slug) }}" class="w-20 h-20 rounded-2xl border border-slate-800 bg-slate-900/40 overflow-hidden flex-shrink-0 hover:opacity-85 transition-opacity">
                                    <img
                                        src="{{ $item->product->thumbnail_url }}"
                                        alt="{{ $item->product->name }}"
                                        class="w-full h-full object-cover"
                                    >
                                </a>

                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2 sm:gap-3">
                                        <div class="min-w-0">
                                            <a href="{{ route('products.show', $item->product->slug) }}" class="theme-text-hover transition-colors">
                                                <p class="text-sm font-bold text-white truncate sm:whitespace-normal sm:line-clamp-2">{{ $item->product->name }}</p>
                                            </a>
                                            <p class="text-xs theme-text font-semibold mt-1">
                                                @if(!empty($item->variant?->name))
                                                    OEM/Part: {{ $item->variant->name }}
                                                @else
                                                    OEM/Part: -
                                                @endif
                                            </p>
                                            <p class="text-xs text-slate-400 mt-1">Qty: <span class="font-bold text-slate-300">{{ $item->quantity }}</span></p>
                                        </div>
                                        <div class="flex items-baseline sm:flex-col sm:items-end gap-1.5 sm:gap-0 mt-1 sm:mt-0">
                                            <p class="text-sm font-extrabold theme-text">Rp {{ number_format((float) $item->getTotalPrice(), 0, ',', '.') }}</p>
                                            <p class="text-[10px] text-slate-500">Subtotal item</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="py-14 text-center">
                                <div class="mx-auto w-14 h-14 rounded-3xl bg-slate-900 border border-slate-800 text-slate-500 flex items-center justify-center">
                                    <i class="fa-solid fa-basket-shopping text-lg"></i>
                                </div>
                                <p class="mt-4 text-sm font-semibold text-slate-300">Keranjang masih kosong</p>
                                <a href="{{ route('products.index') }}" class="mt-3 inline-block text-xs font-bold theme-text hover:underline">Mulai belanja</a>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="mt-6 bg-slate-950/70 border border-slate-800 rounded-3xl p-5 sm:p-6 shadow-premium">
                    <h2 class="text-sm font-bold text-white">Konfirmasi</h2>
                    <p class="text-xs text-slate-400 mt-1">Centang untuk memastikan kamu siap checkout via WhatsApp.</p>

                    <label class="mt-4 flex items-start gap-3 cursor-pointer select-none">
                        <input type="checkbox" x-model="confirmed" class="mt-1 w-5 h-5" style="accent-color: var(--c-primary);" />
                        <span class="text-xs text-slate-300">
                            Saya setuju untuk mengirim rincian pesanan ke WhatsApp tanpa proses payment dan tanpa mengisi alamat.
                        </span>
                    </label>

                    <div class="mt-5 flex items-center gap-3">
                        <button
                            x-bind:disabled="!confirmed || isLoading"
                            x-bind:class="confirmed ? 'theme-btn hover:opacity-90 text-white' : 'bg-slate-800 text-slate-500'"
                            @click="checkoutWhatsapp()"
                            class="flex-1 px-5 py-3.5 rounded-full font-bold text-sm shadow-premium transition-all duration-200 disabled:opacity-70 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                        >
                            <i class="fa-brands fa-whatsapp" aria-hidden="true"></i>
                            <span x-text="isLoading ? 'Membuka WhatsApp...' : 'WhatsApp'"></span>
                        </button>
                    </div>

                    <p class="text-[11px] text-slate-500 mt-3">
                        Sistem akan menghasilkan chat berisi item dan total. Kamu tinggal kirim pesan di WhatsApp.
                    </p>
                </div>
            </div>

            <!-- Summary panel -->
            <div class="lg:col-span-4">
                <div class="bg-slate-950/70 border border-slate-800 rounded-3xl p-5 sm:p-6 shadow-premium">
                    <h2 class="text-sm font-bold text-white">Ringkasan</h2>

                    <div class="mt-4 space-y-3 text-sm">
                        <div class="flex justify-between text-slate-400">
                            <span>Subtotal</span>
                            <span class="text-slate-200 font-bold">Rp {{ number_format((float) $cart->getSubtotal(), 0, ',', '.') }}</span>
                        </div>
                        @php
                            $discount = $cart->getDiscountAmount();
                        @endphp
                        @if($discount > 0)
                            <div class="flex justify-between text-emerald-500">
                                <span>Diskon</span>
                                <span class="font-bold">- Rp {{ number_format((float) $discount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-slate-400">
                            <span>Total</span>
                            <span class="theme-text font-extrabold">Rp {{ number_format((float) $cart->getGrandTotal(), 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="mt-5">
                        <p class="text-[11px] text-slate-500 leading-relaxed">
                            *Tanpa alamat: chat hanya memuat item &amp; total. Untuk order detail lanjutan, admin akan konfirmasi di WhatsApp.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                // no-op
            });
        </script>
        <script>
            function whatsappFastCheckoutPage() {
                return {
                    confirmed: false,
                    isLoading: false,

                    async checkoutWhatsapp() {

                        this.isLoading = true;
                        try {
                            const res = await fetch('{{ route('checkout.whatsappFastLink') }}', {
                                method: 'GET',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                                }
                            });

                            const data = await res.json();
                            if (!data.success) {
                                window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: data.message || 'Gagal membuat link WhatsApp.' } }));
                                return;
                            }

                            window.location.href = data.redirect_url;
                        } catch (e) {
                            window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: 'Terjadi kesalahan saat membuka WhatsApp.' } }));
                        } finally {
                            this.isLoading = false;
                        }
                    }
                }
            }
        </script>
    @endpush

    <script>
        // Bind Alpine state for this page
        document.addEventListener('alpine:init', () => {
            // No-op. Kept for compatibility.
        });
    </script>

</x-frontend-layout>


