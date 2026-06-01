 <div class="modal fade" id="standardmodal" tabindex="-1" aria-labelledby="standardmodalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data" action="{{ route('edit-document-process') }}">
                @csrf
                <div class="modal-header">
                    <p class="modal-title h5" id="standardmodalLabel">Update Document Type</p>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                <div class="form-group mb-3 position-relative check-valid">
                        <div class="form-floating">
                            <input type="text" id="docname"  name="name" class="form-control"  placeholder="Enter Document Type">
                            <label>Document Name</label>
                            @error('name') <small style="color:red"> {{ $message}}</small> @enderror
                        </div>

                </div>
                 <div class="form-group mb-3 position-relative check-valid">
                    <div class="form-floating">
                        <textarea class="form-control" id="docdescription" name="description" placeholder="Enter Description" rows="5"></textarea>
                        <label>Description</label>
                        @error('description') <small style="color:red"> {{ $message}}</small> @enderror
                    </div>
                 </div>
                 <div class="form-group mb-3 position-relative check-valid">
                    <div class="form-floating">
                        <select class="form-control" name="loan_type" id="loantypename">
                                <option value="" selected disabled>--Select Loan Type--</option>
                        @foreach ($listtype as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                        </select>
                        
                         @error('loan_type') <small style="color:red"> {{ $message}}</small> @enderror
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
                 <input type="hidden" name="document_id" id="docID">
            </form>
        </div>
    </div>
</div>