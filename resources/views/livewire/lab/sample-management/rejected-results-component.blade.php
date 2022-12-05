<div>
    <div class="row">
        @if (!$viewReport)
            <div class="col-12">

                <div class="card">
                    <div class="card-header pt-0">
                        <div class="row mb-2">
                            <div class="col-sm-12 mt-3">
                                <div class="d-sm-flex align-items-center">
                                    <h5 class="mb-2 mb-sm-0 text-danger">
                                        Rejected Test Results
                                    </h5>
                                    <div class="ms-auto">
                                        <a type="button" class="btn btn-outline-info" wire:click="refresh()"
                                            data-bs-toggle="tooltip" data-bs-placement="top" title=""
                                            data-bs-original-title="Refresh Table"><i
                                                class="bi bi-arrow-clockwise"></i></a>
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
                                            <th>Tracker</th>
                                            <th>Sample Batch</th>
                                            <th>Study</th>
                                            <th>Participant ID</th>
                                            <th>Sample</th>
                                            <th>Test</th>
                                            <th>Requester</th>
                                            <th>Requested At</th>
                                            <th>Received At</th>
                                            <th>Result Date</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($testResults as $key => $testResult)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>
                                                    <strong class="text-info">{{ $testResult->tracker }}</strong>
                                                </td>
                                                <td>
                                                    <a href="{{ URL::signedRoute('batch-search-results', ['sampleReception' => $testResult->sample->sampleReception->id]) }}"
                                                        class="text-secondary"
                                                        target="_blank">{{ $testResult->sample->sampleReception->batch_no }}
                                                    </a>
                                                </td>
                                                <td>
                                                    {{ $testResult->sample->study->name ?? 'N/A' }}
                                                </td>
                                                <td>
                                                    <a href="{{ URL::signedRoute('participant-search-results', ['participant' => $testResult->sample->participant->id]) }}"
                                                        class="text-secondary"
                                                        target="_blank">{{ $testResult->sample->participant->identity }}
                                                    </a>
                                                </td>
                                                <td>
                                                    {{ $testResult->sample->sampleType->type }}
                                                </td>
                                                <td>
                                                    {{ $testResult->test->name }}
                                                </td>
                                                <td>
                                                    {{ $testResult->sample->requester->name }}
                                                </td>
                                                <td>
                                                    {{ date('d-m-Y', strtotime($testResult->sample->date_requested)) }}
                                                </td>
                                                <td>
                                                    {{ date('d-m-Y H:i', strtotime($testResult->sample->sampleReception->date_delivered)) }}
                                                </td>
                                                <td>
                                                    {{ $testResult->created_at }}
                                                </td>

                                                <td>
                                                    <span class="badge bg-danger">{{ $testResult->status }}</span>
                                                </td>
                                                <td>
                                                    <button type="button"
                                                        wire:click="viewPreliminaryReport({{ $testResult->id }})"
                                                        class="action-ico btn btn-outline-info"><i
                                                            class="bi bi-eye"></i></button>
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
                                        {{ $testResults->links() }}
                                    </div>
                                </div>
                            </div>
                        </div> <!-- end tab-content-->
                    </div> <!-- end card body-->
                </div> <!-- end card -->
            </div><!-- end col-->
        @else
            <div class="col-12">
                @include('reports.sample-management.preliminary-report')
            </div>
        @endif
    </div>
</div>
