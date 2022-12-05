<div class="card border shadow-none" id="reportContainer">
    <div class="card-header py-3">
        <div class="row align-items-center g-3">
            <div class="col-12 col-lg-6">
                <h5 class="mb-0">Preliminary Test Report</h5>
            </div>
            <div class="col-12 col-lg-6 text-md-end">
                @if ($testResults->reviewer == null)
                    <a href="javascript:;" wire:click="$set('viewReport',false)" class="btn btn-sm btn-info me-2"><i
                            class="bi bi-list"></i>
                        Return to Review List
                    </a>
                @elseif($testResults->approver == null)
                    <a href="javascript:;" wire:click="$set('viewReport',false)" class="btn btn-sm btn-info me-2"><i
                            class="bi bi-list"></i>
                        Return to Approval List
                    </a>
                @endif
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
                    <div class="">
                        <strong>Rewiewed By</strong>
                        <div class="">
                            @if ($testResults->reviewer)
                                <p class="text-inverse">{{ $testResults->reviewer->fullName }}</p>
                            @else
                                <a href="javascript: void(0);" type="button" data-bs-toggle="tooltip"
                                    data-bs-placement="bottom" title="" data-bs-original-title="Mark As Review"
                                    wire:click="markAsReviewed({{ $testResults->id }})"
                                    class="action-ico btn btn-outline-success radius-30 px-3 no-print">Mark as
                                    Review</a>
                            @endif
                            <br>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="">
                        <strong>Approved By</strong>
                        <div class="">
                            @if ($testResults->reviewer == null)
                                {{ __('N/A') }}
                            @elseif($testResults->approver == null)
                                @if ($testResults->approver)
                                    <p class="text-inverse">{{ $testResults->approver->fullName }}</p>
                                @else
                                    <a href="javascript: void(0);" type="button" data-bs-toggle="tooltip"
                                        data-bs-placement="bottom" title=""
                                        data-bs-original-title="Mark As Review"
                                        wire:click="markAsApproved({{ $testResults->id }})"
                                        class="action-ico btn btn-outline-success radius-30 px-3 no-print">Approve</a>
                                @endif
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
