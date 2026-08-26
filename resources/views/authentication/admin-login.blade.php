<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Login — Stock Shield</title>
    <link rel="icon" type="image/png" href="{{ asset('backend/assets/img/favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300..800&family=SUSE:wght@100..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --ink: #0b1220;
            --blue: #3b82f6;
            --blue-deep: #1d4ed8;
            --violet: #8b5cf6;
            --cyan: #22d3ee;
            --glass: rgba(255, 255, 255, 0.08);
            --glass-border: rgba(255, 255, 255, 0.18);
        }

        *, *::before, *::after { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Open Sans", sans-serif;
            background: var(--ink);
            color: #fff;
            overflow-x: hidden;
        }

        .scene {
            min-height: 100vh;
            position: relative;
            display: flex;
            flex-direction: column;
        }

        /* ── Background layers ── */
        .scene-bg {
            position: fixed;
            inset: 0;
            z-index: 0;
        }

        .scene-bg img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scale(1.06);
            animation: kenBurns 28s ease-in-out infinite alternate;
        }

        @keyframes kenBurns {
            from { transform: scale(1.06) translate(0, 0); }
            to   { transform: scale(1.12) translate(-1%, -1%); }
        }

        .scene-overlay {
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 80% 60% at 20% 30%, rgba(59, 130, 246, 0.45) 0%, transparent 55%),
                radial-gradient(ellipse 70% 50% at 85% 75%, rgba(139, 92, 246, 0.35) 0%, transparent 50%),
                radial-gradient(ellipse 50% 40% at 60% 10%, rgba(34, 211, 238, 0.2) 0%, transparent 45%),
                linear-gradient(145deg, rgba(7, 12, 24, 0.92) 0%, rgba(15, 30, 70, 0.88) 50%, rgba(10, 15, 35, 0.94) 100%);
        }

        .grid-overlay {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 48px 48px;
            mask-image: radial-gradient(ellipse 80% 70% at 50% 50%, #000 20%, transparent 75%);
        }

        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.55;
            animation: floatOrb 14s ease-in-out infinite;
        }

        .orb-1 { width: 420px; height: 420px; background: #2563eb; top: -8%; left: -5%; animation-delay: 0s; }
        .orb-2 { width: 320px; height: 320px; background: #7c3aed; bottom: 5%; left: 25%; animation-delay: -4s; }
        .orb-3 { width: 280px; height: 280px; background: #0891b2; top: 40%; right: -4%; animation-delay: -7s; }

        @keyframes floatOrb {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33%       { transform: translate(18px, -22px) scale(1.05); }
            66%       { transform: translate(-14px, 16px) scale(0.96); }
        }

        /* ── Layout ── */
        .scene-inner {
            position: relative;
            z-index: 2;
            flex: 1;
            display: grid;
            grid-template-columns: 1.05fr 0.95fr;
            min-height: calc(100vh - 52px);
            max-width: 1280px;
            margin: 0 auto;
            width: 100%;
            padding: 0 1.5rem;
        }

        @media (max-width: 991.98px) {
            .scene-inner { grid-template-columns: 1fr; }
            .brand-col { display: none; }
        }

        /* ── Brand column ── */
        .brand-col {
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 3rem 2rem 3rem 0;
        }

        .brand-logo {
            display: inline-flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2.75rem;
            animation: fadeUp 0.7s ease both;
        }

        .brand-logo-mark {
            width: 56px;
            height: 56px;
            border-radius: 1rem;
            background: linear-gradient(135deg, rgba(255,255,255,0.2), rgba(255,255,255,0.05));
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(12px);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 32px rgba(0,0,0,0.2);
        }

        .brand-logo-mark img { height: 32px; }

        .brand-logo-text h1 {
            font-family: "SUSE", sans-serif;
            font-weight: 800;
            font-size: 1.5rem;
            letter-spacing: 0.06em;
            margin: 0;
        }

        .brand-logo-text p {
            margin: 0.15rem 0 0;
            font-size: 0.72rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            opacity: 0.65;
        }

        .brand-headline {
            font-family: "SUSE", sans-serif;
            font-weight: 700;
            font-size: clamp(2rem, 4vw, 3rem);
            line-height: 1.12;
            letter-spacing: -0.03em;
            margin: 0 0 1.25rem;
            max-width: 480px;
            animation: fadeUp 0.7s ease 0.1s both;
        }

        .brand-headline .gradient-text {
            background: linear-gradient(120deg, #93c5fd 0%, #c4b5fd 45%, #67e8f9 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .brand-sub {
            color: rgba(255, 255, 255, 0.72);
            font-size: 1rem;
            line-height: 1.7;
            max-width: 460px;
            margin: 0 0 2.25rem;
            animation: fadeUp 0.7s ease 0.18s both;
        }

        .stat-row {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-bottom: 2.5rem;
            animation: fadeUp 0.7s ease 0.26s both;
        }

        .stat-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.55rem 1rem;
            border-radius: 2rem;
            background: var(--glass);
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(16px);
            font-size: 0.78rem;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.9);
        }

        .stat-pill i { color: #93c5fd; font-size: 0.95rem; }

        .feature-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.85rem;
            max-width: 480px;
        }

        .feature-card {
            padding: 1.1rem 1.15rem;
            border-radius: 1.125rem;
            background: var(--glass);
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(20px);
            transition: transform 0.25s ease, border-color 0.25s ease, background 0.25s ease;
            animation: fadeUp 0.7s ease both;
        }

        .feature-card:nth-child(1) { animation-delay: 0.32s; }
        .feature-card:nth-child(2) { animation-delay: 0.38s; }
        .feature-card:nth-child(3) { animation-delay: 0.44s; }
        .feature-card:nth-child(4) { animation-delay: 0.5s; }

        .feature-card:hover {
            transform: translateY(-3px);
            border-color: rgba(147, 197, 253, 0.35);
            background: rgba(255, 255, 255, 0.12);
        }

        .feature-card-icon {
            width: 38px;
            height: 38px;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.05rem;
            margin-bottom: 0.65rem;
        }

        .feature-card:nth-child(1) .feature-card-icon { background: rgba(59,130,246,0.25); color: #93c5fd; }
        .feature-card:nth-child(2) .feature-card-icon { background: rgba(34,197,94,0.22); color: #86efac; }
        .feature-card:nth-child(3) .feature-card-icon { background: rgba(168,85,247,0.22); color: #d8b4fe; }
        .feature-card:nth-child(4) .feature-card-icon { background: rgba(34,211,238,0.2); color: #67e8f9; }

        .feature-card h4 {
            font-family: "SUSE", sans-serif;
            font-size: 0.88rem;
            font-weight: 700;
            margin: 0 0 0.25rem;
        }

        .feature-card p {
            margin: 0;
            font-size: 0.72rem;
            color: rgba(255, 255, 255, 0.6);
            line-height: 1.45;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── Form column ── */
        .form-col {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2.5rem 0;
        }

        .login-glass {
            width: 100%;
            max-width: 440px;
            position: relative;
            animation: fadeUp 0.75s ease 0.15s both;
        }

        .login-glass::before {
            content: '';
            position: absolute;
            inset: -1px;
            border-radius: 1.75rem;
            padding: 1px;
            background: linear-gradient(135deg, rgba(147,197,253,0.5), rgba(139,92,246,0.3), rgba(34,211,238,0.2));
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            pointer-events: none;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.97);
            border-radius: 1.75rem;
            padding: 2.5rem 2.25rem 2.25rem;
            box-shadow:
                0 4px 6px rgba(0, 0, 0, 0.04),
                0 24px 80px rgba(0, 0, 0, 0.35),
                inset 0 1px 0 rgba(255, 255, 255, 0.8);
        }

        .mobile-logo {
            display: none;
            align-items: center;
            gap: 0.65rem;
            margin-bottom: 1.75rem;
        }

        .mobile-logo img { height: 34px; }

        .mobile-logo span {
            font-family: "SUSE", sans-serif;
            font-weight: 800;
            font-size: 1.15rem;
            background: linear-gradient(135deg, #1e40af, #6366f1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        @media (max-width: 991.98px) {
            .mobile-logo { display: flex; }
            .form-col { align-items: flex-start; padding-top: 2rem; }
        }

        .card-top-icon {
            width: 52px;
            height: 52px;
            border-radius: 1rem;
            margin: 0 auto 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            color: #fff;
            background: linear-gradient(135deg, #2563eb 0%, #6366f1 50%, #0891b2 100%);
            box-shadow: 0 8px 24px rgba(37, 99, 235, 0.4);
        }

        .card-head {
            text-align: center;
            margin-bottom: 1.85rem;
        }

        .card-head h2 {
            font-family: "SUSE", sans-serif;
            font-weight: 700;
            font-size: 1.65rem;
            color: #0f172a;
            margin: 0 0 0.4rem;
            letter-spacing: -0.02em;
        }

        .card-head p {
            margin: 0;
            color: #64748b;
            font-size: 0.875rem;
        }

        .alert-error {
            display: flex;
            align-items: flex-start;
            gap: 0.6rem;
            padding: 0.85rem 1rem;
            border-radius: 0.875rem;
            background: linear-gradient(135deg, #fef2f2, #fff1f2);
            border: 1px solid #fecaca;
            color: #991b1b;
            font-size: 0.84rem;
            margin-bottom: 1.35rem;
        }

        .alert-error i { font-size: 1rem; margin-top: 0.1rem; flex-shrink: 0; }

        .field { margin-bottom: 1.2rem; }

        .field label {
            display: block;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #64748b;
            margin-bottom: 0.45rem;
        }

        .input-shell {
            position: relative;
        }

        .input-shell .ico {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1rem;
            transition: color 0.2s;
            pointer-events: none;
        }

        .input-shell input {
            width: 100%;
            padding: 0.85rem 3rem 0.85rem 2.75rem;
            border-radius: 0.875rem;
            border: 1.5px solid #e2e8f0;
            background: #f8fafc;
            font-size: 0.9rem;
            color: #0f172a;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        }

        .input-shell input::placeholder { color: #94a3b8; }

        .input-shell input:focus {
            outline: none;
            border-color: #6366f1;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.12);
        }

        .input-shell:focus-within .ico { color: #6366f1; }

        .input-shell input.is-invalid {
            border-color: #ef4444;
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
        }

        .eye-btn {
            position: absolute;
            right: 0.7rem;
            top: 50%;
            transform: translateY(-50%);
            width: 34px;
            height: 34px;
            border: none;
            border-radius: 0.55rem;
            background: #e2e8f0;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.15s, color 0.15s;
        }

        .eye-btn:hover { background: #cbd5e1; color: #334155; }

        .field-error {
            display: block;
            font-size: 0.76rem;
            color: #dc2626;
            margin-top: 0.35rem;
        }

        .form-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            flex-wrap: wrap;
            margin: 0.25rem 0 1.5rem;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.82rem;
            color: #475569;
            cursor: pointer;
            user-select: none;
        }

        .remember input { width: 16px; height: 16px; accent-color: #6366f1; }

        .forgot-link {
            font-size: 0.82rem;
            font-weight: 600;
            color: #6366f1;
            text-decoration: none;
            transition: color 0.15s;
        }

        .forgot-link:hover { color: #4f46e5; }

        .btn-signin {
            width: 100%;
            padding: 0.9rem 1.25rem;
            border: none;
            border-radius: 0.875rem;
            font-size: 0.95rem;
            font-weight: 700;
            color: #fff;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            background: linear-gradient(135deg, #1d4ed8 0%, #4f46e5 45%, #0891b2 100%);
            background-size: 200% 200%;
            box-shadow: 0 8px 28px rgba(79, 70, 229, 0.4);
            transition: transform 0.2s, box-shadow 0.2s, background-position 0.4s;
        }

        .btn-signin:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 36px rgba(79, 70, 229, 0.5);
            background-position: 100% 50%;
        }

        .btn-signin:active { transform: translateY(0); }

        .trust-line {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            margin-top: 1.35rem;
            font-size: 0.72rem;
            color: #94a3b8;
        }

        .trust-line i { color: #22c55e; }

        .scene-footer {
            position: relative;
            z-index: 2;
            text-align: center;
            padding: 0.85rem 1rem 1.25rem;
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.45);
        }

        .scene-footer a {
            color: rgba(255, 255, 255, 0.75);
            text-decoration: none;
        }

        .scene-footer a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="scene">
    <div class="scene-bg">
        <img src="{{ asset('backend/assets/img/background-image/backgorund-image-13.jpg') }}" alt="">
        <div class="scene-overlay"></div>
        <div class="grid-overlay"></div>
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
                                                                </div>
                                                            
    <div class="scene-inner">
        <div class="brand-col">
            <div class="brand-logo">
                <div class="brand-logo-mark">
                    <img src="{{ asset('backend/assets/img/logo-light.svg') }}" alt="">
                </div>
                <div class="brand-logo-text">
                    <h1>STOCK SHIELD</h1>
                    <p>Inventory Platform</p>
                </div>
            </div>

            <h2 class="brand-headline">
                Your stores.<br>
                <span class="gradient-text">One shield.</span><br>
                Total control.
            </h2>

            <p class="brand-sub">
                Enterprise-grade stock management with multi-store workflows,
                approval gates, and real-time visibility — built for teams that move fast.
            </p>

            <div class="stat-row">
                <span class="stat-pill"><i class="bi bi-shield-check"></i> Secure access</span>
                <span class="stat-pill"><i class="bi bi-lightning-charge"></i> Real-time sync</span>
                <span class="stat-pill"><i class="bi bi-building"></i> Multi-store</span>
            </div>

            <div class="feature-grid">
                <div class="feature-card">
                    <div class="feature-card-icon"><i class="bi bi-box-seam"></i></div>
                    <h4>Stock Control</h4>
                    <p>Batches, expiry dates &amp; barcode tracking</p>
                </div>
                <div class="feature-card">
                    <div class="feature-card-icon"><i class="bi bi-check2-square"></i></div>
                    <h4>Approvals</h4>
                    <p>Review before inventory goes live</p>
                </div>
                <div class="feature-card">
                    <div class="feature-card-icon"><i class="bi bi-arrow-left-right"></i></div>
                    <h4>Requisitions</h4>
                    <p>Request, issue &amp; return workflows</p>
                </div>
                <div class="feature-card">
                    <div class="feature-card-icon"><i class="bi bi-bar-chart-line"></i></div>
                    <h4>Reports</h4>
                    <p>Insights across stores &amp; departments</p>
                </div>
            </div>
        </div>

        <div class="form-col">
            <div class="login-glass">
                <div class="login-card">
                    <div class="mobile-logo">
                        <img src="{{ asset('backend/assets/img/logo.svg') }}" alt="">
                        <span>Stock Shield</span>
                </div>

                    <div class="card-top-icon">
                        <i class="bi bi-shield-lock-fill"></i>
            </div>

                    <div class="card-head">
                        <h2>Welcome back</h2>
                        <p>Sign in to access your dashboard</p>
                </div>

                    @if (session('login_error_message'))
                        <div class="alert-error">
                            <i class="bi bi-exclamation-octagon-fill"></i>
                            <span>{{ session('login_error_message') }}</span>
            </div>
                    @endif

                    <form action="{{ route('authentication-process') }}" method="POST" autocomplete="on">
                        @csrf

                        <div class="field">
                            <label for="email">Email address</label>
                            <div class="input-shell">
                                <i class="bi bi-envelope ico"></i>
                                <input type="email"
                                       id="email"
                                       name="email"
                                       value="{{ old('email') }}"
                                       placeholder="name@company.com"
                                       autofocus
                                       class="@error('email') is-invalid @enderror">
                </div>
                            @error('email') <span class="field-error">{{ $message }}</span> @enderror
            </div>

                        <div class="field">
                            <label for="password">Password</label>
                            <div class="input-shell">
                                <i class="bi bi-key ico"></i>
                                <input type="password"
                                       id="password"
                                       name="password"
                                       placeholder="••••••••"
                                       class="@error('password') is-invalid @enderror">
                                <button type="button" class="eye-btn" id="togglePassword" aria-label="Show password">
                                    <i class="bi bi-eye"></i>
                                </button>
                </div>
                            @error('password') <span class="field-error">{{ $message }}</span> @enderror
            </div>

                        <div class="form-row">
                            <label class="remember">
                                <input type="checkbox" name="rememberme" id="rememberme" value="1">
                                Remember me
                            </label>
                            <a href="{{ route('forgot-password') }}" class="forgot-link">Forgot password?</a>
                </div>

                        <button type="submit" class="btn-signin">
                            Sign in to Stock Shield
                            <i class="bi bi-arrow-right"></i>
                        </button>
                    </form>

                    <div class="trust-line">
                        <i class="bi bi-lock-fill"></i>
                        Encrypted &amp; role-protected access
            </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="scene-footer">
        &copy; {{ date('Y') }} <a href="#" target="_blank">Speedlines Technology</a>. All rights reserved.
    </footer>
</div>

<script>
    document.getElementById('togglePassword')?.addEventListener('click', function () {
        const input = document.getElementById('password');
        const icon = this.querySelector('i');
        const show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        icon.classList.toggle('bi-eye', !show);
        icon.classList.toggle('bi-eye-slash', show);
    });
</script>
    </body>
</html>
