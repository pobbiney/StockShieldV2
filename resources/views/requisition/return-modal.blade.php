<div class="modal fade" id="returnModal" tabindex="-1" aria-labelledby="returnModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="border-radius: 1rem; overflow: hidden; border: none;">
            <form method="POST" action="{{ route('add-retrun-item-process') }}" id="returnForm">
                @csrf
                <div class="modal-header border-0 pb-0" style="background: linear-gradient(135deg, #7c2d12, #c2410c); color: #fff;">
                    <div>
                        <h5 class="modal-title fw-bold" id="returnModalLabel">Initiate Return</h5>
                        <p class="mb-0 small opacity-75">Submit for manager approval</p>
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
                                <h6 class="mb-1 fw-bold" id="return_item_name">Item</h6>
                                <div class="small text-secondary">
                                    Code: <span id="return_item_code" class="fw-semibold">—</span>
                                    &middot; Batch: <span id="return_batch_label" class="fw-semibold">—</span>
                                </div>
                                <div class="small text-secondary mt-1">
                                    Available: <strong id="return_available_qty">0</strong>
                                    &middot; Expiry: <span id="return_expiry_label">—</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Return Type</label>
                        <select class="form-select" name="return_status" required>
                            <option value="" selected disabled>Choose return type</option>
                            <option value="All">Full return (all units)</option>
                            <option value="Part">Partial return</option>
                        </select>
                        @error('return_status') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Quantity to Return</label>
                        <input type="number"
                               name="quantity"
                               id="return_quantity"
                               class="form-control"
                               min="1"
                               required>
                        @error('quantity') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-semibold">Reason / Comment</label>
                        <textarea class="form-control"
                                  name="comment"
                                  rows="3"
                                  placeholder="Explain why this stock is being returned..."
                                  required></textarea>
                        @error('comment') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <input type="hidden" id="return_item_id" name="item_id">
                    <input type="hidden" id="return_batch_number" name="batch_number">
                    <input type="hidden" id="return_max_qty">
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn text-white fw-semibold" style="background: #c2410c;">
                        <i class="bi bi-send me-1"></i> Submit Return
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
