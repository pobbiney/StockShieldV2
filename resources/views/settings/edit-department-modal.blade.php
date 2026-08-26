<div class="modal fade" id="editDepartmentModal" tabindex="-1" aria-labelledby="editDepartmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg dept-modal-content">
            <div class="dept-modal-header">
                <h5 class="modal-title" id="editDepartmentModalLabel">
                    <i class="bi bi-pencil-square me-2"></i>Edit Department
                </h5>
                <p class="text-secondary small mb-0 mt-1">Update department name and status.</p>
            </div>
            <form method="POST" enctype="multipart/form-data" action="{{ route('edit-department-process') }}">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold" for="docname">Department Name</label>
                        <input type="text" id="docname" name="name" class="form-control" placeholder="e.g. Logistics" required>
                        @error('name')<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-semibold" for="statusname">Status</label>
                        <select class="form-select" name="status" id="statusname" required>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                        @error('status')<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="dept-modal-footer d-flex justify-content-end gap-2">
                    <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-modal-save">
                        <i class="bi bi-check2"></i> Save Changes
                    </button>
                </div>
                <input type="hidden" name="loan_id" id="docID">
            </form>
        </div>
    </div>
</div>
