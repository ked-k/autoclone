<div class="row">
    <div class="d-sm-flex align-items-center border-bottom ">
        <h6 class="text-success">Sample collected</h6>
    </div>
    <div class="col-md-12 mt-2">
        {{-- <form wire:submit.prevent="updateSampleInformation"> --}}
        <div class="row mx-auto">
            <div class="mb-3 col-md-3">
                <label for="study_id" class="form-label">Study</label>
                <select class="form-select select2" id="study_id" wire:model="study_id">
                    <option selected value="">Select</option>
                    @forelse ($studies as $study)
                        <option value='{{ $study->id }}'>{{ $study->name }}
                        </option>
                    @empty
                    @endforelse
                </select>
                @error('study_id')
                    <div class="text-danger text-small">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3 col-md-3">
                <label for="sample_identity" class="form-label">Sample ID</label>
                <input id="sample_identity" type="text" class="form-control" wire:model.lazy="sample_identity">
                @error('sample_identity')
                    <div class="text-danger text-small">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3 col-md-3">
                <label for="date_collected" class="form-label">Collection
                    Date/Time</label>
                <input id="date_collected" type="datetime-local" class="form-control" wire:model.lazy="date_collected">
                @error('date_collected')
                    <div class="text-danger text-small">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- <div class="modal-footer">
                <x-button class="btn-success">{{ __('Update') }}</x-button>
            </div>
            <!-- end row-->
        </form> --}}
    </div>
</div>
