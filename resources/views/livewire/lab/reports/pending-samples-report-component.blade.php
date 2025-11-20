<div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-header pt-0">
                        <div class="row mb-2">
                            <div class="col-sm-12 mt-3">
                                <div class="d-sm-flex align-items-center">
                                    <h5 class="mb-2 mb-sm-0">
                                        Samples (<strong class="text-danger">{{ count($sampleIds) }}</strong>)
                                    </h5>
                                    <div class="ms-auto">
                                        <a href="javascript:;" wire:click='export' class="btn btn-secondary me-2"><i
                                                class="bi bi-file-earmark-fill"></i> Export</a>
                                        <a type="button" class="btn btn-outline-info" wire:click="refresh()"
                                            data-bs-toggle="tooltip" data-bs-placement="top" title=""
                                            data-bs-original-title="Refresh Table"><i
                                                class="bi bi-arrow-clockwise"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row mb-0">
                            <form>
                                <div class="row">
                                    <div class="mb-3 col-md-3">
                                        <label for="facility_id" class="form-label">Facility</label>
                                        <select class="form-select select2" id="facility_id" wire:model="facility_id">
                                            <option selected value="0">All</option>
                                            @forelse ($facilities as $facility)
                                                <option value='{{ $facility->id }}'>{{ $facility->name }}</option>
                                            @empty
                                            @endforelse
                                        </select>
                                    </div>
                                    <div class="mb-3 col-md-3">
                                        <label for="study" class="form-label">Study</label>
                                        <select class="form-select select2" id="study" wire:model="study_id">
                                            <option selected value="0">All</option>
                                            @forelse ($studies as $study)
                                                <option value='{{ $study->id }}'>{{ $study->name }}</option>
                                            @empty
                                            @endforelse
                                        </select>
                                    </div>
                                    <div class="mb-3 col-md-2">
                                        <label for="job" class="form-label">Sample State</label>
                                        <select class="form-select select2" id="job" wire:model="job">
                                            <option selected value="">All</option>
                                            @forelse ($jobs as $job)
                                                <option value='{{ $job->sample_is_for }}'>{{ $job->sample_is_for }}
                                                </option>
                                            @empty
                                            @endforelse
                                        </select>
                                    </div>

                                    <div class="mb-3 col-md-2">
                                        <label for="sampleType" class="form-label">Sample Type</label>
                                        <select class="form-select select2" id="sampleType" wire:model='sampleType'>
                                            <option selected value="0">All</option>
                                            @foreach ($sampleTypes as $sampleType)
                                                <option value='{{ $sampleType->id }}'>
                                                    {{ $sampleType->type }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3 col-md-2">
                                        <label for="created_by" class="form-label">Accessioned By</label>
                                        <select class="form-select select2" id="created_by" wire:model='created_by'>
                                            @if (Auth::user()->hasPermission('manager-access|master-access'))
                                                <option selected value="0">All</option>
                                                @foreach ($users as $user)
                                                    <option value='{{ $user->id }}'>
                                                        {{ $user->fullName }}</option>
                                                @endforeach
                                            @else
                                                <option selected value="{{ auth()->user()->id }}">
                                                    {{ auth()->user()->fullName }}</option>
                                            @endif
                                        </select>
                                    </div>
                                    <div class="mb-3 col-md-2">
                                        <label for="from_date" class="form-label">Start Date</label>
                                        <input id="from_date" type="date" class="form-control"
                                            wire:model="from_date">
                                    </div>
                                    <div class="mb-3 col-md-2">
                                        <label for="to_date" class="form-label">End Date</label>
                                        <input id="to_date" type="date" class="form-control" wire:model="to_date">
                                    </div>
                                    <div class="mb-2 col-md-2">
                                        <label for="perPage" class="form-label">Per Page</label>
                                        <select wire:model="perPage" class="form-select" id="perPage">
                                            <option value="10">10</option>
                                            <option value="20">20</option>
                                            <option value="30">30</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                            <option value="200">200</option>
                                            <option value="500">500</option>
                                            <option value="1000">1000</option>
                                        </select>
                                    </div>

                                    <div class="mb-3 col-md-2">
                                        <label for="orderBy" class="form-label">OrderBy</label>
                                        <select wire:model="orderBy" class="form-select">
                                            <option value="sample_identity">Sample ID</option>
                                            <option value="lab_no">Lab No</option>
                                            <option value="id">Latest</option>
                                        </select>
                                    </div>

                                    <div class="mb-3 col-md-2">
                                        <label for="orderAsc" class="form-label">Order</label>
                                        <select wire:model="orderAsc" class="form-select" id="orderAsc">
                                            <option value="1">Asc</option>
                                            <option value="0">Desc</option>
                                        </select>
                                    </div>
                                    <div class=" col-md-2 ms-auto position-relative">
                                        <label for="search" class="form-label">Search</label>
                                        <input wire:model.debounce.300ms="search" class="form-control "
                                            type="text" placeholder="search">
                                    </div>
                                </div>
                                <!-- end row-->
                            </form>
                        </div>
                    </div>

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
                                        <th>Facility</th>
                                        <th>Study</th>
                                        <th>Status</th>
                                        <th>Test Request:Performed</th>
                                        <th>Accessioned At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($samples as $key => $sample)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>
                                                <a href="{{ URL::signedRoute('batch-search-results', ['sampleReception' => $sample->sampleReception->id]) }}"
                                                    class="text-secondary"
                                                    target="_blank">{{ $sample->sampleReception->batch_no }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="{{ URL::signedRoute('participant-search-results', ['participant' => $sample->participant->id]) }}"
                                                    class="text-secondary"
                                                    target="_blank">{{ $sample->participant->identity }}
                                                </a>

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
                                                {{ $sample->participant->facility->name ?? 'N/A' }}
                                            </td>
                                            <td>
                                                {{ $sample->study->name ?? 'N/A' }}
                                            </td>
                                            <td>
                                                <span class="badge bg-success">{{ $sample->status }}</span>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge bg-info">{{ $sample->tests_requested_count }}</span>
                                                :
                                                <span
                                                    class="badge bg-success">{{ $sample->tests_performed_count }}</span>
                                            </td>
                                            <td>
                                                {{ date('d-m-Y', strtotime($sample->created_at)) }}
                                            </td>
                                            <td class="table-action">
                                                @if ($sample->sample_is_for == 'Deffered')
                                                    <button class="btn btn-sm btn-outline-danger"
                                                        wire:click="recallSampleConfirmation({{ $sample->id }})"><i
                                                            class="bi bi-arrow-90deg-left"></i></button>
                                                @elseif($sample->sample_is_for == 'Testing')
                                                    <a href="{{ URL::signedRoute('sample-search-results', ['sample' => $sample->id]) }}"
                                                        type="button" class="btn btn-sm btn-outline-success"
                                                        data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                        title="" data-bs-original-title="View Details"
                                                        target="_blank"><i class="bi bi-eye"></i>
                                                    </a>
                                                @elseif($sample->sample_is_for == 'Aliquoting')
                                                    <a href="{{ URL::signedRoute('sample-search-results', $sample->id) }}"
                                                        type="button" class="btn btn-sm btn-outline-info"
                                                        data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                        title="" data-bs-original-title="View Aliquots"
                                                        target="_blank"><i class="bi bi-hourglass-split"></i></a>
                                                @else
                                                    <a href="javascript:;"
                                                        class="action-ico btn-sm btn btn-outline-warning mx-1"
                                                        data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                        title="" data-bs-original-title="Storage Details"
                                                        aria-label="Views"
                                                        wire:click="storageDetails({{ $sample->id }})"
                                                        data-bs-target="#storage-details"><i
                                                            class="bx bx-archive"></i></a>
                                                @endif
                                                @if (
                                                    ($sample->created_by == auth()->user()->id && $sample->status != 'Tests Done') ||
                                                        Auth::user()->hasPermission(['review-results']))
                                                    <button class="btn btn-sm btn-outline-info"
                                                        wire:click="editSample({{ $sample->id }})"
                                                        data-bs-toggle="modal" data-bs-target="#edit-sample-modal"><i
                                                            class="bi bi-pencil"></i></button>
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

    </div>


    {{-- =============EDIT SAMPLE INFORMATION====================== --}}

    <div wire:ignore.self class="modal fade" id="edit-sample-modal" tabindex="-1" data-backdrop="static"
        data-keyboard="false" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Update Sample</h5>
                    <button type="button" class="btn-close" wire:click="cancel()" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <form action="" wire:submit.prevent='updateSample'>
                    <div class="modal-body pt-4 pb-4 row">
                        <div class="form-group mb-1 col-md-6">
                            <label for="name" class="form-label">Sample ID</label>
                            <input type="text" class="form-control" required wire:model.lazy='sample_identity'>
                            @error('sample_identity')
                                <div class="text-danger text-small">{{ __($message) }}</div>
                            @enderror
                        </div>
                        <div class="form-group mb-1 col-md-6">
                            <label for="sample_study" class="form-label">Study</label>
                            <select class="form-select select2" id="sample_study" required
                                wire:model="sample_study_id">
                                <option value=" ">All</option>
                                @forelse ($studies as $study)
                                    <option value='{{ $study->id }}'>{{ $study->name }}</option>
                                @empty
                                @endforelse
                            </select>
                        </div>
                        <div class="form-group mb-1 col-md-6">
                            <label for="sample_study" class="form-label">Requested By</label>
                            <select required class="form-select" id="v" wire:model="requested_by">
                                <option value=" ">All</option>
                                @forelse ($requesters as $requester)
                                    <option value='{{ $requester->id }}'>{{ $requester->name }}</option>
                                @empty
                                @endforelse
                            </select>
                        </div>
                        <div class="form-group mb-1 col-md-6">
                            <label for="date_collected" class="form-label">Collection Date/Time</label>
                            <input id="date_collected" max="{{ $date_requested }}" class="form-control"
                                type="datetime-local" wire:model="date_collected">
                        </div>
                        <div class="form-group mb-1 col-md-4">
                            <label for="date_requested" class="form-label">Request Date</label>
                            <input type="date" class="form-control" min="{{ $date_collected }}" required
                                wire:model.lazy='date_requested'>
                            @error('date_requested')
                                <div class="text-danger text-small">{{ __($message) }}</div>
                            @enderror
                        </div>
                        <div class="form-group mb-1 col-md-4">
                            <label for="lab_no" class="form-label">Lab Number</label>
                            <input type="text" readonly class="form-control" required wire:model.lazy='lab_no'>
                            @error('lab_no')
                                <div class="text-danger text-small">{{ __($message) }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-4">
                            <label for="status" class="form-label">Sample Status</label>
                            <input type="text" readonly class="form-control" required wire:model.lazy='status'>
                            @error('status')
                                <div class="text-danger text-small">{{ __($message) }}</div>
                            @enderror
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-sm btn-success">Update</button>
                        <button class="btn btn-sm btn-danger" type="reset" wire:click="cancel()"
                            data-bs-dismiss="modal" aria-label="Close">Cancel</button>

                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- //SAMPLE RECALL CONFIRMATION MODAL --}}

    <div wire:ignore.self class="modal fade" id="recall-confirmation-modal" tabindex="-1" data-backdrop="static"
        data-keyboard="false" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Recall sample for testing</h5>
                    <button type="button" class="btn-close" wire:click="cancel()" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body pt-4 pb-4">
                    <h6>Are you sure you want to recall this sample for testing?</h6>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-sm btn-success" wire:click="recallForTesting()">Yes! Recall</button>
                    <button class="btn btn-sm btn-info" wire:click="recallBatchForTesting()">Yes! whole Batch</button>
                    <button class="btn btn-sm btn-danger" wire:click="cancel()" data-bs-dismiss="modal"
                        aria-label="Close">Cancel</button>

                </div>
            </div>
        </div>
    </div>
    @if ($sample != null)
        {{-- VIEW STORAGE DETAILS --}}
        <div wire:ignore.self class="modal fade" id="storage-details" data-bs-backdrop="static"
            data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                {{-- @if ($sample != null) --}}
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">Sample (<span
                                class="text-info">{{ $sample_identity ?? 'N/A' }}</span>) Storage Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"
                            wire:click="cancel()"></button>
                    </div> <!-- end modal header -->
                    <div class="modal-body">
                        <div class="row row-cols-1 row-cols-xl-2 row-cols-xxl-3">
                            <div class="col-md-7">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="icon-box bg-light-info border-0">
                                        <i class="bi bi-archive text-success"></i><i
                                            class='bx bxs-vial text-success'></i>
                                    </div>
                                    <div class="info">
                                        <p class="mb-1"><strong>Barcode</strong> : {{ $barcode ?? 'N/A' }}
                                        </p>
                                        <p class="mb-1"><strong>Location</strong> :
                                            {{ $freezer_location ?? 'N/A' }}</p>
                                        <p class="mb-1"><strong>Freezer</strong> :
                                            {{ $freezer ?? 'N/A' }}</p>
                                        <p class="mb-1"><strong>Freezer Temp</strong> :
                                            {{ $temp ?? 'N/A' }}</p>
                                        <p class="mb-1"><strong>Section/Compartment</strong> :
                                            {{ $section_id ?? 'N/A' }}</p>
                                        <p class="mb-1"><strong>Racker/Column</strong> :
                                            {{ $rack_id ?? 'N/A' }}</p>

                                        <p class="mb-1"><strong>Drawer</strong> :
                                            {{ $drawer_id ?? 'N/A' }}</p>
                                        <p class="mb-1"><strong>Box</strong> :
                                            {{ $box_id ?? 'N/A' }}</p>
                                        <p class="mb-1"><strong>Well</strong> :
                                            {{ $box_column . $box_row }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="card border shadow-none radius-10">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="icon-box bg-light-info border-0">
                                                <i class="bi bi-person text-success"></i>
                                            </div>
                                            <div class="info">
                                                <h6 class="mb-2">Stored By</h6>
                                                <p class="mb-1"><strong>Name</strong> : {{ $stored_by }}</p>
                                                <p class="mb-1"><strong>Date</strong> : {{ $date_stored }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <!--end row-->

                    </div>
                </div> <!-- end modal content-->
                {{-- @endif --}}
            </div> <!-- end modal dialog-->
        </div> <!-- end modal-->
    @endif
    @push('scripts')
        <script>
            window.addEventListener('close-modal', event => {
                $('#recall-confirmation-modal').modal('hide');
                $('#storage-details').modal('hide');
                $('#edit-sample-modal').modal('hide');
            });

            window.addEventListener('show-storage-details', event => {
                $('#storage-details').modal('show');
            });

            window.addEventListener('recall-confirmation', event => {
                $('#recall-confirmation-modal').modal('show');
            });
        </script>
    @endpush
</div>
