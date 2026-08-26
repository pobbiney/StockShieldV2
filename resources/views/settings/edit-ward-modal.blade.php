<div class="modal fade" id="editWardModal" tabindex="-1" aria-labelledby="editWardModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg ward-modal-content">
            <div class="ward-modal-header">
                <h5 class="modal-title" id="editWardModalLabel">
                    <i class="bi bi-pencil-square me-2"></i>Edit Ward
                </h5>
                <p class="text-secondary small mb-0 mt-1">Update ward name and status.</p>
            </div>
            <form method="POST" action="{{ route('edit-ward-process') }}">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold" for="wardname">Ward Name</label>
                        <input type="text" id="wardname" name="name" class="form-control" placeholder="e.g. Ward A" required>
                        @error('name')<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-semibold" for="wardstatus">Status</label>
                        <select class="form-select" name="status" id="wardstatus" required>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                        @error('status')<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="ward-modal-footer d-flex justify-content-end gap-2">
                    <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-modal-save">
                        <i class="bi bi-check2"></i> Save Changes
                    </button>
                </div>
                <input type="hidden" name="ward_id" id="wardID">
            </form>
        </div>
    </div>
</div>
