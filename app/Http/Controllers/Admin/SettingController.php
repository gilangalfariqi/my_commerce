<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    /**
     * Semua setting key yang dikelola beserta default-nya.
     */
    private array $settingKeys = [
        // Identitas Toko
        'store_name'                => 'MyCommerce',
        'store_tagline'             => 'Belanja Lebih Mudah, Lebih Hemat',
        'store_email'               => '',
        'store_phone'               => '',
        'store_whatsapp'            => '',
        'store_address'             => '',

        // Navbar / Header
        'nav_logo_text'             => 'MyCommerce',
        'nav_logo_highlight'        => '',           // Kata yang diberi warna primer (opsional)
        'nav_search_placeholder'    => 'Cari produk...',
        'nav_login_text'            => 'Masuk',
        'nav_register_text'         => 'Daftar',

        // Tampilan Homepage — Hero
        'hero_badge_text'           => 'Toko Terpercaya',
        'hero_title'                => 'Temukan Produk Terbaik untuk Anda',
        'hero_subtitle'             => 'Dapatkan penawaran eksklusif setiap hari dengan kualitas terjamin.',
        'hero_cta_text'             => 'Belanja Sekarang',

        // Homepage — Kategori
        'category_section_title'    => 'Kategori Produk',
        'category_section_subtitle' => 'Temukan produk berkualitas berdasarkan kategori',
        'category_view_all_text'    => 'Lihat Semua',

        // Homepage — Garasi (Vehicle Filter)
        'garage_section_title'      => 'Cari Berdasarkan Kendaraan',
        'garage_section_subtitle'   => 'Filter produk yang 100% kompatibel dengan kendaraan Anda.',
        'garage_cta_text'           => 'Cari Produk',

        // Homepage — Produk
        'featured_section_title'    => 'Produk Unggulan',
        'featured_section_subtitle' => 'Pilihan terbaik yang sudah dipercaya ribuan pelanggan',
        'latest_section_title'      => 'Produk Terbaru',
        'latest_section_subtitle'   => 'Produk baru yang baru saja kami tambahkan',

        // Trust Badges
        'badge_1_icon'              => 'fa-shield-halved',
        'badge_1_title'             => 'Produk Original',
        'badge_1_desc'              => '100% produk resmi & bersertifikat',
        'badge_2_icon'              => 'fa-truck-fast',
        'badge_2_title'             => 'Pengiriman Cepat',
        'badge_2_desc'              => 'Estimasi 1–3 hari ke seluruh Indonesia',
        'badge_3_icon'              => 'fa-headset',
        'badge_3_title'             => 'Konsultasi Gratis',
        'badge_3_desc'              => 'Tanya via WhatsApp, respon cepat',
        'badge_4_icon'              => 'fa-rotate-left',
        'badge_4_title'             => 'Garansi Barang',
        'badge_4_desc'              => 'Garansi kualitas dan keaslian produk',

        // Warna Tema
        'color_primary'             => '#ef4444',
        'color_secondary'           => '#f97316',
        'color_accent'              => '#dc2626',
        'color_background'          => '#0b0f19',

        // Footer
        'footer_about'              => 'Toko online terpercaya dengan produk berkualitas dan harga terbaik.',
        'footer_copyright'          => '',
        'footer_nav_title'          => 'Navigasi',
        'footer_contact_title'      => 'Kontak & Bantuan',

        // Sosial Media
        'social_instagram'          => '',
        'social_facebook'           => '',
        'social_tiktok'             => '',
        'social_youtube'            => '',
        'social_twitter'            => '',

        // SEO
        'meta_description'          => 'Toko online terpercaya dengan produk berkualitas dan harga terbaik.',
        'meta_keywords'             => '',
    ];

    public function index(): View
    {
        $raw      = Setting::all()->pluck('value', 'key');
        // Gabungkan default + value dari DB
        $settings = collect($this->settingKeys)->map(
            fn($default, $key) => $raw->get($key, $default)
        );

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'store_name'        => 'required|string|max:100',
            'store_tagline'     => 'nullable|string|max:255',
            'store_email'       => 'nullable|email|max:150',
            'store_phone'       => 'nullable|string|max:30',
            'store_whatsapp'    => 'nullable|string|max:30',
            'store_address'     => 'nullable|string|max:500',
            'color_primary'     => 'nullable|string|regex:/^#[0-9a-fA-F]{6}$/',
            'color_secondary'   => 'nullable|string|regex:/^#[0-9a-fA-F]{6}$/',
            'color_accent'      => 'nullable|string|regex:/^#[0-9a-fA-F]{6}$/',
            'color_background'  => 'nullable|string|regex:/^#[0-9a-fA-F]{6}$/',
            'meta_description'  => 'nullable|string|max:300',
        ]);

        foreach (array_keys($this->settingKeys) as $key) {
            Setting::setValue($key, $request->input($key, ''));
        }

        // Flush semua cache setting sekaligus
        Cache::flush();

        return redirect()->route('admin.settings.index')
            ->with('success', 'Pengaturan toko berhasil disimpan.');
    }
}
