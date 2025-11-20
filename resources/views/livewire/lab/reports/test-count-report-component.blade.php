<div class="card">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0">Tests Requested Per Quarter Report</h5>
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
                    <label class="form-label">Study</label>
                    <select class="form-select select2" wire:model="selectedStudy">
                        <option value="">All Studies</option>
                        @foreach ($studies as $study)
                            <option value="{{ $study->id }}">{{ $study->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <div class="form-check mt-4 pt-2">
                        <input type="checkbox" class="form-check-input" id="showZeroCounts" wire:model="showZeroCounts">
                        <label class="form-check-label" for="showZeroCounts">
                            Show tests with zero counts
                        </label>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <label class="form-label">Tests</label>
                            <input wire:model.debounce.300ms="search" class="form-control ps-5" type="text"
                                placeholder="search">
                        </div>
                        <div class="card-body" style="max-height: 234px; overflow-y: auto;">
                            <div class="row">
                                @foreach ($tests as $test)
                                    <div class="col-md-3 mb-2">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" value="{{ $test->id }}"
                                                wire:model="selectedTests" id="test-{{ $test->id }}">
                                            <label class="form-check-label" for="test-{{ $test->id }}">
                                                {{ $test->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-chart-bar me-2"></i> Generate Report
                    </button>
                </div>
            </div>
        </form>

        @if (count($reportData) > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Test requested</th>
                            @foreach ($quarterColumns as $quarter)
                                <th class="text-center">{{ $quarter }}</th>
                            @endforeach
                            <th class="text-center">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reportData as $row)
                            <tr>
                                <td>{{ $row['test_name'] }}</td>
                                @foreach ($quarterColumns as $quarter)
                                    <td class="text-center">{{ $row['quarters'][$quarter] }}</td>
                                @endforeach
                                <td class="text-center fw-bold">{{ $row['total'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-secondary">
                        <tr>
                            <th class="text-end">Quarter Totals:</th>
                            @foreach ($quarterColumns as $quarter)
                                <th class="text-center">{{ $quarterTotals[$quarter] }}</th>
                            @endforeach
                            <th class="text-center">{{ array_sum($quarterTotals) }}</th>
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
