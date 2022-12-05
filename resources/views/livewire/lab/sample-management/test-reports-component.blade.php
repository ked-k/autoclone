<div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pt-0">
                    <div class="row mb-2">
                        <div class="col-sm-12 mt-3">
                            <div class="d-sm-flex align-items-center">
                                <h5 class="mb-2 mb-sm-0">
                                    Test Result Reports
                                </h5>
                                <div class="ms-auto">
                                    @if (count($combinedList)>=2)
                                    <a href="javascript:;" class="btn btn-sm btn-info me-2" wire:click='combinedTestReport'><i class="bi bi-list"></i>
                                        Combined Test Report
                                    </a>
                                    @endif
                                    
                                    <a type="button" class="btn btn-outline-info" wire:click="refresh()"
                                        data-bs-toggle="tooltip" data-bs-placement="top" title=""
                                        data-bs-original-title="Refresh Table"><i class="bi bi-arrow-clockwise"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if (count($combinedList)>=2)
                    You have selected <strong class="text-success">{{ count($combinedList) }}</strong> samples for the combined test report (<a href="javascript:;" class="text-info" wire:click="$set('combinedList',[])">Clear All</a>)
                    @endif
                </div>

                <div class="card-body">
                    <x-table-utilities display='d-none'>
                        <div>
                            <div class="d-flex align-items-center ml-4 me-2">
                                <label for="orderBy" class="text-nowrap mr-2 mb-0">OrderBy</label>
                                <select wire:model="orderBy" class="form-select">
                                    <option value="approved_at">Latest</option>
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
                                        <th>Tracker</th>
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
                                                <a href="{{ URL::signedRoute('report-search-results', ['testResult' => $testResult->id]) }}"
                                                    target="_blank"><strong
                                                        class="text-info">{{ $testResult->tracker }}</strong>
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
                                                <input type="checkbox" value="{{$testResult->sample->id}}" class="me-2 float-end" wire:model="combinedList">
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
                                                <span class="badge bg-success">{{ $testResult->status }}</span>
                                            </td>
                                            <td class="action-ico">
                                                <a href="{{ route('result-report', $testResult->id) }}" type="button"
                                                    data-bs-toggle="tooltip" data-bs-placement="bottom" title=""
                                                    data-bs-original-title="Result Report"
                                                    class="action-ico btn btn-outline-info"
                                                    wire:click='incrementDownloadCount({{ $testResult->id }})'><i
                                                        class="bi bi-arrow-down-square"></i></a>
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

    </div>

    @push('scripts')
    <script>
        window.addEventListener('loadCombinedReport', event => {
            window.open(`${event.detail.url}`, '_blank').focus();
        });
    </script>
@endpush
</div>
