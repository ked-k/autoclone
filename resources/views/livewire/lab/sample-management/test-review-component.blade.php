<div>
    <div class="row">
        @if (!$viewReport)
            <div class="col-12">

                <div class="card">
                    <div class="card-header pt-0">
                        <div class="row mb-2">
                            <div class="col-sm-12 mt-3">
                                <div class="d-sm-flex align-items-center">
                                    <h5 class="mb-2 mb-sm-0">
                                        Test Result Reviews
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
                                                    <a href="{{ route('result-report', $testResult->id) }}"
                                                        type="button" data-bs-toggle="tooltip"
                                                        data-bs-placement="bottom" title=""
                                                        data-bs-original-title="Preliminary Result Report"
                                                        class="text-info">{{ $testResult->test->name }}</a>
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
                                                    <span class="badge bg-success">{{ $testResult->status }}</span>
                                                </td>
                                                <td>
                                                    <a href="javascript: void(0);" type="button"
                                                        data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                        title="" data-bs-original-title="Review Results"
                                                        wire:click="viewPreliminaryReport({{ $testResult->id }})"
                                                        class="action-ico btn btn-outline-info"><i
                                                            class="bi bi-check-square"></i></a>
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
