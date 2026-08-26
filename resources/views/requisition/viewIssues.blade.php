@php
    $pageName = 'stock';
    $subpageName = 'approve-issues';
    $totalIssueQty = (int) ($groupedIssues ?? collect())->sum('prepared_qty');
    $totalLineValue = (float) ($groupedIssues ?? collect())->sum(function ($group) {
        return $group->lines->sum(fn ($line) => $line->prepared_qty * $line->amount);
    });
    $uniqueItemCount = ($groupedIssues ?? collect())->count();
@endphp

@extends('layouts.backendapp')

@section('css')
<style>
    .vi-page { padding: 0 0.5rem 2rem; }

    .vi-hero {
        background: linear-gradient(135deg, #5b21b6 0%, #7c3aed 60%, #a78bfa 100%);
        border-radius: 1.25rem;
        padding: 1.75rem 2rem;
        margin-bottom: 1.5rem;
        color: #fff;
        box-shadow: 0 8px 32px rgba(124, 58, 237, 0.25);
    }

    .vi-hero h2 {
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

    .vi-summary {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-bottom: 1.25rem;
    }

    .vi-summary-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.55rem 0.9rem;
        border-radius: 0.75rem;
        background: #fff;
        border: 1px solid rgba(0, 0, 0, 0.06);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        font-size: 0.82rem;
        color: #475569;
    }

    .vi-summary-chip strong { color: #0f172a; }

    .vi-table-card {
        border-radius: 1.25rem;
        border: 1px solid rgba(0, 0, 0, 0.06);
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
    }

    .vi-table-head {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    #viTable thead th {
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

    #viTable tbody td {
        padding: 0.85rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
    }

    #viTable tbody tr:nth-child(even) { background: #fafafa; }
    #viTable tbody tr:hover { background: #f5f3ff !important; }

    .qty-input {
        max-width: 100px;
        border-radius: 0.5rem;
        border: 1.5px solid #e2e8f0;
    }

    .qty-input.is-invalid { border-color: #dc2626; }

    .qty-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 2rem;
        padding: 0.2rem 0.55rem;
        border-radius: 0.375rem;
        background: rgba(124, 58, 237, 0.1);
        color: #7c3aed;
        font-weight: 700;
        font-size: 0.82rem;
    }

    .stock-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 2rem;
        padding: 0.2rem 0.55rem;
        border-radius: 0.375rem;
        font-weight: 700;
        font-size: 0.82rem;
    }

    .stock-badge.ok   { background: rgba(16, 185, 129, 0.12); color: #047857; }
    .stock-badge.low  { background: rgba(245, 158, 11, 0.12); color: #b45309; }
    .stock-badge.none { background: rgba(239, 68, 68, 0.12); color: #dc2626; }

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
        line-height: 1.45;
    }

    .batch-breakdown span {
        display: inline-block;
        margin-right: 0.5rem;
        white-space: nowrap;
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

    .approve-note {
        margin: 0 1.5rem 1rem;
        padding: 0.75rem 1rem;
        border-radius: 0.75rem;
        background: rgba(124, 58, 237, 0.08);
        border: 1px solid rgba(124, 58, 237, 0.15);
        font-size: 0.82rem;
        color: #5b21b6;
    }

    .vi-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid #f1f5f9;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .vi-footer-meta {
        font-size: 0.82rem;
        color: #64748b;
    }

    .btn-approve-submit {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.65rem 1.25rem;
        border-radius: 0.625rem;
        border: none;
        background: #7c3aed;
        color: #fff;
        font-weight: 600;
    }

    .btn-approve-submit:hover { background: #6d28d9; color: #fff; }

    .staff-swal-confirm.approve { background: #7c3aed !important; }
    .staff-swal-confirm.approve:hover { background: #6d28d9 !important; }
</style>
@endsection

@section('content')

<div class="container-fluid vi-page px-3 px-lg-4 mt-3">

    <div class="vi-hero">
        <a href="{{ route('IssueApproval') }}" class="btn-back-link d-inline-flex align-items-center gap-1 mb-2">
            <i class="bi bi-arrow-left"></i> Back to Issue Approval
        </a>
        <h2>Review Issue Request</h2>
        <span class="req-no-badge">{{ $requisitionNo ?? '' }}</span>
        @if($listissues->isNotEmpty())
            @php $first = $listissues->first(); @endphp
            <p class="mb-0 mt-2 opacity-90">
                Issue to <strong>{{ $first->storename->name ?? '—' }}</strong>
                · From <strong>{{ $first->issuefrom->name ?? 'Central Store' }}</strong>
                · Prepared by <strong>{{ $first->staffname->name ?? '—' }}</strong>
            </p>
        @endif
    </div>

    @if(($groupedIssues ?? collect())->isNotEmpty())
        <div class="vi-summary">
            <span class="vi-summary-chip">
                <i class="bi bi-list-check text-primary"></i>
                <strong>{{ $uniqueItemCount }}</strong> unique item{{ $uniqueItemCount !== 1 ? 's' : '' }}
                @if($listissues->count() > $uniqueItemCount)
                    <span class="text-muted">({{ $listissues->count() }} batch lines)</span>
                @endif
            </span>
            <span class="vi-summary-chip">
                <i class="bi bi-box-seam text-success"></i>
                Total issue qty: <strong>{{ number_format($totalIssueQty) }}</strong>
            </span>
            <span class="vi-summary-chip">
                <i class="bi bi-currency-exchange text-warning"></i>
                Est. value: <strong>{{ number_format($totalLineValue, 2) }}</strong>
            </span>
        </div>
    @endif

    <div class="vi-table-card mb-5">
        <div class="vi-table-head">
            <h5 class="mb-0 fw-bold"><i class="bi bi-clipboard2-check me-1 text-primary"></i> Pending Issue Lines</h5>
            @if(($groupedIssues ?? collect())->isNotEmpty())
                <span class="badge rounded-pill text-bg-warning-subtle text-warning-emphasis border border-warning-subtle">
                    <i class="bi bi-hourglass-split me-1"></i> Pending Issue
                </span>
            @endif
        </div>

        @if(($groupedIssues ?? collect())->isNotEmpty())
            <div class="approve-note">
                <i class="bi bi-info-circle me-1"></i>
                Items are grouped by product. Multiple batches (FEFO) appear under each item. Confirm total quantities to approve — stock will be <strong>deducted per batch</strong> and an invoice generated.
            </div>

            <form method="POST" action="{{ route('approveIssue-process') }}" id="approveIssueForm">
                @csrf
                <div class="table-responsive">
                    <table class="table mb-0" id="viTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Item Code</th>
                                <th>Item Name</th>
                                <th>UoM</th>
                                <th>Batches</th>
                                <th>Stock Balance</th>
                                <th>Qty Requested</th>
                                <th>Prepared Qty</th>
                                <th>Approve Qty</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($groupedIssues as $group)
                                @php
                                    $preparedQty = (int) $group->prepared_qty;
                                    $totalBalance = (int) $group->total_balance;
                                    $stockClass = $totalBalance <= 0 ? 'none' : ($totalBalance < $preparedQty ? 'low' : 'ok');
                                @endphp
                                <tr class="issue-group"
                                    data-item-id="{{ $group->item_id }}"
                                    data-item-name="{{ $group->itemname->name ?? 'Item' }}"
                                    data-prepared="{{ $preparedQty }}"
                                    data-balance="{{ $totalBalance }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td><code>{{ $group->itemcode->item_code ?? '—' }}</code></td>
                                    <td class="fw-semibold">{{ $group->itemname->name ?? '—' }}</td>
                                    <td>{{ $group->itemname->unitname->name ?? '—' }}</td>
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
                                                <span>{{ $line->batch_number }}: {{ $line->prepared_qty }} prep · {{ $line->balance }} avail</span>
                                            @endforeach
                                        </div>
                                        @foreach($group->lines as $line)
                                            <span class="issue-line d-none"
                                                  data-issue-id="{{ $line->issue_id }}"
                                                  data-prepared="{{ $line->prepared_qty }}"
                                                  data-balance="{{ $line->balance }}"></span>
                                            <input type="hidden" name="issue_id[]" value="{{ $line->issue_id }}" class="issue-id-input" data-issue-id="{{ $line->issue_id }}">
                                            <input type="hidden" name="qty[{{ $line->issue_id }}]" value="0" class="issue-qty-hidden" data-issue-id="{{ $line->issue_id }}">
                                        @endforeach
                                    </td>
                                    <td><span class="stock-badge {{ $stockClass }}">{{ $totalBalance > 0 ? number_format($totalBalance) : 'Out' }}</span></td>
                                    <td><span class="qty-badge">{{ number_format($group->requested_qty) }}</span></td>
                                    <td><span class="qty-badge" style="background:rgba(59,130,246,0.1);color:#2563eb;">{{ number_format($preparedQty) }}</span></td>
                                    <td>
                                        <input type="number"
                                               class="form-control qty-input qty-to-approve-group"
                                               value="{{ old('group_qty.' . $group->item_id, $preparedQty) }}"
                                               min="0"
                                               max="{{ max($preparedQty, $totalBalance) }}"
                                               required>
                                        @if($group->lines->count() > 1)
                                            <small class="text-muted d-block mt-1" style="font-size:0.68rem;">Split across batches on approve</small>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($group->lines->count() === 1)
                                            <button type="button"
                                                    class="btn-reject-row showmodal"
                                                    data-url="{{ route('issue-item-id', $group->lines->first()->issue_id) }}"
                                                    data-item-name="{{ $group->itemname->name ?? 'Item' }}"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#standardmodal">
                                                <i class="bi bi-x-circle"></i> Reject
                                            </button>
                                        @else
                                            <div class="d-flex flex-column gap-1 align-items-end">
                                                @foreach($group->lines as $line)
                                                    <button type="button"
                                                            class="btn-reject-row showmodal"
                                                            style="font-size:0.68rem;padding:0.3rem 0.65rem;"
                                                            data-url="{{ route('issue-item-id', $line->issue_id) }}"
                                                            data-item-name="{{ ($group->itemname->name ?? 'Item') . ' (' . $line->batch_number . ')' }}"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#standardmodal">
                                                        <i class="bi bi-x-circle"></i> {{ $line->batch_number }}
                                                    </button>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <input type="hidden" name="store_id" value="{{ $listissues->first()->issue_to }}">

                <div class="vi-footer">
                    <div class="vi-footer-meta">
                        Approving will deduct stock per batch and mark lines as <strong>issued</strong>.
                    </div>
                    <button type="submit" class="btn-approve-submit btn-confirm-approve">
                        <i class="bi bi-patch-check"></i> Approve All Issues
                    </button>
                </div>
            </form>
        @else
            <div class="text-center py-5 text-muted">
                <p>No pending issue lines found for this requisition.</p>
                <a href="{{ route('IssueApproval') }}" class="btn btn-outline-secondary btn-sm">Back to Issue Approval</a>
            </div>
        @endif
    </div>
</div>

@include('stock.reject-issue-modal')

@endsection

@section('scripts')
<script>
const IssueApprovalAlert = {
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
            title: 'Issues Approved',
            html: '<p class="mb-0">' + message + '</p>'
                + '<small class="text-muted d-block mt-2">Stock has been deducted and an invoice was generated for dispatch.</small>',
            btnClass: 'approve',
            timer: 4000,
            timerProgressBar: true,
            confirmButtonText: '<i class="bi bi-check-lg me-1"></i> Done',
        });
    },
    rejected(message) {
        return this._base({
            icon: 'warning',
            title: 'Issue Line Rejected',
            html: '<p class="mb-0">' + message + '</p>'
                + '<small class="text-muted d-block mt-2">The rejected line will not proceed to stock deduction.</small>',
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
    validationErrors(errors) {
        return this._base({
            icon: 'error',
            title: 'Cannot Approve Issues',
            html: '<ul class="text-start mb-0 ps-3" style="font-size:0.875rem;line-height:1.6;">'
                + errors.map(function (e) { return '<li class="mb-1">' + e + '</li>'; }).join('')
                + '</ul>',
            btnClass: 'error',
            confirmButtonText: '<i class="bi bi-x-lg me-1"></i> Fix & retry',
        });
    },
    confirmApprove(onConfirm) {
        return this._base({
            icon: 'question',
            title: 'Approve All Issues?',
            html: '<p class="mb-2">Confirm approving the quantities shown?</p>'
                + '<small class="text-muted">Stock will be deducted from the central store and an invoice will be generated.</small>',
            btnClass: 'approve',
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
        var form = $('#approveIssueForm');
        var errors = [];

        // Reset all hidden qty inputs
        form.find('.issue-qty-hidden').val(0);
        form.find('.issue-id-input').prop('disabled', true);

        $('#viTable tbody tr.issue-group').each(function () {
            var group = $(this);
            var itemName = group.data('item-name');
            var totalPrepared = parseInt(group.data('prepared'), 10) || 0;
            var totalBalance = parseInt(group.data('balance'), 10) || 0;
            var input = group.find('.qty-to-approve-group');
            var totalApprove = parseInt(input.val(), 10);

            input.removeClass('is-invalid');

            if (isNaN(totalApprove) || input.val() === '') {
                errors.push(itemName + ': enter a valid approve quantity.');
                input.addClass('is-invalid');
                return;
            }

            if (totalApprove < 0) {
                errors.push(itemName + ': quantity cannot be negative.');
                input.addClass('is-invalid');
                return;
            }

            if (totalApprove > totalPrepared) {
                errors.push(itemName + ': cannot approve more than prepared quantity (' + totalPrepared + ').');
                input.addClass('is-invalid');
                return;
            }

            if (totalApprove > totalBalance) {
                errors.push(itemName + ': insufficient stock balance (' + totalBalance + ' available across batches).');
                input.addClass('is-invalid');
                return;
            }

            if (totalApprove === 0) {
                return;
            }

            var remaining = totalApprove;
            group.find('.issue-line').each(function () {
                var line = $(this);
                var issueId = line.data('issue-id');
                var prepared = parseInt(line.data('prepared'), 10) || 0;
                var balance = parseInt(line.data('balance'), 10) || 0;
                var allocate = Math.min(remaining, prepared, balance);

                if (allocate > 0) {
                    form.find('.issue-qty-hidden[data-issue-id="' + issueId + '"]').val(allocate);
                    form.find('.issue-id-input[data-issue-id="' + issueId + '"]').prop('disabled', false);
                    remaining -= allocate;
                }
            });

            if (remaining > 0) {
                errors.push(itemName + ': could not allocate full quantity across batches.');
                input.addClass('is-invalid');
            }
        });

        if (errors.length) {
            IssueApprovalAlert.validationErrors(errors);
            return;
        }

        var hasApprovedLine = form.find('.issue-qty-hidden').filter(function () {
            return parseInt($(this).val(), 10) > 0;
        }).length > 0;

        if (!hasApprovedLine) {
            IssueApprovalAlert.error('Nothing to approve', 'Enter an approve quantity greater than zero for at least one item.');
            return;
        }

        IssueApprovalAlert.confirmApprove(function () {
            form.submit();
        });
    });

    $('.qty-to-approve-group').on('input', function () {
        $(this).removeClass('is-invalid');
    });
});

@if(session('message_success'))
@php $successMsg = session('message_success'); @endphp
@if(stripos($successMsg, 'reject') !== false)
IssueApprovalAlert.rejected(@json($successMsg));
@else
IssueApprovalAlert.approved(@json($successMsg));
@endif
@endif

@if(session('message_error'))
IssueApprovalAlert.error('Approval Failed', @json(session('message_error')));
@endif
</script>
@endsection
