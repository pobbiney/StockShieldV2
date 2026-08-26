<div class="modal fade reject-modal" id="standardmodal" tabindex="-1" aria-labelledby="standardmodalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('add-rejection-process') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title d-flex align-items-center gap-2" id="standardmodalLabel">
                        <i class="bi bi-x-octagon-fill"></i> Reject Issue Line
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="reject-item-name mb-3">
                        <small class="text-muted d-block mb-1">Item</small>
                        <strong id="itemname">—</strong>
                    </div>
                    <p class="text-muted small mb-3">
                        This issue line will be rejected and will not proceed to stock deduction or dispatch.
                    </p>
                    <div class="mb-0">
                        <label for="rejectReason" class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control"
                                  id="rejectReason"
                                  name="reason"
                                  rows="4"
                                  required
                                  placeholder="Explain why this issue line is being rejected…"></textarea>
                        @error('reason') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-modal-clear" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-modal-reject">
                        <i class="bi bi-x-circle"></i> Confirm Rejection
                    </button>
                </div>
                <input type="hidden" id="itemID" name="item_id">
            </form>
        </div>
    </div>
</div>

<style>
    .reject-modal .modal-content {
        border-radius: 1.25rem;
        border: none;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.18);
    }

    .reject-modal .modal-header {
        background: linear-gradient(135deg, #991b1b 0%, #dc2626 100%);
        color: #fff;
        border: none;
        padding: 1.25rem 1.5rem;
    }

    .reject-modal .modal-header .modal-title {
        font-family: "SUSE", sans-serif;
        font-weight: 700;
        font-size: 1.05rem;
    }

    .reject-modal .modal-header .btn-close {
        filter: invert(1) grayscale(1) brightness(2);
    }

    .reject-modal .modal-body { padding: 1.35rem 1.5rem; }

    .reject-modal .modal-footer {
        border-top: 1px solid #f1f5f9;
        padding: 1rem 1.5rem;
        background: #f8fafc;
        gap: 0.5rem;
    }

    .reject-modal .form-label {
        font-size: 0.78rem;
        font-weight: 600;
        color: #64748b;
        margin-bottom: 0.35rem;
    }

    .reject-modal textarea {
        border-radius: 0.75rem;
        border: 1.5px solid #e2e8f0;
        font-size: 0.875rem;
        resize: vertical;
    }

    .reject-modal textarea:focus {
        border-color: #dc2626;
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.12);
    }

    .reject-item-name {
        padding: 0.75rem 1rem;
        border-radius: 0.75rem;
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #991b1b;
    }

    .btn-reject-row {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.4rem 0.9rem;
        border-radius: 2rem;
        border: none;
        background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
        color: #fff;
        font-size: 0.78rem;
        font-weight: 600;
        box-shadow: 0 3px 10px rgba(220, 38, 38, 0.28);
        transition: background 0.15s ease, transform 0.15s ease, box-shadow 0.15s ease;
        cursor: pointer;
        white-space: nowrap;
    }

    .btn-reject-row:hover {
        background: linear-gradient(135deg, #b91c1c 0%, #dc2626 100%);
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 5px 14px rgba(220, 38, 38, 0.35);
    }

    .btn-modal-reject {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.55rem 1.15rem;
        border-radius: 0.625rem;
        border: none;
        background: #dc2626;
        color: #fff;
        font-weight: 600;
        font-size: 0.875rem;
    }

    .btn-modal-reject:hover { background: #b91c1c; color: #fff; }

    .btn-modal-clear {
        display: inline-flex;
        align-items: center;
        padding: 0.55rem 1.15rem;
        border-radius: 0.625rem;
        border: 1.5px solid #e2e8f0;
        background: #fff;
        color: #64748b;
        font-weight: 600;
        font-size: 0.875rem;
    }

    .btn-modal-clear:hover { background: #f8fafc; color: #334155; }
</style>
