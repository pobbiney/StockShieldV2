 <div class="modal fade" id="standardmodal" tabindex="-1" aria-labelledby="standardmodalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data" action="{{ route('add-bulkupload-process') }}">
                @csrf
                <div class="modal-header">
                    <p class="modal-title h5" id="standardmodalLabel">Item Bulk Uploads</p> 
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                     
                      
                    <div class="form-group mb-3 position-relative check-valid">
                            <div class="form-floating">
                                  <input type="file" id="file"  name="file" class="form-control"  placeholder="Upload Document">
                                <label>Upload Document</label>
                                @error('file') <small style="color:red"> {{ $message}}</small> @enderror
                            </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-theme">Upload File</button>
                </div>
                 
            </form>
        </div>
    </div>
</div>