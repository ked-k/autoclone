<div>
    <div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header pt-0">
                <div class="row mb-2">
                    <div class="col-sm-12 mt-3">
                        <div class="d-sm-flex align-items-center">
                            <h5 class="mb-2 mb-sm-0">
                                Referral Requests
                            </h5>
                            <div class="ms-auto">
                                <a type="button" class="btn btn-outline-info" wire:click="refresh()"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title=""
                                    data-bs-original-title="Refresh Table">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <x-table-utilities>
                    <div class="d-flex align-items-center gap-3">
                        <div class=" align-items-center me-2">
                            <label for="search" class="text-nowrap mr-2 mb-0">Search</label>
                            <input type="text" wire:model.debounce.300ms="search" class="form-control"
                                   placeholder="Search requests...">
                        </div>

                        <div class="align-items-center me-2">
                            <label for="statusFilter" class="text-nowrap mr-2 mb-0">Status</label>
                            <select wire:model="statusFilter" class="form-select">
                                <option value="incoming">Incoming</option>
                                <option value="outgoing">Outgoing</option>
                            </select>
                        </div>

                        <div class="align-items-center me-2">
                            <label for="orderBy" class="text-nowrap mr-2 mb-0">Order By</label>
                            <select wire:model="orderBy" class="form-select">
                                <option value="created_at">Date Created</option>
                                <option value="request_no">Request No</option>
                                <option value="status">Status</option>
                                <option value="no_of_samples">Samples Count</option>
                            </select>
                        </div>

                        <div class="align-items-center">
                            <label for="perPage" class="text-nowrap mr-2 mb-0">Per Page</label>
                            <select wire:model="perPage" class="form-select">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
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
                                    <th>Request No</th>
                                    @if(in_array($this->statusFilter, ['outgoing']))
                                        <th>Receiving Institution</th>
                                    @else
                                    <th>Requester Institution</th>
                                    @endif
                                    <th>Samples</th>
                                    <th>Pathogen</th>
                                    <th>Status</th>
                                    <th>Expected Date</th>
                                    <th>Date Created</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($requests as $key => $request)
                                    <tr>
                                        <td>{{ ($requests->currentPage() - 1) * $requests->perPage() + $key + 1 }}</td>
                                        <td>
                                            <strong>{{ $request['request_no'] }}</strong>
                                            @if($request['require_support'])
                                                <span class="badge bg-info ms-1" data-bs-toggle="tooltip"
                                                      title="Requires Support">Support</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div>
                                                @if(in_array($this->statusFilter, ['outgoing']))
                                                    <strong>{{ $request['receiving_institution']['name'] ?? 'N/A' }}</strong>
                                                    @if($request['receiving_institution']['short_code'] ?? false)
                                                        <br>
                                                        <small class="text-muted">
                                                            {{ $request['receiving_institution']['short_code'] }}
                                                        </small>
                                                    @else
                                                    <strong>{{ $request['requester_institution']['name'] ?? 'N/A' }}</strong>
                                                    @if($request['requester_institution']['short_code'] ?? false)
                                                        <br>
                                                        <small class="text-muted">
                                                            {{ $request['requester_institution']['short_code'] }}
                                                        </small>
                                                    @endif
                                                    @if($request['requester_institution']['country'] ?? false)
                                                        <br>
                                                        <small class="text-muted">
                                                            {{ $request['requester_institution']['country']['name'] }}
                                                        </small>
                                                    @endif
                                                @endif

                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $request['no_of_samples'] }}</strong> samples
                                                {{-- @if(count($request['samples'] ?? []) > 0)
                                                    <br>
                                                    <small class="text-muted">
                                                        {{ $request['samples']->first()['specimen_type'] ?? '' }}
                                                    </small>
                                                @endif --}}
                                            </div>
                                        </td>
                                        <td>
                                            {{ $request['pathogen']['name'] ?? 'N/A' }}
                                        </td>
                                        <td>
                                            <span class="badge {{ $this->getStatusBadgeClass($request['status']) }}">
                                                {{ ucfirst($request['status']) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($request['timeline']['expected_date'])
                                                {{ \Carbon\Carbon::parse($request['timeline']['expected_date'])->format('d-m-Y') }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>
                                            @if($request['timeline']['created_at'])
                                                {{ \Carbon\Carbon::parse($request['timeline']['created_at'])->format('d-m-Y') }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td class="table-action">
                                            <a href="javascript:void(0);"
                                               class="action-ico btn btn-outline-info mx-1"
                                               wire:click="viewRequest('{{ $request['request_no'] }}')"
                                               data-bs-toggle="tooltip" title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </a>

                                            @if(in_array(strtolower($request['status']), ['pending', 'submitted']))
                                                <a href="javascript:void(0);"
                                                   class="action-ico btn btn-outline-warning mx-1"
                                                   data-bs-toggle="tooltip" title="Process Request">
                                                    <i class="bi bi-gear"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="bi bi-inbox display-4"></i>
                                                <h5 class="mt-2">No referral requests found</h5>
                                                <p>There are no requests matching your current filters.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between align-items-center">
                                {{-- <div class="text-muted">
                                    Showing {{ $requests->firstItem() ?? 0 }} to {{ $requests->lastItem() ?? 0 }}
                                    of {{ $requests->total() }} entries
                                </div> --}}
                                @if(count($requests) > 0)
                                <div class="btn-group">
                                    {{ $requests->links('vendor.livewire.bootstrap') }}
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .table-action .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }

    .badge {
        font-size: 0.75rem;
    }
</style>
@endpush

@push('scripts')
<script>
    // Initialize tooltips
    document.addEventListener('livewire:load', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });

    // Refresh tooltips when Livewire updates
    document.addEventListener('livewire:update', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>
@endpush
</div>
