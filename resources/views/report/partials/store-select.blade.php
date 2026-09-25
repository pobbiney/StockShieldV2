<div>
    <label class="form-label">Store</label>
    <div class="rp-select-wrap">
        <i class="bi bi-shop"></i>
        <select class="form-select" name="department" required>
            <option value="" disabled {{ old('department', request('department')) ? '' : 'selected' }}>Choose store</option>
            @foreach($liststores as $store)
                <option value="{{ $store->id }}" {{ (string) old('department', request('department')) === (string) $store->id ? 'selected' : '' }}>
                    {{ $store->name }}
                </option>
            @endforeach
        </select>
    </div>
    @error('department') <small class="text-danger">{{ $message }}</small> @enderror
</div>
