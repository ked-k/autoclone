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
                            <div class="col-md-12">
                                <table class="table table-striped mb-0 w-100">
                                    <thead>
                                        <tr>
                                            <th>{{ auth()->user()->laboratory->laboratory_name }}</th>
                                            <th>SOURCE FACILITY</th>
                                            <th>SAMPLE DATA</th>
                                            <th>PARTICIPANT</th>
                                            @if ($active_referral)
                                                <th>REFERRED TO</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <strong class="text-inverse">Entry Type: </strong>
                                                {{ $sample->participant->entry_type }}<br>
                                                <strong class="text-inverse">Entry Date:
                                                </strong>{{ date('d-m-Y H:i', strtotime($sample->participant->created_at ?? 'NA')) }}<br>
                                                <strong class="text-inverse">Sample Count:
                                                </strong>{{ $sample->participant->sample_count ?? 'N/A' }}<br>
                                                <strong class="text-inverse">Test Count:
                                                </strong>{{ $sample->participant->test_result_count ?? 'N/A' }}
                                            </td>
                                            <td>
                                                <strong class="text-inverse">Name: </strong>
                                                {{ $sample->participant->facility->name ?? 'N/A' }}<br>
                                                <strong class="text-inverse">Study: </strong>
                                                {{ $sample->participant->study->name ?? 'N/A' }}
                                            </td>
                                            <td>
                                                <strong class="text-inverse">Sample Id: </strong>
                                                {{ $sample->sample_identity ?? 'N/A' }}<br>
                                                <strong class="text-inverse">Lab No: </strong>
                                                {{ $sample->lab_no ?? 'N/A' }}<br>
                                                <strong class="text-inverse">Collection Date: </strong>
                                                {{ $sample->date_collected ?? 'N/A' }}<br>
                                                <strong class="text-inverse">Request Date: </strong>
                                                {{ $sample->date_requested ?? 'N/A' }}
                                            </td>
                                            <td>
                                                <strong class="text-inverse">Participant ID:
                                                </strong>{{ $sample->participant->identity ?? 'N/A' }}<br>
                                                <strong class="text-inverse">Age:
                                                </strong>
                                                @if ($sample->participant->age != null)
                                                    {{ $sample->participant->age }}yrs &nbsp;
                                                @elseif ($sample->participant->months != null)
                                                    {{ $sample->participant->months }}months
                                                @else
                                                    N/A
                                                @endif
                                                </strong>{{ $sample->participant->gender ?? 'N/A' }}<br>
                                                <strong class="text-inverse">Contact:
                                                </strong>{{ $sample->participant->contact ?? 'N/A' }}<br>
                                                <strong class="text-inverse">Address:
                                                </strong>{{ $sample->participant->address ?? 'N/A' }}<br>
                                                <strong class="text-inverse">Kin Contact:
                                                </strong>{{ $sample->participant->nok_contact ?? 'N/A' }}
                                            </td>

                                            @if ($active_referral)
                                                <td>
                                                    <strong class="text-inverse">Name: </strong>
                                                    {{ $active_referral->referralable->name ?? ($active_referral->referralable->laboratory_name ?? 'N/A') }}<br>

                                                </td>
                                            @endif
                                        </tr>
                                        @if ($sample->participant->clinical_notes)
                                            <tr>
                                                <td colspan="3">
                                                    <strong class="text-inverse">Clinical Notes
                                                    </strong>
                                                    <p>{{ $sample->participant->clinical_notes }}</p>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-12 mb-0">
                                <div class="table-responsive">
                                    <table class="table table-striped mb-0 w-100">
                                        <thead>
                                            <tr>
                                                <th>Test Requested</th>
                                                <th>Results and Comments</th>
                                            </tr>
                                        </thead>
                                        <tbody>
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
                                                                    @if ($test->result_type == 'Multiple')
                                                                        <div class="col-9">
                                                                            <div class="row">
                                                                                <hr>
                                                                                <h6>Tests</h6>
                                                                                @foreach ($test->sub_tests as $key => $sub_test)
                                                                                    <div class="col-md-4">
                                                                                        <div class="mb-2">
                                                                                            {{-- <label class="form-label">{{ $sub_test }}</label> --}}
                                                                                            <input class="form-label"
                                                                                                type="text"
                                                                                                style="border: none; outline: none;  background-color: transparent;"
                                                                                                readonly
                                                                                                wire:model="testResults.{{ $key }}.test"
                                                                                                required
                                                                                                value="{{ $sub_test }}">
                                                                                            <input type="text"
                                                                                                required
                                                                                                class="form-control"
                                                                                                wire:model="testResults.{{ $key }}.result"
                                                                                                {{-- wire:model.lazy="subTestResults.{{ $sub_test }}" --}}
                                                                                                placeholder="{{ $sub_test }} results">
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-2">
                                                                                        <div class="mb-2">
                                                                                            <label class="form-label">Ct
                                                                                                Value</label>
                                                                                            <input type="text"
                                                                                                required
                                                                                                class="form-control"
                                                                                                wire:model="testResults.{{ $key }}.CtValue"
                                                                                                placeholder="Ct Value">
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <div class="mb-2">
                                                                                            <label
                                                                                                class="form-label">{{ $sub_test }}
                                                                                                Comment</label>
                                                                                            <textarea type="text" required class="form-control" {{-- wire:model.lazy="subTestResultComment.{{ $sub_test }}" --}}
                                                                                                wire:model="testResults.{{ $key }}.comment" placeholder="Enter {{ $sub_test }} comments"></textarea>
                                                                                        </div>
                                                                                    </div>
                                                                                    @error('subTestResults')
                                                                                        <div class="text-danger text-small">
                                                                                            {{ $message }}</div>
                                                                                    @enderror
                                                                                @endforeach
                                                                            </div>
                                                                        </div>
                                                                    @else
                                                                        <div class="col-md-5">
                                                                            @if ($test->result_type == 'Absolute')
                                                                                @if(is_array($test?->absolute_results))
                                                                                <div class="mb-2">
                                                                                    <label
                                                                                        class="form-label">Result</label>
                                                                                    <select class="form-select"
                                                                                        id="result"
                                                                                        wire:model.lazy="result">
                                                                                        <option selected value="">
                                                                                            Select</option>
                                                                                        @foreach ($test?->absolute_results as $result)
                                                                                            <option
                                                                                                value='{{ $result }}'>
                                                                                                {{ $result }}
                                                                                            </option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                    @error('result')
                                                                                        <div class="text-danger text-small">
                                                                                            {{ $message }}</div>
                                                                                    @enderror
                                                                                </div>
                                                                                @else
                                                                                <div class="mb-2">
                                                                                    <label
                                                                                        class="form-label">Result</label>
                                                                                    <input type="text"
                                                                                        class="form-control"
                                                                                        id="result"
                                                                                        wire:model.lazy="result"
                                                                                        placeholder="Enter Result">
                                                                                    @error('result')
                                                                                        <div class="text-danger text-small">
                                                                                            {{ $message }}</div>
                                                                                    @enderror
                                                                                    @endif
                                                                                </div>
                                                                            @elseif($test->result_type == 'Text')
                                                                                <div class="mb-2">
                                                                                    <label
                                                                                        class="form-label">Result</label>
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
                                                                                        <label
                                                                                            class="form-label">Result</label>
                                                                                        <div
                                                                                            class="input-group form-group mb-2">
                                                                                            <input type="number"
                                                                                                step="0.001"
                                                                                                class="form-control"
                                                                                                id="result"
                                                                                                wire:model.lazy='result'>
                                                                                            <div
                                                                                                class="input-group-append">
                                                                                                <span
                                                                                                    class="input-group-text">
                                                                                                    {{ $test->measurable_result_uom }}
                                                                                                </span>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    @error('result')
                                                                                        <div
                                                                                            class="text-danger text-small">
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
                                                                                        <div
                                                                                            class="text-danger text-small">
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
                                                                                        <div
                                                                                            class="text-danger text-small">
                                                                                            {{ $message }}</div>
                                                                                    @enderror
                                                                                </div>
                                                                            @endif

                                                                        </div>

                                                                        {{-- COMMENTS --}}
                                                                        <div class="col-md-4">
                                                                            <div class="mb-2">
                                                                                <label
                                                                                    class="form-label">Comment</label>
                                                                                @if ($test->comments != null)
                                                                                    <select class="form-select"
                                                                                        id="comment"
                                                                                        wire:model="comment">
                                                                                        <option selected
                                                                                            value="">
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
                                                                    @endif
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

                                                                </div>

                                                                {{-- PARAMETERS --}}
                                                                @if ($test->parameters != null)
                                                                    <div class="row">
                                                                        <hr>
                                                                        <h6>Parameters</h6>
                                                                        @foreach ($test->parameters as $parameter)
                                                                            <div class="col-md-4">
                                                                                <div class="mb-2">
                                                                                    <label
                                                                                        class="form-label">{{ $parameter }}</label>
                                                                                    <input type="text"
                                                                                        class="form-control"
                                                                                        wire:model.lazy="testParameters.{{ $parameter }}"
                                                                                        placeholder="Enter parameter value">
                                                                                </div>
                                                                            </div>
                                                                        @endforeach
                                                                        @error('testParameters')
                                                                            <div class="text-danger text-small">
                                                                                {{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                @endif

                                                                {{-- KIT USED INFO --}}
                                                                <div class="row">
                                                                    <hr>
                                                                    <h6>Kit Used</h6>

                                                                    <div class="col-md-4">
                                                                        <div class="mb-2">
                                                                            <label class="form-label">Kit</label>
                                                                            <select class="form-select select2"
                                                                                data-model="kit_id" id="kit_id"
                                                                                wire:model="kit_id">
                                                                                <option selected value="">Select
                                                                                </option>
                                                                                @foreach ($kits as $kit)
                                                                                    <option
                                                                                        value='{{ $kit->id }}'>
                                                                                        {{ $kit->name }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                            @error('kit_id')
                                                                                <div class="text-danger text-small">
                                                                                    {{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-md-4">
                                                                        <div class="mb-2">
                                                                            <label class="form-label">Verified Kit
                                                                                Lot</label>
                                                                            <input wire:model.lazy="verified_lot"
                                                                                class="form-control"
                                                                                placeholder="{{ __('verified lot') }}">

                                                                            @error('verified_lot')
                                                                                <div class="text-danger text-small">
                                                                                    {{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-md-4">
                                                                        <div class="mb-2">
                                                                            <label class="form-label">Kit Expiry
                                                                                Date</label>
                                                                            <input type="date"
                                                                                name="kit_expiry_date"
                                                                                class="form-control"
                                                                                id="kit_expiry_date"
                                                                                wire:model="kit_expiry_date" required>
                                                                            @error('kit_expiry_date')
                                                                                <div class="text-danger text-small">
                                                                                    {{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    @php
                                                                        $dateRequested = \Carbon\Carbon::parse(
                                                                            $sample->sampleReception->date_delivered ??
                                                                                $sample->date_requested,
                                                                        );
                                                                        $tat = $dateRequested->diffInHours($today);
                                                                    @endphp
                                                                    @if ($test->tat > 0 && $tat > $test->tat)
                                                                        <div class="col-md-12">
                                                                            <div class="mb-2">
                                                                                <label class="form-label">Reason why
                                                                                    the result is outside the
                                                                                    TAT <small class="text-danger">The
                                                                                        test was requested on
                                                                                        {{ $sample->sampleReception->date_delivered ?? $sample->date_requested }}
                                                                                        and the TAT for this test is
                                                                                        {{ $test?->tat }} and the
                                                                                        result TAT is
                                                                                        {{ $tat }}</small></label>
                                                                                <textarea wire:model.lazy="tat_comment" id="tat_comment" required class="form-control"></textarea>
                                                                                @error('tat_comment')
                                                                                    <div class="text-danger text-small">
                                                                                        {{ $message }}</div>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                    @if ($active_referral)
                                                                        <div class="row">
                                                                            @if (!$test->result_type == 'File')
                                                                                <div class="col-md-4">
                                                                                    <div class="mb-2">
                                                                                        <label
                                                                                            class="form-label">Result
                                                                                            File</label>
                                                                                        <input type="file"
                                                                                            wire:model.lazy="ref_result_file"
                                                                                            class="form-control"
                                                                                            required>

                                                                                        @error('ref_result_file')
                                                                                            <div
                                                                                                class="text-danger text-small">
                                                                                                {{ $message }}</div>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>
                                                                            @endif
                                                                            <div class="col-md-4">
                                                                                <div class="mb-2">
                                                                                    <label class="form-label">Received
                                                                                        On</label>
                                                                                    <input type="date"
                                                                                        wire:model.lazy="received_date"
                                                                                        class="form-control">

                                                                                    @error('received_date')
                                                                                        <div
                                                                                            class="text-danger text-small">
                                                                                            {{ $message }}</div>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md">
                                                                                <div class="mb-2">
                                                                                    <label class="form-label">Referral
                                                                                        Comments</label>
                                                                                    <textarea wire:model="ref_comments" id="ref_comments" class="form-control"></textarea>
                                                                                    @error('ref_comments')
                                                                                        <div
                                                                                            class="text-danger text-small">
                                                                                            {{ $message }}</div>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>

                                                                        </div>
                                                                    @endif
                                                                </div>



                                                                <div class="modal-footer">
                                                                    <x-button
                                                                        class="me-0">{{ __('Save') }}</x-button>
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
    @push('scripts')
        @include('livewire.layout.select-2')
    @endpush
</div>
