@php
    $initials = strtoupper(collect(explode(' ', Auth::user()->name))->filter()->take(2)->map(fn ($w) => $w[0])->join(''));
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Choose Store - Stock Shield</title>
    <link rel="icon" type="image/png" href="{{ asset('backend/assets/img/favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300..800&family=SUSE:wght@100..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css">
    <style>
        :root {
            --cs-bg: #0f172a;
            --cs-accent: #2563eb;
            --cs-accent-dark: #1e40af;
        }

        body {
            font-family: "Open Sans", sans-serif;
            min-height: 100vh;
            margin: 0;
            background: var(--cs-bg);
            color: #0f172a;
        }

        .cs-page {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
        }

        .cs-bg {
            position: fixed;
            inset: 0;
            z-index: 0;
        }

        .cs-bg img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.35;
        }

        .cs-bg-overlay {
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 15% 20%, rgba(37, 99, 235, 0.35) 0%, transparent 45%),
                radial-gradient(circle at 85% 80%, rgba(99, 102, 241, 0.25) 0%, transparent 40%),
                linear-gradient(165deg, rgba(15, 23, 42, 0.92) 0%, rgba(30, 58, 138, 0.88) 100%);
        }

        .cs-header {
            position: relative;
            z-index: 2;
            padding: 1.25rem 0;
        }

        .cs-brand {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: #fff;
        }

        .cs-brand img { height: 40px; }

        .cs-brand-title {
            font-family: "SUSE", sans-serif;
            font-weight: 700;
            font-size: 1.15rem;
            letter-spacing: -0.02em;
            margin: 0;
        }

        .cs-brand-tagline {
            font-size: 0.72rem;
            opacity: 0.75;
            margin: 0;
        }

        .cs-main {
            flex: 1;
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            padding: 1.5rem 0 2.5rem;
        }

        .cs-panel {
            width: 100%;
            max-width: 920px;
            margin: 0 auto;
            animation: csFadeUp 0.55s ease both;
        }

        @keyframes csFadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .cs-welcome {
            text-align: center;
            color: #fff;
            margin-bottom: 1.75rem;
        }

        .cs-avatar {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            margin: 0 auto 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: "SUSE", sans-serif;
            font-weight: 700;
            font-size: 1.5rem;
            color: #fff;
            background: linear-gradient(135deg, #2563eb 0%, #6366f1 100%);
            box-shadow: 0 8px 28px rgba(37, 99, 235, 0.45);
            border: 3px solid rgba(255, 255, 255, 0.2);
        }

        .cs-welcome h1 {
            font-family: "SUSE", sans-serif;
            font-weight: 700;
            font-size: clamp(1.5rem, 4vw, 2rem);
            margin-bottom: 0.35rem;
            letter-spacing: -0.02em;
        }

        .cs-welcome p {
            color: rgba(255, 255, 255, 0.78);
            font-size: 0.95rem;
            margin-bottom: 0.85rem;
            max-width: 480px;
            margin-left: auto;
            margin-right: auto;
        }

        .cs-role-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.3rem 0.85rem;
            border-radius: 2rem;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
            font-size: 0.78rem;
            font-weight: 600;
        }

        .cs-card {
            background: rgba(255, 255, 255, 0.97);
            border-radius: 1.35rem;
            border: 1px solid rgba(255, 255, 255, 0.25);
            box-shadow: 0 20px 60px rgba(15, 23, 42, 0.35);
            overflow: hidden;
        }

        .cs-card-head {
            padding: 1.35rem 1.5rem 1rem;
            border-bottom: 1px solid #f1f5f9;
            background: linear-gradient(180deg, #f8fafc 0%, #fff 100%);
        }

        .cs-card-head h2 {
            font-family: "SUSE", sans-serif;
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 0.2rem;
            color: #0f172a;
        }

        .cs-stats {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 0.75rem;
        }

        .cs-stat-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.28rem 0.7rem;
            border-radius: 2rem;
            font-size: 0.72rem;
            font-weight: 600;
            background: rgba(37, 99, 235, 0.08);
            color: #2563eb;
        }

        .cs-card-body {
            padding: 1.35rem 1.5rem 1.5rem;
        }

        .cs-alert {
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            margin-bottom: 1.25rem;
        }

        .store-pick-form { height: 100%; }

        .store-pick-card {
            width: 100%;
            height: 100%;
            text-align: left;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            background: #fff;
            padding: 1.25rem;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
            position: relative;
            overflow: hidden;
        }

        .store-pick-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #2563eb, #6366f1);
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .store-pick-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 14px 32px rgba(37, 99, 235, 0.15);
            border-color: rgba(37, 99, 235, 0.35);
        }

        .store-pick-card:hover::after { opacity: 1; }

        .store-pick-card:hover .store-pick-enter {
            color: #2563eb;
            transform: translateX(3px);
        }

        .store-pick-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 0.75rem;
            margin-bottom: 0.85rem;
        }

        .store-pick-icon {
            width: 48px;
            height: 48px;
            border-radius: 0.875rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .store-pick-icon.central {
            background: rgba(37, 99, 235, 0.12);
            color: #2563eb;
        }

        .store-pick-icon.satellite {
            background: rgba(124, 58, 237, 0.12);
            color: #7c3aed;
        }

        .store-pick-icon.default {
            background: rgba(13, 148, 136, 0.12);
            color: #0d9488;
        }

        .store-pick-group {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.22rem 0.6rem;
            border-radius: 2rem;
            font-size: 0.68rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .store-pick-group.central {
            background: rgba(37, 99, 235, 0.1);
            color: #1d4ed8;
        }

        .store-pick-group.satellite {
            background: rgba(124, 58, 237, 0.1);
            color: #7c3aed;
        }

        .store-pick-name {
            font-family: "SUSE", sans-serif;
            font-weight: 700;
            font-size: 1.05rem;
            color: #0f172a;
            margin-bottom: 0.35rem;
        }

        .store-pick-desc {
            font-size: 0.82rem;
            color: #64748b;
            margin-bottom: 0.85rem;
            line-height: 1.45;
        }

        .store-pick-enter {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 0.82rem;
            font-weight: 600;
            color: #94a3b8;
            transition: color 0.2s ease, transform 0.2s ease;
        }

        .cs-footer {
            position: relative;
            z-index: 2;
            padding: 1rem 0 1.25rem;
            text-align: center;
        }

        .cs-footer-text {
            color: rgba(255, 255, 255, 0.55);
            font-size: 0.78rem;
            margin: 0;
        }

        .cs-logout {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            margin-top: 1rem;
            padding: 0.45rem 1rem;
            border-radius: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            background: rgba(255, 255, 255, 0.08);
            color: rgba(255, 255, 255, 0.85);
            font-size: 0.82rem;
            font-weight: 500;
            text-decoration: none;
            transition: background 0.2s, border-color 0.2s;
        }

        .cs-logout:hover {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.35);
            color: #fff;
        }
    </style>
</head>
<body>
<div class="cs-page">
    <div class="cs-bg">
        <img src="{{ asset('backend/assets/img/background-image/backgorund-image-13.jpg') }}" alt="">
        <div class="cs-bg-overlay"></div>
    </div>

    <header class="cs-header">
        <div class="container">
            <a href="#" class="cs-brand">
                <img src="{{ asset('backend/assets/img/logo-light.svg') }}" alt="Stock Shield">
                <div>
                    <p class="cs-brand-title">STOCK<span class="fw-bold">SHIELD</span></p>
                    <p class="cs-brand-tagline">Korle Bu Teaching Hospital</p>
                </div>
            </a>
        </div>
    </header>

    <main class="cs-main">
        <div class="container">
            <div class="cs-panel">
                <div class="cs-welcome">
                    <div class="cs-avatar">{{ $initials }}</div>
                    <h1>Welcome back, {{ Auth::user()->name }}</h1>
                    <p>Select the store you want to work in for this session. Your stock, requisitions, and reports will be scoped to this location.</p>
                    <span class="cs-role-badge">
                        <i class="bi bi-person-badge"></i> {{ $userRole }}
                    </span>
                </div>

                <div class="cs-card">
                    <div class="cs-card-head">
                        <h2><i class="bi bi-shop me-2 text-primary"></i>Choose Your Store</h2>
                        <p class="text-secondary small mb-0">You have access to {{ $storeCount }} {{ Str::plural('store', $storeCount) }}. Pick one to continue.</p>
                        <div class="cs-stats">
                            <span class="cs-stat-pill"><i class="bi bi-collection"></i> {{ $storeCount }} available</span>
                            @if($centralCount > 0)
                                <span class="cs-stat-pill"><i class="bi bi-building"></i> {{ $centralCount }} central</span>
                            @endif
                            @if($satelliteCount > 0)
                                <span class="cs-stat-pill"><i class="bi bi-geo-alt"></i> {{ $satelliteCount }} satellite</span>
                            @endif
                        </div>
                    </div>

                    <div class="cs-card-body">
                        @if (session('message_error'))
                            <div class="cs-alert alert alert-danger mb-0">{{ session('message_error') }}</div>
                        @endif

                        <div class="row g-3">
                            @foreach ($mappedStores as $store)
                                @php
                                    $isCentral = $store->store_group === 'central';
                                    $isSatellite = $store->store_group === 'satellite';
                                    $iconClass = $isCentral ? 'central' : ($isSatellite ? 'satellite' : 'default');
                                    $icon = $isCentral ? 'bi-building' : ($isSatellite ? 'bi-geo-alt-fill' : 'bi-shop');
                                @endphp
                                <div class="col-sm-6">
                                    <form action="{{ route('select-store-process') }}" method="POST" class="store-pick-form">
                                        @csrf
                                        <input type="hidden" name="store_id" value="{{ $store->id }}">
                                        <button type="submit" class="store-pick-card">
                                            <div class="store-pick-top">
                                                <div class="store-pick-icon {{ $iconClass }}">
                                                    <i class="bi {{ $icon }}"></i>
                                                </div>
                                                @if($isCentral)
                                                    <span class="store-pick-group central">Central</span>
                                                @elseif($isSatellite)
                                                    <span class="store-pick-group satellite">Satellite</span>
                                                @endif
                                            </div>
                                            <div class="store-pick-name">{{ $store->name }}</div>
                                            <p class="store-pick-desc">
                                                @if($isCentral)
                                                    Main distribution point — enter to manage central stock operations.
                                                @elseif($isSatellite)
                                                    Branch location — enter to manage site-level inventory.
                                                @else
                                                    Click to enter and start working in this store.
                                                @endif
                                            </p>
                                            <span class="store-pick-enter">
                                                Enter store <i class="bi bi-arrow-right"></i>
                                            </span>
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <a href="{{ route('logout') }}" class="cs-logout">
                        <i class="bi bi-box-arrow-left"></i> Sign out
                    </a>
                </div>
            </div>
        </div>
    </main>

    <footer class="cs-footer">
        <p class="cs-footer-text">Copyright &copy; 2026 · Developed by Speedlines Technology</p>
    </footer>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
</body>
</html>
