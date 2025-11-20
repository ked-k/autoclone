<div class="row">
    <div class="d-sm-flex align-items-center border-bottom ">
        <h6 class="text-success">Participant</h6>
    </div>
    
    <div class="col-md-12 mt-2">
        {{-- <form wire:submit.prevent="updateParticipant"> --}}
            <div class="row mx-auto">
                <div class="mb-3 col-md-2">
                    <label for="identity" class="form-label">Participant ID
                    </label>
                    <input type="text" id="identity" class="form-control" size="14" wire:model.lazy="identity">
                    @error('identity')
                        <div class="text-danger text-small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3 col-md-1">
                    <label for="age" class="form-label">Age</label>
                    <input type="number" id="age" class="form-control" wire:model.lazy="age">
                    @error('age')
                        <div class="text-danger text-small">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3 col-md-1">
                    <label for="age" class="form-label">Months</label>
                    <input type="text" id="months" class="form-control" wire:model.lazy="months">
                    @error('months')
                        <div class="text-danger text-small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3 col-md-2">
                    <label for="gender" class="form-label">Gender<span class="text-danger">*</span></label>
                    <select class="form-select select2" id="gender" wire:model="gender">
                        <option selected value="">Select</option>
                        <option value='Male'>Male</option>
                        <option value='Female'>Female</option>
                        <option value='N/A'>N/A</option>
                    </select>
                    @error('gender')
                        <div class="text-danger text-small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3 col">
                    <label for="address" class="form-label">Address<span class="text-danger">*</span></label>
                    <input type="text" id="address" class="form-control text-uppercase"
                        onkeyup="this.value = this.value.toUpperCase();" wire:model.lazy="address">
                    @error('address')
                        <div class="text-danger text-small">{{ $message }}</div>
                    @enderror
                </div>
            </div>
{{-- 
            <div class="modal-footer">
                <x-button class="btn-success">{{ __('Update') }}</x-button>
            </div>
            <!-- end row-->
        </form> --}}
    </div>
</div>
