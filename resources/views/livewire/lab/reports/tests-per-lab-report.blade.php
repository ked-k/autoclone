<div class="card">
    <div class="card-header bg-primary text-white">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                @if ($reportType === 'tests')
                    Tests Per Laboratory Report
                @else
                    Samples Per Laboratory Report
                @endif
            </h5>
            <div>
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
                    <label class="form-label">Start Year</label>
                    <input type="number" class="form-control" wire:model="startYear" min="2000" max="2100">
                </div>

                <div class="col-md-3">
                    <label class="form-label">End Year</label>
                    <input type="number" class="form-control" wire:model="endYear" min="{{ $startYear }}"
                        max="2100">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Report Type</label>
                    <div class="btn-group w-100" role="group">
                        <button type="button"
                            class="btn {{ $reportType === 'tests' ? 'btn-primary' : 'btn-outline-primary' }}"
                            wire:click="$set('reportType', 'tests')">
                            Tests
                        </button>
                        <button type="button"
                            class="btn {{ $reportType === 'samples' ? 'btn-primary' : 'btn-outline-primary' }}"
                            wire:click="$set('reportType', 'samples')">
                            Samples
                        </button>
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Laboratories</label>
                    <select class="form-select" wire:model="selectedLabs" multiple>
                        @foreach ($allLabs as $lab)
                            <option value="{{ $lab->id }}">{{ $lab->laboratory_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-chart-bar me-2"></i> Generate Report
                    </button>
                </div>
            </div>
        </form>

        @if (count($reportData) > 0)
            <div class="alert alert-info">
                Total {{ $reportType === 'tests' ? 'Tests' : 'Samples' }}:
                <strong>{{ number_format($totalCount) }}</strong>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Laboratory</th>
                            @foreach ($years as $year)
                                <th class="text-center">{{ $year }}</th>
                            @endforeach
                            <th class="text-center">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reportData as $lab)
                            <tr>
                                <td>{{ $lab['name'] }}</td>
                                @foreach ($years as $year)
                                    <td class="text-center">{{ number_format($lab['years'][$year]) }}</td>
                                @endforeach
                                <td class="text-center fw-bold">{{ number_format($lab['total']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-secondary">
                        <tr>
                            <th class="text-end">Year Total</th>
                            @foreach ($years as $year)
                                @php
                                    $yearTotal = 0;
                                    foreach ($reportData as $lab) {
                                        $yearTotal += $lab['years'][$year];
                                    }
                                @endphp
                                <th class="text-center">{{ number_format($yearTotal) }}</th>
                            @endforeach
                            <th class="text-center">{{ number_format($totalCount) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="alert alert-warning mt-4">
                No data found for the selected filters.
            </div>
        @endif
    </div>
</div>
