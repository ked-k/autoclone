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
                            {{-- <form> --}}
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
                                    <label for="from_date" class="form-label">Start Date</label>
                                    <input id="from_date" type="date" class="form-control" wire:model="from_date">
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
                                    <label for="search" class="form-label">Return</label>
                                    <select class="form-select select2" name="return_type" wire:model='return_type'>
                                        <option value="">Select</option>
                                        <option value="List">List</option>
                                        <option value="Count">Count</option>
                                    </select>
                                </div>
                                @if ($return_type == 'Count')
                                    <div class=" col-md-2 ms-auto position-relative">
                                        <label for="search" class="form-label">Group By</label>
                                        <select class="form-select select2" name="group_by" wire:model='group_by'>
                                            <option value="">Select</option>
                                            <option value="type">Type</option>
                                        </select>
                                    </div>
                                @endif
                            </div>
                            <button wire:click='listSamplesCounts' class="btn-btn-success">Submit</button>
                            <!-- end row-->
                            {{-- </form> --}}
                        </div>
                    </div>
                    @if ($return_type == 'Count')
                        {{-- {{ $samples }} --}}
                        <table class="table table-striped mb-0 w-100 sortable">
                            <tr>
                                <td>Sample Type</td>
                                <td>id</td>
                                <td>total</td>
                            </tr>
                            @foreach ($samples as $sample)
                                <tr>
                                    <td>
                                        {{ $sample->sample_type_name ?? 'N/A' }}
                                    </td>
                                    <td>
                                        {{ $sample->new_sample_type ?? 'N/A' }}
                                    </td>
                                    <td>
                                        {{ $sample->total_samples ?? 'N/A' }}
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    @else
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
                                            <th>For</th>
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
                                                    @if ($sample->sample_is_for == 'Testing')
                                                        <span
                                                            class="badge bg-success">{{ $sample->sample_is_for }}</span>
                                                    @elseif($sample->sample_is_for == 'Deffered')
                                                        <span
                                                            class="badge bg-danger">{{ $sample->sample_is_for }}</span>
                                                    @elseif($sample->sample_is_for == 'Aliquoting')
                                                        <span
                                                            class="badge bg-info">{{ $sample->sample_is_for }}</span>
                                                    @else
                                                        <span
                                                            class="badge bg-warning">{{ $sample->sample_is_for }}</span>
                                                    @endif
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
                                                        <button class="btn btn-sm btn-outline-primary"
                                                            wire:click="editSample({{ $sample->id }})"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#edit-sample-modal"><i
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
                                        {{-- {{ $samples->links() }} --}}
                                    </div>
                                </div>
                            </div>
                        </div> <!-- end tab-content-->
                    @endif
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div><!-- end col-->

    </div>


    {{-- =============EDIT SAMPLE INFORMATION====================== --}}


    {{-- //SAMPLE RECALL CONFIRMATION MODAL --}}


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
                                    <div class="icon-box bg-light-primary border-0">
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
                                            <div class="icon-box bg-light-primary border-0">
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
