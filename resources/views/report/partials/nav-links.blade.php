<div class="rp-nav">
    @foreach($navLinks as $link)
        <a href="{{ route($link['route']) }}"
           class="{{ ($activeRoute ?? null) === $link['route'] ? 'active' : '' }}">
            <i class="bi bi-{{ $link['icon'] ?? 'search' }}"></i> {{ $link['label'] }}
        </a>
    @endforeach
</div>
