<div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pt-0">
                    <div class="row mb-2">
                        <div class="col-sm-12 mt-2">
                            <div class="d-sm-flex align-items-center">
                                <h5 class="mb-2 mb-sm-0">
                                    Result Amendment
                                </h5>
                                <div class="ms-auto">

                                    <a type="button" class="btn btn-outline-info" wire:click="refresh()"
                                        data-bs-toggle="tooltip" data-bs-placement="top" title=""
                                        data-bs-original-title="Refresh Table"><i class="bi bi-arrow-clockwise"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-0">
                        <div class="mb-3 col-md-8">
                            <label for="result_tracker" class="form-label">Result Tracker</label>
                            <input id="result_tracker" type="text" class="form-control"
                                wire:model.lazy="result_tracker" placeholder="Enter result tracker to load details">
                            @error('result_tracker')
                                <div class="text-danger text-small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mt-4 col-md-4">
                            <a href="javascript: void(0);" wire:click="getResultDetails()"
                                class="action-ico btn btn-outline-success mx-1">
                                <i class="bi bi-search"></i>Load Details</a>
                        </div>
                        {{-- <button wire:click='copyAmended'>Copy</button> --}}
                    </div>
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div><!-- end col-->
    </div>

    @if ($testResultId != null)
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pt-0">
                        <div class="row mb-2">
                            <div class="col-sm-12 mt-3">
                                <div class="d-sm-flex align-items-center">
                                        <h5 class="modal-title" id="staticBackdropLabel">Amend Test Results For <strong
                                                class="text-info">{{ $testResults->tracker }}</strong></h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @include('livewire.lab.sample-management.result-amendment.amendment-reason')
                        @if ($toggleEditForms)
                            <div class="alert border-0 bg-light-info show">
                                <div
                                    class="d-flex align-items-center text-warning">
                                    <div class="fs-3"><i class="bi bi-exclamation-triangle-fill"></i>
                                    </div>
                                    <div class="ms-3">
                                        <div>Please be careful when performing this operation as this amendment can only be performed once for this result, After submitting, no more amendment will be performed!</div>
                                    </div>
                                </div>
                            </div>
                            @include('livewire.lab.sample-management.result-amendment.participant')
                            @include('livewire.lab.sample-management.result-amendment.sample')
                            @include('livewire.lab.sample-management.result-amendment.results')
                            <div class="modal-footer">
                                <x-button wire:click='amendResults()'>{{ __('Update') }}</x-button>
                            </div>
                        @endif
                       
                    </div> <!-- end body -->
                </div> <!-- end card -->
            </div><!-- end col-->
        </div>
    @endif
</div>
