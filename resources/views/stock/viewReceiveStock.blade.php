@php
    $pageName = 'stock';
    $subpageName = 'receive-stock';
    $totalQty = (int) ($groupedIssues ?? collect())->sum('total_qty');
@endphp

@extends('layouts.backendapp')

@section('css')
<style>
    .vrs-page { padding: 0 0.5rem 2rem; }

    .vrs-hero {
        background: linear-gradient(135deg, #065f46 0%, #059669 60%, #34d399 100%);
        border-radius: 1.25rem;
        padding: 1.75rem 2rem;
        margin-bottom: 1.5rem;
        color: #fff;
        box-shadow: 0 8px 32px rgba(5, 150, 105, 0.25);
    }

    .vrs-hero h2 {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: 1.5rem;
        margin-bottom: 0.35rem;
    }

    .req-no-badge {
        display: inline-block;
        padding: 0.25rem 0.6rem;
        border-radius: 0.375rem;
        background: rgba(255, 255, 255, 0.2);
        font-family: monospace;
        font-weight: 700;
        font-size: 0.85rem;
    }

    .btn-back-link {
        color: rgba(255, 255, 255, 0.85);
        text-decoration: none;
        font-size: 0.875rem;
    }

    .btn-back-link:hover { color: #fff; }

    .vrs-table-card {
        border-radius: 1.25rem;
        border: 1px solid rgba(0, 0, 0, 0.06);
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
    }

    .vrs-table-head {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    #vrsTable thead th {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #64748b;
        background: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
        padding: 0.85rem 1rem;
        white-space: nowrap;
    }

    #vrsTable tbody td {
        padding: 0.85rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
    }

    #vrsTable tbody tr:nth-child(even) { background: #fafafa; }
    #vrsTable tbody tr:hover { background: #ecfdf5 !important; }

    .batch-code {
        display: inline-block;
        padding: 0.15rem 0.45rem;
        border-radius: 0.35rem;
        background: #f1f5f9;
        font-family: monospace;
        font-size: 0.78rem;
        color: #475569;
        margin: 0.1rem 0.15rem 0.1rem 0;
    }

    .batch-breakdown {
        margin-top: 0.35rem;
        padding-top: 0.35rem;
        border-top: 1px dashed #e2e8f0;
        font-size: 0.72rem;
        color: #64748b;
    }

    .batch-count-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.15rem 0.5rem;
        border-radius: 2rem;
        background: rgba(100, 116, 139, 0.12);
        color: #475569;
        font-size: 0.68rem;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .qty-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 2rem;
        padding: 0.2rem 0.55rem;
        border-radius: 0.375rem;
        background: rgba(5, 150, 105, 0.1);
        color: #059669;
        font-weight: 700;
        font-size: 0.82rem;
    }

    .receive-note {
        margin: 0 1.5rem 1rem;
        padding: 0.75rem 1rem;
        border-radius: 0.75rem;
        background: rgba(5, 150, 105, 0.08);
        border: 1px solid rgba(5, 150, 105, 0.15);
        font-size: 0.82rem;
        color: #065f46;
    }

    .vrs-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid #f1f5f9;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .vrs-footer-meta { font-size: 0.82rem; color: #64748b; }

    .btn-accept-submit {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.65rem 1.25rem;
        border-radius: 0.625rem;
        border: none;
        background: #059669;
        color: #fff;
        font-weight: 600;
    }

    .btn-accept-submit:hover { background: #047857; color: #fff; }

    .btn-print-link {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.55rem 1rem;
        border-radius: 0.625rem;
        border: 1.5px solid #e2e8f0;
        background: #fff;
        color: #475569;
        font-size: 0.875rem;
        font-weight: 600;
        text-decoration: none;
    }

    .btn-print-link:hover { background: #f8fafc; color: #334155; }

    .staff-swal-confirm.receive { background: #059669 !important; }
    .staff-swal-confirm.receive:hover { background: #047857 !important; }
</style>
@endsection

@section('content')

<div class="container-fluid vrs-page px-3 px-lg-4 mt-3">

    <div class="vrs-hero">
        <a href="{{ route('ReceiveStock') }}" class="btn-back-link d-inline-flex align-items-center gap-1 mb-2">
            <i class="bi bi-arrow-left"></i> Back to Receive Stock
        </a>
        <h2>Accept Transfer</h2>
        <span class="req-no-badge">{{ $requisitionNo ?? '' }}</span>
        @if($invoiceNumber ?? null)
            <span class="req-no-badge ms-1">{{ $invoiceNumber }}</span>
        @endif
        <p class="mb-0 mt-2 opacity-90">
            From <strong>{{ $centralStore->name ?? 'Central Store' }}</strong>
            · Into <strong>{{ $activeStore->name ?? '—' }}</strong>
            · {{ $groupedIssues->count() }} item(s) · {{ number_format($totalQty) }} units
        </p>
    </div>

    <div class="vrs-table-card mb-5">
        <div class="vrs-table-head">
            <h5 class="mb-0 fw-bold"><i class="bi bi-box-arrow-in-down me-1 text-success"></i> Items to Accept</h5>
            <span class="badge rounded-pill text-bg-success-subtle text-success-emphasis border border-success-subtle">
                <i class="bi bi-truck me-1"></i> Issued — Awaiting Receipt
            </span>
        </div>

        @if($groupedIssues->isNotEmpty())
            <div class="receive-note">
                <i class="bi bi-info-circle me-1"></i>
                Confirm acceptance to add these items to <strong>{{ $activeStore->name }}</strong> inventory. Quantities will be merged into existing batches where applicable.
            </div>

            <form method="POST" action="{{ route('acceptReceiveStock', Crypt::encrypt($requisitionNo)) }}" id="acceptReceiveForm">
                @csrf
                <div class="table-responsive">
                    <table class="table mb-0" id="vrsTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Item Code</th>
                                <th>Item Name</th>
                                <th>UoM</th>
                                <th>Issuing Store</th>
                                <th>Batches</th>
                                <th>Issued Qty</th>
                                <th>Qty to Accept</th>
                                <th>Unit Cost</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($groupedIssues as $group)
                                @php
                                    $avgCost = $group->lines->avg('amount');
                                @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><code>{{ $group->itemcode->item_code ?? '—' }}</code></td>
                                    <td class="fw-semibold">{{ $group->itemname->name ?? '—' }}</td>
                                    <td>{{ $group->itemname->unitname->name ?? '—' }}</td>
                                    <td>{{ $group->issuing_store->name ?? ($centralStore->name ?? '—') }}</td>
                                    <td>
                                        @if($group->lines->count() > 1)
                                            <span class="batch-count-badge">
                                                <i class="bi bi-layers"></i> {{ $group->lines->count() }} batches
                                            </span>
                                        @endif
                                        @foreach($group->lines as $line)
                                            <span class="batch-code">{{ $line->batch_number }}</span>
                                        @endforeach
                                        <div class="batch-breakdown">
                                            @foreach($group->lines as $line)
                                                <span>{{ $line->batch_number }}: {{ number_format($line->qty) }} issued</span>
                                                @if(($group->total_qty_multiplier ?? null) && $line->accept_qty !== $line->qty)
                                                    <span class="ms-1">→ {{ number_format($line->accept_qty) }} units</span>
                                                @endif
                                            @endforeach
                                        </div>
                                    </td>
                                    <td>{{ number_format($group->issued_qty ?? $group->total_qty) }}</td>
                                    <td>
                                        <span class="qty-badge">{{ number_format($group->total_qty) }}</span>
                                        @if(($group->total_qty_multiplier ?? null) && ($group->issued_qty ?? 0) !== $group->total_qty)
                                            <div class="batch-breakdown mb-0 border-0 pt-1">
                                                × {{ number_format($group->total_qty_multiplier) }} per pack
                                            </div>
                                        @endif
                                    </td>
                                    <td>{{ number_format($avgCost ?? 0, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="vrs-footer">
                    <div class="vrs-footer-meta">
                        Accepting adds <strong>{{ number_format($totalQty) }}</strong> units to your store inventory.
                    </div>
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        @if($invoiceNumber)
                            <a href="{{ route('requisition.print', Crypt::encrypt($invoiceNumber)) }}"
                               target="_blank"
                               class="btn-print-link">
                                <i class="bi bi-printer"></i> Print Invoice
                            </a>
                        @endif
                        <button type="submit" class="btn-accept-submit btn-confirm-accept">
                            <i class="bi bi-box-arrow-in-down"></i> Accept into Store
                        </button>
                    </div>
                </div>
            </form>
        @else
            <div class="text-center py-5 text-muted">
                <p>No items pending receipt for this transfer.</p>
                <a href="{{ route('ReceiveStock') }}" class="btn btn-outline-secondary btn-sm">Back to list</a>
            </div>
        @endif
    </div>
</div>

@endsection

@section('scripts')
<script>
const ReceiveAlert = {
    _base(opts) {
        return Swal.fire(Object.assign({
            width: '28rem',
            padding: '1.5rem 1.75rem 1.35rem',
            buttonsStyling: false,
            customClass: {
                popup: 'staff-swal-popup',
                title: 'staff-swal-title',
                htmlContainer: 'staff-swal-text',
                confirmButton: 'btn staff-swal-confirm ' + (opts.btnClass || 'neutral'),
                cancelButton: 'btn btn-light border staff-swal-cancel',
            },
        }, opts));
    },
    success(message) {
        return this._base({
            icon: 'success',
            title: 'Stock Accepted',
            html: '<p class="mb-0">' + message + '</p>',
            btnClass: 'receive',
            timer: 4500,
            timerProgressBar: true,
            confirmButtonText: '<i class="bi bi-check-lg me-1"></i> Done',
        });
    },
    error(title, text) {
        return this._base({
            icon: 'error',
            title: title || 'Something went wrong',
            text: text,
            btnClass: 'error',
            confirmButtonText: '<i class="bi bi-x-lg me-1"></i> Close',
        });
    },
    confirmAccept(onConfirm) {
        return this._base({
            icon: 'question',
            title: 'Accept into Store?',
            html: '<p class="mb-2">Confirm receiving all items listed into your store inventory?</p>'
                + '<small class="text-muted">This action cannot be undone. Stock will become available immediately.</small>',
            btnClass: 'receive',
            showCancelButton: true,
            confirmButtonText: '<i class="bi bi-box-arrow-in-down me-1"></i> Yes, accept',
            cancelButtonText: 'Cancel',
        }).then(function (result) {
            if (result.isConfirmed && onConfirm) onConfirm();
        });
    },
};

$(document).ready(function () {
    $('.btn-confirm-accept').on('click', function (e) {
        e.preventDefault();
        var form = $('#acceptReceiveForm');
        ReceiveAlert.confirmAccept(function () {
            form.submit();
        });
    });
});

@if(session('message_success'))
ReceiveAlert.success(@json(session('message_success')));
@endif

@if(session('message_error'))
ReceiveAlert.error('Receive Failed', @json(session('message_error')));
@endif
</script>
@endsection
