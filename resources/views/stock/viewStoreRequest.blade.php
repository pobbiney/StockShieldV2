@php
    $pageName = 'stock';
    $subpageName = 'issue-item';
@endphp

@extends('layouts.backendapp')

@section('css')
<style>
    .vsr-page { padding: 0 0.5rem 2rem; }

    .vsr-hero {
        background: linear-gradient(135deg, #92400e 0%, #d97706 60%, #f59e0b 100%);
        border-radius: 1.25rem;
        padding: 1.75rem 2rem;
        margin-bottom: 1.5rem;
        color: #fff;
        box-shadow: 0 8px 32px rgba(217, 119, 6, 0.25);
    }

    .vsr-hero h2 {
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

    .vsr-table-card {
        border-radius: 1.25rem;
        border: 1px solid rgba(0, 0, 0, 0.06);
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
    }

    .vsr-table-head {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    #vsrTable thead th {
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

    #vsrTable tbody td {
        padding: 0.85rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
    }

    .qty-input {
        max-width: 100px;
        border-radius: 0.5rem;
        border: 1.5px solid #e2e8f0;
    }

    .qty-input:disabled {
        background: #f1f5f9;
        color: #94a3b8;
        cursor: not-allowed;
    }

    .qty-input.is-invalid { border-color: #dc2626; }

    .uom-select {
        min-width: 140px;
        height: 38px;
        border-radius: 0.5rem;
        border: 1.5px solid #c7d2fe;
        background: #eef2ff;
        color: #1e1b4b;
        font-size: 0.82rem;
        font-weight: 600;
        padding: 0.25rem 1.75rem 0.25rem 0.65rem;
    }

    .uom-select:focus {
        outline: none;
        border-color: #4f46e5;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
    }

    .unit-modal .modal-content {
        border-radius: 1.25rem;
        border: none;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.18);
    }

    .unit-modal .modal-header {
        background: linear-gradient(135deg, #92400e 0%, #d97706 100%);
        color: #fff;
        border: none;
        padding: 1.25rem 1.5rem;
    }

    .unit-modal .modal-header .modal-title {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: 1.05rem;
    }

    .unit-modal .modal-header .btn-close {
        filter: invert(1) grayscale(1) brightness(2);
    }

    .unit-modal .modal-body { padding: 1.35rem 1.5rem; }

    .unit-modal .modal-footer {
        border-top: 1px solid #f1f5f9;
        padding: 1rem 1.5rem;
        background: #f8fafc;
        gap: 0.5rem;
    }

    .unit-modal .form-label {
        font-size: 0.78rem;
        font-weight: 600;
        color: #64748b;
        margin-bottom: 0.35rem;
    }

    .unit-modal .form-control {
        border-radius: 0.75rem;
        border: 1.5px solid #e2e8f0;
        font-size: 0.9rem;
        height: 44px;
    }

    .unit-modal .form-control:focus {
        border-color: #d97706;
        box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.15);
    }

    .btn-modal-continue {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.55rem 1.15rem;
        border-radius: 0.625rem;
        border: none;
        background: #d97706;
        color: #fff;
        font-weight: 600;
        font-size: 0.875rem;
    }

    .btn-modal-continue:hover { background: #b45309; color: #fff; }

    .avail-badge {
        display: inline-block;
        padding: 0.15rem 0.5rem;
        border-radius: 0.35rem;
        font-size: 0.72rem;
        font-weight: 700;
    }

    .avail-badge.ok      { background: rgba(16, 185, 129, 0.12); color: #047857; }
    .avail-badge.low     { background: rgba(245, 158, 11, 0.12); color: #b45309; }
    .avail-badge.none    { background: rgba(239, 68, 68, 0.12); color: #dc2626; }
    .avail-badge.expired { background: rgba(239, 68, 68, 0.12); color: #dc2626; }

    .batch-hint {
        font-size: 0.68rem;
        color: #94a3b8;
        margin-top: 0.2rem;
        line-height: 1.35;
    }

    .fefo-note {
        margin: 0 1.5rem 1rem;
        padding: 0.75rem 1rem;
        border-radius: 0.75rem;
        background: rgba(217, 119, 6, 0.08);
        border: 1px solid rgba(217, 119, 6, 0.15);
        font-size: 0.82rem;
        color: #92400e;
    }

    .btn-issue-submit {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.65rem 1.25rem;
        border-radius: 0.625rem;
        border: none;
        background: #d97706;
        color: #fff;
        font-weight: 600;
    }

    .btn-issue-submit:hover { background: #b45309; color: #fff; }

    .btn-back-link {
        color: rgba(255,255,255,0.85);
        text-decoration: none;
        font-size: 0.875rem;
    }

    .btn-back-link:hover { color: #fff; }

    .staff-swal-confirm.issue { background: #d97706 !important; }
    .staff-swal-confirm.issue:hover { background: #b45309 !important; }
</style>
@endsection

@section('content')

<div class="container-fluid vsr-page px-3 px-lg-4 mt-3">

    <div class="vsr-hero">
        <a href="{{ route('IssueItem') }}" class="btn-back-link d-inline-flex align-items-center gap-1 mb-2">
            <i class="bi bi-arrow-left"></i> Back to Issue Items
        </a>
        <h2>Issue Requisition</h2>
        <span class="req-no-badge">{{ $requisitionNo ?? '' }}</span>
        @if($listrequest->isNotEmpty())
            <p class="mb-0 mt-2 opacity-90">
                Request from <strong>{{ $listrequest->first()->storename->name ?? '—' }}</strong>
                · {{ $listrequest->count() }} item(s)
            </p>
        @endif
    </div>

    <div class="vsr-table-card mb-5">
        <div class="vsr-table-head">
            <h5 class="mb-0 fw-bold">Line Items</h5>
        </div>

        @if($listrequest->isNotEmpty())
            <div class="fefo-note">
                <i class="bi bi-info-circle me-1"></i>
                Stock is issued using <strong>FEFO</strong> (nearest expiry first). Items with <strong>zero available stock</strong> cannot have a quantity entered — submit the form to record them as zero and continue with items that have stock.
            </div>
            <form method="POST" action="{{ route('issue-request-process') }}" id="issueForm">
                @csrf
                <input type="hidden" name="requisition_unit" id="requisitionUnitHidden" value="{{ old('requisition_unit') }}">
                <div class="table-responsive">
                    <table class="table mb-0" id="vsrTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Item Code</th>
                                <th>Item Name</th>
                                <th>UoM</th>
                                <th>Qty Requested</th>
                                <th>Qty Approved</th>
                                <th>Available Qty</th>
                                <th>Qty to Issue</th>
                                <th>Requested By</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($listrequest as $lists)
                                @php
                                    $approvedQty = $lists->qty ?? $lists->qty_requested;
                                    $avail = $stockAvailability[$lists->id] ?? [
                                        'available_qty' => 0,
                                        'available_effective_qty' => 0,
                                        'total_qty_multiplier' => null,
                                        'expired_only' => false,
                                        'batch_count' => 0,
                                        'batches' => [],
                                    ];
                                    $noStock = $avail['expired_only'] || $avail['available_qty'] <= 0;
                                    $maxIssue = $noStock ? 0 : min($approvedQty, $avail['available_qty']);
                                    $availClass = $avail['expired_only'] ? 'expired' : ($avail['available_qty'] <= 0 ? 'none' : ($avail['available_qty'] < $approvedQty ? 'low' : 'ok'));
                                    $usesSatellite = !empty($usesSatelliteByLine[$lists->id]);
                                @endphp
                                <tr data-request-id="{{ $lists->id }}"
                                    data-approved="{{ $approvedQty }}"
                                    data-available="{{ $avail['available_qty'] }}"
                                    data-no-stock="{{ $noStock ? '1' : '0' }}"
                                    data-expired-only="{{ $avail['expired_only'] ? '1' : '0' }}"
                                    data-uses-satellite="{{ $usesSatellite ? '1' : '0' }}"
                                    data-item-name="{{ $lists->itemname->name ?? 'Item' }}">
                                    <td>
                                        {{ $loop->iteration }}
                                        <input type="hidden" name="request_id[]" value="{{ $lists->id }}">
                                    </td>
                                    <td><code>{{ $lists->itemcode->item_code ?? '—' }}</code></td>
                                    <td class="fw-semibold">{{ $lists->itemname->name ?? '—' }}</td>
                                    <td>
                                        @php
                                            $itemUnitId = old('unit_id.'.$lists->id, optional($lists->itemname)->unit_id);
                                        @endphp
                                        @if($usesSatellite)
                                            <select name="unit_id[{{ $lists->id }}]" class="form-select uom-select" aria-label="Unit of measure">
                                                <option value="" disabled {{ $itemUnitId ? '' : 'selected' }}>Choose unit</option>
                                                @foreach($listunit as $unit)
                                                    <option value="{{ $unit->id }}" {{ (string) $itemUnitId === (string) $unit->id ? 'selected' : '' }}>
                                                        {{ $unit->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        @else
                                            {{ optional($lists->itemname->unitname)->name ?? '—' }}
                                        @endif
                                    </td>
                                    <td><strong>{{ $lists->qty_requested }}</strong></td>
                                    <td><strong class="text-success">{{ $approvedQty }}</strong></td>
                                    <td>
                                        @if($avail['expired_only'])
                                            <span class="avail-badge expired">Expired</span>
                                            <div class="batch-hint">All batches expired</div>
                                        @else
                                            <span class="avail-badge {{ $availClass }}">{{ number_format($avail['available_qty']) }}</span>
                                            @if($avail['batch_count'] > 0)
                                                <div class="batch-hint">
                                                    {{ $avail['batch_count'] }} batch{{ $avail['batch_count'] !== 1 ? 'es' : '' }}
                                                    @if($avail['nearest_expiry'])
                                                        · nearest expiry {{ \Carbon\Carbon::parse($avail['nearest_expiry'])->format('M d, Y') }}
                                                    @endif
                                                </div>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @if($noStock)
                                            <input type="hidden" name="qty[{{ $lists->id }}]" value="0">
                                            <input type="number"
                                                   value="0"
                                                   class="form-control qty-input qty-to-issue"
                                                   min="0"
                                                   max="0"
                                                   step="1"
                                                   disabled
                                                   tabindex="-1"
                                                   aria-label="No stock available">
                                            <div class="batch-hint text-secondary">No stock — cannot enter quantity</div>
                                        @else
                                            <input type="number"
                                                   name="qty[{{ $lists->id }}]"
                                                   value="{{ old('qty.' . $lists->id, $maxIssue > 0 ? $maxIssue : '') }}"
                                                   class="form-control qty-input qty-to-issue"
                                                   min="0"
                                                   max="{{ $maxIssue }}"
                                                   step="1"
                                                   placeholder="0">
                                        @endif
                                    </td>
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
                    <button type="submit" class="btn-issue-submit btn-confirm-issue">
                        <i class="bi bi-box-arrow-right"></i> Issue Selected Items
                    </button>
                </div>
            </form>
        @else
            <div class="text-center py-5 text-muted">
                <p>No approved line items found for this requisition.</p>
                <a href="{{ route('IssueItem') }}" class="btn btn-outline-secondary btn-sm">Back to list</a>
            </div>
        @endif
    </div>
</div>

@include('stock.reject-request-modal')

<div class="modal fade unit-modal" id="requisitionUnitModal" tabindex="-1" aria-labelledby="requisitionUnitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title d-flex align-items-center gap-2" id="requisitionUnitModalLabel">
                    <i class="bi bi-rulers"></i> Unit from requisition
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-3">
                    Enter the unit this requisition is coming from. It will be applied to all satellite items on this issue.
                </p>
                <label for="requisitionUnitInput" class="form-label">Unit</label>
                <input type="text"
                       id="requisitionUnitInput"
                       class="form-control"
                       maxlength="100"
                       placeholder="Unit from requisition"
                       autocomplete="off">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-modal-clear" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn-modal-continue" id="requisitionUnitContinue">
                    Continue
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
const IssueAlert = {
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
    issued(message) {
        return this._base({
            icon: 'success',
            title: 'Submitted for Issue',
            html: '<p class="mb-0">' + message + '</p>'
                + '<small class="text-muted d-block mt-2">Items are now pending issue approval by HOD before stock is deducted and dispatched.</small>',
            btnClass: 'issue',
            timer: 4500,
            timerProgressBar: true,
            confirmButtonText: '<i class="bi bi-hourglass-split me-1"></i> OK',
        });
    },
    warning(title, text) {
        return this._base({
            icon: 'warning',
            title: title,
            text: text,
            btnClass: 'issue',
            confirmButtonText: '<i class="bi bi-check-lg me-1"></i> OK',
        });
    },
    validationErrors(errors) {
        return this._base({
            icon: 'error',
            title: 'Cannot Issue Items',
            html: '<ul class="text-start mb-0 ps-3" style="font-size:0.875rem;line-height:1.6;">'
                + errors.map(function (e) { return '<li class="mb-1">' + e + '</li>'; }).join('')
                + '</ul>',
            btnClass: 'error',
            confirmButtonText: '<i class="bi bi-x-lg me-1"></i> Fix & retry',
        });
    },
    confirmIssue(onConfirm) {
        return this._base({
            icon: 'question',
            title: 'Issue Items?',
            html: '<p class="mb-2">Confirm issuing the quantities entered?</p>'
                + '<small class="text-muted">Stock will be allocated from nearest-expiry batches first (FEFO), then sent for HOD approval.</small>',
            btnClass: 'issue',
            showCancelButton: true,
            confirmButtonText: '<i class="bi bi-box-arrow-right me-1"></i> Yes, issue',
            cancelButtonText: 'Cancel',
        }).then(function (result) {
            if (result.isConfirmed && onConfirm) onConfirm();
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

    $('.btn-confirm-issue').on('click', function (e) {
        e.preventDefault();
        var form = $('#issueForm');
        var errors = [];
        var hasPositiveQty = false;
        var hasNoStockLine = false;
        var hasSatelliteQty = false;

        $('#vsrTable tbody tr[data-request-id]').each(function () {
            var row = $(this);
            var itemName = row.data('item-name');
            var approved = parseInt(row.data('approved'), 10) || 0;
            var available = parseInt(row.data('available'), 10) || 0;
            var noStock = row.data('no-stock') === 1 || row.data('no-stock') === '1';
            var expiredOnly = row.data('expired-only') === 1 || row.data('expired-only') === '1';
            var usesSatellite = row.data('uses-satellite') === 1 || row.data('uses-satellite') === '1';
            var input = row.find('.qty-to-issue');

            if (noStock) {
                hasNoStockLine = true;
                return;
            }

            var raw = input.val();
            var qty = raw === '' ? 0 : parseInt(raw, 10);

            input.removeClass('is-invalid');

            if (raw === '' || isNaN(qty)) {
                return;
            }

            if (qty < 0) {
                errors.push(itemName + ': quantity cannot be negative.');
                input.addClass('is-invalid');
                return;
            }

            if (qty === 0) {
                return;
            }

            hasPositiveQty = true;
            if (usesSatellite) {
                hasSatelliteQty = true;
            }

            if (expiredOnly) {
                errors.push(itemName + ': all stock batches have expired.');
                input.addClass('is-invalid');
                return;
            }

            if (qty > approved) {
                errors.push(itemName + ': cannot issue more than approved quantity (' + approved + ').');
                input.addClass('is-invalid');
                return;
            }

            if (qty > available) {
                errors.push(itemName + ': requested quantity (' + qty + ') exceeds available stock (' + available + ').');
                input.addClass('is-invalid');
                return;
            }
        });

        if (!hasPositiveQty && !hasNoStockLine) {
            IssueAlert.warning('No Quantities Entered', 'Enter a quantity greater than zero for at least one item with available stock.');
            return;
        }

        if (errors.length) {
            IssueAlert.validationErrors(errors);
            return;
        }

        var submitIssue = function () {
            IssueAlert.confirmIssue(function () {
                form.submit();
            });
        };

        if (hasSatelliteQty) {
            var unitModalEl = document.getElementById('requisitionUnitModal');
            var unitModal = bootstrap.Modal.getOrCreateInstance(unitModalEl);
            $('#requisitionUnitInput').val($('#requisitionUnitHidden').val() || '');
            unitModal.show();
            unitModalEl.addEventListener('shown.bs.modal', function () {
                $('#requisitionUnitInput').trigger('focus');
            }, { once: true });
            return;
        }

        submitIssue();
    });

    $('#requisitionUnitContinue').on('click', function () {
        var unit = $.trim($('#requisitionUnitInput').val() || '');
        $('#requisitionUnitHidden').val(unit);
        var unitModalEl = document.getElementById('requisitionUnitModal');
        var unitModal = bootstrap.Modal.getInstance(unitModalEl);
        var showConfirm = function () {
            IssueAlert.confirmIssue(function () {
                $('#issueForm').submit();
            });
        };

        if (unitModal && $(unitModalEl).hasClass('show')) {
            $(unitModalEl).one('hidden.bs.modal', showConfirm);
            unitModal.hide();
            return;
        }

        showConfirm();
    });

    $('#requisitionUnitInput').on('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            $('#requisitionUnitContinue').trigger('click');
        }
    });

    $('.qty-to-issue').on('input', function () {
        $(this).removeClass('is-invalid');
    });
});

@if(session('message_success'))
IssueAlert.issued(@json(session('message_success')));
@endif

@if(session('message_error'))
IssueAlert.error('Issue Failed', @json(session('message_error')));
@endif
</script>
@endsection
