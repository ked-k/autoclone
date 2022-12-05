<div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pt-0">
                    <div class="row mb-2">
                        <div class="col-sm-12 mt-3">
                            <div class="d-sm-flex align-items-center">
                                <h5 class="mb-2 mb-sm-0">
                                    <h6 class="modal-title" id="staticBackdropLabel">Attach Test Results For Sample (<span
                                            class="text-info">{{ $sample_identity }}</span>) with Lab_No <span
                                            class="text-info">{{ $lab_no }}</span></h6>
                                </h5>
                                {{-- result:{{ $result }} comment:{{ $comment }} performed_by:{{ $performed_by }} --}}
                                <div class="ms-auto">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-outline-info">More...</button>
                                        <button type="button"
                                            class="btn btn-outline-info split-bg-primary dropdown-toggle dropdown-toggle-split"
                                            data-bs-toggle="dropdown"> <span class="visually-hidden">Toggle
                                                Dropdown</span>
                                        </button>

                                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg-end">
                                            <a class="dropdown-item" href="javascript:;" wire:click="close()">Reset
                                                form</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if (!$testsRequested->isEmpty())
                        <div class="row">
                            <div class="mb-0">
                                <div class="table-responsive">
                                    <table class="table table-striped mb-0 w-100">
                                        <thead>
                                            <tr>
                                                <th>Test Requested</th>
                                                <th>Results and Comments</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{-- @if (!$testsRequested->isEmpty()) --}}
                                            @foreach ($testsRequested as $test)
                                                <tr>
                                                    <td>
                                                        <a href="javascript: void(0);" class="action-ico"
                                                            wire:click="activateResultInput({{ $test->id }})"><strong
                                                                class="text-success">{{ $test->name }}
                                                            </strong></a>
                                                    </td>
                                                    <td>
                                                        @if ($test->id === $test_id)
                                                            <form wire:submit.prevent="storeTestResults()"
                                                                class="me-2">
                                                                <div class="row">
                                                                    <div class="col-md-5">
                                                                        @if ($test->result_type == 'Absolute')
                                                                            <div class="mb-2">
                                                                                <label class="form-label">Result</label>
                                                                                <select class="form-select"
                                                                                    id="result" wire:model.lazy="result">
                                                                                    <option selected value="">
                                                                                        Select</option>
                                                                                    @foreach ($test->absolute_results as $result)
                                                                                        <option
                                                                                            value='{{ $result }}'>
                                                                                            {{ $result }}</option>
                                                                                    @endforeach
                                                                                </select>
                                                                                @error('result')
                                                                                    <div class="text-danger text-small">
                                                                                        {{ $message }}</div>
                                                                                @enderror
                                                                            </div>
                                                                        @elseif($test->result_type == 'Text')
                                                                            <div class="mb-2">
                                                                                <label class="form-label">Result</label>
                                                                                <textarea rows="2" class="form-control" placeholder="{{ __('Enter Free text Results') }}" wire:model.lazy="result"></textarea>
                                                                                @error('result')
                                                                                    <div class="text-danger text-small">
                                                                                        {{ $message }}</div>
                                                                                @enderror
                                                                            </div>
                                                                        @elseif($test->result_type == 'Measurable')
                                                                            <div class="mb-2">
                                                                                <div class="form-group">
                                                                                    <label
                                                                                        class="form-label">Result</label>
                                                                                    <div
                                                                                        class="input-group form-group mb-2">
                                                                                        <input type="number"
                                                                                            step="0.001"
                                                                                            class="form-control"
                                                                                            id="result"
                                                                                            wire:model.lazy='result'>
                                                                                        <div class="input-group-append">
                                                                                            <span
                                                                                                class="input-group-text">
                                                                                                {{ $test->measurable_result_uom }}
                                                                                            </span>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                @error('result')
                                                                                    <div class="text-danger text-small">
                                                                                        {{ $message }}</div>
                                                                                @enderror
                                                                            </div>
                                                                        @elseif($test->result_type == 'File')
                                                                            <div class="mb-2">
                                                                                <label class="form-label">Result
                                                                                    Attachment</label>
                                                                                <input type="file"
                                                                                    class="form-control"
                                                                                    wire:model="attachment"
                                                                                    placeholder="Attach file">
                                                                                @error('attachment')
                                                                                    <div class="text-danger text-small">
                                                                                        {{ $message }}</div>
                                                                                @enderror
                                                                            </div>
                                                                        @elseif($test->result_type == 'Link')
                                                                            <div class="mb-2">
                                                                                <label class="form-label">Result
                                                                                    Link(URL)</label>
                                                                                <input type="text"
                                                                                    class="form-control"
                                                                                    wire:model.lazy="link"
                                                                                    placeholder="Enter valid link">
                                                                                @error('link')
                                                                                    <div class="text-danger text-small">
                                                                                        {{ $message }}</div>
                                                                                @enderror
                                                                            </div>
                                                                        @endif
                                                                    </div>

                                                                    {{-- COMMENTS --}}
                                                                    <div class="col-md-3">
                                                                        <div class="mb-2">
                                                                            <label class="form-label">Comment</label>
                                                                            @if ($test->comments != null)
                                                                                <select class="form-select"
                                                                                    id="comment" wire:model="comment">
                                                                                    <option selected value="">
                                                                                        Select</option>
                                                                                    @foreach ($test->comments as $comment)
                                                                                        <option
                                                                                            value='{{ $comment }}'>
                                                                                            {{ $comment }}
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                            @else
                                                                                <textarea wire:model.lazy="comment" rows="2" class="form-control" placeholder="{{ __('comment') }}"></textarea>
                                                                            @endif
                                                                            @error('comment')
                                                                                <div class="text-danger text-small">
                                                                                    {{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <div class="mb-2">
                                                                            <label class="form-label">Performed
                                                                                By</label>
                                                                            <select class="form-select"
                                                                                wire:model="performed_by">
                                                                                <option selected value="">Select
                                                                                </option>
                                                                                @foreach ($users as $user)
                                                                                    <option
                                                                                        value='{{ $user->id }}'>
                                                                                        {{ $user->fullName }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                            @error('performed_by')
                                                                                <div class="text-danger text-small">
                                                                                    {{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-1 mt-4">
                                                                        <x-button>{{ __('Save') }}</x-button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        @else
                                                            <p>Please click Test to enter Result</p>
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
