<div class="modal fade" id="editStoreModal" tabindex="-1" aria-labelledby="editStoreModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg store-modal-content">
            <div class="store-modal-header">
                <h5 class="modal-title" id="editStoreModalLabel">
                    <i class="bi bi-pencil-square me-2"></i>Edit Store
                </h5>
                <p class="text-secondary small mb-0 mt-1">Update store name, group, and status.</p>
            </div>
            <form method="POST" enctype="multipart/form-data" action="{{ route('edit-store-process') }}">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold" for="edit_store_name">Store Name</label>
                        <input type="text" id="edit_store_name" name="name" class="form-control" placeholder="e.g. Main Warehouse" required>
                        @error('name')<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold" for="edit_store_group">Store Group</label>
                        <select class="form-select" name="store_group" id="edit_store_group">
                            <option value="">No group</option>
                            <option value="central">Central Store</option>
                            <option value="satellite">Satellite Store</option>
                        </select>
                        @error('store_group')<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold" for="edit_store_status">Status</label>
                        <select class="form-select" name="status" id="edit_store_status" required>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                        @error('status')<div class="field-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-0" id="edit_route_to_hub_wrap">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="route_requisitions_to_hub" value="1" id="edit_route_requisitions_to_hub">
                            <label class="form-check-label small" for="edit_route_requisitions_to_hub">
                                Route requisitions to hub store
                            </label>
                        </div>
                        <p class="text-secondary small mb-0 mt-1">When enabled, this satellite store sends requisitions to the configured requisition hub instead of central stores.</p>
                    </div>
                </div>
                <div class="store-modal-footer d-flex justify-content-end gap-2">
                    <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-modal-save">
                        <i class="bi bi-check2"></i> Save Changes
                    </button>
                </div>
                <input type="hidden" name="store_id" id="edit_store_id">
            </form>
        </div>
    </div>
</div>
