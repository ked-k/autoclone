@if ($test)
    <div class="row">
        <div class="d-sm-flex align-items-center border-bottom ">
            <h6 class="text-success">Current Results</h6>
        </div>
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
                        <tr>
                            <td><strong class="text-success">{{ $test->name }}</strong>
                            </td>
                            <td>
                                {{-- <form wire:submit.prevent="updateResult()" class="me-2"> --}}
                                <div class="row">
                                    <div class="col">
                                        @if ($test->result_type == 'Absolute')
                                            <div class="mb-2">
                                                <label class="form-label">Result</label>
                                                <select class="form-select select2" id="result"
                                                    wire:model.lazy="result">
                                                    <option selected value="">
                                                        Select</option>
                                                    @foreach ($test->absolute_results as $result)
                                                        <option value='{{ $result }}'>
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
                                                <textarea rows="2" class="form-control" placeholder="{{ __('Enter Free text Results') }}"
                                                    wire:model.lazy="result"></textarea>
                                                @error('result')
                                                    <div class="text-danger text-small">
                                                        {{ $message }}</div>
                                                @enderror
                                            </div>
                                        @elseif($test->result_type == 'Measurable')
                                            <div class="mb-2">
                                                <div class="form-group">
                                                    <label class="form-label">Result</label>
                                                    <div class="input-group form-group mb-2">
                                                        <input type="number" step="0.001" class="form-control"
                                                            id="result" wire:model.lazy='result'>
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">
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
                                                <input type="file" class="form-control" wire:model="attachment"
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
                                                <input type="text" class="form-control" wire:model.lazy="link"
                                                    placeholder="Enter valid link" readonly>
                                                @error('link')
                                                    <div class="text-danger text-small">
                                                        {{ $message }}</div>
                                                @enderror
                                            </div>
                                        @endif
                                    </div>

                                    {{-- COMMENTS --}}
                                    <div class="col">
                                        <div class="mb-2">
                                            <label class="form-label">Comment</label>
                                            @if ($test->comments != null)
                                                <select class="form-select select2" id="comment" wire:model="comment">
                                                    <option selected value="">
                                                        Select</option>
                                                    @foreach ($test->comments as $comment)
                                                        <option value='{{ $comment }}'>
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
                                </div>
                                {{-- PARAMETERS --}}
                                @if (count($testParameters) > 0)
                                    <div class="row">
                                        <hr>
                                        <h6>Parameters</h6>
                                        @foreach ($testParameters as $parameter => $value)
                                            <div class="col">
                                                <div class="mb-2">
                                                    <label class="form-label">{{ $parameter }}</label>
                                                    <input type="text" class="form-control"
                                                        wire:model.lazy="testParameters.{{ $parameter }}"
                                                        placeholder="{{ $value }}">
                                                </div>
                                            </div>
                                        @endforeach
                                        @error('testParameters')
                                            <div class="text-danger text-small">
                                                {{ $message }}</div>
                                        @enderror
                                    </div>
                                @endif
                                {{-- KIT USED --}}

                                <div class="row">
                                    <hr>
                                    <h6>Kit Used</h6>
                                    <div class="col">
                                        <div class="mb-2">
                                            <label class="form-label">Kit Used</label>
                                            <select class="form-select select2" wire:model="kit_id">
                                                <option selected value="">Select
                                                </option>
                                                @foreach ($kits as $kit)
                                                    <option value='{{ $kit->id }}'>
                                                        {{ $kit->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('kit_id')
                                                <div class="text-danger text-small">
                                                    {{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col">
                                        <div class="mb-2">
                                            <label class="form-label">Verified Lot</label>
                                            <textarea wire:model.lazy="verified_lot" rows="1" class="form-control" placeholder="{{ __('verified lot') }}"></textarea>

                                            @error('verified_lot')
                                                <div class="text-danger text-small">
                                                    {{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col">
                                        <div class="mb-2">
                                            <label class="form-label">Kit expiry date</label>
                                            <input type="date" name="kit_expiry_date" class="form-control"
                                                id="kit_expiry_date" wire:model="kit_expiry_date">
                                            @error('kit_expiry_date')
                                                <div class="text-danger text-small">
                                                    {{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- <div class="col mt-4">
                                            <x-button>{{ __('Update') }}</x-button>
                                        </div> --}}

                                </div>
                                {{-- </form> --}}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div> <!-- end preview-->
    </div>
    <hr>
@endif
