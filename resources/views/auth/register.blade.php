<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" id="registerForm" novalidate>
        @csrf

        {{-- Name Field --}}
        <div style="margin-bottom: 1.1rem;">
            <label class="field-label" for="name">
                <i class="fa-solid fa-user" style="margin-right:0.3em;"></i> Nama Lengkap
            </label>
            <div class="field-wrap" style="margin-bottom:0;">
                <i class="fa-solid fa-id-card field-icon"></i>
                <input
                    id="name"
                    class="field-input @error('name') border-red-500 @enderror"
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    placeholder="Nama Anda"
                    required
                    autofocus
                    autocomplete="name"
                >
            </div>
            @error('name')
                <div class="field-error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Email Field --}}
        <div style="margin-bottom: 1.1rem;">
            <label class="field-label" for="email">
                <i class="fa-solid fa-envelope" style="margin-right:0.3em;"></i> Alamat Email
            </label>
            <div class="field-wrap" style="margin-bottom:0;">
                <i class="fa-solid fa-at field-icon"></i>
                <input
                    id="email"
                    class="field-input @error('email') border-red-500 @enderror"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="nama@email.com"
                    required
                    autocomplete="username"
                >
            </div>
            @error('email')
                <div class="field-error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Password Field --}}
        <div style="margin-bottom: 1.1rem;">
            <label class="field-label" for="password">
                <i class="fa-solid fa-lock" style="margin-right:0.3em;"></i> Password
            </label>
            <div class="field-wrap" style="margin-bottom:0;">
                <i class="fa-solid fa-lock field-icon"></i>
                <input
                    id="password"
                    class="field-input @error('password') border-red-500 @enderror"
                    type="password"
                    name="password"
                    placeholder="••••••••"
                    required
                    autocomplete="new-password"
                    style="padding-right: 2.8rem;"
                >
                <button type="button" class="pass-toggle" onclick="togglePassword('password', this)" tabindex="-1">
                    <i class="fa-solid fa-eye"></i>
                </button>
            </div>
            @error('password')
                <div class="field-error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Confirm Password Field --}}
        <div style="margin-bottom: 1.5rem;">
            <label class="field-label" for="password_confirmation">
                <i class="fa-solid fa-lock" style="margin-right:0.3em;"></i> Konfirmasi Password
            </label>
            <div class="field-wrap" style="margin-bottom:0;">
                <i class="fa-solid fa-check-double field-icon"></i>
                <input
                    id="password_confirmation"
                    class="field-input @error('password_confirmation') border-red-500 @enderror"
                    type="password"
                    name="password_confirmation"
                    placeholder="••••••••"
                    required
                    autocomplete="new-password"
                    style="padding-right: 2.8rem;"
                >
                <button type="button" class="pass-toggle" onclick="togglePassword('password_confirmation', this)" tabindex="-1">
                    <i class="fa-solid fa-eye"></i>
                </button>
            </div>
            @error('password_confirmation')
                <div class="field-error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn-submit" id="submitBtn">
            <span class="btn-submit-text">
                <i class="fa-solid fa-user-plus btn-submit-icon"></i>
                Daftar Akun Baru
            </span>
        </button>
    </form>

    <script>
        const form = document.getElementById('registerForm');
        const submitBtn = document.getElementById('submitBtn');

        form.addEventListener('submit', function() {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin btn-submit-icon"></i> Memproses...';
            submitBtn.style.opacity = '0.8';
        });

        document.querySelectorAll('.field-input').forEach(inp => {
            if (inp.classList.contains('border-red-500')) {
                @if($errors->any())
                inp.style.animation = 'shake 0.4s ease';
                setTimeout(() => inp.style.animation = '', 400);
                @endif
            }
        });
    </script>

    <style>
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%       { transform: translateX(-6px); }
            40%       { transform: translateX(6px); }
            60%       { transform: translateX(-4px); }
            80%       { transform: translateX(4px); }
        }
        .border-red-500 {
            border-color: rgba(248,113,113,0.6) !important;
            box-shadow: 0 0 0 3px rgba(248,113,113,0.1) !important;
        }
    </style>
</x-guest-layout>
