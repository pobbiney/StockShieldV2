@php
    $variant = $variant ?? 'light';
    $items = $items ?? [];
@endphp

<nav aria-label="breadcrumb" class="ss-bc ss-bc--{{ $variant }}">
    <ol class="ss-bc__list">
        @foreach ($items as $index => $item)
            @if ($index > 0)
                <li class="ss-bc__sep" aria-hidden="true">
                    <svg viewBox="0 0 16 16" fill="currentColor" focusable="false">
                        <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
                    </svg>
                </li>
            @endif
            <li class="ss-bc__item {{ !empty($item['active']) ? 'is-current' : '' }}">
                @if (!empty($item['active']))
                    @if (!empty($item['icon']))
                        <span class="ss-bc__icon"><i class="bi {{ $item['icon'] }}"></i></span>
                    @endif
                    <span class="ss-bc__label">{{ $item['label'] }}</span>
                @else
                    <a href="{{ $item['url'] ?? '#' }}" class="ss-bc__link">
                        @if (!empty($item['icon']))
                            <span class="ss-bc__icon"><i class="bi {{ $item['icon'] }}"></i></span>
                        @endif
                        <span class="ss-bc__label">{{ $item['label'] }}</span>
                    </a>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
