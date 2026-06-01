 <div class="modal fade" id="standardmodal" tabindex="-1" aria-labelledby="standardmodalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data" action="{{ route('edit-itemcategory-process') }}">
                @csrf
                <div class="modal-header">
                    <p class="modal-title h5" id="standardmodalLabel">Update Category  </p>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3 position-relative check-valid">
                        <div class="form-floating">
                        
                            <input type="text" id="catname"  name="name" class="form-control"  placeholder="Enter Loan Type">
                            <label>Name</label>
                            @error('name') <small style="color:red"> {{ $message}}</small> @enderror
                        </div>
                    </div>
                    <div class="form-group mb-3 position-relative check-valid">
                            <div class="form-floating">
                                <select class="form-control" name="status" id="statusname">
                                    <option value="" selected disabled>--Choose Option--</option>
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                                <label>Status</label>
                                @error('status') <small style="color:red"> {{ $message}}</small> @enderror
                            </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-theme">Save changes</button>
                </div>
                 <input type="hidden" name="cat_id" id="catID">
            </form>
        </div>
    </div>
</div>