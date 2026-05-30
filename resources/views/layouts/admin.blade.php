<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Admin Dashboard — {{ config('app.name', 'MyCommerce') }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- CSS / Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        heading: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#f5f3ff', 100: '#ede9fe', 200: '#ddd6fe',
                            300: '#c4b5fd', 400: '#a78bfa', 500: '#8b5cf6',
                            600: '#7c3aed', 700: '#6d28d9', 800: '#5b21b6', 900: '#4c1d95',
                        },
                        brand: { dark: '#0B0F19', slate: '#1E293B', mid: '#141826' }
                    },
                    boxShadow: {
                        'premium': '0 2px 16px rgba(0,0,0,0.04)',
                        'premium-hover': '0 20px 40px rgba(0,0,0,0.08)',
                        'glow': '0 0 30px rgba(124,58,237,0.25)',
                        'sidebar-active': '0 4px 24px rgba(124,58,237,0.35)',
                    },
                    animation: {
                        'slide-in-left': 'slideInLeft 0.3s cubic-bezier(0.16,1,0.3,1)',
                        'fade-up': 'fadeUp 0.4s cubic-bezier(0.16,1,0.3,1)',
                        'pulse-soft': 'pulseSoft 3s ease-in-out infinite',
                    },
                    keyframes: {
                        slideInLeft: {
                            from: { opacity: '0', transform: 'translateX(-12px)' },
                            to:   { opacity: '1', transform: 'translateX(0)' },
                        },
                        fadeUp: {
                            from: { opacity: '0', transform: 'translateY(10px)' },
                            to:   { opacity: '1', transform: 'translateY(0)' },
                        },
                        pulseSoft: {
                            '0%,100%': { opacity: '1' },
                            '50%':     { opacity: '0.6' },
                        },
                    }
                }
            }
        }
    </script>

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- AlpineJS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }

        * { box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #F0F2F8;
        }
        h1, h2, h3, h4, h5, h6 { font-family: 'Outfit', sans-serif; }

        /* ── Sidebar custom scrollbar ── */
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.08); border-radius: 99px; }
        .sidebar-scroll::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.18); }

        /* ── Sidebar nav item ── */
        .nav-item {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            padding: 0.7rem 1rem;
            border-radius: 14px;
            font-size: 0.85rem;
            font-weight: 600;
            color: rgba(148,163,184,1);
            text-decoration: none;
            position: relative;
            transition: all 0.25s cubic-bezier(0.4,0,0.2,1);
            overflow: hidden;
        }
        .nav-item::before {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(255,255,255,0.04);
            border-radius: 14px;
            opacity: 0;
            transition: opacity 0.25s;
        }
        .nav-item:hover { color: #fff; transform: translateX(4px); }
        .nav-item:hover::before { opacity: 1; }

        .nav-item-icon {
            width: 34px; height: 34px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            background: rgba(255,255,255,0.06);
            color: rgba(148,163,184,1);
            flex-shrink: 0;
            transition: all 0.25s;
        }
        .nav-item:hover .nav-item-icon {
            background: rgba(124,58,237,0.2);
            color: #a78bfa;
        }

        /* Active state */
        .nav-item.active {
            color: #fff;
            background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%);
            box-shadow: 0 4px 20px rgba(124,58,237,0.4), 0 0 0 1px rgba(255,255,255,0.08) inset;
        }
        .nav-item.active .nav-item-icon {
            background: rgba(255,255,255,0.15);
            color: #fff;
        }
        .nav-item.active:hover { transform: none; }

        /* ── Active indicator dot ── */
        .nav-item.active::after {
            content: '';
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 6px; height: 6px;
            border-radius: 50%;
            background: rgba(255,255,255,0.6);
            box-shadow: 0 0 6px rgba(255,255,255,0.4);
            animation: pulseDot 2s ease-in-out infinite;
        }
        @keyframes pulseDot {
            0%, 100% { opacity: 1; transform: translateY(-50%) scale(1); }
            50%       { opacity: 0.5; transform: translateY(-50%) scale(0.7); }
        }

        /* ── Nav group label ── */
        .nav-group-label {
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.2);
            padding: 0 1rem;
            margin-bottom: 0.4rem;
            margin-top: 1.2rem;
        }

        /* ── Header search ── */
        .header-search {
            background: rgba(241,245,249,1);
            border: 1px solid rgba(226,232,240,1);
            border-radius: 12px;
            padding: 0.5rem 1rem;
            font-size: 0.82rem;
            color: #64748b;
            outline: none;
            transition: all 0.2s;
            min-width: 200px;
        }
        .header-search:focus {
            border-color: #a78bfa;
            box-shadow: 0 0 0 3px rgba(124,58,237,0.1);
        }

        /* ── Notification badge ── */
        .notif-dot {
            position: absolute;
            top: 2px; right: 2px;
            width: 8px; height: 8px;
            border-radius: 50%;
            background: #ef4444;
            border: 2px solid #fff;
            animation: pulseDot 2s ease-in-out infinite;
        }

        /* ── Status badge ── */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            font-size: 0.72rem;
            font-weight: 700;
            padding: 0.25rem 0.65rem;
            border-radius: 99px;
            letter-spacing: 0.04em;
        }
        .status-badge::before {
            content: '';
            width: 5px; height: 5px;
            border-radius: 50%;
            background: currentColor;
        }

        /* ── Toast notifications ── */
        .toast-container {
            position: fixed;
            top: 1.5rem;
            right: 1.5rem;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        .toast {
            background: #fff;
            border-radius: 16px;
            padding: 1rem 1.25rem;
            box-shadow: 0 8px 30px rgba(0,0,0,0.12), 0 0 0 1px rgba(0,0,0,0.04);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            min-width: 280px;
            max-width: 380px;
            animation: toastIn 0.4s cubic-bezier(0.16,1,0.3,1);
            position: relative;
        }
        @keyframes toastIn {
            from { opacity: 0; transform: translateX(40px) scale(0.95); }
            to   { opacity: 1; transform: translateX(0) scale(1); }
        }
        .toast-out {
            animation: toastOut 0.3s ease forwards;
        }
        @keyframes toastOut {
            to { opacity: 0; transform: translateX(40px) scale(0.95); }
        }
        .toast-icon {
            width: 36px; height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
        }
        .toast-success .toast-icon { background: #ecfdf5; color: #059669; }
        .toast-error   .toast-icon { background: #fff1f2; color: #e11d48; }
        .toast-body { flex: 1; }
        .toast-title { font-weight: 700; font-size: 0.85rem; color: #0f172a; }
        .toast-msg   { font-size: 0.78rem; color: #64748b; margin-top: 0.15rem; }
        .toast-close {
            background: none; border: none; cursor: pointer;
            color: #94a3b8; font-size: 0.85rem;
            padding: 4px; border-radius: 6px;
            transition: color 0.2s;
        }
        .toast-close:hover { color: #334155; }

        /* ── Progress bar on top ── */
        #page-progress {
            position: fixed;
            top: 0; left: 0;
            height: 3px;
            background: linear-gradient(90deg, #7c3aed, #818cf8, #c084fc);
            z-index: 9999;
            width: 0%;
            transition: width 0.4s ease;
            border-radius: 0 99px 99px 0;
        }

        /* ── Main content fade in ── */
        .content-fade {
            animation: fadeUp 0.5s cubic-bezier(0.16,1,0.3,1);
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── Sidebar gradient line ── */
        .sidebar-gradient-top {
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 200px;
            background: radial-gradient(ellipse at top left, rgba(124,58,237,0.18) 0%, transparent 70%);
            pointer-events: none;
        }

        /* ── User avatar gradient ── */
        .user-avatar {
            width: 38px; height: 38px;
            border-radius: 12px;
            background: linear-gradient(135deg, #7c3aed, #4f46e5);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.9rem;
            color: #fff;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(124,58,237,0.35);
        }

        /* ── Logout button ── */
        .logout-btn {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.65rem 1rem;
            border-radius: 12px;
            border: 1px solid rgba(255,255,255,0.06);
            background: rgba(239,68,68,0.08);
            color: #fca5a5;
            font-size: 0.82rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.25s;
        }
        .logout-btn:hover {
            background: rgba(239,68,68,0.15);
            color: #f87171;
            border-color: rgba(239,68,68,0.2);
        }

        /* ── Responsive ── */
        @media (max-width: 1024px) {
            .admin-sidebar { width: 280px; }
        }
    </style>

    @stack('styles')
</head>
<body class="antialiased text-slate-700" x-data="{ sidebarOpen: false, searchOpen: false }">

    <!-- Page progress bar -->
    <div id="page-progress"></div>

    <!-- Toast container -->
    <div class="toast-container" id="toastContainer"></div>

    <!-- Mobile Header -->
    <div class="lg:hidden flex items-center justify-between sticky top-0 z-40 px-4 py-3"
         style="background: #0B0F19; border-bottom: 1px solid rgba(255,255,255,0.06); box-shadow: 0 4px 20px rgba(0,0,0,0.3);">
        <div class="flex items-center gap-3">
            <div style="width:38px;height:38px;border-radius:12px;background:linear-gradient(135deg,#7c3aed,#4f46e5);display:flex;align-items:center;justify-content:center;color:#fff;font-size:1rem;box-shadow:0 4px 12px rgba(124,58,237,0.4);">
                <i class="fa-solid fa-store"></i>
            </div>
            <span style="font-family:'Outfit',sans-serif;font-weight:800;font-size:1.05rem;color:#fff;letter-spacing:-0.3px;">
                Admin Console
            </span>
        </div>
        <button @click="sidebarOpen = !sidebarOpen"
                style="width:38px;height:38px;border-radius:10px;background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.08);color:rgba(255,255,255,0.7);display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all 0.2s;"
                :style="sidebarOpen ? 'background:rgba(124,58,237,0.25);color:#a78bfa;' : ''">
            <i class="fa-solid" :class="sidebarOpen ? 'fa-xmark' : 'fa-bars'"></i>
        </button>
    </div>

    <div class="flex min-h-screen">
        <!-- ══════════ SIDEBAR ══════════ -->
        <aside class="admin-sidebar fixed inset-y-0 left-0 z-40 w-64 flex flex-col transform -translate-x-full lg:translate-x-0 lg:static lg:flex-shrink-0 transition-transform duration-300 ease-in-out"
               style="background: #0B0F19; border-right: 1px solid rgba(255,255,255,0.06);"
               :class="{ 'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen }">

            <div class="sidebar-gradient-top"></div>

            <!-- Brand -->
            <div class="hidden lg:flex items-center gap-3 px-5 pt-6 pb-5" style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                <div style="width:40px;height:40px;border-radius:13px;background:linear-gradient(135deg,#7c3aed,#4f46e5);display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.05rem;box-shadow:0 6px 20px rgba(124,58,237,0.45);flex-shrink:0;">
                    <i class="fa-solid fa-store"></i>
                </div>
                <div>
                    <div style="font-family:'Outfit',sans-serif;font-weight:900;font-size:1.05rem;color:#fff;letter-spacing:-0.3px;line-height:1.1;">Admin Console</div>
                    <div style="font-size:0.65rem;color:rgba(255,255,255,0.25);font-weight:600;letter-spacing:0.08em;text-transform:uppercase;margin-top:1px;">MyCommerce Platform</div>
                </div>
            </div>

            <!-- Navigation -->
            <div class="flex-1 overflow-y-auto sidebar-scroll px-3 py-4">

                <div class="nav-group-label">Main Menu</div>

                <a href="{{ route('admin.dashboard') }}"
                   class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <span class="nav-item-icon"><i class="fa-solid fa-chart-pie"></i></span>
                    Dashboard
                </a>

                <div class="nav-group-label">Catalog</div>

                <a href="{{ route('admin.products.index') }}"
                   class="nav-item {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                    <span class="nav-item-icon"><i class="fa-solid fa-box-open"></i></span>
                    Products
                </a>

                <a href="{{ route('admin.categories.index') }}"
                   class="nav-item {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <span class="nav-item-icon"><i class="fa-solid fa-tags"></i></span>
                    Categories
                </a>

                <div class="nav-group-label">Commerce</div>

                <a href="{{ route('admin.orders.index') }}"
                   class="nav-item {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                    <span class="nav-item-icon"><i class="fa-solid fa-receipt"></i></span>
                    Orders
                </a>

                <a href="{{ route('admin.coupons.index') }}"
                   class="nav-item {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
                    <span class="nav-item-icon"><i class="fa-solid fa-ticket"></i></span>
                    Coupons
                </a>

                <a href="{{ route('admin.flash-sales.index') }}"
                   class="nav-item {{ request()->routeIs('admin.flash-sales.*') ? 'active' : '' }}">
                    <span class="nav-item-icon"><i class="fa-solid fa-bolt"></i></span>
                    Flash Sales
                </a>

                <div class="nav-group-label">Content</div>

                <a href="{{ route('admin.banners.index') }}"
                   class="nav-item {{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">
                    <span class="nav-item-icon"><i class="fa-solid fa-image"></i></span>
                    Slider Banners
                </a>

                <div class="nav-group-label">Management</div>

                <a href="{{ route('admin.users.index') }}"
                   class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <span class="nav-item-icon"><i class="fa-solid fa-users"></i></span>
                    Users
                </a>

                <a href="{{ route('admin.settings.index') }}"
                   class="nav-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <span class="nav-item-icon"><i class="fa-solid fa-sliders"></i></span>
                    Store Settings
                </a>
            </div>

            <!-- User Footer -->
            <div class="px-3 pb-4 pt-3" style="border-top: 1px solid rgba(255,255,255,0.05);">
                <div class="flex items-center gap-3 mb-3 px-1">
                    <div class="user-avatar">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p style="font-size:0.82rem;font-weight:700;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ auth()->user()->name }}</p>
                        <p style="font-size:0.65rem;color:rgba(255,255,255,0.3);font-weight:700;letter-spacing:0.08em;text-transform:uppercase;margin-top:1px;">Super Admin</p>
                    </div>
                    <a href="{{ route('home') }}" target="_blank" title="View Shopfront"
                       style="width:30px;height:30px;border-radius:8px;background:rgba(255,255,255,0.05);display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,0.3);transition:all 0.2s;text-decoration:none;"
                       onmouseover="this.style.color='#a78bfa';this.style.background='rgba(124,58,237,0.2)'"
                       onmouseout="this.style.color='rgba(255,255,255,0.3)';this.style.background='rgba(255,255,255,0.05)'">
                        <i class="fa-solid fa-arrow-up-right-from-square" style="font-size:0.7rem;"></i>
                    </a>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        Keluar dari Console
                    </button>
                </form>
            </div>
        </aside>

        <!-- ══════════ MAIN CONTENT ══════════ -->
        <div class="flex-1 flex flex-col min-w-0 overflow-x-hidden">

            <!-- Header -->
            <header class="hidden lg:flex items-center justify-between px-8 h-[70px] sticky top-0 z-30"
                    style="background: rgba(255,255,255,0.85); backdrop-filter: blur(20px); border-bottom: 1px solid rgba(226,232,240,0.8); box-shadow: 0 1px 20px rgba(0,0,0,0.04);">

                <!-- Page Title -->
                <div class="flex items-center gap-3">
                    <div style="width:3px;height:22px;border-radius:99px;background:linear-gradient(180deg,#7c3aed,#4f46e5);"></div>
                    <h2 style="font-family:'Outfit',sans-serif;font-size:1.05rem;font-weight:800;color:#0f172a;letter-spacing:-0.2px;">
                        @if(request()->routeIs('admin.dashboard'))    Dashboard
                        @elseif(request()->routeIs('admin.products.*')) Products Management
                        @elseif(request()->routeIs('admin.categories.*')) Category Directory
                        @elseif(request()->routeIs('admin.orders.*'))  Orders Ledger
                        @elseif(request()->routeIs('admin.coupons.*')) Promotional Coupons
                        @elseif(request()->routeIs('admin.flash-sales.*')) Flash Sales
                        @elseif(request()->routeIs('admin.banners.*')) Slider Banners
                        @elseif(request()->routeIs('admin.users.*'))   User Accounts
                        @elseif(request()->routeIs('admin.settings.*')) Store Settings
                        @else Control Panel
                        @endif
                    </h2>
                </div>

                <!-- Right actions -->
                <div class="flex items-center gap-3">
                    <!-- Shopfront link -->
                    <a href="{{ route('home') }}" target="_blank"
                       style="display:flex;align-items:center;gap:0.4rem;font-size:0.8rem;font-weight:700;color:#7c3aed;text-decoration:none;padding:0.45rem 0.9rem;border-radius:10px;border:1px solid rgba(124,58,237,0.2);background:rgba(124,58,237,0.05);transition:all 0.2s;"
                       onmouseover="this.style.background='rgba(124,58,237,0.1)'"
                       onmouseout="this.style.background='rgba(124,58,237,0.05)'">
                        <i class="fa-solid fa-store"></i>
                        Lihat Toko
                    </a>

                    <!-- Notification bell -->
                    <div style="position:relative;">
                        <button style="width:38px;height:38px;border-radius:10px;background:#f1f5f9;border:1px solid #e2e8f0;display:flex;align-items:center;justify-content:center;color:#64748b;cursor:pointer;transition:all 0.2s;"
                                onmouseover="this.style.background='#e2e8f0'"
                                onmouseout="this.style.background='#f1f5f9'">
                            <i class="fa-solid fa-bell" style="font-size:0.9rem;"></i>
                        </button>
                        <span class="notif-dot"></span>
                    </div>

                    <!-- Active session badge -->
                    <div style="display:flex;align-items:center;gap:0.4rem;font-size:0.7rem;font-weight:700;color:#059669;padding:0.3rem 0.75rem;border-radius:99px;background:#ecfdf5;border:1px solid #bbf7d0;letter-spacing:0.04em;text-transform:uppercase;">
                        <span style="width:6px;height:6px;border-radius:50%;background:#10b981;animation:pulseDot 2s ease-in-out infinite;"></span>
                        Active Session
                    </div>

                    <!-- User avatar -->
                    <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#7c3aed,#4f46e5);display:flex;align-items:center;justify-content:center;font-weight:800;font-size:0.85rem;color:#fff;box-shadow:0 4px 10px rgba(124,58,237,0.3);">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                </div>
            </header>

            <!-- Main content -->
            <main class="flex-1 p-6 md:p-8 content-fade" style="background: #F0F2F8; min-height: calc(100vh - 70px);">

                <!-- Flash messages -->
                @if(session('success'))
                    <div class="mb-6 flex items-center gap-4 p-4 rounded-2xl"
                         style="background:#ecfdf5;border:1px solid #bbf7d0;color:#065f46;"
                         id="flash-success">
                        <span style="width:38px;height:38px;border-radius:12px;background:#d1fae5;color:#059669;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0;">
                            <i class="fa-solid fa-circle-check"></i>
                        </span>
                        <div>
                            <p style="font-weight:700;font-size:0.85rem;">Berhasil!</p>
                            <p style="font-size:0.8rem;opacity:0.75;margin-top:1px;">{{ session('success') }}</p>
                        </div>
                        <button onclick="this.parentElement.style.animation='toastOut 0.3s ease forwards';setTimeout(()=>this.parentElement.remove(),300)"
                                style="margin-left:auto;background:none;border:none;color:#6ee7b7;cursor:pointer;font-size:1rem;padding:4px;">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 flex items-center gap-4 p-4 rounded-2xl"
                         style="background:#fff1f2;border:1px solid #fecdd3;color:#881337;"
                         id="flash-error">
                        <span style="width:38px;height:38px;border-radius:12px;background:#ffe4e6;color:#e11d48;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0;">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </span>
                        <div>
                            <p style="font-weight:700;font-size:0.85rem;">Terjadi Kesalahan!</p>
                            <p style="font-size:0.8rem;opacity:0.75;margin-top:1px;">{{ session('error') }}</p>
                        </div>
                        <button onclick="this.parentElement.style.animation='toastOut 0.3s ease forwards';setTimeout(()=>this.parentElement.remove(),300)"
                                style="margin-left:auto;background:none;border:none;color:#fda4af;cursor:pointer;font-size:1rem;padding:4px;">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>

    <!-- Mobile overlay -->
    <div x-show="sidebarOpen" x-cloak
         @click="sidebarOpen = false"
         style="position:fixed;inset:0;background:rgba(0,0,0,0.6);backdrop-filter:blur(4px);z-index:30;"
         class="lg:hidden"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
    </div>

    <script>
        // ── Page progress bar ──
        window.addEventListener('load', () => {
            const bar = document.getElementById('page-progress');
            bar.style.width = '100%';
            setTimeout(() => { bar.style.opacity = '0'; }, 400);
        });

        // ── Auto-dismiss flash messages ──
        ['flash-success', 'flash-error'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                setTimeout(() => {
                    el.style.transition = 'all 0.4s ease';
                    el.style.opacity = '0';
                    el.style.transform = 'translateY(-8px)';
                    setTimeout(() => el.remove(), 400);
                }, 4000);
            }
        });

        // ── Global toast helper ──
        function showToast(type, title, message) {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            const icon = type === 'success'
                ? '<i class="fa-solid fa-circle-check"></i>'
                : '<i class="fa-solid fa-triangle-exclamation"></i>';
            toast.innerHTML = `
                <div class="toast-icon">${icon}</div>
                <div class="toast-body">
                    <div class="toast-title">${title}</div>
                    ${message ? `<div class="toast-msg">${message}</div>` : ''}
                </div>
                <button class="toast-close" onclick="dismissToast(this.parentElement)">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            `;
            container.appendChild(toast);
            setTimeout(() => dismissToast(toast), 5000);
        }

        function dismissToast(toast) {
            toast.classList.add('toast-out');
            setTimeout(() => toast.remove(), 300);
        }
    </script>

    @stack('scripts')
</body>
</html>
