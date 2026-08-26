<div class="modal fade" id="reverseApprovalModal" tabindex="-1" aria-labelledby="reverseApprovalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="border-radius: 1rem; overflow: hidden; border: none;">
            <form method="POST" action="" id="reverseApprovalForm">
                @csrf
                <div class="modal-header border-0 pb-0" style="background: linear-gradient(135deg, #1e3a8a, #2563eb); color: #fff;">
                    <div>
                        <h5 class="modal-title fw-bold" id="reverseApprovalModalLabel">Review Reverse Entry</h5>
                        <p class="mb-0 small opacity-75">Approve or reject this batch correction</p>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body pt-4">
                    <div class="rounded-3 p-3 mb-4" style="background: #eff6ff; border: 1px solid #bfdbfe;">
                        <div class="d-flex align-items-start gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                 style="width: 44px; height: 44px; background: rgba(37, 99, 235, 0.15); color: #2563eb;">
                                <i class="bi bi-box-seam fs-5"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 fw-bold" id="approval_item_name">Item</h6>
                                <div class="small text-secondary">
                                    Code: <span id="approval_item_code" class="fw-semibold">—</span>
                                    &middot; Batch: <span id="approval_batch_label" class="fw-semibold">—</span>
                                </div>
                                <div class="small text-secondary mt-1">
                                    Store: <span id="approval_store_name">—</span>
                                </div>
                                <div class="small text-secondary mt-1">
                                    Reverse qty: <strong id="approval_reverse_qty">0</strong>
                                    &middot; Type: <span id="approval_reversal_type">—</span>
                                </div>
                                <div class="small text-secondary mt-1">
                                    Requested by: <span id="approval_requested_by">—</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-3 p-3 mb-4" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                        <div class="small text-secondary mb-1">Staff reason</div>
                        <div class="small" id="approval_staff_comment">—</div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-semibold">Approval Comment</label>
                        <textarea class="form-control"
                                  name="comment"
                                  id="approval_comment"
                                  rows="3"
                                  placeholder="Add your approval or rejection notes..."
                                  required></textarea>
                        @error('comment') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0 gap-2">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="btnRejectReversal" class="btn btn-outline-danger fw-semibold">
                        <i class="bi bi-x-lg me-1"></i> Reject
                    </button>
                    <button type="button" id="btnApproveReversal" class="btn text-white fw-semibold" style="background: #2563eb;">
                        <i class="bi bi-check-lg me-1"></i> Approve
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
