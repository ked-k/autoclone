<div>
    <div class="card">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">Laboratory Test Reports</h5>
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
                            <input type="checkbox" class="form-check-input" id="showZeroCounts"
                                wire:model="showZeroCounts">
                            <label class="form-check-label" for="showZeroCounts">
                                Show tests with zero counts
                            </label>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Report Type</label>
                                <div class="btn-group w-100" role="group">
                                    <button type="button"
                                        class="btn {{ $reportType === 'test_count' ? 'btn-info' : 'btn-outline-info' }}"
                                        wire:click="$set('reportType', 'test_count')">
                                        Test Count
                                    </button>
                                    <button type="button"
                                        class="btn {{ $reportType === 'study_count' ? 'btn-info' : 'btn-outline-info' }}"
                                        wire:click="$set('reportType', 'study_count')">
                                        Study Count
                                    </button>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Chart Type</label>
                                <div class="btn-group w-100" role="group">
                                    <button type="button"
                                        class="btn {{ $chartType === 'bar' ? 'btn-info' : 'btn-outline-info' }}"
                                        wire:click="$set('chartType', 'bar')">
                                        <i class="fas fa-chart-bar"></i> Bar
                                    </button>
                                    <button type="button"
                                        class="btn {{ $chartType === 'line' ? 'btn-info' : 'btn-outline-info' }}"
                                        wire:click="$set('chartType', 'line')">
                                        <i class="fas fa-chart-line"></i> Line
                                    </button>
                                    <button type="button"
                                        class="btn {{ $chartType === 'pie' ? 'btn-info' : 'btn-outline-info' }}"
                                        wire:click="$set('chartType', 'pie')">
                                        <i class="fas fa-chart-pie"></i> Pie
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <label class="form-label">Tests</label>
                                {{-- <input wire:model.debounce.300ms="search" class="form-control ps-5" type="text"
                                placeholder="search"> --}}
                            </div>
                            <div class="card-body" style="max-height: 234px; overflow-y: auto;">
                                <div class="row">
                                    @foreach ($tests as $test)
                                        <div class="col-md-3 mb-2">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input"
                                                    value="{{ $test->id }}" wire:model="selectedTests"
                                                    id="test-{{ $test->id }}">
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
                <!-- Chart Section -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            {{ $reportType === 'test_count' ? 'Tests Requested Per Quarter' : 'Studies Requesting Tests Per Quarter' }}
                        </h6>
                    </div>
                    <div class="card-body">
                        @if ($columnChartModel)
                            @if ($chartType === 'pie')
                                <livewire:livewire-pie-chart key="{{ $columnChartModel->reactiveKey() }}"
                                    :pie-chart-model="$columnChartModel" />
                            @elseif($chartType === 'line')
                                <livewire:livewire-line-chart key="{{ $columnChartModel->reactiveKey() }}"
                                    :line-chart-model="$columnChartModel" />
                            @else
                                <livewire:livewire-column-chart key="{{ $columnChartModel->reactiveKey() }}"
                                    :column-chart-model="$columnChartModel" />
                            @endif
                        @else
                            <div class="alert alert-warning">
                                No chart data available
                            </div>
                        @endif

                    </div>
                </div>

                <!-- Report Table -->
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            {{ $reportType === 'test_count' ? 'Detailed Test Count Report' : 'Detailed Study Count Report' }}
                        </h6>
                    </div>
                    <div class="card-body">
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
                                                <td class="text-center">
                                                    {{ $reportType === 'test_count' ? $row['quarters'][$quarter] : $row['study_counts'][$quarter] }}
                                                </td>
                                            @endforeach
                                            <td class="text-center fw-bold">
                                                {{ $reportType === 'test_count' ? $row['test_count_total'] : $row['study_count_total'] }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-secondary">
                                    <tr>
                                        <th class="text-end">Quarter Totals:</th>
                                        @foreach ($quarterColumns as $quarter)
                                            <th class="text-center">{{ $displayTotals[$quarter] }}</th>
                                        @endforeach
                                        <th class="text-center">{{ array_sum($displayTotals) }}
                                        </th>
                                    </tr>
                                </tfoot>

                            </table>
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-warning mt-4">
                    No data found for the selected filters.
                </div>
            @endif
        </div>
    </div>
</div>
