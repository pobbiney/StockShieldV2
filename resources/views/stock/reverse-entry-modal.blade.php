<div class="modal fade" id="reverseModal" tabindex="-1" aria-labelledby="reverseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="border-radius: 1rem; overflow: hidden; border: none;">
            <form method="POST" action="{{ route('add-reverse-entry-process') }}" id="reverseForm">
                @csrf
                <div class="modal-header border-0 pb-0" style="background: linear-gradient(135deg, #7c2d12, #c2410c); color: #fff;">
                    <div>
                        <h5 class="modal-title fw-bold" id="reverseModalLabel">Submit Reverse Entry</h5>
                        <p class="mb-0 small opacity-75">Correction requires approval before inventory changes</p>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body pt-4">
                    <div class="rounded-3 p-3 mb-4" style="background: #fff7ed; border: 1px solid #fed7aa;">
                        <div class="d-flex align-items-start gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                 style="width: 44px; height: 44px; background: rgba(194, 65, 12, 0.15); color: #c2410c;">
                                <i class="bi bi-box-seam fs-5"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 fw-bold" id="reverse_item_name">Item</h6>
                                <div class="small text-secondary">
                                    Code: <span id="reverse_item_code" class="fw-semibold">—</span>
                                    &middot; Batch: <span id="reverse_batch_label" class="fw-semibold">—</span>
                                </div>
                                <div class="small text-secondary mt-1">
                                    Store: <span id="reverse_store_name">—</span>
                                    &middot; Expiry: <span id="reverse_expiry_label">—</span>
                                </div>
                                <div class="small text-secondary mt-1">
                                    Available: <strong id="reverse_available_qty">0</strong> units
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Reversal Type</label>
                        <select class="form-select" name="reversal_type" id="reverse_type" required>
                            <option value="" selected disabled>Choose reversal type</option>
                            <option value="partial">Partial — reverse specific quantity</option>
                            <option value="full">Full — reverse all available units</option>
                            <option value="delete">Delete — void entire batch (soft delete)</option>
                        </select>
                        @error('reversal_type') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3 d-none" id="reverse_qty_wrap">
                        <label class="form-label small fw-semibold">Quantity to Reverse</label>
                        <input type="number"
                               name="qty"
                               id="reverse_qty"
                               class="form-control"
                               min="1">
                        @error('qty') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-semibold">Reason / Comment</label>
                        <textarea class="form-control"
                                  name="reason"
                                  rows="3"
                                  placeholder="Explain why this batch entry needs correction..."
                                  required>{{ old('reason') }}</textarea>
                        @error('reason') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <input type="hidden" id="reverse_batch_number" name="batch_number" value="{{ old('batch_number') }}">
                    <input type="hidden" id="reverse_max_qty">
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn text-white fw-semibold" style="background: #c2410c;">
                        <i class="bi bi-send me-1"></i> Submit for Approval
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
