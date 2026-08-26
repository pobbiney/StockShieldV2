@php
    $pageName = 'request';
    $subpageName = 'approve-request';
@endphp

@extends('layouts.backendapp')

@section('css')
<style>
    .vr-page { padding: 0 0.5rem 2rem; }

    .vr-hero {
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 60%, #3b82f6 100%);
        border-radius: 1.25rem;
        padding: 1.75rem 2rem;
        margin-bottom: 1.5rem;
        color: #fff;
        box-shadow: 0 8px 32px rgba(37, 99, 235, 0.25);
    }

    .vr-hero h2 {
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

    .vr-table-card {
        border-radius: 1.25rem;
        border: 1px solid rgba(0, 0, 0, 0.06);
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
    }

    .vr-table-head {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    #vrTable thead th {
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

    #vrTable tbody td {
        padding: 0.85rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
    }

    .qty-input {
        max-width: 100px;
        border-radius: 0.5rem;
        border: 1.5px solid #e2e8f0;
    }

    .qty-requested-badge {
        display: inline-block;
        padding: 0.2rem 0.55rem;
        border-radius: 0.375rem;
        background: rgba(37, 99, 235, 0.1);
        color: #2563eb;
        font-weight: 700;
        font-size: 0.85rem;
    }

    .btn-approve-submit {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.65rem 1.25rem;
        border-radius: 0.625rem;
        border: none;
        background: #2563eb;
        color: #fff;
        font-weight: 600;
    }

    .btn-approve-submit:hover { background: #1d4ed8; color: #fff; }
</style>
@endsection

@section('content')

<div class="container-fluid vr-page px-3 px-lg-4 mt-3">

    <div class="vr-hero">
        <a href="{{ route('ApproveRequest') }}" class="btn-back-link d-inline-flex align-items-center gap-1 mb-2">
            <i class="bi bi-arrow-left"></i> Back to Approvals
        </a>
        <h2>Review Requisition</h2>
        <span class="req-no-badge">{{ $requisitionNo ?? '' }}</span>
        @if($listrequest->isNotEmpty())
            <p class="mb-0 mt-2 opacity-90">
                From <strong>{{ $listrequest->first()->storename->name ?? '—' }}</strong>
                · Fulfilled by <strong>{{ $listrequest->first()->sourceStore->name ?? 'Central' }}</strong>
                · {{ $listrequest->count() }} item(s)
            </p>
        @endif
    </div>

    <div class="vr-table-card mb-5">
        <div class="vr-table-head">
            <h5 class="mb-0 fw-bold">Line Items — Adjust Approved Quantities</h5>
        </div>

        @if($listrequest->isNotEmpty())
            <form method="POST" action="{{ route('approve-request-process') }}">
                @csrf
                <div class="table-responsive">
                    <table class="table mb-0" id="vrTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Item Code</th>
                                <th>Item Name</th>
                                <th>UoM</th>
                                <th>Req. Qty</th>
                                <th>Approve Qty</th>
                                <th>Unit Cost</th>
                                <th>Requested By</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($listrequest as $lists)
                                <tr>
                                    <td>
                                        {{ $loop->iteration }}
                                        <input type="hidden" name="request_id[]" value="{{ $lists->id }}">
                                    </td>
                                    <td><code>{{ $lists->itemcode->item_code ?? '—' }}</code></td>
                                    <td class="fw-semibold">{{ $lists->itemname->name ?? '—' }}</td>
                                    <td>{{ $lists->itemname->unitname->name ?? '—' }}</td>
                                    <td><span class="qty-requested-badge">{{ $lists->qty_requested }}</span></td>
                                    <td>
                                        <input type="number"
                                               name="qty[{{ $lists->id }}]"
                                               class="form-control qty-input"
                                               value="{{ old('qty.' . $lists->id, $lists->qty_requested) }}"
                                               min="0"
                                               max="{{ $lists->qty_requested }}"
                                               required>
                                    </td>
                                    <td>{{ $lists->amount ?? '—' }}</td>
                                    <td>{{ $lists->staffname->name ?? '—' }}</td>
                                    <td class="text-end">
                                        <button type="button"
                                                class="btn-reject-row showmodal"
                                                data-url="{{ route('request-item-id', $lists->id) }}"
                                                data-item-name="{{ $lists->itemname->name ?? 'Item' }}"
                                                data-bs-toggle="modal"
                                                data-bs-target="#standardmodal">
                                            <i class="bi bi-x-circle"></i> Reject
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-3 border-top bg-light text-end">
                    <button type="submit" class="btn-approve-submit btn-confirm-approve">
                        <i class="bi bi-patch-check"></i> Approve All Items
                    </button>
                </div>
            </form>
        @else
            <div class="text-center py-5 text-muted">
                <p>No pending line items found for this requisition.</p>
                <a href="{{ route('ApproveRequest') }}" class="btn btn-outline-secondary btn-sm">Back to list</a>
            </div>
        @endif
    </div>
</div>

@include('requisition.reject-request-modal')

@endsection

@section('scripts')
<script>
const ApproveAlert = {
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
    approved(message) {
        return this._base({
            icon: 'success',
            title: 'Requisition Approved',
            html: '<p class="mb-0">' + message + '</p>'
                + '<small class="text-muted d-block mt-2">The central store can now issue these items to the requesting satellite store.</small>',
            btnClass: 'info',
            timer: 3500,
            timerProgressBar: true,
            confirmButtonText: '<i class="bi bi-patch-check me-1"></i> Done',
        });
    },
    rejected(message) {
        return this._base({
            icon: 'warning',
            title: 'Line Item Rejected',
            html: '<p class="mb-0">' + message + '</p>'
                + '<small class="text-muted d-block mt-2">The rejected line will not proceed to stock issue.</small>',
            btnClass: 'error',
            timer: 3500,
            timerProgressBar: true,
            confirmButtonText: '<i class="bi bi-check-lg me-1"></i> OK',
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
    confirmApprove(onConfirm) {
        return this._base({
            icon: 'question',
            title: 'Approve Requisition?',
            html: '<p class="mb-2">Approve all line items with the quantities shown?</p>'
                + '<small class="text-muted">Approved items will be sent to the central store for issuing.</small>',
            btnClass: 'info',
            showCancelButton: true,
            confirmButtonText: '<i class="bi bi-patch-check me-1"></i> Yes, approve',
            cancelButtonText: 'Cancel',
        }).then(function (result) {
            if (result.isConfirmed && onConfirm) onConfirm();
        });
    },
};

$(document).ready(function () {
    $('body').on('click', '.showmodal', function () {
        var userUrl = $(this).data('url');
        var itemName = $(this).data('item-name') || 'Item';
        $.get(userUrl, function (data) {
            $('#itemID').val(data.id);
            $('#itemname').text(data.name || itemName);
            $('#rejectReason').val('');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('standardmodal')).show();
        });
    });

    $('.btn-confirm-approve').on('click', function (e) {
        e.preventDefault();
        var form = $(this).closest('form');
        ApproveAlert.confirmApprove(function () {
            form.submit();
        });
    });
});

@if(session('message_success'))
@php $successMsg = session('message_success'); @endphp
@if(stripos($successMsg, 'reject') !== false)
ApproveAlert.rejected(@json($successMsg));
@else
ApproveAlert.approved(@json($successMsg));
@endif
@endif

@if(session('message_error'))
ApproveAlert.error('Action Failed', @json(session('message_error')));
@endif
</script>
@endsection
