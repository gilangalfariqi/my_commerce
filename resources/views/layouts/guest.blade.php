<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'MyCommerce') }} — Login</title>

        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@500;600;700;800;900&display=swap" rel="stylesheet">

        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            * { box-sizing: border-box; margin: 0; padding: 0; }

            body {
                font-family: 'Plus Jakarta Sans', sans-serif;
                min-height: 100vh;
                background: #050A18;
                overflow: hidden;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            /* ── Animated gradient background ── */
            .bg-mesh {
                position: fixed;
                inset: 0;
                z-index: 0;
                overflow: hidden;
            }
            .bg-mesh::before {
                content: '';
                position: absolute;
                width: 120vw; height: 120vh;
                top: -10vh; left: -10vw;
                background: radial-gradient(ellipse 80% 60% at 20% 30%, rgba(124,58,237,0.35) 0%, transparent 60%),
                            radial-gradient(ellipse 70% 70% at 80% 70%, rgba(79,70,229,0.25) 0%, transparent 60%),
                            radial-gradient(ellipse 60% 60% at 50% 50%, rgba(16,185,129,0.08) 0%, transparent 60%);
                animation: bgShift 12s ease-in-out infinite alternate;
            }
            .bg-mesh::after {
                content: '';
                position: absolute;
                inset: 0;
                background: radial-gradient(ellipse 50% 50% at 70% 20%, rgba(236,72,153,0.12) 0%, transparent 60%),
                            radial-gradient(ellipse 40% 40% at 30% 80%, rgba(6,182,212,0.10) 0%, transparent 60%);
                animation: bgShift 15s ease-in-out infinite alternate-reverse;
            }

            @keyframes bgShift {
                0%   { transform: translate(0, 0) scale(1); }
                100% { transform: translate(3%, 3%) scale(1.06); }
            }

            /* ── Grid overlay ── */
            .bg-grid {
                position: fixed;
                inset: 0;
                z-index: 0;
                background-image:
                    linear-gradient(rgba(255,255,255,0.025) 1px, transparent 1px),
                    linear-gradient(90deg, rgba(255,255,255,0.025) 1px, transparent 1px);
                background-size: 60px 60px;
            }

            /* ── Floating orbs ── */
            .orb {
                position: fixed;
                border-radius: 50%;
                filter: blur(80px);
                opacity: 0.18;
                pointer-events: none;
                z-index: 0;
                animation: orbFloat 20s ease-in-out infinite;
            }
            .orb-1 { width: 500px; height: 500px; background: #7c3aed; top: -200px; left: -100px; animation-delay: 0s; }
            .orb-2 { width: 400px; height: 400px; background: #4f46e5; bottom: -150px; right: -100px; animation-delay: -6s; }
            .orb-3 { width: 300px; height: 300px; background: #06b6d4; top: 60%; left: 60%; animation-delay: -12s; }

            @keyframes orbFloat {
                0%, 100% { transform: translate(0, 0) scale(1); }
                33%       { transform: translate(20px, -30px) scale(1.05); }
                66%       { transform: translate(-15px, 20px) scale(0.95); }
            }

            /* ── Floating particles ── */
            .particle-field {
                position: fixed;
                inset: 0;
                z-index: 0;
                pointer-events: none;
                overflow: hidden;
            }
            .particle {
                position: absolute;
                border-radius: 50%;
                background: rgba(255,255,255,0.6);
                animation: particleRise linear infinite;
            }

            @keyframes particleRise {
                0%   { transform: translateY(110vh) scale(0); opacity: 0; }
                10%  { opacity: 1; }
                90%  { opacity: 0.8; }
                100% { transform: translateY(-10vh) scale(1); opacity: 0; }
            }

            /* ── Card ── */
            .login-card {
                position: relative;
                z-index: 10;
                width: 100%;
                max-width: 440px;
                margin: 1rem;
                background: rgba(255,255,255,0.04);
                border: 1px solid rgba(255,255,255,0.10);
                border-radius: 28px;
                padding: 2.5rem;
                backdrop-filter: blur(30px) saturate(160%);
                -webkit-backdrop-filter: blur(30px) saturate(160%);
                box-shadow:
                    0 0 0 1px rgba(255,255,255,0.05) inset,
                    0 40px 80px rgba(0,0,0,0.5),
                    0 0 80px rgba(124,58,237,0.12);
                animation: cardEntrance 0.7s cubic-bezier(0.16, 1, 0.3, 1) both;
            }

            @keyframes cardEntrance {
                from { opacity: 0; transform: translateY(40px) scale(0.96); }
                to   { opacity: 1; transform: translateY(0) scale(1); }
            }

            /* ── Card shine sweep ── */
            .card-shine {
                position: absolute;
                inset: 0;
                border-radius: 28px;
                overflow: hidden;
                pointer-events: none;
            }
            .card-shine::before {
                content: '';
                position: absolute;
                top: -60%;
                left: -60%;
                width: 120%;
                height: 60%;
                background: linear-gradient(135deg, rgba(255,255,255,0) 40%, rgba(255,255,255,0.05) 50%, rgba(255,255,255,0) 60%);
                animation: shineSweep 5s ease-in-out infinite;
            }
            @keyframes shineSweep {
                0%   { top: -60%; left: -60%; }
                100% { top: 120%; left: 120%; }
            }

            /* ── Logo ── */
            .logo-wrap {
                display: flex;
                flex-direction: column;
                align-items: center;
                margin-bottom: 2rem;
            }
            .logo-icon {
                width: 64px; height: 64px;
                border-radius: 20px;
                background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%);
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.6rem;
                color: #fff;
                box-shadow: 0 8px 32px rgba(124,58,237,0.5), 0 0 0 1px rgba(255,255,255,0.1) inset;
                margin-bottom: 1rem;
                animation: logoFloat 4s ease-in-out infinite;
            }
            @keyframes logoFloat {
                0%, 100% { transform: translateY(0); }
                50%       { transform: translateY(-6px); }
            }
            .logo-title {
                font-family: 'Outfit', sans-serif;
                font-size: 1.5rem;
                font-weight: 800;
                color: #fff;
                letter-spacing: -0.5px;
                background: linear-gradient(135deg, #fff 30%, #a78bfa);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }
            .logo-subtitle {
                font-size: 0.78rem;
                color: rgba(255,255,255,0.4);
                margin-top: 0.2rem;
                letter-spacing: 0.05em;
                font-weight: 500;
            }

            /* ── Form labels ── */
            .field-label {
                display: block;
                font-size: 0.78rem;
                font-weight: 600;
                color: rgba(255,255,255,0.55);
                margin-bottom: 0.45rem;
                letter-spacing: 0.04em;
                text-transform: uppercase;
            }

            /* ── Inputs ── */
            .field-wrap {
                position: relative;
                margin-bottom: 1.1rem;
            }
            .field-icon {
                position: absolute;
                left: 14px;
                top: 50%;
                transform: translateY(-50%);
                color: rgba(255,255,255,0.3);
                font-size: 0.9rem;
                pointer-events: none;
                transition: color 0.3s;
            }
            .field-wrap:focus-within .field-icon {
                color: #a78bfa;
            }
            .field-input {
                width: 100%;
                padding: 0.8rem 1rem 0.8rem 2.6rem;
                background: rgba(255,255,255,0.06);
                border: 1px solid rgba(255,255,255,0.10);
                border-radius: 14px;
                color: #fff;
                font-family: 'Plus Jakarta Sans', sans-serif;
                font-size: 0.9rem;
                font-weight: 500;
                outline: none;
                transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
            }
            .field-input::placeholder {
                color: rgba(255,255,255,0.2);
            }
            .field-input:focus {
                background: rgba(124,58,237,0.12);
                border-color: rgba(167,139,250,0.6);
                box-shadow: 0 0 0 3px rgba(124,58,237,0.15), 0 0 20px rgba(124,58,237,0.08);
            }
            .field-input:-webkit-autofill,
            .field-input:-webkit-autofill:hover,
            .field-input:-webkit-autofill:focus {
                -webkit-text-fill-color: #fff;
                -webkit-box-shadow: 0 0 0px 1000px rgba(30,15,60,0.95) inset;
                transition: background-color 5000s ease-in-out 0s;
            }

            /* ── Password toggle ── */
            .pass-toggle {
                position: absolute;
                right: 14px;
                top: 50%;
                transform: translateY(-50%);
                color: rgba(255,255,255,0.3);
                cursor: pointer;
                font-size: 0.9rem;
                transition: color 0.2s;
                background: none;
                border: none;
                padding: 4px;
            }
            .pass-toggle:hover { color: rgba(255,255,255,0.7); }

            /* ── Divider ── */
            .section-divider {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                margin: 1.25rem 0;
            }
            .section-divider::before,
            .section-divider::after {
                content: '';
                flex: 1;
                height: 1px;
                background: rgba(255,255,255,0.08);
            }

            /* ── Remember + Forgot row ── */
            .remember-row {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 1.5rem;
            }
            .remember-label {
                display: flex;
                align-items: center;
                gap: 0.5rem;
                color: rgba(255,255,255,0.5);
                font-size: 0.8rem;
                cursor: pointer;
                user-select: none;
            }
            .remember-label input[type="checkbox"] {
                appearance: none;
                -webkit-appearance: none;
                width: 16px; height: 16px;
                border: 1.5px solid rgba(255,255,255,0.2);
                border-radius: 5px;
                background: transparent;
                cursor: pointer;
                position: relative;
                transition: all 0.2s;
            }
            .remember-label input[type="checkbox"]:checked {
                background: linear-gradient(135deg, #7c3aed, #4f46e5);
                border-color: transparent;
            }
            .remember-label input[type="checkbox"]:checked::after {
                content: '';
                position: absolute;
                left: 4px; top: 2px;
                width: 5px; height: 8px;
                border: 2px solid #fff;
                border-top: none;
                border-left: none;
                transform: rotate(45deg);
            }
            .forgot-link {
                font-size: 0.8rem;
                color: #a78bfa;
                text-decoration: none;
                font-weight: 600;
                transition: color 0.2s;
            }
            .forgot-link:hover { color: #c4b5fd; text-decoration: underline; }

            /* ── Submit button ── */
            .btn-submit {
                width: 100%;
                padding: 0.9rem 1.5rem;
                background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%);
                border: none;
                border-radius: 14px;
                color: #fff;
                font-family: 'Plus Jakarta Sans', sans-serif;
                font-size: 0.95rem;
                font-weight: 700;
                cursor: pointer;
                position: relative;
                overflow: hidden;
                transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
                box-shadow: 0 8px 24px rgba(124,58,237,0.4), 0 0 0 1px rgba(255,255,255,0.1) inset;
                letter-spacing: 0.02em;
            }
            .btn-submit::before {
                content: '';
                position: absolute;
                top: 0; left: -100%;
                width: 100%; height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
                transition: left 0.5s ease;
            }
            .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 16px 40px rgba(124,58,237,0.5), 0 0 0 1px rgba(255,255,255,0.15) inset; }
            .btn-submit:hover::before { left: 100%; }
            .btn-submit:active { transform: translateY(0); box-shadow: 0 4px 12px rgba(124,58,237,0.4); }

            .btn-submit-icon { margin-right: 0.5rem; }
            .btn-submit-text { display: inline; }

            /* ── Error message ── */
            .field-error {
                color: #f87171;
                font-size: 0.75rem;
                margin-top: 0.35rem;
                font-weight: 500;
                display: flex;
                align-items: center;
                gap: 0.3rem;
            }

            /* ── Session alert ── */
            .session-alert {
                background: rgba(16,185,129,0.12);
                border: 1px solid rgba(16,185,129,0.25);
                border-radius: 12px;
                color: #6ee7b7;
                font-size: 0.82rem;
                font-weight: 600;
                padding: 0.7rem 1rem;
                margin-bottom: 1.25rem;
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }

            /* ── Register link ── */
            .register-row {
                text-align: center;
                margin-top: 1.5rem;
                font-size: 0.82rem;
                color: rgba(255,255,255,0.35);
            }
            .register-row a {
                color: #a78bfa;
                font-weight: 700;
                text-decoration: none;
                transition: color 0.2s;
            }
            .register-row a:hover { color: #c4b5fd; }

            /* ── Responsive ── */
            @media (max-width: 480px) {
                body { overflow-y: auto; align-items: flex-start; padding: 1.5rem 0; }
                .login-card { border-radius: 20px; padding: 2rem 1.5rem; }
                .logo-icon { width: 54px; height: 54px; font-size: 1.3rem; }
            }
        </style>
    </head>
    <body>
        <!-- Animated Background -->
        <div class="bg-mesh"></div>
        <div class="bg-grid"></div>
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>

        <!-- Particle Field -->
        <div class="particle-field" id="particleField"></div>

        <!-- Login Card -->
        <div class="login-card">
            <div class="card-shine"></div>

            <!-- Logo / Brand -->
            <div class="logo-wrap">
                <div class="logo-icon">
                    <i class="fa-solid fa-store"></i>
                </div>
                <div class="logo-title">MyCommerce</div>
                <div class="logo-subtitle">Admin Control Panel</div>
            </div>

            {{ $slot }}

            <!-- Register link (optional) -->
            @if (Route::has('register'))
            <div class="register-row">
                Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a>
            </div>
            @endif
        </div>

        <script>
            // ── Generate floating particles ──
            const field = document.getElementById('particleField');
            const count = 28;
            for (let i = 0; i < count; i++) {
                const p = document.createElement('span');
                p.className = 'particle';
                const size = Math.random() * 3 + 1;
                const left = Math.random() * 100;
                const duration = Math.random() * 18 + 12;
                const delay = Math.random() * 20;
                p.style.cssText = `
                    width:${size}px; height:${size}px;
                    left:${left}%;
                    animation-duration:${duration}s;
                    animation-delay:-${delay}s;
                    opacity:${Math.random() * 0.5 + 0.2};
                `;
                field.appendChild(p);
            }

            // ── Password visibility toggle ──
            function togglePassword(id, btn) {
                const inp = document.getElementById(id);
                const icon = btn.querySelector('i');
                if (inp.type === 'password') {
                    inp.type = 'text';
                    icon.className = 'fa-solid fa-eye-slash';
                } else {
                    inp.type = 'password';
                    icon.className = 'fa-solid fa-eye';
                }
            }
        </script>
    </body>
</html>
