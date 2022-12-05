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
                        <strong class="text-inverse">Age: </strong>{{ $testResults->sample->participant->age }}<b>
                            Gender: </b>{{ $testResults->sample->participant->gender }}<br>
                        <strong class="text-inverse">Study Name:
                        </strong>{{ $testResults->sample->study->name ?? 'N/A' }}<br>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="">
                    <strong>REQUESTER</strong>
                    <div class="">
                        <strong class="text-inverse">Name: </strong>{{ $testResults->sample->requester->name }}<br>
                        <strong class="text-inverse">Telephone:
                        </strong>{{ $testResults->sample->requester->contact }}<br>
                        <strong class="text-inverse">Email: </strong>{{ $testResults->sample->requester->email }}<br>
                        <strong class="text-inverse">Date Requested:
                        </strong>{{ date('d-m-Y', strtotime($testResults->sample->date_requested)) }}<br>
                        <strong class="text-inverse">Organisation: </strong>
                        {{ $testResults->sample->requester->facility->name }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <tbody>
                    <tr>
                        <td colspan="2">
                            <strong class="text-inverse">Test Requested: </strong>{{ $testResults->test->name }}
                        </td>
                        <td>
                            <strong class="text-inverse">Sample Type:
                            </strong>{{ $testResults->sample->sampleType->type }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="">
                            <strong class="text-inverse">Collection Date:
                            </strong>{{ date('d-m-Y', strtotime($testResults->sample->date_collected)) }}
                        </td>
                        <td>
                            <strong class="text-inverse">Date Received:
                            </strong>{{ date('d-m-Y H:i', strtotime($testResults->sample->sampleReception->date_delivered)) }}
                        </td>
                        <td>
                            <strong class="text-inverse">Result Date: </strong>{{ $testResults->created_at }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <strong class="text-inverse">Result:
                            </strong>
                            @if ($testResults->result)
                                {{ $testResults->result }}
                            @else
                                <a href="{{ route('attachment.download', $testResults->id) }}">See Attachment</a>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="row bg-light align-items-center m-0">
            <strong class="text-inverse">Comments:</strong>
            <p>{{ $testResults->comment }}</p>
        </div>
        <!--end row-->

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
                        @else
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
                    @if ($test)
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
                                                            <div class="col-md-5">
                                                                @if ($test->result_type == 'Absolute')
                                                                    <div class="mb-2">
                                                                        <label class="form-label">Result</label>
                                                                        <select class="form-select" id="result"
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
                                                            <div class="col-md-5">
                                                                <div class="mb-2">
                                                                    <label class="form-label">Comment</label>
                                                                    @if ($test->comments != null)
                                                                        <select class="form-select" id="comment"
                                                                            wire:model="comment">
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
                                                            {{-- <div class="col-md-3">
                                                                <div class="mb-2">
                                                                    <label class="form-label">Performed
                                                                        By</label>
                                                                    <select class="form-select"
                                                                        wire:model="performed_by">
                                                                        <option selected value="">Select
                                                                        </option>
                                                                        <option value="{{ auth()->user()->id }}">
                                                                            {{ auth()->user()->fullName }}
                                                                        </option>
                                                                        @error('performed_by')
                                                                            <div class="text-danger text-small">
                                                                                {{ $message }}</div>
                                                                        @enderror
                                                                </div>
                                                            </div> --}}
                                                            
                                                            <div class="col-md-1 mt-4">
                                                                <x-button>{{ __('Save') }}</x-button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </td>
                                            </tr>
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
@endif
