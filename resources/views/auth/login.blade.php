<x-guest-layout>
    {{-- Session Status --}}
    @if (session('status'))
        <div class="session-alert">
            <i class="fa-solid fa-circle-check"></i>
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" id="loginForm" novalidate>
        @csrf

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
                    autofocus
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
        <div style="margin-bottom: 0.5rem;">
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
                    autocomplete="current-password"
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

        {{-- Remember & Forgot --}}
        <div class="remember-row">
            <label class="remember-label" for="remember_me">
                <input type="checkbox" id="remember_me" name="remember">
                <span>Ingat Saya</span>
            </label>
            @if (Route::has('password.request'))
                <a class="forgot-link" href="{{ route('password.request') }}">
                    Lupa password?
                </a>
            @endif
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn-submit" id="submitBtn">
            <span class="btn-submit-text">
                <i class="fa-solid fa-right-to-bracket btn-submit-icon"></i>
                Masuk ke Dashboard
            </span>
        </button>
    </form>

    <script>
        // Loading state on submit
        const form = document.getElementById('loginForm');
        const submitBtn = document.getElementById('submitBtn');

        form.addEventListener('submit', function() {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin btn-submit-icon"></i> Memverifikasi...';
            submitBtn.style.opacity = '0.8';
        });

        // Input shake animation on error
        document.querySelectorAll('.field-input').forEach(inp => {
            if (inp.classList.contains('border-red-500') || inp.value === '') {
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
