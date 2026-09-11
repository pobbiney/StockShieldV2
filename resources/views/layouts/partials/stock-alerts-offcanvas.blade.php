@if(($stockAlertCount ?? 0) > 0)
    <div class="px-3 py-2 border-top border-bottom bg-light">
        <strong class="small text-uppercase text-secondary">Stock Alerts</strong>
        @if($alertStoreName ?? null)
            <div class="small text-muted mt-1"><i class="bi bi-shop me-1"></i>{{ $alertStoreName }}</div>
        @endif
    </div>

    @if(($reorderItemsCount ?? 0) > 0)
        <div class="px-3 pt-2 pb-1">
            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">
                <i class="bi bi-arrow-repeat me-1"></i> Re-order ({{ $reorderItemsCount }})
            </span>
        </div>
        <div class="px-2 pb-2">
            @foreach($reorderItems ?? collect() as $item)
                @php $isCritical = $item->total_qty < $item->reorder_level; @endphp
                <div class="alert {{ $isCritical ? 'alert-danger' : 'alert-warning' }} mb-2 py-2">
                    <div class="d-flex gap-2 align-items-start">
                        <figure class="avatar avatar-30 rounded-circle {{ $isCritical ? 'bg-danger' : 'bg-warning' }} text-white flex-shrink-0 mb-0">
                            <i class="bi bi-{{ $isCritical ? 'arrow-down-circle' : 'dash-circle' }}"></i>
                        </figure>
                        <div>
                            <p class="small fw-semibold mb-1">{{ $item->name }}</p>
                            <p class="small mb-0 text-secondary">
                                Stock {{ number_format($item->total_qty) }}
                                &middot; Reorder {{ number_format($item->reorder_level) }}
                                &middot; {{ $isCritical ? 'Below level' : 'At level' }}
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @if(($expiringCount ?? 0) > 0)
        <div class="px-3 pt-2 pb-1">
            <span class="badge bg-orange-subtle text-warning-emphasis border" style="background:#fff7ed;color:#c2410c;border-color:#fed7aa !important;">
                <i class="bi bi-clock-history me-1"></i> Expiring soon ({{ $expiringCount }})
            </span>
        </div>
        <div class="px-2 pb-2">
            @foreach($expiringItems ?? collect() as $note)
                <div class="alert alert-warning mb-2 py-2">
                    <div class="d-flex gap-2 align-items-start">
                        <figure class="avatar avatar-30 rounded-circle bg-warning text-white flex-shrink-0 mb-0">
                            <i class="bi bi-calendar-event"></i>
                        </figure>
                        <div>
                            <p class="small fw-semibold mb-1">{{ $note->name }}</p>
                            <p class="small mb-0 text-secondary">
                                Qty {{ number_format($note->qty) }}
                                &middot; Expires {{ \Carbon\Carbon::parse($note->expiry_date)->format('M d, Y') }}
                                &middot; {{ $note->days_left }} day{{ $note->days_left != 1 ? 's' : '' }} left
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @if(($expiredCount ?? 0) > 0)
        <div class="px-3 pt-2 pb-1">
            <span class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle">
                <i class="bi bi-calendar-x me-1"></i> Expired ({{ $expiredCount }})
            </span>
        </div>
        <div class="px-2 pb-2">
            @foreach($expiredItems ?? collect() as $note)
                <div class="alert alert-danger mb-2 py-2">
                    <div class="d-flex gap-2 align-items-start">
                        <figure class="avatar avatar-30 rounded-circle bg-danger text-white flex-shrink-0 mb-0">
                            <i class="bi bi-exclamation-octagon"></i>
                        </figure>
                        <div>
                            <p class="small fw-semibold mb-1">{{ $note->name }}</p>
                            <p class="small mb-0 text-secondary">
                                Qty {{ number_format($note->qty) }}
                                &middot; Expired {{ \Carbon\Carbon::parse($note->expiry_date)->format('M d, Y') }}
                                &middot; {{ abs((int) $note->days_left) }} day{{ abs((int) $note->days_left) != 1 ? 's' : '' }} ago
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endif
