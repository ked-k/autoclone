<div class="card border shadow-none" id="reportContainer">
    <div class="card-header py-3">
        <div class="row align-items-center g-3">
            <div class="col-12 col-lg-6">
                <h5 class="mb-0">Preliminary Test Report for <strong
                        class="text-info">{{ $testResults->tracker }}</strong></h5>
            </div>
            <div class="col-12 col-lg-6 text-md-end">
                <a href="javascript:;" wire:click="$set('viewReport',false)" class="btn btn-sm btn-info me-2"><i
                        class="bi bi-list"></i>
                    Return to List
                </a>
            </div>
        </div>
    </div>
    <div class="card-header py-2 bg-light">
        <div class="row row-cols-1 row-cols-lg-2">
            <div class="col">
                <div class="">
                    <strong>PARTICIPANT</strong>
                    <div class="">
                        <strong class="text-inverse">Lab No: </strong>{{ $testResults->sample->lab_no }}<br>
                        <strong class="text-inverse">Participant ID:
                        </strong>{{ $testResults->sample->participant->identity }}<br>
                        <strong class="text-inverse">Sample ID: </strong>{{ $testResults->sample->sample_identity }}<br>
                        <strong class="text-inverse">Name:
                        </strong>{{ $testResults->sample->participant->surname ?? 'N/A' }}<br>
                        <strong class="text-inverse">Age: </strong>
                        @if ($testResults->sample->participant->age != null)
                            {{ $testResults->sample->participant->age }}yrs
                        @elseif ($testResults->sample->participant->months != null)
                            &nbsp; {{ $testResults->sample->participant->months }} Months
                        @else
                            N/A
                        @endif
                        <b>
                            Gender: </b>{{ $testResults->sample->participant->gender }}<br>
                        <strong class="text-inverse">Address:
                        </strong>{{ $testResults->sample->participant->address ?? 'N/A' }}<br>
                        <strong class="text-inverse">Study Name:
                        </strong>{{ $testResults->sample->study->name ?? 'N/A' }}<br>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="">
                    <strong>REQUESTER</strong>
                    <div class="">
                        <strong class="text-inverse">Name: </strong>{{ $testResults->sample?->requester?->name??'N/A' }}<br>
                        <strong class="text-inverse">Telephone:
                        </strong>{{ $testResults->sample?->requester?->contact ??'N/A' }}<br>
                        <strong class="text-inverse">Email: </strong>{{ $testResults->sample?->requester?->email??'N/A' }}<br>
                        <strong class="text-inverse">Date Requested:
                        </strong>{{ date('d-m-Y', strtotime($testResults->sample->date_requested)) }}<br>
                        <strong class="text-inverse">Organisation: </strong>
                        {{ $testResults->sample?->requester?->facility->name??'N/A' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        @if ($testResults->amended_state)
            <div class="alert border-0 bg-warning-info show">
                <div class="d-flex align-items-center text-danger">
                    <div class="fs-3"><i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <div class="ms-3">
                        <div>Please note that this is an amended result with comment--></div>
                    </div>
                    <div class="ms-3 text-info">
                        <div>{{ $testResults->amendment_comment }}</div>
                    </div>
                    <a target="_blank" href="{{ route('print-original-report', $testResults->id) }}"
                        class="action-ico btn btn-outline-success"><i class="bi bi-eye"></i>Original results</a>
                </div>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table">
                <tbody>
                    <tr>
                        <td colspan="2">
                            <strong class="text-inverse">Test Requested:
                            </strong>{{ $testResults->test->name }}
                        </td>
                        <td>
                            <strong class="text-inverse">Sample Type:
                            </strong>{{ $testResults->sample->sampleType->type }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="">
                            <strong class="text-inverse">Collection Date:
                            </strong>{{ $testResults->sample->date_collected ? date('d-m-Y H:i', strtotime($testResults->sample->date_collected)) : 'N/A' }}
                        </td>
                        <td>
                            <strong class="text-inverse">Date Received:
                            </strong>{{ date('d-m-Y H:i', strtotime($testResults->sample->sampleReception->date_delivered)) }}
                        </td>
                        <td>
                            <strong class="text-inverse">Result Date:
                            </strong>
                            @if ($testResult->amended_state)
                                {{ date('d-m-Y H:i', strtotime($testResult->amended_at)) }}
                            @else
                                {{ date('d-m-Y H:i', strtotime($testResult->created_at)) }}
                            @endif
                        </td>
                    </tr>
                    @if ($testResults->test->result_type === 'Multiple')
                        <tr>
                            <table class="table nowrap w-100 table-bordered">
                                <thead>
                                    <tr>
                                        <th>Test</th>
                                        <th>Result</th>
                                        <th>Ct Value</th>
                                        <th>Comment</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $test_results = json_decode($testResults->result, true);
                                    @endphp
                                    @foreach ($test_results as $result)
                                        <tr>
                                            <td>{{ $result['test'] ?? 'N/A' }}</td>
                                            <td>{{ $result['result'] ?? 'N/A' }}</td>
                                            <td>{{ $result['CtValue'] ?? 'N/A' }}</td>
                                            <td>{{ $result['comment'] ?? 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </tr>
                    @else
                        <tr>
                            @if ($testResults->parameters != null && $testResults->test->result_presentation == 'Tabular')
                                <table class="table nowrap w-100 table-bordered">
                                    <thead>
                                        @if ($testResults->test->parameter_uom)
                                            <tr>
                                                <th colspan="{{ count($testResults->parameters) + 1 }}">
                                                    {{ $testResults->test->parameter_uom }}
                                                </th>

                                            </tr>
                                        @endif
                                        <tr>
                                            @foreach (array_keys($testResults->parameters) as $key)
                                                <th>
                                                    {{ $key }}
                                                </th>
                                            @endforeach
                                            <th>
                                                Result
                                            </th>
                                        </tr>
                                        <tr>
                                            @foreach (array_values($testResults->parameters) as $parameter)
                                                <td>
                                                    {{ $parameter }}
                                                </td>
                                            @endforeach
                                            <td>
                                                {{ $testResults->result }}
                                            </td>
                                        </tr>
                                    </thead>
                                </table>
                            @elseif($testResults->parameters != null && $testResults->test->result_presentation == 'Non-Tabular')
                                <td class="btop" style="width:60%; color:#1A2232">
                                    <div><b style="font-size: 18px">Results:</b>
                                        @if ($testResults->result)
                                            <span>{{ $testResults->result }}</span>
                                        @else
                                            <a href="{{ route('attachment.download', $testResults->id) }}">See
                                                Attachment</a>
                                        @endif
                                        <br>
                                        @foreach ($testResults->parameters as $key => $parameter)
                                            <i>{{ $key }}</i> :{{ $parameter }}<br>
                                        @endforeach
                                    </div>
                                </td>
                            @else
                                <td colspan="3">
                                    <strong class="text-inverse">Result:
                                    </strong>
                                    @if ($testResults->result)
                                        {{ $testResults->result }}
                                    @else
                                        <a href="{{ route('attachment.download', $testResults->id) }}">See
                                            Attachment</a>
                                    @endif
                                </td>
                            @endif
                        </tr>

                    @endif
                </tbody>
            </table>
        </div>
        @if ($testResults->test->result_type != 'Multiple')
            <div class="row bg-light align-items-center m-0">
                <strong class="text-inverse">Comments:</strong>
                <p>{{ $testResults->comment }}</p>
            </div>
            <!--end row-->
        @endif
        <hr>
        <div class="row row-cols-1 row-cols-lg-3">
            <div class="col"><b>Kit Used:</b> {{ $testResult->kit->name ?? 'N/A' }}</div>
            <div class="col"><b>Verified Kit Lot:</b> {{ $testResult->verified_lot ?? 'N/A' }}</div>
            <div class="col"><b>Kit Expiry Date:</b> {{ $testResult->kit_expiry_date ?? 'N/A' }}</div>
        </div>
        <hr>
        <div class="my-3">
            <div class="row row-cols-1 row-cols-lg-3">
                <div class="col">
                    <div class="">
                        <strong>Performed By</strong>
                        <div class="">
                            <p class="text-inverse">
                                {{ $testResults->performer ? $testResults->performer->fullName : 'N/A' }}</p><br>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <strong>Rewiewer</strong>
                    <div class="">
                        @if ($testResults->reviewer)
                            <p class="text-inverse">{{ $testResults->reviewer->fullName }}</p>
                            @if ($testResults->reviewer_comment)
                                <div class="alert border-0 bg-light-info show">
                                    <div
                                        class="d-flex align-items-center @if ($testResults->status == 'Rejected' && $testResults->approver == null) text-danger
                            @else
                            text-success @endif">
                                        <div class="fs-3"><i
                                                class="bi @if ($testResults->status == 'Rejected' && $testResults->approver == null) bi-x-circle-fill
                                @else
                                bi-check-circle-fill @endif"></i>
                                        </div>
                                        <div class="ms-3">
                                            <div>{{ $testResults->reviewer_comment }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @elseif($testResults->reviewer == null && $testResults->status != 'Rejected')
                            <form>
                                <div class="row">
                                    <div class="mb-1 col-12">
                                        <textarea type="text" class="form-control" wire:model.lazy="reviewer_comment"
                                            placeholder="{{ $testResults->reviewer_comment == null ? 'Review Comment' : '' }}">{{ $testResults->reviewer_comment ?? '' }}</textarea>
                                        @error('reviewer_comment')
                                            <div class="text-danger text-small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <!-- end row-->
                                <div class="modal-footer">
                                    <button type="button" wire:click="markAsReviewed({{ $testResults->id }})"
                                        class="action-ico btn btn-outline-success radius-30 px-3 no-print">Mark as
                                        Reviewed</button>
                                    <button type="button" wire:click="markAsDeclined({{ $testResults->id }})"
                                        class="action-ico btn btn-outline-danger radius-30 px-3 no-print">Decline</button>
                                </div>
                            </form>
                        @endif
                        <br>
                    </div>
                </div>
                <div class="col">
                    <div class="">
                        <strong>Approver</strong>
                        <div class="">
                            @if ($testResults->reviewer == null)
                                {{ __('N/A') }}
                            @elseif($testResults->reviewer != null && $testResults->approver == null && $testResults->status == 'Reviewed')
                                <form>
                                    <div class="row">
                                        <div class="mb-1 col-12">
                                            <textarea type="text" class="form-control" wire:model.lazy="approver_comment"
                                                placeholder="{{ $testResults->approver_comment == null ? 'Comment' : '' }}">{{ $testResults->approver_comment ?? '' }}</textarea>
                                            @error('approver_comment')
                                                <div class="text-danger text-small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <!-- end row-->
                                    <div class="modal-footer">
                                        <button type="button" wire:click="markAsApproved({{ $testResults->id }})"
                                            class="action-ico btn btn-outline-success radius-30 px-3 no-print">Mark as
                                            Approved</button>
                                        <button type="button" wire:click="markAsDeclined({{ $testResults->id }})"
                                            class="action-ico btn btn-outline-danger radius-30 px-3 no-print">Decline</button>
                                    </div>
                                </form>
                            @elseif($testResults->reviewer != null && $testResults->approver != null && $testResults->status == 'Approved')
                                <p class="text-inverse">{{ $testResults->approver->fullName }}</p>
                            @elseif($testResults->reviewer != null && $testResults->approver != null && $testResults->status == 'Rejected')
                                <p class="text-inverse">{{ $testResults->approver->fullName }}</p>
                                @if ($testResults->approver_comment)
                                    <div class="alert border-0 bg-light-info show">
                                        <div
                                            class="d-flex align-items-center @if ($testResults->status == 'Rejected') text-danger
                                    @else
                                    text-success @endif">
                                            <div class="fs-3"><i class="bi bi-x-circle-fill"></i>
                                            </div>
                                            <div class="ms-3">
                                                <div>{{ $testResults->approver_comment }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @else
                                {{ __('N/A') }}
                            @endif

                            <br>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-footer py-3">
        <p class="text-center mb-2">
            The Laboratory is Certified by the Ministry of Health Uganda
        </p>
    </div>
</div>

@if ($testResults->status == 'Rejected')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pt-0">
                    <div class="row mb-2">
                        <div class="col-sm-12 mt-3">
                            <div class="d-sm-flex align-items-center">
                                <h5 class="mb-2 mb-sm-0">
                                    <h6 class="modal-title" id="staticBackdropLabel">Amend Test Results For <strong
                                            class="text-info">{{ $testResults->tracker }}</strong></h6>
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if (isset($test) && $test)
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
                                            <tr>
                                                <td><strong class="text-success">{{ $test->name }}</strong>
                                                </td>
                                                <td>
                                                    <form wire:submit.prevent="updateResult()" class="me-2">
                                                        <div class="row">
                                                            <div class="col">
                                                                @if ($test->result_type == 'Absolute')
                                                                    <div class="mb-2">
                                                                        <label class="form-label">Result</label>
                                                                        <select class="form-select select2"
                                                                            id="result" wire:model.lazy="result">
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
                                                                                <input type="number" step="0.001"
                                                                                    class="form-control"
                                                                                    id="result"
                                                                                    wire:model.lazy='result'>
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
                                                                        <input type="file" class="form-control"
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
                                                                        <input type="text" class="form-control"
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
                                                            <div class="col">
                                                                <div class="mb-2">
                                                                    <label class="form-label">Comment</label>
                                                                    @if ($test->comments != null)
                                                                        <select class="form-select select2"
                                                                            id="comment" wire:model="comment">
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
                                                                            <label
                                                                                class="form-label">{{ $parameter }}</label>
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
                                                                    <select class="form-select select2"
                                                                        wire:model="kit_id">
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
                                                                    <textarea wire:model.lazy="verified_lot" rows="1" class="form-control"
                                                                        placeholder="{{ __('verified lot') }}"></textarea>

                                                                    @error('verified_lot')
                                                                        <div class="text-danger text-small">
                                                                            {{ $message }}</div>
                                                                    @enderror
                                                                </div>
                                                            </div>

                                                            <div class="col">
                                                                <div class="mb-2">
                                                                    <label class="form-label">Kit expiry date</label>
                                                                    <input type="date" name="kit_expiry_date"
                                                                        class="form-control" id="kit_expiry_date"
                                                                        wire:model="kit_expiry_date" required>
                                                                    @error('kit_expiry_date')
                                                                        <div class="text-danger text-small">
                                                                            {{ $message }}</div>
                                                                    @enderror
                                                                </div>
                                                            </div>

                                                            <div class="col mt-4">
                                                                <x-button>{{ __('Save') }}</x-button>
                                                            </div>

                                                        </div>
                                                    </form>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div> <!-- end preview-->
                        </div>
                        <hr>
                    @endif
                </div> <!-- end body -->
            </div> <!-- end card -->
        </div><!-- end col-->
    </div>
@endif
