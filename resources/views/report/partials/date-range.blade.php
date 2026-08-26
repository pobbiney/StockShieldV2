<div>
    <label class="form-label">Start Date</label>
    <input type="text" class="form-control datepicker1" name="start_date"
           value="{{ old('start_date', request('start_date')) }}" placeholder="Start date">
    @error('start_date') <small class="text-danger">{{ $message }}</small> @enderror
</div>
<div>
    <label class="form-label">End Date</label>
    <input type="text" class="form-control datepicker2" name="end_date"
           value="{{ old('end_date', request('end_date')) }}" placeholder="End date">
    @error('end_date') <small class="text-danger">{{ $message }}</small> @enderror
</div>
