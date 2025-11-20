<div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pt-0">
                    <div class="row mb-2">
                        <div class="col-sm-12 mt-3">
                            <div class="d-sm-flex align-items-center">
                                <h5 class="mb-2 mb-sm-0">
                                    <h6 class="modal-title" id="staticBackdropLabel">Aliquots For Sample (<span
                                            class="text-danger">{{ $sample->sample_identity }}</span>) with Lab_No <span
                                            class="text-info">{{ $sample->lab_no }}</span></h6>
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    {{-- <form wire:submit.prevent="storeAliquots">
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label fw-bold text-success">Aliquots</label>
                                @foreach ($aliquots as $key => $aliquot)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" wire:model='aliquots_performed'
                                            id="aliquot{{ $aliquot->id }}" value="{{ $aliquot->id }}">
                                        <label class="form-check-label"
                                            for="aliquot{{ $aliquot->id }}">{{ $aliquot->type }}</label>
                                        <input type="text" wire:model.lazy="aliquotIdentities.{{ $aliquot->id }}"
                                            class="form-control" placeholder="{{ $sample_identity.'-'.$key+1 }}">
                                    </div>
                                    <div class="mb-3 col-md-3">
                                        <label for="sample_identity" class="form-label">Sample ID</label>
                                        <input id="sample_identity" type="text" class="form-control"
                                            wire:model.lazy="sample_identity">
                                        @error('sample_identity')
                                            <div class="text-danger text-small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3 col-md-3">
                                        <label for="sample_is_for" class="form-label">Sample is For?<span
                                                class="text-danger">*</span></label>
                                        <select class="form-select select2"  id="sample_is_for"
                                            wire:model="sample_is_for">
                                            <option selected value="">Select</option>
                                            <option value='Testing'>Testing</option>
                                            <option value='Aliquoting'>Aliquoting</option>
                                            <option value='Deffered'>Deffered Testing</option>
                                            <option value='Storage'>Storage</option>
                                        </select>
                                        @error('sample_is_for')
                                            <div class="text-danger text-small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3 col-md-3">
                                        <label for="priority" class="form-label">Priority<span
                                                class="text-danger">*</span></label>
                                        <select class="form-select select2"  id="priority" wire:model="priority">
                                            <option selected value="">Select</option>
                                            <option value='Normal'>Normal</option>
                                            <option value='Urgent'>Urgent</option>
                                        </select>
                                        @error('priority')
                                            <div class="text-danger text-small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endforeach
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="comment" class="form-label">Comment</label>
                                <textarea wire:model.lazy="comment" rows="3" class="form-control" placeholder="{{ __('comment') }}"></textarea>

                                @error('comment')
                                    <div class="text-danger text-small">
                                        {{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <x-button>{{ __('Save') }}</x-button>
                        </div>
                    </form> --}}
                    @if (!$aliquots->isEmpty())
                        <div class="row">
                            <div class="mb-0">
                                <div class="table-responsive">
                                    <table class="table table-striped mb-0 w-100">
                                        <thead>
                                            <tr>
                                                <th>Aliquot Obtained</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($aliquotsRequested as $key => $aliquot)
                                                <tr>
                                                    <td>
                                                        <a href="javascript: void(0);" class="action-ico"
                                                            wire:click="activateAliquotInput({{ $aliquot->id }})"><strong
                                                                class="text-success">{{ $aliquot->type }}
                                                            </strong></a>
                                                    </td>
                                                    <td>
                                                        @if ($aliquot->id === $aliquot_id)
                                                            <form wire:submit.prevent="storeAliquotInformation">
                                                                <div class="row">

                                                                    {{-- <div class="form-check mb-2">
                                                                        <input class="form-check-input" type="checkbox" wire:model='aliquots_performed'
                                                                            id="aliquot{{ $aliquot->id }}" value="{{ $aliquot->id }}">
                                                                        <label class="form-check-label"
                                                                            for="aliquot{{ $aliquot->id }}">{{ $aliquot->type }}</label>
                                                                        <input type="text" wire:model.lazy="aliquotIdentities.{{ $aliquot->id }}"
                                                                            class="form-control" placeholder="{{ $sample_identity.'-'.$key+1 }}">
                                                                    </div> --}}
                                                                    <div class="mb-3 col-md-3">
                                                                        <label for="sample_identity"
                                                                            class="form-label">Aliquot ID</label>
                                                                        <input id="sample_identity" type="text"
                                                                            class="form-control"
                                                                            wire:model.lazy="sample_identity" readonly>
                                                                        @error('sample_identity')
                                                                            <div class="text-danger text-small">
                                                                                {{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                    <div class="mb-2 col-md-3">
                                                                        <div class="form-group">
                                                                            <label for="volume"
                                                                                class="form-label">{{ __('Volume Obtained') }}</label>
                                                                            <div class="input-group form-group mb-2">
                                                                                <input type="number" step="any"
                                                                                    class="form-control"
                                                                                    wire:model.lazy='volume'>
                                                                                <div class="input-group-append">
                                                                                    <span class="input-group-text">
                                                                                        ml
                                                                                    </span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        @error('volume')
                                                                            <div class="text-danger text-small">
                                                                                {{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                    <div class="mb-3 col-md-3">
                                                                        <label for="sample_is_for"
                                                                            class="form-label">Aliquot is For?<span
                                                                                class="text-danger">*</span></label>
                                                                        <select class="form-select select2"
                                                                            id="sample_is_for"
                                                                            wire:model="sample_is_for">
                                                                            <option selected value="">Select
                                                                            </option>
                                                                            <option value='Testing'>Testing</option>
                                                                            {{-- <option value='Aliquoting'>Aliquoting
                                                                            </option> --}}
                                                                            <option value='Deffered'>Deffered Testing
                                                                            </option>
                                                                            <option value='Storage'>Storage</option>
                                                                        </select>
                                                                        @error('sample_is_for')
                                                                            <div class="text-danger text-small">
                                                                                {{ $message }}</div>
                                                                        @enderror
                                                                    </div>

                                                                    <div class="mb-3 col-md-3">
                                                                        <label for="priority"
                                                                            class="form-label">Priority<span
                                                                                class="text-danger">*</span></label>
                                                                        <select class="form-select select2"
                                                                            id="priority" wire:model="priority">
                                                                            <option selected value="">Select
                                                                            </option>
                                                                            <option value='Normal'>Normal</option>
                                                                            <option value='Urgent'>Urgent</option>
                                                                        </select>
                                                                        @error('priority')
                                                                            <div class="text-danger text-small">
                                                                                {{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                    {{-- <div class="mb-3 col-md-3">
                                                                        <label for="comment"
                                                                            class="form-label">Comment</label>
                                                                        <textarea wire:model.lazy="comment" rows="3" class="form-control" placeholder="{{ __('comment') }}"></textarea>

                                                                        @error('comment')
                                                                            <div class="text-danger text-small">
                                                                                {{ $message }}</div>
                                                                        @enderror
                                                                    </div> --}}

                                                                </div>

                                                                @if ($sample_is_for == 'Testing' || $sample_is_for == 'Deffered')
                                                                    <div wire:loading.delay
                                                                        wire:target="updatedSampleIsFor">
                                                                        <div class="spinner-border text-info"
                                                                            role="status"> <span
                                                                                class="visually-hidden">Loading...</span>
                                                                        </div>
                                                                    </div>
                                                                    @if (!$tests->isEmpty())
                                                                        <div class="row mx-auto"
                                                                            wire:loading.class='invisible'>
                                                                            <h6> <strong class="text-success">Test(s)
                                                                                    Requested</strong>
                                                                            </h6>
                                                                            <hr>
                                                                            <div class="row col-md-12  mx-auto">
                                                                                @foreach ($tests as $test)
                                                                                    <div class="col-md-3">
                                                                                        <div
                                                                                            class="form-check form-check-inline mb-1">
                                                                                            <label
                                                                                                class="form-check-label"
                                                                                                for="test{{ $test->id }}">{{ $test->name }}</label>
                                                                                            <input
                                                                                                class="form-check-input"
                                                                                                type="checkbox"
                                                                                                id="test{{ $test->id }}"
                                                                                                value="{{ $test->id }}"
                                                                                                wire:model='tests_requested'>
                                                                                        </div>
                                                                                    </div>
                                                                                @endforeach
                                                                                @error('tests_requested')
                                                                                    <div class="text-danger text-small">
                                                                                        {{ $message }}</div>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                    @else
                                                                        <div class="row mx-auto"
                                                                            wire:loading.class='invisible'
                                                                            wire:target="updatedSampleIsFor">
                                                                            <div class="text-danger col-md-12">No
                                                                                associated tests! Please
                                                                                select
                                                                                sample type</div>
                                                                        </div>
                                                                    @endif
                                                                @endif
                                                                <div class="modal-footer">
                                                                    <x-button>{{ __('Save') }}</x-button>
                                                                </div>
                                                            </form>
                                                        @else
                                                            <p>Please click Aliquot to enter details</p>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div> <!-- end preview-->
                            </div>
                            <hr>
                        </div>
                    @else
                    @endif
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div><!-- end col-->
    </div>
</div>
