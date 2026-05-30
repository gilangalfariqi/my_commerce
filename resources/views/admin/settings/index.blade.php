<x-admin-layout>
<style>
    /* ── Tab Nav ── */
    .tab-nav { display: flex; gap: 0.25rem; background: #f1f5f9; border-radius: 14px; padding: 4px; }
    .tab-btn {
        flex: 1; padding: 0.55rem 1rem; border-radius: 10px; font-size: 0.78rem;
        font-weight: 700; color: #64748b; border: none; background: transparent;
        cursor: pointer; transition: all 0.2s; white-space: nowrap;
        display: flex; align-items: center; justify-content: center; gap: 0.4rem;
    }
    .tab-btn.active { background: #fff; color: #0f172a; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
    .tab-btn:hover:not(.active) { background: rgba(255,255,255,0.6); color: #334155; }

    /* ── Settings card ── */
    .settings-card {
        background: #fff; border: 1px solid rgba(226,232,240,0.8);
        border-radius: 20px; padding: 1.5rem; margin-bottom: 1.25rem;
    }
    .settings-card-title {
        font-family: 'Outfit', sans-serif; font-size: 0.8rem; font-weight: 800;
        text-transform: uppercase; letter-spacing: 0.07em; color: #475569;
        margin-bottom: 1.2rem; display: flex; align-items: center; gap: 0.5rem;
    }
    .settings-card-title .dot {
        width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0;
    }

    /* ── Form fields ── */
    .field-group { margin-bottom: 1rem; }
    .field-label {
        display: block; font-size: 0.72rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.06em;
        color: #64748b; margin-bottom: 0.4rem;
    }
    .field-input {
        width: 100%; padding: 0.65rem 0.9rem;
        background: #f8fafc; border: 1px solid #e2e8f0;
        border-radius: 12px; font-size: 0.85rem; color: #0f172a;
        font-family: 'Plus Jakarta Sans', sans-serif;
        outline: none; transition: all 0.2s;
    }
    .field-input:focus {
        border-color: #a78bfa; background: #fff;
        box-shadow: 0 0 0 3px rgba(124,58,237,0.1);
    }
    textarea.field-input { resize: vertical; min-height: 80px; }
    .field-hint { font-size: 0.7rem; color: #94a3b8; margin-top: 0.3rem; }

    /* ── Color picker ── */
    .color-field { display: flex; align-items: center; gap: 0.75rem; }
    .color-input-wrap {
        position: relative; width: 44px; height: 44px;
        border-radius: 12px; overflow: hidden; flex-shrink: 0;
        border: 2px solid #e2e8f0; cursor: pointer;
        transition: border-color 0.2s;
    }
    .color-input-wrap:hover { border-color: #7c3aed; }
    .color-input-wrap input[type="color"] {
        position: absolute; inset: -4px; width: calc(100% + 8px);
        height: calc(100% + 8px); cursor: pointer; border: none;
        padding: 0; opacity: 1;
    }
    .color-hex {
        flex: 1; padding: 0.6rem 0.85rem;
        background: #f8fafc; border: 1px solid #e2e8f0;
        border-radius: 10px; font-size: 0.82rem; font-family: 'Courier New', monospace;
        font-weight: 600; color: #0f172a; outline: none;
        transition: all 0.2s; text-transform: uppercase;
    }
    .color-hex:focus { border-color: #a78bfa; box-shadow: 0 0 0 3px rgba(124,58,237,0.1); }

    /* ── Preview box ── */
    .color-preview-bar {
        height: 10px; border-radius: 99px;
        background: linear-gradient(90deg, var(--c-primary, #ef4444), var(--c-secondary, #f97316));
        transition: background 0.4s;
        margin-top: 0.75rem;
    }

    /* ── Save button ── */
    .save-btn {
        display: inline-flex; align-items: center; gap: 0.5rem;
        background: linear-gradient(135deg, #7c3aed, #4f46e5);
        color: #fff; font-weight: 700; font-size: 0.88rem;
        padding: 0.75rem 2rem; border-radius: 14px; border: none;
        cursor: pointer; transition: all 0.25s;
        box-shadow: 0 6px 20px rgba(124,58,237,0.35);
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .save-btn:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(124,58,237,0.45); }
    .save-btn:active { transform: none; }

    /* ── 2-col grid helper ── */
    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 0.9rem; }
    @media (max-width: 640px) { .grid-2 { grid-template-columns: 1fr; } }

    /* ── Tab panels ── */
    .tab-panel { display: none; }
    .tab-panel.active { display: block; animation: fadeUp 0.3s ease; }
    @keyframes fadeUp {
        from { opacity:0; transform:translateY(8px); }
        to   { opacity:1; transform:translateY(0); }
    }

    /* ── Homepage preview ── */
    .preview-wrap {
        background: #0b0f19; border-radius: 16px; overflow: hidden;
        border: 1px solid rgba(255,255,255,0.06); margin-top: 1rem;
    }
    .preview-header {
        padding: 0.6rem 1rem; background: rgba(255,255,255,0.03);
        border-bottom: 1px solid rgba(255,255,255,0.05);
        display: flex; align-items: center; gap: 0.4rem;
    }
    .preview-dot { width: 8px; height: 8px; border-radius: 50%; }
    .preview-body { padding: 1.25rem 1.5rem; }
    .preview-badge {
        display: inline-block; font-size: 0.6rem; font-weight: 800;
        letter-spacing: 0.12em; text-transform: uppercase;
        padding: 0.25rem 0.65rem; border-radius: 99px;
        background: rgba(239,68,68,0.15); color: var(--c-primary, #ef4444);
        margin-bottom: 0.6rem;
    }
    .preview-title { font-size: 1rem; font-weight: 900; color: #fff; margin-bottom: 0.35rem; }
    .preview-sub { font-size: 0.7rem; color: rgba(255,255,255,0.45); margin-bottom: 0.75rem; }
    .preview-cta {
        display: inline-block; font-size: 0.7rem; font-weight: 800;
        padding: 0.4rem 1.1rem; border-radius: 99px; color: #fff;
        background: var(--c-primary, #ef4444);
    }
</style>

<form method="POST" action="{{ route('admin.settings.update') }}" id="settingsForm">
    @csrf

    {{-- Page header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 style="font-family:'Outfit',sans-serif;font-size:1.4rem;font-weight:900;color:#0f172a;letter-spacing:-0.3px;">
                Store Settings
            </h1>
            <p style="font-size:0.8rem;color:#94a3b8;margin-top:2px;">
                Konfigurasi konten dan tampilan toko Anda
            </p>
        </div>
        <button type="submit" class="save-btn">
            <i class="fa-solid fa-floppy-disk"></i>
            Simpan Pengaturan
        </button>
    </div>

    {{-- Tab navigation --}}
    <div class="tab-nav mb-6" id="tabNav">
        <button type="button" class="tab-btn active" data-tab="identity">
            <i class="fa-solid fa-store"></i> Identitas
        </button>
        <button type="button" class="tab-btn" data-tab="navbar">
            <i class="fa-solid fa-bars"></i> Navbar
        </button>
        <button type="button" class="tab-btn" data-tab="homepage">
            <i class="fa-solid fa-house"></i> Konten Utama
        </button>
        <button type="button" class="tab-btn" data-tab="colors">
            <i class="fa-solid fa-palette"></i> Warna Tema
        </button>
        <button type="button" class="tab-btn" data-tab="social">
            <i class="fa-solid fa-share-nodes"></i> Sosial & Footer
        </button>
        <button type="button" class="tab-btn" data-tab="seo">
            <i class="fa-solid fa-magnifying-glass"></i> SEO
        </button>
    </div>

    {{-- ══════════ TAB 1: IDENTITAS TOKO ══════════ --}}
    <div class="tab-panel active" id="tab-identity">
        <div class="settings-card">
            <div class="settings-card-title">
                <span class="dot" style="background:#7c3aed;"></span>
                Informasi Toko
            </div>
            <div class="grid-2">
                <div class="field-group">
                    <label class="field-label">Nama Toko *</label>
                    <input type="text" name="store_name" class="field-input"
                           value="{{ old('store_name', $settings->get('store_name')) }}"
                           placeholder="Nama toko Anda" required>
                </div>
                <div class="field-group">
                    <label class="field-label">Tagline</label>
                    <input type="text" name="store_tagline" class="field-input"
                           value="{{ old('store_tagline', $settings->get('store_tagline')) }}"
                           placeholder="Slogan singkat toko Anda">
                </div>
                <div class="field-group">
                    <label class="field-label">Email Toko</label>
                    <input type="email" name="store_email" class="field-input"
                           value="{{ old('store_email', $settings->get('store_email')) }}"
                           placeholder="info@toko.com">
                </div>
                <div class="field-group">
                    <label class="field-label">Nomor Telepon</label>
                    <input type="text" name="store_phone" class="field-input"
                           value="{{ old('store_phone', $settings->get('store_phone')) }}"
                           placeholder="021-xxxx-xxxx">
                </div>
                <div class="field-group">
                    <label class="field-label">Nomor WhatsApp</label>
                    <input type="text" name="store_whatsapp" class="field-input"
                           value="{{ old('store_whatsapp', $settings->get('store_whatsapp')) }}"
                           placeholder="628xxxxxxx (tanpa + atau -)">
                    <p class="field-hint">Format internasional tanpa tanda + contoh: 6281234567890</p>
                </div>
                <div class="field-group">
                    <label class="field-label">Alamat Toko</label>
                    <textarea name="store_address" class="field-input" placeholder="Alamat lengkap toko"
                    >{{ old('store_address', $settings->get('store_address')) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Trust Badges --}}
        <div class="settings-card">
            <div class="settings-card-title">
                <span class="dot" style="background:#10b981;"></span>
                Trust Badges (Bagian Bawah Homepage)
            </div>
            <p style="font-size:0.75rem;color:#94a3b8;margin-bottom:1rem;">
                4 badge kepercayaan yang tampil di bagian bawah halaman utama.
                Untuk ikon, gunakan nama class FontAwesome Solid (contoh: <code style="background:#f1f5f9;padding:1px 5px;border-radius:4px;">fa-shield-halved</code>).
            </p>
            @foreach([1,2,3,4] as $i)
            <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:14px;padding:1rem;margin-bottom:0.75rem;">
                <p style="font-size:0.72rem;font-weight:700;color:#7c3aed;margin-bottom:0.6rem;text-transform:uppercase;letter-spacing:0.06em;">
                    <i class="fa-solid fa-shield-halved" style="margin-right:0.3rem;"></i> Badge {{ $i }}
                </p>
                <div style="display:grid;grid-template-columns:1fr 1fr 2fr;gap:0.75rem;">
                    <div class="field-group" style="margin-bottom:0;">
                        <label class="field-label">Ikon FA</label>
                        <input type="text" name="badge_{{ $i }}_icon" class="field-input"
                               value="{{ old('badge_'.$i.'_icon', $settings->get('badge_'.$i.'_icon','fa-shield-halved')) }}"
                               placeholder="fa-shield-halved">
                    </div>
                    <div class="field-group" style="margin-bottom:0;">
                        <label class="field-label">Judul</label>
                        <input type="text" name="badge_{{ $i }}_title" class="field-input"
                               value="{{ old('badge_'.$i.'_title', $settings->get('badge_'.$i.'_title')) }}"
                               placeholder="Judul badge">
                    </div>
                    <div class="field-group" style="margin-bottom:0;">
                        <label class="field-label">Deskripsi</label>
                        <input type="text" name="badge_{{ $i }}_desc" class="field-input"
                               value="{{ old('badge_'.$i.'_desc', $settings->get('badge_'.$i.'_desc')) }}"
                               placeholder="Keterangan singkat">
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ══════════ TAB 2: NAVBAR ══════════ --}}
    <div class="tab-panel" id="tab-navbar">
        <div class="settings-card">
            <div class="settings-card-title">
                <span class="dot" style="background:#6366f1;"></span>
                Navbar / Header
            </div>
            <p style="font-size:0.78rem;color:#64748b;margin-bottom:1.25rem;">Teks yang tampil di bagian atas halaman (navigasi).</p>
            <div class="grid-2">
                <div class="field-group">
                    <label class="field-label">Teks Logo Navbar</label>
                    <input type="text" name="nav_logo_text" class="field-input"
                           value="{{ old('nav_logo_text', $settings->get('nav_logo_text')) }}"
                           placeholder="Nama toko di logo">
                    <p class="field-hint">Nama lengkap yang tampil di sebelah ikon logo.</p>
                </div>
                <div class="field-group">
                    <label class="field-label">Kata yang Diwarnai (Highlight)</label>
                    <input type="text" name="nav_logo_highlight" class="field-input"
                           value="{{ old('nav_logo_highlight', $settings->get('nav_logo_highlight')) }}"
                           placeholder="Bagian nama yang berwarna">
                    <p class="field-hint">Kosongkan jika tidak ingin ada highlight. Contoh: jika logo = "TokoHebat" dan highlight = "Hebat", maka "Hebat" berwarna primer.</p>
                </div>
                <div class="field-group">
                    <label class="field-label">Placeholder Search Bar</label>
                    <input type="text" name="nav_search_placeholder" class="field-input"
                           value="{{ old('nav_search_placeholder', $settings->get('nav_search_placeholder')) }}"
                           placeholder="Cari produk...">
                </div>
                <div class="field-group">
                    <label class="field-label">Teks Tombol Login</label>
                    <input type="text" name="nav_login_text" class="field-input"
                           value="{{ old('nav_login_text', $settings->get('nav_login_text','Masuk')) }}"
                           placeholder="Masuk">
                </div>
                <div class="field-group">
                    <label class="field-label">Teks Tombol Register</label>
                    <input type="text" name="nav_register_text" class="field-input"
                           value="{{ old('nav_register_text', $settings->get('nav_register_text','Daftar')) }}"
                           placeholder="Daftar">
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════ TAB 3: KONTEN UTAMA ══════════ --}}
    <div class="tab-panel" id="tab-homepage">
        {{-- Hero Section --}}
        <div class="settings-card">
            <div class="settings-card-title">
                <span class="dot" style="background:#f59e0b;"></span>
                Hero Section (Banner Utama)
            </div>

            <div class="grid-2" style="gap:0.75rem 1.25rem;">
                <div class="field-group">
                    <label class="field-label">Teks Badge Kecil</label>
                    <input type="text" name="hero_badge_text" class="field-input"
                           id="prev_badge"
                           value="{{ old('hero_badge_text', $settings->get('hero_badge_text')) }}"
                           placeholder="cth: Toko Terpercaya">
                    <p class="field-hint">Teks label kecil di atas judul hero</p>
                </div>
                <div class="field-group">
                    <label class="field-label">Teks Tombol CTA</label>
                    <input type="text" name="hero_cta_text" class="field-input"
                           id="prev_cta"
                           value="{{ old('hero_cta_text', $settings->get('hero_cta_text')) }}"
                           placeholder="cth: Belanja Sekarang">
                </div>
            </div>
            <div class="field-group">
                <label class="field-label">Judul Utama Hero</label>
                <input type="text" name="hero_title" class="field-input"
                       id="prev_title"
                       value="{{ old('hero_title', $settings->get('hero_title')) }}"
                       placeholder="Judul besar di hero banner">
            </div>
            <div class="field-group" style="margin-bottom:0;">
                <label class="field-label">Sub-Judul / Deskripsi</label>
                <textarea name="hero_subtitle" class="field-input" id="prev_sub"
                          placeholder="Kalimat deskripsi di bawah judul">{{ old('hero_subtitle', $settings->get('hero_subtitle')) }}</textarea>
            </div>

            {{-- Live preview --}}
            <div class="preview-wrap">
                <div class="preview-header">
                    <div class="preview-dot" style="background:#ef4444;"></div>
                    <div class="preview-dot" style="background:#f59e0b;"></div>
                    <div class="preview-dot" style="background:#22c55e;"></div>
                    <span style="font-size:0.62rem;color:rgba(255,255,255,0.2);margin-left:0.5rem;">Live Preview</span>
                </div>
                <div class="preview-body">
                    <div class="preview-badge" id="previewBadge">{{ $settings->get('hero_badge_text') }}</div>
                    <div class="preview-title" id="previewTitle">{{ $settings->get('hero_title') }}</div>
                    <div class="preview-sub" id="previewSub">{{ $settings->get('hero_subtitle') }}</div>
                    <div class="preview-cta" id="previewCta">{{ $settings->get('hero_cta_text') }}</div>
                </div>
            </div>
        </div>

        {{-- Section Titles --}}
        <div class="settings-card">
            <div class="settings-card-title">
                <span class="dot" style="background:#6366f1;"></span>
                Judul Seksi Produk & Halaman
            </div>
            <div class="grid-2">
                <div class="field-group">
                    <label class="field-label">Judul Seksi Kategori</label>
                    <input type="text" name="category_section_title" class="field-input"
                           value="{{ old('category_section_title', $settings->get('category_section_title','Kategori Produk')) }}"
                           placeholder="Kategori Produk">
                </div>
                <div class="field-group">
                    <label class="field-label">Sub-judul Kategori</label>
                    <input type="text" name="category_section_subtitle" class="field-input"
                           value="{{ old('category_section_subtitle', $settings->get('category_section_subtitle')) }}"
                           placeholder="Keterangan singkat">
                </div>
                <div class="field-group">
                    <label class="field-label">Teks "Lihat Semua" (Kategori)</label>
                    <input type="text" name="category_view_all_text" class="field-input"
                           value="{{ old('category_view_all_text', $settings->get('category_view_all_text','Lihat Semua')) }}"
                           placeholder="Lihat Semua">
                </div>
                <div class="field-group">
                    <label class="field-label">Judul Seksi Kendaraan (Garasi)</label>
                    <input type="text" name="garage_section_title" class="field-input"
                           value="{{ old('garage_section_title', $settings->get('garage_section_title')) }}"
                           placeholder="Cari Berdasarkan Kendaraan">
                </div>
                <div class="field-group">
                    <label class="field-label">Sub-judul Seksi Kendaraan</label>
                    <input type="text" name="garage_section_subtitle" class="field-input"
                           value="{{ old('garage_section_subtitle', $settings->get('garage_section_subtitle')) }}"
                           placeholder="Filter produk yang kompatibel">
                </div>
                <div class="field-group">
                    <label class="field-label">Teks Tombol Cari (Kendaraan)</label>
                    <input type="text" name="garage_cta_text" class="field-input"
                           value="{{ old('garage_cta_text', $settings->get('garage_cta_text','Cari Produk')) }}"
                           placeholder="Cari Produk">
                </div>
                <div class="field-group">
                    <label class="field-label">Judul Produk Unggulan</label>
                    <input type="text" name="featured_section_title" class="field-input"
                           value="{{ old('featured_section_title', $settings->get('featured_section_title')) }}"
                           placeholder="Produk Unggulan">
                </div>
                <div class="field-group">
                    <label class="field-label">Sub-judul Produk Unggulan</label>
                    <input type="text" name="featured_section_subtitle" class="field-input"
                           value="{{ old('featured_section_subtitle', $settings->get('featured_section_subtitle')) }}"
                           placeholder="Keterangan seksi">
                </div>
                <div class="field-group">
                    <label class="field-label">Judul Produk Terbaru</label>
                    <input type="text" name="latest_section_title" class="field-input"
                           value="{{ old('latest_section_title', $settings->get('latest_section_title')) }}"
                           placeholder="Produk Terbaru">
                </div>
                <div class="field-group">
                    <label class="field-label">Sub-judul Produk Terbaru</label>
                    <input type="text" name="latest_section_subtitle" class="field-input"
                           value="{{ old('latest_section_subtitle', $settings->get('latest_section_subtitle')) }}"
                           placeholder="Keterangan seksi">
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════ TAB 3: WARNA TEMA ══════════ --}}
    <div class="tab-panel" id="tab-colors">
        <div class="settings-card">
            <div class="settings-card-title">
                <span class="dot" style="background:#ec4899;"></span>
                Palet Warna Toko
            </div>
            <p style="font-size:0.78rem;color:#64748b;margin-bottom:1.5rem;line-height:1.6;">
                Warna ini akan digunakan pada tombol, badge, border aktif, dan elemen highlight di seluruh halaman.
                Klik kotak warna atau ketik kode HEX untuk mengubahnya.
            </p>

            <div style="display:flex;flex-direction:column;gap:1.25rem;">

                {{-- Primary --}}
                <div>
                    <label class="field-label" style="margin-bottom:0.6rem;">
                        Warna Utama (Primary)
                    </label>
                    <div class="color-field">
                        <div class="color-input-wrap">
                            <input type="color" id="colorPrimaryPicker"
                                   value="{{ old('color_primary', $settings->get('color_primary', '#ef4444')) }}">
                        </div>
                        <input type="text" name="color_primary" id="colorPrimaryHex"
                               class="color-hex"
                               value="{{ strtoupper(old('color_primary', $settings->get('color_primary', '#ef4444'))) }}"
                               placeholder="#ef4444" maxlength="7">
                        <span style="font-size:0.75rem;color:#94a3b8;white-space:nowrap;">
                            Tombol, badge, highlight
                        </span>
                    </div>
                </div>

                {{-- Secondary --}}
                <div>
                    <label class="field-label" style="margin-bottom:0.6rem;">
                        Warna Sekunder (Secondary)
                    </label>
                    <div class="color-field">
                        <div class="color-input-wrap">
                            <input type="color" id="colorSecondaryPicker"
                                   value="{{ old('color_secondary', $settings->get('color_secondary', '#f97316')) }}">
                        </div>
                        <input type="text" name="color_secondary" id="colorSecondaryHex"
                               class="color-hex"
                               value="{{ strtoupper(old('color_secondary', $settings->get('color_secondary', '#f97316'))) }}"
                               placeholder="#f97316" maxlength="7">
                        <span style="font-size:0.75rem;color:#94a3b8;white-space:nowrap;">
                            Gradien, aksen
                        </span>
                    </div>
                </div>

                {{-- Accent --}}
                <div>
                    <label class="field-label" style="margin-bottom:0.6rem;">
                        Warna Aksen (Hover/Active)
                    </label>
                    <div class="color-field">
                        <div class="color-input-wrap">
                            <input type="color" id="colorAccentPicker"
                                   value="{{ old('color_accent', $settings->get('color_accent', '#dc2626')) }}">
                        </div>
                        <input type="text" name="color_accent" id="colorAccentHex"
                               class="color-hex"
                               value="{{ strtoupper(old('color_accent', $settings->get('color_accent', '#dc2626'))) }}"
                               placeholder="#dc2626" maxlength="7">
                        <span style="font-size:0.75rem;color:#94a3b8;white-space:nowrap;">
                            Hover state, border
                        </span>
                    </div>
                </div>

                {{-- Background --}}
                <div>
                    <label class="field-label" style="margin-bottom:0.6rem;">
                        Warna Background (Latar Belakang)
                    </label>
                    <div class="color-field">
                        <div class="color-input-wrap">
                            <input type="color" id="colorBackgroundPicker"
                                   value="{{ old('color_background', $settings->get('color_background', '#0b0f19')) }}">
                        </div>
                        <input type="text" name="color_background" id="colorBackgroundHex"
                               class="color-hex"
                               value="{{ strtoupper(old('color_background', $settings->get('color_background', '#0b0f19'))) }}"
                               placeholder="#0b0f19" maxlength="7">
                        <span style="font-size:0.75rem;color:#94a3b8;white-space:nowrap;">
                            Background halaman website
                        </span>
                    </div>
                </div>
            </div>

            {{-- Color preview --}}
            <div style="margin-top:1.5rem;">
                <p style="font-size:0.72rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:0.75rem;">
                    Preview Warna
                </p>
                <div style="display:flex;gap:1rem;flex-wrap:wrap;align-items:center;">
                    <button type="button" id="btnPreviewPrimary"
                            style="padding:0.55rem 1.25rem;border-radius:10px;font-weight:700;font-size:0.82rem;color:#fff;border:none;cursor:default;transition:background 0.3s;">
                        Tombol Utama
                    </button>
                    <button type="button" id="btnPreviewSecondary"
                            style="padding:0.55rem 1.25rem;border-radius:10px;font-weight:700;font-size:0.82rem;color:#fff;border:none;cursor:default;transition:background 0.3s;">
                        Tombol Sekunder
                    </button>
                    <div id="colorGradBar" style="flex:1;min-width:120px;height:10px;border-radius:99px;transition:background 0.3s;"></div>
                </div>

                {{-- Swatches --}}
                <div style="display:flex;gap:0.5rem;margin-top:0.75rem;">
                    @foreach([
                        ['#ef4444','#dc2626','#f97316'],
                        ['#7c3aed','#6d28d9','#a78bfa'],
                        ['#2563eb','#1d4ed8','#60a5fa'],
                        ['#059669','#047857','#34d399'],
                        ['#0891b2','#0e7490','#67e8f9'],
                        ['#d97706','#b45309','#fcd34d'],
                        ['#0f172a','#1e293b','#475569'],
                    ] as [$p,$a,$s])
                    <button type="button" class="swatch-set" title="{{ $p }}"
                            data-primary="{{ $p }}" data-secondary="{{ $s }}" data-accent="{{ $a }}"
                            style="display:flex;gap:3px;padding:5px;border-radius:10px;border:2px solid transparent;cursor:pointer;background:#f8fafc;transition:border-color 0.2s;"
                            onmouseover="this.style.borderColor='#7c3aed'" onmouseout="this.style.borderColor='transparent'">
                        <span style="width:16px;height:16px;border-radius:4px;background:{{ $p }};display:block;"></span>
                        <span style="width:16px;height:16px;border-radius:4px;background:{{ $a }};display:block;"></span>
                        <span style="width:16px;height:16px;border-radius:4px;background:{{ $s }};display:block;"></span>
                    </button>
                    @endforeach
                </div>
                <p class="field-hint" style="margin-top:0.5rem;">
                    Klik palette di atas untuk menerapkan preset warna secara cepat.
                </p>
            </div>
        </div>
    </div>

    {{-- ══════════ TAB 4: SOSIAL MEDIA & FOOTER ══════════ --}}
    <div class="tab-panel" id="tab-social">
        <div class="settings-card">
            <div class="settings-card-title">
                <span class="dot" style="background:#0ea5e9;"></span>
                Akun Sosial Media
            </div>
            <div class="grid-2">
                <div class="field-group">
                    <label class="field-label"><i class="fa-brands fa-instagram" style="color:#e1306c;"></i> Instagram</label>
                    <input type="text" name="social_instagram" class="field-input"
                           value="{{ old('social_instagram', $settings->get('social_instagram')) }}"
                           placeholder="https://instagram.com/tokoanda">
                </div>
                <div class="field-group">
                    <label class="field-label"><i class="fa-brands fa-facebook" style="color:#1877f2;"></i> Facebook</label>
                    <input type="text" name="social_facebook" class="field-input"
                           value="{{ old('social_facebook', $settings->get('social_facebook')) }}"
                           placeholder="https://facebook.com/tokoanda">
                </div>
                <div class="field-group">
                    <label class="field-label"><i class="fa-brands fa-tiktok"></i> TikTok</label>
                    <input type="text" name="social_tiktok" class="field-input"
                           value="{{ old('social_tiktok', $settings->get('social_tiktok')) }}"
                           placeholder="https://tiktok.com/@tokoanda">
                </div>
                <div class="field-group">
                    <label class="field-label"><i class="fa-brands fa-youtube" style="color:#ff0000;"></i> YouTube</label>
                    <input type="text" name="social_youtube" class="field-input"
                           value="{{ old('social_youtube', $settings->get('social_youtube')) }}"
                           placeholder="https://youtube.com/@tokoanda">
                </div>
            </div>
        </div>

        <div class="settings-card">
            <div class="settings-card-title">
                <span class="dot" style="background:#64748b;"></span>
                Konten Footer
            </div>
            <div class="field-group">
                <label class="field-label">Deskripsi Toko (Footer About)</label>
                <textarea name="footer_about" class="field-input" style="min-height:90px;"
                          placeholder="Kalimat singkat tentang toko di bagian footer"
                >{{ old('footer_about', $settings->get('footer_about')) }}</textarea>
            </div>
            <div class="field-group" style="margin-bottom:0;">
                <label class="field-label">Teks Copyright</label>
                <input type="text" name="footer_copyright" class="field-input"
                       value="{{ old('footer_copyright', $settings->get('footer_copyright')) }}"
                       placeholder="cth: © 2025 NamaToko. All rights reserved.">
                <p class="field-hint">Kosongkan untuk menggunakan teks default otomatis.</p>
            </div>
        </div>
    </div>

    {{-- ══════════ TAB 5: SEO ══════════ --}}
    <div class="tab-panel" id="tab-seo">
        <div class="settings-card">
            <div class="settings-card-title">
                <span class="dot" style="background:#22c55e;"></span>
                Pengaturan SEO
            </div>
            <p style="font-size:0.78rem;color:#64748b;margin-bottom:1.25rem;line-height:1.6;">
                Konfigurasi ini membantu mesin pencari memahami konten toko Anda.
            </p>
            <div class="field-group">
                <label class="field-label">Meta Description</label>
                <textarea name="meta_description" class="field-input" style="min-height:80px;"
                          placeholder="Deskripsi singkat toko untuk hasil pencarian Google (maks. 160 karakter)"
                          maxlength="300"
                >{{ old('meta_description', $settings->get('meta_description')) }}</textarea>
                <p class="field-hint">Idealnya 120–160 karakter. Ini akan tampil sebagai deskripsi di hasil Google.</p>
            </div>
            <div class="field-group" style="margin-bottom:0;">
                <label class="field-label">Meta Keywords</label>
                <input type="text" name="meta_keywords" class="field-input"
                       value="{{ old('meta_keywords', $settings->get('meta_keywords')) }}"
                       placeholder="kata kunci, suku cadang, sparepart, toko online">
                <p class="field-hint">Pisahkan dengan koma. Contoh: sparepart motor, oli mesin, aksesoris motor</p>
            </div>
        </div>

        {{-- Google preview --}}
        <div class="settings-card">
            <div class="settings-card-title">
                <span class="dot" style="background:#4285f4;"></span>
                Preview Google Search
            </div>
            <div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:1.25rem;font-family:Arial,sans-serif;">
                <div style="font-size:0.7rem;color:#006621;margin-bottom:2px;">
                    {{ config('app.url', 'https://toko.com') }}
                </div>
                <div style="font-size:1rem;color:#1a0dab;font-weight:400;margin-bottom:3px;" id="seoTitle">
                    {{ $settings->get('store_name') }} — {{ $settings->get('store_tagline') }}
                </div>
                <div style="font-size:0.82rem;color:#545454;line-height:1.5;" id="seoDesc">
                    {{ $settings->get('meta_description', 'Toko online terpercaya...') }}
                </div>
            </div>
        </div>
    </div>

    {{-- Bottom save button --}}
    <div style="display:flex;justify-content:flex-end;margin-top:1.5rem;">
        <button type="submit" class="save-btn">
            <i class="fa-solid fa-floppy-disk"></i>
            Simpan Semua Pengaturan
        </button>
    </div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Tab switching ──
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            document.getElementById('tab-' + this.dataset.tab).classList.add('active');
        });
    });

    // ── Hero live preview ──
    function bindPreview(inputId, previewId) {
        const inp = document.getElementById(inputId);
        const out = document.getElementById(previewId);
        if (!inp || !out) return;
        inp.addEventListener('input', () => { out.textContent = inp.value || inp.placeholder; });
    }
    bindPreview('prev_badge', 'previewBadge');
    bindPreview('prev_title', 'previewTitle');
    bindPreview('prev_sub',   'previewSub');
    bindPreview('prev_cta',   'previewCta');

    // ── Color sync: picker ↔ hex input ──
    function syncColor(pickerId, hexId, cssVar) {
        const picker = document.getElementById(pickerId);
        const hex    = document.getElementById(hexId);
        if (!picker || !hex) return;

        function applyColor(val) {
            document.documentElement.style.setProperty(cssVar, val);
            updateColorPreview();
        }

        picker.addEventListener('input', () => {
            hex.value = picker.value.toUpperCase();
            applyColor(picker.value);
        });
        hex.addEventListener('input', () => {
            const v = hex.value.trim();
            if (/^#[0-9a-fA-F]{6}$/.test(v)) {
                picker.value = v;
                applyColor(v);
            }
        });
        // Init
        applyColor(picker.value);
    }

    syncColor('colorPrimaryPicker',   'colorPrimaryHex',   '--c-primary');
    syncColor('colorSecondaryPicker', 'colorSecondaryHex', '--c-secondary');
    syncColor('colorAccentPicker',    'colorAccentHex',    '--c-accent');
    syncColor('colorBackgroundPicker','colorBackgroundHex','--c-bg');

    function updateColorPreview() {
        const p = document.getElementById('colorPrimaryHex')?.value   || '#ef4444';
        const s = document.getElementById('colorSecondaryHex')?.value || '#f97316';
        const a = document.getElementById('colorAccentHex')?.value    || '#dc2626';

        const bar = document.getElementById('colorGradBar');
        if (bar) bar.style.background = `linear-gradient(90deg, ${p}, ${s})`;

        const btnP = document.getElementById('btnPreviewPrimary');
        const btnS = document.getElementById('btnPreviewSecondary');
        if (btnP) btnP.style.background = p;
        if (btnS) btnS.style.background = s;

        // Update preview badge & cta color
        const badge = document.querySelector('.preview-badge');
        const cta   = document.querySelector('.preview-cta');
        if (badge) badge.style.color = p;
        if (cta)   cta.style.background = p;
    }

    // ── Preset palette swatches ──
    document.querySelectorAll('.swatch-set').forEach(btn => {
        btn.addEventListener('click', function () {
            const p = this.dataset.primary;
            const s = this.dataset.secondary;
            const a = this.dataset.accent;

            document.getElementById('colorPrimaryPicker').value   = p;
            document.getElementById('colorPrimaryHex').value      = p.toUpperCase();
            document.getElementById('colorSecondaryPicker').value = s;
            document.getElementById('colorSecondaryHex').value    = s.toUpperCase();
            document.getElementById('colorAccentPicker').value    = a;
            document.getElementById('colorAccentHex').value       = a.toUpperCase();

            updateColorPreview();
        });
    });

    // ── SEO preview ──
    const metaDesc = document.querySelector('[name="meta_description"]');
    const seoDesc  = document.getElementById('seoDesc');
    if (metaDesc && seoDesc) {
        metaDesc.addEventListener('input', () => {
            seoDesc.textContent = metaDesc.value || 'Toko online terpercaya...';
        });
    }

    // ── Init preview ──
    updateColorPreview();
});
</script>
@endpush
</x-admin-layout>
