<div>
    <div class="row">
        <div class="card radius-10 col-12">
            <div class="card-header bg-transparent">
                <div class="row g-3 align-items-center">
                    <div class="col">
                        <h5 class="mb-0">Dashboard</h5>
                    </div>
                    <div class="col">
                        <select wire:model="view" class="form-select">
                            <option value="all">View All</option>
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                            <option value="year">This Year</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 row-cols-xxl-4">

        <div class="col">
            <div class="card radius-10 border-start border-purple border-3">
                <div class="card-body">
                    <p>TOTAL SAMPLE RECEPTION</p>
                    <h2 class="text-center fw-light">{{ $samplesDelivered }}</h2>
                    <div id="chart1"></div>
                </div>
                <div class="card-footer bg-transparent border-top">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="text-center">
                            <p class="font-13 mb-0 text-success">Accepted</p>
                            <p class="mb-0 fw-bold">{{ $samplesAccepted }}</p>
                        </div>
                        <div class="text-center">
                            <p class="font-13 mb-0 text-danger">Rejected</p>
                            <p class="mb-0 fw-bold">{{ $samplesRejected }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card radius-10 border-start border-info border-3">
                <div class="card-body">
                    <p>TOTAL SAMPLES ACCESSIONED</p>
                    <h2 class="text-center fw-light">{{ $sampleAccessioned }}</h2>
                    <div id="chart1"></div>
                </div>
                <div class="card-footer bg-transparent border-top">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="text-center text-success">
                            <p class="font-13 mb-0">For Testing</p>
                            <p class="mb-0 fw-bold">{{ $sampleForTesting }}</p>
                        </div>
                        <div class="text-center">
                            <p class="font-13 mb-0 text-info">Participants</p>
                            <p class="mb-0 fw-bold">
                                {{ $participantCount }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card radius-10 border-start border-success border-3">
                <div class="card-body">
                    <p>TOTAL TESTS ASSIGNED</p>
                    <h2 class="text-center fw-light">{{ $testAssignedCount }}</h2>
                    <div id="chart1"></div>
                </div>
                <div class="card-footer bg-transparent border-top">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="text-center">
                            <p class="font-13 mb-0 text-success">Completed</p>
                            <p class="mb-0 fw-bold">{{ $testDoneAssignedCount }}</p>
                        </div>
                        <div class="text-center">
                            <p class="font-13 mb-0 text-warning">Pending</p>
                            <p class="mb-0 fw-bold">
                                {{ $testAssignedCount - $testDoneAssignedCount }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card radius-10 border-start border-warning border-3">
                <div class="card-body">
                    <p>TOTAL TESTS PERFORMED</p>
                    <h2 class="text-center fw-light">{{ $completedTestResultCount }}</h2>
                    <div id="chart1"></div>
                </div>
                <div class="card-footer bg-transparent border-top">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="text-center">
                            <small class="font-13 mb-0 text-info">Pending Review</small>
                            <p class="mb-0 fw-bold">{{ $testsPendindReviewCount }}</p>
                        </div>
                        <div class="text-center">
                            <small class="font-13 mb-0 text-success">Pending Approval</small>
                            <p class="mb-0 fw-bold">{{ $testsPendindApprovalCount }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">

        <div class="card radius-10 col-12">
            <div class="card-body">

                <livewire:admin.dashboards.user-dashboard-charts-component />
            </div>
        </div>
    </div>
</div>
