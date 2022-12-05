<div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pt-0">
                    <div class="row mb-2">
                        <div class="col-sm-12 mt-3">
                            <div class="d-sm-flex align-items-center">
                                <h5 class="mb-2 mb-sm-0">
                                    Test Requests
                                </h5>
                                <div class="ms-auto">
                                    <a type="button" class="btn btn-outline-info" wire:click="refresh()"
                                        data-bs-toggle="tooltip" data-bs-placement="top" title=""
                                        data-bs-original-title="Refresh Table"><i class="bi bi-arrow-clockwise"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <x-table-utilities display='d-none'>
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
                                        <th>Batch No</th>
                                        <th>Participant ID</th>
                                        <th>Sample</th>
                                        <th>Sample ID</th>
                                        <th>Lab No</th>
                                        <th>Study</th>
                                        <th>Requested By</th>
                                        <th>Collected By</th>
                                        <th>Test Count</th>
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
                                                <a href="{{ URL::signedRoute('batch-search-results', ['sampleReception' => $sample->sampleReception->id]) }}"
                                                    class="text-secondary"
                                                    target="_blank">{{ $sample->sampleReception->batch_no }}
                                                </a>
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
                                                <a href="javascript: void(0);"
                                                    wire:click="viewTests({{ $sample->id }})" class="action-ico">
                                                    <strong class="text-success">{{ $sample->lab_no }}</strong>
                                                </a>
                                            </td>
                                            <td>
                                                {{ $sample->study->name??'N/A' }}
                                            </td>
                                            <td>
                                                {{ $sample->requester->name }}
                                            </td>
                                            <td>
                                                {{ $sample->collector->name??'N/A' }}
                                            </td>
                                            <td>
                                                {{ $sample->test_count }}
                                            </td>
                                            @if ($sample->priority == 'Normal')
                                                <td><span class="badge bg-info">{{ $sample->priority }}</span>
                                                </td>
                                            @else
                                                <td><span class="badge bg-danger">{{ $sample->priority }}</span>
                                                </td>
                                            @endif
                                            <td>
                                                @if ($sample->status == 'Assigned')
                                                    <span class="badge bg-warning">{{ $sample->status }}</span>
                                                @else
                                                    <span class="badge bg-info">{{ $sample->status }}</span>
                                                @endif

                                            </td>
                                            <td class="table-action">
                                                <a href="{{ route('attach-test-results', $sample->id) }}"
                                                    type="button" class="btn btn-outline-info" data-bs-toggle="tooltip"
                                                    data-bs-placement="bottom" title=""
                                                    data-bs-original-title="Attach Results"><i
                                                        class="bi bi-file-earmark-medical"></i></a>
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

        <div wire:ignore.self class="modal fade" id="view-tests" data-bs-backdrop="static" data-bs-keyboard="false"
            tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title" id="staticBackdropLabel">Tests for sample (<span
                                class="text-info">{{ $sample_identity }}</span>) with Lab_No <span
                                class="text-info">{{ $lab_no }}</span></h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"
                            wire:click="close()"></button>
                    </div> <!-- end modal header -->
                    <div class="row">
                        <div class="col-md-12">
                            <ul class="list-group">
                                @forelse ($tests_requested as $test)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        {{ $test->name }}
                                    </li>
                                @empty
                                @endforelse
                            </ul>
                        </div>
                        @if ($clinical_notes)
                            <div class="col-md-12">
                                <div class="card-body text-center">
                                    <div>
                                        <h5 class="card-title">Clinical Notes</h5>
                                    </div>
                                    <p class="card-text">{{ $clinical_notes }}</p>
                                </div>
                            </div>
                        @endif

                    </div>

                    <div class="modal-footer">
                        @if ($request_acknowledged_by)
                            <a href="{{ route('attach-test-results', $sample_id) }}" type="button"
                                class="btn btn-success radius-30 px-3">Process</a>
                        @endif

                        <button class="btn  btn-danger radius-30 px-3" wire:click="close()" data-bs-dismiss="modal"
                            aria-label="Close">Close</button>
                    </div>
                </div>
            </div>
        </div>

        @push('scripts')
            <script>
                window.addEventListener('close-modal', event => {
                    $('#view_tests').modal('hide');
                });

                window.addEventListener('view-tests', event => {
                    $('#view-tests').modal('show');
                });
            </script>
        @endpush
    </div>
</div>
