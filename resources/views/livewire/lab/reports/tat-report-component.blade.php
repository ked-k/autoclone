<div class="card" id="tat-report">
    <div class="card-header bg-info text-white">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Turnaround Time (TAT) Report</h5>
            <div>
                <button wire:click="printReport" class="btn btn-light btn-sm me-2">
                    <i class="fas fa-print"></i> Print
                </button>
                <button wire:click="exportCsv" class="btn btn-light btn-sm">
                    <i class="fas fa-file-csv"></i> Export CSV
                </button>
            </div>
        </div>
    </div>

    <div class="card-body">
        <form wire:submit.prevent="generateReport">
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label">Start Date</label>
                    <input type="date" class="form-control" wire:model="startDate">
                </div>

                <div class="col-md-3">
                    <label class="form-label">End Date</label>
                    <input type="date" class="form-control" wire:model="endDate">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Time Frame</label>
                    <select class="form-select" wire:model="timeframe">
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly" selected>Monthly</option>
                        <option value="quarterly">Quarterly</option>
                        <option value="yearly">Yearly</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Group By</label>
                    <select class="form-select" wire:model="groupedBy">
                        <option value="test">Test</option>
                        <option value="sample">Sample</option>
                        <option value="study">Study</option>
                        <option value="user">Approver</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <div class="form-check mt-4 pt-2">
                        <input type="checkbox" class="form-check-input" id="showDetails" wire:model="showDetails">
                        <label class="form-check-label" for="showDetails">
                            Show Detailed View
                        </label>
                    </div>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-chart-bar me-2"></i> Generate Report
                    </button>
                </div>
            </div>
        </form>

        @if (count($tatData) > 0)
            <div class="alert alert-info">
                Overall Average TAT: <strong>{{ $averageTat }} days</strong>
            </div>

            <!-- Summary Table -->
            <div class="table-responsive mb-5">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>{{ $groupedBy === 'test' ? 'Test' : ucfirst($groupedBy) }}</th>
                            @foreach ($periods as $period)
                                <th class="text-center">{{ $period }}</th>
                            @endforeach
                            <th class="text-center">Average TAT</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tatData as $group)
                            <tr>
                                <td>{{ $group['name'] }}</td>
                                @foreach ($periods as $period)
                                    <td class="text-center">
                                        @if ($group['periods'][$period]['average_tat'] === 'N/A')
                                            <span class="text-muted">N/A</span>
                                        @else
                                            {{ $group['periods'][$period]['average_tat'] }} days
                                            @if ($group['periods'][$period]['count'] > 0)
                                                <br><small
                                                    class="text-muted">(n={{ $group['periods'][$period]['count'] }})</small>
                                            @endif
                                        @endif
                                    </td>
                                @endforeach
                                <td class="text-center fw-bold">
                                    {{ $group['average_tat'] }} days
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-secondary">
                        <tr>
                            <th class="text-end">Period Average</th>
                            @foreach ($periods as $period)
                                @php
                                    $periodAvg = 0;
                                    $periodCount = 0;
                                    foreach ($tatData as $group) {
                                        if ($group['periods'][$period]['count'] > 0) {
                                            $periodAvg += $group['periods'][$period]['average_tat'];
                                            $periodCount++;
                                        }
                                    }
                                    $periodAverage = $periodCount ? round($periodAvg / $periodCount, 1) : 'N/A';
                                @endphp
                                <td class="text-center fw-bold">
                                    {{ $periodAverage !== 'N/A' ? $periodAverage . ' days' : 'N/A' }}
                                </td>
                            @endforeach
                            <td class="text-center fw-bold">
                                {{ $averageTat }} days
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Detailed View -->
            @if ($showDetails)
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Detailed TAT Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="accordion" id="detailsAccordion">
                            @foreach ($tatData as $groupKey => $group)
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading{{ $groupKey }}">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapse{{ $groupKey }}" aria-expanded="true"
                                            aria-controls="collapse{{ $groupKey }}">
                                            {{ $group['name'] }} ({{ count($group['details']) }} results)
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $groupKey }}" class="accordion-collapse collapse"
                                        aria-labelledby="heading{{ $groupKey }}"
                                        data-bs-parent="#detailsAccordion">
                                        <div class="accordion-body">
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Sample ID</th>
                                                            <th>Lab No</th>
                                                            <th>Collection Date</th>
                                                            <th>Approval Date</th>
                                                            <th>TAT (Days)</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($group['details'] as $detail)
                                                            <tr>
                                                                <td>{{ $detail['sample_id'] }}</td>
                                                                <td>{{ $detail['lab_no'] }}</td>
                                                                <td>{{ \Carbon\Carbon::parse($detail['collection_date'])->format('d M Y') }}
                                                                </td>
                                                                <td>{{ \Carbon\Carbon::parse($detail['approval_date'])->format('d M Y') }}
                                                                </td>
                                                                <td>{{ $detail['tat'] }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        @else
            <div class="alert alert-warning mt-4">
                No data found for the selected filters.
            </div>
        @endif
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('livewire:load', function() {
            Livewire.on('print-ready', () => {
                // Trigger print after a small delay to ensure DOM updates
                setTimeout(() => {
                    const printContents = document.getElementById('tat-report').innerHTML;
                    const originalContents = document.body.innerHTML;

                    document.body.innerHTML = printContents;
                    window.print();
                    document.body.innerHTML = originalContents;

                    // Reinitialize Livewire after print
                    Livewire.rescan();
                }, 500);
            });
        });
    </script>
@endpush
