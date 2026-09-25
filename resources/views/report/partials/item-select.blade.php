<div>
    <label class="form-label">Item</label>
    <div class="rp-select-wrap">
        <i class="bi bi-box-seam"></i>
        <select class="js-example-basic-single form-select" name="item">
            <option value="" disabled {{ old('item', request('item')) ? '' : 'selected' }}>Choose item</option>
            @foreach ($getItemid as $listitems)
                <option value="{{ $listitems->id }}" {{ (string) old('item', request('item')) === (string) $listitems->id ? 'selected' : '' }}>
                    {{ $listitems->name }}
                </option>
            @endforeach
        </select>
    </div>
    @error('item') <small class="text-danger">{{ $message }}</small> @enderror
</div>
