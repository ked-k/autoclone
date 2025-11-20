<div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pt-0">
                    <div class="row mb-2">
                        <div class="col-sm-12 mt-3">
                            <div class="d-sm-flex align-items-center">
                                <h5 class="mb-2 mb-sm-0">
                                    Assign Tasks for <span class="text-danger fw-bold">{{ $sample_is_for }}</span>
                                </h5>
                                <div class="ms-auto">
                                    <a type="button" class="btn btn-outline-success me-2 fw-bold mb-1"
                                        wire:click="$set('sample_is_for','Testing')">
                                        @if ($sample_is_for === 'Testing')
                                            <span class="spinner-grow spinner-grow-sm" role="status"
                                                aria-hidden="true"></span>
                                        @endif
                                        <i class="bx bxs-flask"></i>Testing (<strong
                                            class="text-danger">{{ $forTestingCount }}</strong>)
                                    </a>
                                    <a type="button" class="btn btn-outline-info me-2 fw-bold mb-1"
                                        wire:click="$set('sample_is_for','Aliquoting')">
                                        @if ($sample_is_for === 'Aliquoting')
                                            <span class="spinner-grow spinner-grow-sm" role="status"
                                                aria-hidden="true"></span>
                                        @endif
                                        <i class="bi bi-hourglass-split"></i>Aliquoting (<strong
                                            class="text-danger">{{ $forAliquotingCount }}</strong>)
                                    </a>

                                    <a type="button" class="btn btn-outline-warning me-2 fw-bold mb-1"
                                        wire:click="$set('sample_is_for','Storage')">
                                        @if ($sample_is_for === 'Storage')
                                            <span class="spinner-grow spinner-grow-sm" role="status"
                                                aria-hidden="true"></span>
                                        @endif

                                        <i class="bx bx-archive"></i> Storage (<strong
                                            class="text-danger">{{ $forStorageCount }}</strong>)
                                    </a>

                                    <a type="button" class="btn btn-outline-info me-2" wire:click="refresh()"
                                        data-bs-toggle="tooltip" data-bs-placement="top" title=""
                                        data-bs-original-title="Refresh Table"><i class="bi bi-arrow-clockwise"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @include('livewire.partials.sample-filter')

                    <x-table-utilities display='d-blocl'>
                        <div>
                            <div class="d-flex align-items-center ml-4 me-2">
                                <label for="orderBy" class="text-nowrap mr-2 mb-0">OrderBy</label>
                                <select wire:model="orderBy" class="form-select">
                                    <option value="sample_identity">Sample ID</option>
                                    <option value="lab_no">Lab No</option>
                                    <option value="id">Latest</option>
                                </select>
                            </div>
                        </div>
                    </x-table-utilities>
                    <div class="tab-content">
                        <div class="table-responsive">
                            <table id="datableButton" class="table table-striped mb-0 w-100 sortable">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Batch</th>
                                        <th>PID</th>
                                        <th>Sample</th>
                                        <th>Sample ID</th>
                                        <th>Lab No</th>
                                        <th>Study</th>
                                        <th>Requester</th>
                                        <th>Collector</th>
                                        <th>For</th>
                                        @if ($sample_is_for == 'Testing')
                                            <th> TestCount</th>
                                        @elseif($sample_is_for == 'Aliquoting')
                                            <th> Aliquot Count</th>
                                        @endif
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($samples as $key => $sample)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>
                                                {{ $sample->sampleReception->batch_no }}
                                            </td>
                                            <td>
                                                {{ $sample->participant->identity }}
                                            </td>
                                            <td>
                                                {{ $sample->sampleType->type }}
                                            </td>
                                            <td>
                                                {{ $sample->sample_identity }}
                                            </td>
                                            <td>
                                                <strong class="text-success">{{ $sample->lab_no }}</strong>

                                            </td>
                                            <td>
                                                {{ $sample->study->name ?? 'N/A' }}
                                            </td>
                                            <td>
                                                {{ $sample->requester->name ?? 'N/A' }}
                                            </td>
                                            <td>
                                                {{ $sample->collector->name ?? 'N/A' }}
                                            </td>
                                            <td>
                                                <span class="badge bg-success">{{ $sample->sample_is_for }}</span>
                                            </td>
                                            @if ($sample_is_for == 'Testing' || $sample_is_for == 'Aliquoting')
                                                <td>
                                                    {{ $sample->test_count }}
                                                </td>
                                            @endif

                                            @if ($sample->priority == 'Normal')
                                                <td><span class="badge bg-info">{{ $sample->priority }}</span>
                                                </td>
                                            @else
                                                <td><span class="badge bg-danger">{{ $sample->priority }}</span>
                                                </td>
                                            @endif
                                            <td>
                                                @if ($sample->status == 'Accessioned')
                                                    <span class="badge bg-warning">{{ $sample->status }}</span>
                                                @elseif($sample->status == 'Processing')
                                                    <span class="badge bg-success">{{ $sample->status }}</span>
                                                @endif
                                            </td>
                                            <td class="table-action">
                                                @if ($sample->sample_is_for == 'Testing')
                                                    <a href="javascript: void(0);"
                                                        wire:click="viewTests({{ $sample->id }})" type="button"
                                                        class="btn btn-outline-info" data-bs-toggle="modal"
                                                        title="Assign" data-bs-target="#view-tests"><i
                                                            class="bi bi-list"></i>
                                                    </a>
                                                @elseif($sample->sample_is_for == 'Aliquoting')
                                                    <a href="javascript: void(0);"
                                                        wire:click="viewAliquots({{ $sample->id }})" type="button"
                                                        class="btn btn-outline-success" data-bs-toggle="modal"
                                                        title="Assign" data-bs-target="#view-aliquots"><i
                                                            class="bi bi-list"></i>
                                                    </a>
                                                @elseif($sample->sample_is_for == 'Storage')
                                                    <a href="javascript: void(0);"
                                                        wire:click="viewAliquots({{ $sample->id }})" type="button"
                                                        class="btn btn-outline-success" data-bs-toggle="modal"
                                                        title="Assign" data-bs-target="#view-aliquots"><i
                                                            class="bi bi-list"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>

                                    @empty
                                    @endforelse
                                </tbody>
                            </table>
                        </div> <!-- end preview-->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="btn-group float-end">
                                    {{ $samples->links() }}
                                </div>
                            </div>
                        </div>
                    </div> <!-- end tab-content-->
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div><!-- end col-->

        @if ($sample && $sample?->sample_is_for == 'Testing')
            <div wire:ignore.self class="modal fade" id="view-tests" data-bs-backdrop="static" data-bs-keyboard="false"
                tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h6 class="modal-title" id="staticBackdropLabel">Tests for sample (<span
                                    class="text-info">{{ $sampleId ?? '...' }}</span>) with Lab_No <span
                                    class="text-info">{{ $labNo ?? '...' }}</span></h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"
                                wire:click="close()"></button>
                        </div> <!-- end modal header -->
                        <div class="row">
                            <div class="mb-0">

                                <div class="table-responsiv">
                                    <table class="table table-striped mb-0 w-100">
                                        <thead>
                                            <tr>
                                                <th>Test Requested</th>
                                                <th>Assignment</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($tests as $key => $test)
                                                <tr>
                                                    <td>
                                                        <strong class="text-danger">Test-{{ $key + 1 }}</strong>
                                                        <a href="javascript: void(0);" class="action-ico"
                                                            wire:click="activateTest({{ $test->id }})"><strong
                                                                class="text-success">{{ $test->name }}
                                                            </strong></a>
                                                    </td>
                                                    <td>
                                                        @if ($request_acknowledged_by)
                                                            @if ($test->id === $test_id)
                                                                <form wire:submit.prevent="assignTest">
                                                                    <div class="row">

                                                                        <div class="col-md-8">
                                                                            <div class="mb-2">
                                                                                <label class="form-label">Assignee
                                                                                    @if ($backlog)
                                                                                        (Backlog: <strong
                                                                                            class="text-danger">{{ $backlog }}</strong>)
                                                                                    @endif
                                                                                </label>
                                                                                <select class="form-select select2"
                                                                                    wire:model="assignee">
                                                                                    <option selected value="">
                                                                                        Select
                                                                                    </option>
                                                                                    @foreach ($users as $user)
                                                                                        <option
                                                                                            value='{{ $user->id }}'>
                                                                                            {{ $user->fullName }}
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                                @error('assignee')
                                                                                    <div class="text-danger text-small">
                                                                                        {{ $message }}</div>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-2 mb-2">
                                                                            <label class="form-label">Is
                                                                                Referral?</label>
                                                                            <select class="form-select select2"
                                                                                wire:model="refer_samples">
                                                                                <option value="">Select</option>
                                                                                <option value="1">Refer Sample
                                                                                </option>
                                                                                <option value="0">Test Sample
                                                                                </option>
                                                                            </select>
                                                                        </div>
                                                                        @if ($refer_samples)
                                                                            @include('livewire.lab.sample-management.inc.refer-samples-form')
                                                                        @endif
                                                                        <div class="col-md-2 mt-4 text-start">
                                                                            <x-button>{{ __('Assign') }}</x-button>
                                                                        </div>

                                                                    </div>
                                                                </form>
                                                            @else
                                                                <p>Please click Test to assign</p>
                                                            @endif
                                                        @else
                                                            <p>Acknowledge to Assign</p>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div> <!-- end preview-->
                            </div>

                            @if ($sample?->clinical_notes && $request_acknowledged_by)
                                <div class="col-md-12">
                                    <div class="card-body text-center">
                                        <div>
                                            <h5 class="card-title">Clinical Notes</h5>
                                        </div>
                                        <p class="card-text">{{ $sample->clinical_notes }}</p>
                                    </div>
                                </div>
                            @endif

                        </div>

                        <div class="modal-footer">
                            @if ($assignee && $request_acknowledged_by)
                                <a href="javascript: void(0);" wire:click="assignAllTests"
                                    class="action-ico btn btn-info radius-30 px-3">Assign All</a>
                            @endif
                            @if (!$request_acknowledged_by && $sampleId)
                                <a href="javascript: void(0);" wire:click="acknowledgeRequest"
                                    class="action-ico btn btn-success radius-30 px-3">
                                    <i class="bi bi-hand-thumbs-up"></i>Acknowledge</a>
                            @endif
                            @if (!$sampleId)
                                <h6 class="text-success">Loading data please wait......</h6>
                            @endif
                            <button class="btn  btn-danger radius-30 px-3" wire:click="close()"
                                data-bs-dismiss="modal" aria-label="Close">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if (($sample && $sample?->sample_is_for == 'Aliquoting') || $sample?->sample_is_for == 'Storage')
            <div wire:ignore.self class="modal fade" id="view-aliquots" data-bs-backdrop="static"
                data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h6 class="modal-title" id="staticBackdropLabel">
                                @if ($sample->sample_is_for == 'Aliquoting')
                                    Requested Aliquots
                                @else
                                    Assign storage task
                                @endif
                                for sample (<span class="text-info">{{ $sample->sample_identity }}</span>) with Lab_No
                                <span class="text-info">{{ $sample->lab_no }}</span>
                            </h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"
                                wire:click="close()"></button>
                        </div> <!-- end modal header -->

                        <div class="row">
                            <div class="mb-0">
                                <div class="card">
                                    <div class="card-body">

                                        @if ($sample?->sample_is_for == 'Aliquoting')
                                            <ul class="list-group">
                                                @foreach ($aliquots as $key => $aliquot)
                                                    <li class="list-group-item"><strong
                                                            class="text-danger">Aliquot-{{ $key + 1 }}
                                                        </strong>{{ $aliquot->type }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif

                                        @if ($request_acknowledged_by)
                                            <form wire:submit.prevent="assignAliquotingTasks" class="mt-2">
                                                <div class="row">

                                                    <div class="col-md-8">
                                                        <div class="mb-2">
                                                            <label class="form-label fw-bold">Assignee</label>
                                                            <select class="form-select select2" wire:model="assignee">
                                                                <option selected value="">Select
                                                                </option>
                                                                @foreach ($users as $user)
                                                                    <option value='{{ $user->id }}'>
                                                                        {{ $user->fullName }}</option>
                                                                @endforeach
                                                            </select>
                                                            @error('assignee')
                                                                <div class="text-danger text-small">
                                                                    {{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4 mt-4 text-end">
                                                        <x-button>{{ __('assign') }}</x-button>
                                                    </div>

                                                </div>
                                            </form>
                                        @else
                                        @endif
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            @if (!$request_acknowledged_by)
                                <div class="d-flex align-items-center">
                                    <div class="fs-3 text-info"><i class="bi bi-info-circle-fill "></i>
                                    </div>
                                    <div class="ms-3 text-secondary">
                                        <div>Acknowledge to Assign
                                        </div>
                                    </div>
                                </div>
                                <a href="javascript: void(0);" wire:click="acknowledgeRequest"
                                    class="action-ico btn btn-success radius-30 px-3">
                                    <i class="bi bi-hand-thumbs-up"></i>Acknowledge</a>
                            @endif
                            <button class="btn  btn-danger radius-30 px-3" wire:click="close()"
                                data-bs-dismiss="modal" aria-label="Close">Close</button>
                        </div>
                    </div>x

                </div>
            </div>
        @endif

        @push('scripts')
            <script>
                window.addEventListener('close-modal', event => {
                    $('#view-tests').modal('hide');
                    $('#view-aliquots').modal('hide');
                });

                window.addEventListener('view-tests', event => {
                    $('#view-tests').modal('show');
                });

                window.addEventListener('view-aliquots', event => {
                    $('#view-aliquots').modal('show');
                });
            </script>
        @endpush
    </div>
</div>
