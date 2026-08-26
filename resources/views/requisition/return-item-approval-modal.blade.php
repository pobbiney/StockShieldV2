<div class="modal fade" id="returnApprovalModal" tabindex="-1" aria-labelledby="returnApprovalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="border-radius: 1rem; overflow: hidden; border: none;">
            <form method="POST" action="{{ route('add-retrun-item-approval-process') }}" id="returnApprovalForm">
                @csrf
                <div class="modal-header border-0 pb-0" style="background: linear-gradient(135deg, #1e3a8a, #2563eb); color: #fff;">
                    <div>
                        <h5 class="modal-title fw-bold" id="returnApprovalModalLabel">Review Return Request</h5>
                        <p class="mb-0 small opacity-75">Approve or reject this return</p>
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
                                    &middot; Expiry: <span id="approval_expiry_label">—</span>
                                </div>
                                <div class="small text-secondary mt-1">
                                    Return qty: <strong id="approval_return_qty">0</strong>
                                    &middot; Type: <span id="approval_return_type">—</span>
                                    &middot; Batch stock: <span id="approval_available_qty">—</span>
                                </div>
                                <div class="small text-secondary mt-1">
                                    Returned by: <span id="approval_returned_by">—</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-3 p-3 mb-4" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                        <div class="small text-secondary mb-1">Staff comment</div>
                        <div class="small" id="approval_staff_comment">—</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Decision</label>
                        <select class="form-select" name="status" required>
                            <option value="" selected disabled>Choose decision</option>
                            <option value="approve">Approve return</option>
                            <option value="reject">Reject return</option>
                        </select>
                        @error('status') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-semibold">HOD Comment</label>
                        <textarea class="form-control"
                                  name="comment"
                                  rows="3"
                                  placeholder="Add your approval or rejection notes..."
                                  required></textarea>
                        @error('comment') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <input type="hidden" id="approval_item_id" name="item_id">
                    <input type="hidden" id="approval_batch_number" name="batch_number">
                    <input type="hidden" id="approval_qty" name="qty">
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn text-white fw-semibold" style="background: #2563eb;">
                        <i class="bi bi-check-lg me-1"></i> Submit Decision
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
