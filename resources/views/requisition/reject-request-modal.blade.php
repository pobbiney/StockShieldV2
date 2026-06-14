 <div class="modal fade" id="standardmodal" tabindex="-1" aria-labelledby="standardmodalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data" action="{{ route('add-reject-request-process') }}">
                @csrf
                <div class="modal-header">
                    <p class="modal-title h5" id="standardmodalLabel">Reject Request</p> 
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                     
                      
                    <div class="form-group mb-3 position-relative check-valid">
                            <div class="form-floating">
                                   <textarea class="form-control" name="reason"></textarea>
                                <label>Reasons</label>
                                @error('reason') <small style="color:red"> {{ $message}}</small> @enderror
                            </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-theme">Save Changes</button>
                </div>
                 <input type="hidden" id="itemID" name="item_id"/>
            </form>
        </div>
    </div>
</div>