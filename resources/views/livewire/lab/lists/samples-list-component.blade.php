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
                                        Samples
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
                                        <select class="form-select" id="facility_id" wire:model="facility_id">
                                            <option selected value="0">All</option>
                                            @forelse ($facilities as $facility)
                                                <option value='{{ $facility->id }}'>{{ $facility->name }}</option>
                                            @empty
                                            @endforelse
                                        </select>
                                        @error('facility_id')
                                            <div class="text-danger text-small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3 col-md-3">
                                        <label for="study" class="form-label">Study</label>
                                        <select class="form-select" id="study" wire:model="study_id">
                                            <option selected value="0">All</option>
                                            @forelse ($studies as $study)
                                                <option value='{{ $study->id }}'>{{ $study->name }}</option>
                                            @empty
                                            @endforelse
                                        </select>
                                        @error('study_id')
                                            <div class="text-danger text-small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3 col-md-2">
                                        <label for="job" class="form-label">Sample State</label>
                                        <select class="form-select" id="job" wire:model="job">
                                            <option selected value="">All</option>
                                            @forelse ($jobs as $job)
                                                <option value='{{ $job->sample_is_for }}'>{{ $job->sample_is_for }}
                                                </option>
                                            @empty
                                            @endforelse
                                        </select>
                                        @error('job')
                                            <div class="text-danger text-small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3 col-md-2">
                                        <label for="sampleType" class="form-label">Sample Type</label>
                                        <select class="form-select" id="sampleType"
                                        wire:model='sampleType'>
                                        <option selected value="0">All</option>
                                        @foreach ($sampleTypes as $sampleType)
                                            <option value='{{ $sampleType->id }}'>
                                                {{ $sampleType->type }}</option>
                                        @endforeach
                                    </select>
                                        @error('sampleType')
                                            <div class="text-danger text-small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3 col-md-2">
                                        <label for="from_date" class="form-label">Start Date</label>
                                        <input id="from_date" type="date" class="form-control"
                                            wire:model="from_date">
                                        @error('from_date')
                                            <div class="text-danger text-small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3 col-md-2">
                                        <label for="to_date" class="form-label">End Date</label>
                                        <input id="to_date" type="date" class="form-control" wire:model="to_date">
                                        @error('to_date')
                                            <div class="text-danger text-small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3 col-md-2">
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
                                        <th>Batch No</th>
                                        <th>Participant ID</th>
                                        <th>Sample</th>
                                        <th>Sample ID</th>
                                        <th>Lab No</th>
                                        <th>Facility</th>
                                        <th>Study</th>
                                        <th>For</th>
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
                                                    <span class="badge bg-success">{{ $sample->sample_is_for }}</span>
                                                @elseif($sample->sample_is_for == 'Deffered')
                                                    <span class="badge bg-warning">{{ $sample->sample_is_for }}</span>
                                                @else
                                                    <span class="badge bg-info">{{ $sample->sample_is_for }}</span>
                                                @endif
                                            </td>
                                            <td class="table-action">
                                                @if ($sample->sample_is_for == 'Deffered')
                                                {{-- <a href="javascript: void(0);"
                                                class="btn btn-outline-success" data-bs-toggle="tooltip"
                                                data-bs-placement="bottom" title=""
                                                data-bs-original-title="Recall for Testing" wire:click="recallSampleConfirmation({{ $sample->id }})"><i   class="bi bi-arrow-90deg-left"
                                                    ></i></a> --}}

                                                <button class="btn btn-outline-success" wire:click="recallSampleConfirmation({{ $sample->id }})"><i   class="bi bi-arrow-90deg-left"
                                                    ></i></button>
                                                @elseif($sample->sample_is_for == 'Testing')
                                                    <a href="{{ URL::signedRoute('sample-search-results', ['sample' => $sample->id]) }}"
                                                        type="button" class="btn btn-outline-info"
                                                        data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                        title="" data-bs-original-title="View Details"
                                                        target="_blank"><i class="bi bi-eye"></i>
                                                    </a>
                                                @else
                                                    N/A
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

    @push('scripts')
        <script>
            window.addEventListener('close-modal', event => {
                $('#recall-confirmation-modal').modal('hide');
            });

            window.addEventListener('recall-confirmation', event => {
                $('#recall-confirmation-modal').modal('show');
            });
        </script>
    @endpush
</div>
