<div class="row">
    <div class="d-sm-flex align-items-center border-bottom ">
        <h6 class="text-danger">Amendment Reason</h6>
    </div>
    <div class="col-md-12 mt-2">
        <form wire:submit.prevent="showEditForms">
            <div class="row mx-auto">
                <div class="mb-3 col-md-4">
                    <label for="amendment_type" class="form-label">Amendment type</label>
                    <select class="form-select select2" id="amendment_type" wire:model="amendment_type">
                        <option selected value="">Select</option>
                        <option value="Post-result-issuance">Post result issuance</option>
                        <option value="Pre-result-issuance">Pre result issuance</option>
                    </select>
                    @error('amendment_type')
                        <div class="text-danger text-small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3 col-md-8">
                    <label for="amendment_comment" class="form-label">Comment/Reason for amendment</label>
                    <textarea id="amendment_comment" type="text" class="form-control" wire:model.lazy="amendment_comment"
                        placeholder="Comment"></textarea>
                    @error('amendment_comment')
                        <div class="text-danger text-small">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="modal-footer">
                <x-button class="btn-success">{{ __('submit') }}</x-button>
            </div>
            <!-- end row-->
        </form>
    </div>
</div>
