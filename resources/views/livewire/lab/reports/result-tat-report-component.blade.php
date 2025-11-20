<div>
    <div class="card">
        <div class="card-header pt-0">
            <div class="row mb-2">
                <div class="col-sm-12 mt-3">
                    <div class="d-sm-flex align-items-center">
                        <h5 class="mb-2 mb-sm-0">Test Results TAT Report</h5>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label">Start Date</label>
                    <input type="date" class="form-control" wire:model="startDate">
                </div>
                <div class="col-md-3">
                    <label class="form-label">End Date</label>
                    <input type="date" class="form-control" wire:model="endDate">
                </div>
                {{-- <div class="col-md-3">
                    <label class="form-label">Group By</label>
                    <select class="form-select" wire:model="groupedBy">
                        <option value="test">Test</option>
                        <option value="study">Study</option>
                        <option value="user">User</option>
                    </select>
                </div> --}}
                <div class="col-md-3">
                    <div class="form-check mt-4">
                        <input type="checkbox" class="form-check-input" id="showDetails" wire:model="showDetails">
                        <label class="form-check-label" for="showDetails">Show Summary</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if ($showDetails && count($resultTat) > 0)
                <div class="mt-4">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card border">
                                <div class="card-body">
                                    <h5 class="card-title">TAT Summary Statistics (Hours)</h5>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="border-end">
                                                <p class="mb-1"><strong>Reception to Acknowledge</strong></p>
                                                <h4 class="mb-0">
                                                    {{ number_format($resultTat->pluck('tat_details.reception_to_acknowledge')->filter()->average(), 1) }}
                                                </h4>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="border-end">
                                                <p class="mb-1"><strong>Acknowledge to Result</strong></p>
                                                <h4 class="mb-0">
                                                    {{ number_format($resultTat->pluck('tat_details.acknowledge_to_result')->filter()->average(), 1) }}
                                                </h4>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="border-end">
                                                <p class="mb-1"><strong>Result to Review</strong></p>
                                                <h4 class="mb-0">
                                                    {{ number_format($resultTat->pluck('tat_details.result_to_review')->filter()->average(), 1) }}
                                                </h4>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div>
                                                <p class="mb-1"><strong>Total TAT</strong></p>
                                                <h4 class="mb-0">
                                                    {{ number_format($resultTat->pluck('tat_details.total_tat')->filter()->average(), 1) }}
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="datableButton" class="table table-striped mb-0 w-100 sortable">
                    <thead>
                        <tr>
                            <th>Test</th>
                            <th>Sample ID</th>
                            <th>Lab No</th>
                            <th>Study</th>
                            <th>Reception to Acknowledge</th>
                            <th>Acknowledge to Result</th>
                            <th>Result to Review</th>
                            <th>Review to Approval</th>
                            <th>Total TAT (hrs)</th>
                            <th>Performed By</th>
                            <th>Reviewed By</th>
                            <th>Approved By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($resultTat as $result)
                            <tr>
                                <td>{{ $result['test_name'] }}</td>
                                <td>{{ $result['sample_identity'] }}</td>
                                <td>{{ $result['lab_no'] }}</td>
                                <td>{{ $result['study'] }}</td>
                                <td>{{ $result['tat_details']['reception_to_acknowledge'] ?? 'N/A' }}</td>
                                <td>{{ $result['tat_details']['acknowledge_to_result'] ?? 'N/A' }}</td>
                                <td>{{ $result['tat_details']['result_to_review'] ?? 'N/A' }}</td>
                                <td>{{ $result['tat_details']['review_to_approval'] ?? 'N/A' }}</td>
                                <td>{{ $result['tat_details']['total_tat'] ?? 'N/A' }}</td>
                                <td>{{ $result['performer'] }}</td>
                                <td>{{ $result['reviewer'] }}</td>
                                <td>{{ $result['approver'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-center">No results found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>


        </div>
    </div>
</div>
